<?php

namespace App\Http\Controllers;
use App\Models\RekapPresensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RekapPresensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', now()->format('m'));
        $tahun = $request->query('tahun', now()->format('Y'));

        $rekapPresensi = RekapPresensi::where('bulan', $bulan)
                                        ->where('tahun', $tahun)
                                        ->get();


        if ($rekapPresensi->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => "Data tidak ditemukan untuk bulan $bulan tahun $tahun.",
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Data berhasil ditemukan untuk bulan $bulan tahun $tahun.",
            'data' => $rekapPresensi
        ]);
    }


    public function calculatePresensi()
    {
        // Ambil semua ticketing tanpa filter bulan/tahun
        $ticketings = DB::table('ticketings')->get();

        $presensi = [];

        foreach ($ticketings as $ticketing) {
            // Ambil data jeep berdasarkan jeep_id
            $jeep = DB::table('jeeps')->where('jeep_id', $ticketing->jeep_id)->first();
            if (!$jeep || !$jeep->driver_id) continue;

            $driver_id = $jeep->driver_id;

            // Ambil bulan & tahun dari ticketing.created_at (atau bisa ganti dengan bookings.tour_date jika relevan)
            $tanggal = Carbon::parse($ticketing->created_at);
            $bulan = $tanggal->month;
            $tahun = $tanggal->year;

            // Gunakan kombinasi unik driver_id + bulan + tahun sebagai key
            $key = $driver_id . '-' . $bulan . '-' . $tahun;

            if (!isset($presensi[$key])) {
                $presensi[$key] = [
                    'driver_id' => $driver_id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah_kehadiran' => 1,
                ];
            } else {
                $presensi[$key]['jumlah_kehadiran']++;
            }
        }

        return array_values($presensi); // ubah jadi array numerik
    }


    public function rekapPresensi()
    {
        $dataPresensi = $this->calculatePresensi();
    
        foreach ($dataPresensi as $data) {
            $driver_id = $data['driver_id'];
            $bulan = $data['bulan'];
            $tahun = $data['tahun'];
            $jumlah_kehadiran = $data['jumlah_kehadiran'];
        
            $user = DB::table('users')->where('id', $driver_id)->first();
        
            if ($user) {
                DB::table('rekap_presensi')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ],
                    [
                        'nama_lengkap' => $user->name,
                        'no_hp' => $user->telepon,
                        'role' => $user->role,
                        'tanggal_bergabung' => $user->tanggal_bergabung,
                        'jumlah_kehadiran' => $jumlah_kehadiran,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    
        return response()->json(['message' => 'Rekap presensi berhasil diproses dan disimpan.']);
    }



}
