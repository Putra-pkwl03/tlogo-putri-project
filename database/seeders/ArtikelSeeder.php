<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('artikel')->insert([
            [
                'tanggal' => Carbon::now()->subDays(3)->toDateString(),
                'judul' => 'Menjelajahi Keindahan Lereng Merapi dengan Jeep Wisata',
                'pemilik' => 'TP TLOGOPUTRI',
                'kategori' => 'Wisata Alam',
                'isi_konten' => 'Gunung Merapi di Yogyakarta menawarkan pengalaman wisata alam yang seru dan mendebarkan. Salah satu cara terbaik untuk menjelajahi lereng Merapi adalah dengan menggunakan jeep wisata. Melalui rute yang menantang dan pemandangan bekas erupsi, wisatawan dapat melihat sisa-sisa letusan besar, bunker Kaliadem, serta panorama yang menakjubkan dari puncak Merapi.',
                'gambar' => 'merapi-jeep.jpg',
                'status' => 'terbit',
                'caption_gambar' => 'Wisatawan menikmati jeep adventure di lereng Merapi',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tanggal' => Carbon::now()->subDays(2)->toDateString(),
                'judul' => 'Museum Gunungapi Merapi: Belajar dari Letusan Dahsyat',
                'pemilik' => 'TP TLOGOPUTRI',
                'kategori' => 'Edukasi',
                'isi_konten' => 'Museum Gunungapi Merapi merupakan destinasi edukatif yang wajib dikunjungi saat berwisata ke kawasan Merapi. Museum ini menyajikan informasi lengkap tentang aktivitas vulkanik, sejarah letusan Merapi, serta berbagai artefak dan dokumentasi terkait. Cocok untuk keluarga dan pelajar yang ingin belajar tentang geologi dan mitigasi bencana alam.',
                'gambar' => 'museum-merapi.jpg',
                'status' => 'terbit',
                'caption_gambar' => 'Suasana di dalam Museum Gunungapi Merapi',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tanggal' => Carbon::now()->subDay()->toDateString(),
                'judul' => 'Romantisme Sunrise di Bukit Klangon Merapi',
                'pemilik' => 'TP TLOGOPUTRI',
                'kategori' => 'Pemandangan',
                'isi_konten' => 'Bukit Klangon merupakan spot favorit bagi pencinta sunrise di kawasan Gunung Merapi. Dari titik ini, wisatawan dapat menyaksikan pemandangan matahari terbit yang menyinari gagahnya Merapi. Fasilitas seperti gardu pandang, camping ground, dan jalur sepeda gunung juga tersedia, menjadikan tempat ini pilihan ideal untuk aktivitas outdoor di pagi hari.',
                'gambar' => 'bukit-klangon.jpg',
                'status' => 'terbit',
                'caption_gambar' => 'View sunrise dari Bukit Klangon, Merapi',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
