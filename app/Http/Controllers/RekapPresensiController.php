<?php

namespace App\Http\Controllers;
use App\Models\RekapPresensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RekapPresensiController extends Controller
{
    public function index()
    {
        {
            $rekapPresensi = RekapPresensi::all();
            return response()->json($rekapPresensi);
        }
    }

    public function rekapPresensi()
    {
        $now = Carbon::now();
        $bulan = $now->month;
        $tahun = $now->year;

        // Ambil semua ticketings yang berkaitan dengan bookings bulan ini
        $ticketings = DB::table('ticketings')
            ->join('bookings', 'ticketings.booking_id', '=', 'bookings.booking_id')
            ->whereMonth('bookings.tour_date', $bulan)
            ->whereYear('bookings.tour_date', $tahun)
            ->select('ticketings.jeep_id')
            ->get();

        $presensi = [];

        foreach ($ticketings as $ticketing) {
            // Ambil data jeep berdasarkan jeep_id
            $jeep = DB::table('jeeps')->where('jeep_id', $ticketing->jeep_id)->first();
            if (!$jeep || !$jeep->driver_id) continue;

            // Hitung jumlah kehadiran per driver_id
            $driver_id = $jeep->driver_id;
            if (!isset($presensi[$driver_id])) {
                $presensi[$driver_id] = 1;
            } else {
                $presensi[$driver_id]++;
            }
        }

        foreach ($presensi as $driver_id => $jumlah_kehadiran) {
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

        return response()->json(['message' => 'Rekap presensi bulanan berhasil disimpan.']);
    }


}
