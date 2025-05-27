<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;
use App\Models\Articel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class ContentGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $query = $request->input('query');
        if (!$query) {
            return response()->json(['error' => 'Keyword tidak boleh kosong'], 400);
        }        
        $searchUrl = 'https://www.googleapis.com/customsearch/v1';
        $apiKey = env('GOOGLE_API_KEY');
        $cx = env('GOOGLE_CX');

        $linkStorage = storage_path('app/content-generate/links.txt');
        if (!file_exists(dirname($linkStorage))) {
            mkdir(dirname($linkStorage), 0777, true);
        }

        $existingLinks = file_exists($linkStorage)
            ? file($linkStorage, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            : [];

        $newLinks = [];
        $startIndex = 1;
        $maxAttempts = 5;

        // 1. Looping cari link baru
        while ($maxAttempts-- > 0) {
            $response = Http::get($searchUrl, [
                'key' => $apiKey,
                'cx' => $cx,
                'q' => $query,
                'start' => $startIndex
            ]);

            $items = $response->json('items') ?? [];
            if (empty($items)) break;

            foreach ($items as $item) {
                $link = $item['link'];
                if (!in_array($link, $existingLinks)) {
                    $newLinks[] = $link;
                    file_put_contents($linkStorage, $link . PHP_EOL, FILE_APPEND);
                }
                if (count($newLinks) >= 3) break 2;
            }

            $startIndex += 10;
            sleep(1);
        }

        if (empty($newLinks)) {
            return response()->json(['error' => 'Tidak ada link baru yang ditemukan.']);
        }

        // 2. Scraping

        $allContent = '';
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        foreach ($newLinks as $link) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => $userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])->get($link);

                $html = $response->body();

                preg_match_all('/<(p|article|section|div|h[1-3])[^>]>(.?)<\/\1>/is', $html, $matches);

                $filtered = array_filter($matches[2], function ($text) {
                    return str_word_count(strip_tags($text)) > 30;
                });

                $text = implode("\n\n", array_map('strip_tags', $filtered));

                if (strlen($text) > 15000) {
                    $cut = strpos($text, '.', 14500);
                    $text = $cut !== false ? substr($text, 0, $cut + 1) : substr($text, 0, 15000);
                }

                $allContent .= $text . "\n\n";
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!$allContent) {
            return response()->json(['error' => 'Tidak ada konten yang bisa diambil dari link.']);
        }
        
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        // 3. Optimasi dengan OpenAI
        $promptMain = "Terjemahkan teks berikut ke dalam Bahasa Indonesia dengan gaya yang baik tanpa mengurangi jumlah kata. Jika jumlah kata kurang dari 150, kamu bisa tambahkan kata-kata yang sangat banyak dengan topik yang sama agar kata-kata konten yang di optimalkan lebih banyak. Lalu, buat kesimpulan di kalimat terakhir tanpa menyebutkan kalimat itu kesimpulan.\n\n" . $allContent;
        $responseMain = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptMain]],
        ]);

        $optimized = $responseMain['choices'][0]['message']['content'];

        // 4. Buat Judul
        $promptTitle = "Buatkan SATU judul artikel yang menarik dalam Bahasa Indonesia berdasarkan konten berikut:\n\n" . $optimized;
        $responseTitle = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptTitle]],
        ]);

        $title = $responseTitle['choices'][0]['message']['content'];

        // 5. buat kategori
        $promptCategory = "Buatkan keyword-keyword tanpa menyebutkan kata dan judul keyword berdasarkan konten berikut:\n\n" . $optimized;
        $responseCategory = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptCategory]],
        ]);

        $category = $responseCategory['choices'][0]['message']['content'];

        return response()->json([
            'title' => trim($title),
            'content' => trim($optimized),
            'category' => trim($category)
        ]);
    }
    //Baca artikel apa kek
        public function read_all()
    {
        try {
            $artikels = Articel::all(); // Ambil semua artikel
            return response()->json([
                'success' => true,
                'data' => $artikels
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data artikel: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data artikel'
            ], 500);
        }
    }

    public function optimize(Request $request)
    {
        $content = $request->input('content');

        if (!$content) {
            return response()->json(['error' => 'Tidak ada konten yang bisa di optimize']);
        }

        //Optimasi dengan OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $promptMain = "Bisa kah kamu optimize konten ini?. Optimize ini tidak mengurangi jumlah kata, Namun menambahkan jumlah kata lebih banyak, SANGAT BANYAK.\n\n" . $content;
        $responseMain = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptMain]],
        ]);

        $optimized = $responseMain['choices'][0]['message']['content'];

        //Buat Judul
        $promptTitle = "Buatkan SATU judul artikel yang menarik dalam Bahasa Indonesia berdasarkan konten berikut:\n\n" . $optimized;
        $responseTitle = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptTitle]],
        ]);

        $title = $responseTitle['choices'][0]['message']['content'];
        
        // buat kategori
        $promptCategory = "Buatkan keyword-keyword tanpa menyebutkan kata dan judul keyword berdasarkan konten berikut:\n\n" . $optimized;
        $responseCategory = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptCategory]],
        ]);

        $category = $responseCategory['choices'][0]['message']['content'];

        return response()->json([
            'title' => trim($title),
            'content' => trim($optimized),
            'category' => trim($category)
        ]);
    }    

    //Update artikel
    public function updateArtikel(Request $request, $id)
{
    Log::info("Mulai update artikel dengan id: $id");

    try {
        $artikel = Articel::find($id);
        if (!$artikel) {
            Log::warning("Artikel dengan id $id tidak ditemukan");
            return response()->json(['error' => 'Artikel tidak ditemukan.'], 404);
        }

        $validatedData = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'pemilik' => 'sometimes|string|max:100',
            'kategori' => 'sometimes|string',
            'isi_konten' => 'sometimes|string',
            'status' => 'sometimes|string|max:255',
            'gambar' => 'sometimes|string|max:255',
        ]);

        $artikel->update($validatedData);

        Log::info("Artikel berhasil diperbarui: ID $id");

        return response()->json([
            'message' => 'Artikel berhasil diperbarui.',
            'data' => $artikel
        ]);
    } catch (\Exception $e) {
        Log::error("Error saat update artikel: " . $e->getMessage());
        return response()->json([
            'error' => 'Terjadi kesalahan server.',
            'message' => $e->getMessage()
        ], 500);
    }
}


      public function destroy($id)
    {
        Log::info("Mulai menghapus artikel dengan id: $id");

        try {
            $artikel = Articel::find($id);
            if (!$artikel) {
                Log::warning("Artikel dengan id $id tidak ditemukan");
                return response()->json(['error' => 'Artikel tidak ditemukan.'], 404);
            }

            $artikel->status = 'sampah';
            $artikel->save();

            Log::info("Artikel berhasil dihapus: ID $id");

            return response()->json([
                'message' => 'Status artikel berhasil diubah menjadi "hapus".',
                'data' => $artikel
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat menerbitkan artikel: " . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan server.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    


    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string',
            'pemilik' => 'required|string',
            'kategori' => 'required|string',
            'isi_konten' => 'required|string',
        ]);

        $validated['tanggal'] = Carbon::today(); 
        $validated['gambar'] = null;
        $validated['status'] = 'konsep';

        // Simpan ke database
        $artikel = Articel::create($validated);

        return response()->json([
            'message' => 'Artikel berhasil disimpan',
            'data' => $artikel
        ], 201);
    }

            public function read_one($id)
{
    try {
        $artikel = Articel::find($id); // Cari artikel berdasarkan ID

        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $artikel
        ]);
    } catch (\Exception $e) {
        Log::error('Gagal mengambil data artikel: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data artikel'
        ], 500);
    }
}

    public function read_all_terbit()
    {
        try {
            $artikels = Articel::where('status', 'terbit')->get();
            
            return response()->json([
                'success' => true,
                'data' => $artikels
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data artikel terbit: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data artikel terbit'
            ], 500);
        }
    }
    public function read_all_konsep()
    {
        try {
            $artikels = Articel::where('status', 'konsep')->get();
            
            return response()->json([
                'success' => true,
                'data' => $artikels
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data artikel terbit: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data artikel terbit'
            ], 500);
        }
    }
    public function read_all_sampah()
    {
        try {
            $artikels = Articel::where('status', 'sampah')->get();
            
            return response()->json([
                'success' => true,
                'data' => $artikels
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data artikel terbit: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data artikel terbit'
            ], 500);
        }
    }

    public function updateGambar(Request $request, $id)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $artikel = Articel::find($id);
        if (!$artikel) {
            return response()->json(['error' => 'Artikel tidak ditemukan'], 404);
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = $file->getClientOriginalName();
            $folderPath = storage_path('app/public/gambar');

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            $file->move($folderPath, $filename);

            $artikel->gambar = $filename;
            $artikel->save();
        }

        return response()->json([
            'message' => 'Gambar berhasil diperbarui.',
            'data' => $artikel
        ]);
    }

    public function CustomOptimize(Request $request)
    {
        $content = $request->input('content');
        $query = $request->input('query');
        if (!$query) {
            return response()->json(['error' => 'Keyword tidak boleh kosong'], 400);
        }
        if (!$content) {
            return response()->json(['error' => 'Konten tidak boleh kosong'], 400);
        }

        //Optimasi dengan OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $promptMain = "Berikut adalah sebuah konten artikel. Jangan ubah atau menyusun ulang seluruh isi artikel. Fokus hanya pada instruksi khusus yang diberikan.\n\nInstruksi:$query\n\nKonten Artikel:\n\n" . $content;
        $responseMain = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptMain]],
        ]);

        $optimized = $responseMain['choices'][0]['message']['content'];

        //Buat Judul
        $promptTitle = "Buatkan SATU judul artikel yang menarik dalam Bahasa Indonesia berdasarkan konten berikut:\n\n" . $optimized;
        $responseTitle = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptTitle]],
        ]);

        $title = $responseTitle['choices'][0]['message']['content'];
        
        // buat kategori
        $promptCategory = "Buatkan keyword-keyword tanpa menyebutkan kata dan judul keyword berdasarkan konten berikut:\n\n" . $optimized;
        $responseCategory = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $promptCategory]],
        ]);

        $category = $responseCategory['choices'][0]['message']['content'];

        return response()->json([
            'title' => trim($title),
            'content' => trim($optimized),
            'category' => trim($category)
        ]);
    }    

}