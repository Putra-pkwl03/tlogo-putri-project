<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;

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
                if (count($newLinks) >= 2) break 2;
            }

            $startIndex += 10;
            sleep(1);
        }

        if (empty($newLinks)) {
            return response()->json(['error' => 'Tidak ada link baru yang ditemukan.']);
        }

        // 2. Ambil konten HTML dari link (ambil <p>)
        $allContent = '';
        foreach ($newLinks as $link) {
            try {
                $html = Http::get($link)->body();
                preg_match_all('/<p>(.*?)<\/p>/is', $html, $matches);
                $text = implode("\n\n", array_map('strip_tags', $matches[0]));

                if (strlen($text) > 10000) {
                    $text = substr($text, 0, strpos($text, '.', 9500)) . '.';
                }

                $allContent .= $text . "\n\n";
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!$allContent) {
            return response()->json(['error' => 'Tidak ada konten yang bisa diambil dari link.']);
        }

        // 3. Optimasi dengan OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $promptMain = "Terjemahkan teks berikut ke dalam Bahasa Indonesia dengan gaya yang baik. Lalu, buat kesimpulan di akhir.\n\n" . $allContent;
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

        return response()->json([
            'title' => trim($title),
            'content' => trim($optimized)
        ]);
    }
}
