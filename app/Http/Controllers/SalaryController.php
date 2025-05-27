<?php

namespace App\Http\Controllers;

use App\Models\SalaryPreview;
use App\Models\Ticketing;
use App\Models\Salary;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User; 
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{

    public function previewSalary($userId, $role)
    {
        if ($role === 'fron office') {
            // Cek apakah hari ini tanggal 1
            if (Carbon::now()->day !== 26) {
                return response()->json([
                    'message' => 'Gaji hanya bisa dilihat pada tanggal 1 setiap bulan.',
                    'data' => null,
                ], 403);
            }
        
            // Ambil data user
            $user = User::find($userId);
        
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan.',
                    'data' => null,
                ], 404);
            }
        
             // Ambil status dari salary preview
                $preview = SalaryPreview::where('user_id', $userId)->first();

                return response()->json([
                    'message' => 'Preview gaji front office berhasil.',
                    'data' => [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'salary' => 1000000,
                        'gaji_bulan_ini' => 'Rp 1.000.000',
                        'status' => $preview?->status ?? 'belum',
                    ],
                    'total_fo_share' => 1000000,
            ]);
        }
        
        if ($role === 'driver') {
            $tickets = Ticketing::with(['booking.package', 'jeep.driver', 'jeep.owner'])
                        ->whereHas('jeep.driver', fn($q) => $q->where('id', $userId))
                        ->get();
        } elseif ($role === 'owner') {
            $tickets = Ticketing::with(['booking.package', 'jeep.owner', 'jeep.driver'])
                        ->whereHas('jeep.owner', fn($q) => $q->where('id', $userId))
                        ->get();
        } else {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }
    
        if ($tickets->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ditemukan tiket untuk ' . $role . ' ini.',
                'data' => []
            ]);
        }

        $previewSalaries = [];

        foreach ($tickets as $ticket) {
            if ($role === 'driver') {
                $relatedUser = $ticket->jeep->driver;
            } elseif ($role === 'owner') {
                $relatedUser = $ticket->jeep->owner;
            }
        
            if (!$relatedUser) continue; // skip kalau tidak ada relasi user
        
            // Ambil status dari tabel salary_previews
            $status = SalaryPreview::where('ticketing_id', $ticket->id)
                        ->where('user_id', $relatedUser->id)
                        ->value('status');
        }
    
    
        $previewSalaries = [];
    
        foreach ($tickets as $ticket) {
            $booking = $ticket->booking;
            $package = $booking?->package;
            $jeep = $ticket->jeep;
            $driver = $jeep?->driver;
            $owner = $jeep?->owner;
    
            if (!$booking || !$package) {
                continue;
            }
    
            $referralType = $booking->referral_code;
            $price = $package->price;
    
            // Hitung kas dan operasional sama untuk driver & owner
            $kas = 0;
            $operasional = 0;
            $referralCut = 0;
            $bonusDriver = 0;
    
            switch ($price) {
                case 400000:
                    $kas = 30000;
                    $operasional = 35000;
                    break;
                case 450000:
                    $kas = 35000;
                    $operasional = 40000;
                    break;
                case 550000:
                    $kas = 40000;
                    $operasional = 45000;
                    break;
                default:
                    continue 2;
            }
    
            if ($referralType === 'rn') {
                $referralCut = 50000;
            } elseif ($referralType === 'op') {
                $kas = 25000;
                $operasional = 25000;
                if ($role === 'driver') {
                    $bonusDriver = 30000;
                }
            }
    
            $net = $price - ($kas + $operasional + $referralCut);
    
            if ($role === 'driver') {
                $driverShare = ($net * 0.7) + $bonusDriver;
                $ownerShare = $net * 0.3;
                $previewSalaries[] = [
                    'ticketing_id' => $ticket->id,
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->name,
                    'driver_role' => $driver->role,
                    'no_lambung' => $jeep->no_lambung ?? '-',
                    'package' => [
                        'kas' => $kas,
                        'operasional' => $operasional,
                        'id' => $package->id,
                        'slug' => $package->slug,
                        'price' => $price,
                        'description' => $package->description,
                    ],
                    'referral_cut' => $referralCut,
                    'bonus_driver' => $bonusDriver,
                    'net' => $net,
                    'driver_share' => $driverShare,
                    'owner_share' => $ownerShare,
                       'status' => $status ?? 'belum',
                    'owner' => [
                        'id' => $owner?->id,
                        'name' => $owner?->name,
                        'email' => $owner?->email,
                    ],
                ];
            } else { // owner
                $ownerShare = $net * 0.3;
                $previewSalaries[] = [
                    'ticketing_id' => $ticket->id,
                    'owner_id' => $owner->id,
                    'owner_name' => $owner->name,
                    'owner_email' => $owner->email,
                    'driver_id' => $driver?->id,
                    'driver_name' => $driver?->name,
                    'no_lambung' => $jeep->no_lambung ?? '-',
                    'package' => [
                        'kas' => $kas,
                        'operasional' => $operasional,
                        'id' => $package->id,
                        'slug' => $package->slug,
                        'price' => $price,
                        'description' => $package->description,
                    ],
                    'referral_cut' => $referralCut,
                    'net' => $net,
                    'owner_share' => $ownerShare,
                    'status' => $status ?? 'belum',
                ];
            }
        }
    
        $totalShareKey = $role === 'driver' ? 'driver_share' : 'owner_share';
        $totalShare = array_sum(array_column($previewSalaries, $totalShareKey));
    
        return response()->json([
            'message' => 'Preview gaji ' . $role . ' berhasil.',
            'data' => $previewSalaries,
            'total_' . $role . '_share' => $totalShare,
        ]);
    }
    

    public function storeSalary(Request $request, $userId, $role)
{
    $data = $request->input('salaries');

    if (!in_array($role, ['driver', 'owner', 'fron office'])) {
        return response()->json(['message' => 'Role tidak valid.'], 400);
    }

    if (!$data || !is_array($data)) {
        return response()->json(['message' => 'Data gaji tidak valid atau kosong.'], 400);
    }

    $savedCount = 0;

    if ($role === 'owner') {
        // Group dan jumlahkan per owner_id (anggap $userId == owner_id)
        $ownerTotal = 0;
        $ownerData = null;

        foreach ($data as $salary) {
            // pastikan data lengkap
            if (empty($salary['salarie'])) continue;

            // simpan data owner (ambil dari first item)
            if (!$ownerData) {
                $ownerData = [
                    'nama' => $salary['nama'] ?? null,
                    'no_lambung' => $salary['no_lambung'] ?? null,
                    'kas' => $salary['kas'] ?? 0,
                    'operasional' => $salary['operasional'] ?? 0,
                    'payment_date' => $salary['payment_date'] ?? date('Y-m-d'),
                ];
            }
            $ownerTotal += $salary['salarie'];
        }

        if ($ownerData && $ownerTotal > 0) {
            // cek duplikat berdasarkan user_id dan role saja (karena tidak pakai ticketing_id)
            $existing = Salary::where('user_id', $userId)->where('role', $role)->first();
            if (!$existing) {
                Salary::create([
                    'user_id' => $userId,
                    'ticketing_id' => null,
                    'nama' => $ownerData['nama'],
                    'role' => $role,
                    'no_lambung' => $ownerData['no_lambung'],
                    'kas' => $ownerData['kas'],
                    'operasional' => $ownerData['operasional'],
                    'salarie' => $ownerTotal,
                    'total_salary' => $ownerTotal,
                    'payment_date' => $ownerData['payment_date'],
                    'status' => 'Diterima',
                ]);
                $savedCount++;
            }
        }
    } else {
        // Untuk driver dan fron office simpan per tiket
        foreach ($data as $salary) {
            $ticketingId = $salary['ticketing_id'] ?? null;
            $nama = $salary['nama'] ?? null;
            $noLambung = $salary['no_lambung'] ?? null;
            $kas = $salary['kas'] ?? 0;
            $operasional = $salary['operasional'] ?? 0;
            $salarie = $salary['salarie'] ?? null;
            $totalSalary = $salary['total_salary'] ?? null;
            $paymentDate = $salary['payment_date'] ?? null;

            if ($role !== 'fron office' && !$ticketingId) continue;
            if (!$salarie) continue;

            $existing = Salary::where('ticketing_id', $ticketingId)
                ->where('user_id', $userId)
                ->first();

            if ($existing) continue;

            Salary::create([
                'user_id' => $userId,
                'ticketing_id' => $ticketingId,
                'nama' => $nama,
                'role' => $role,
                'no_lambung' => $noLambung,
                'kas' => $kas,
                'operasional' => $operasional,
                'salarie' => $salarie,
                'total_salary' => $totalSalary,
                'payment_date' => $paymentDate,
                'status' => 'Diterima',
            ]);

            $savedCount++;
        }
    }

    return response()->json([
        'message' => "$savedCount data gaji berhasil disimpan.",
    ]);
}


public function getAllSalaries(Request $request)
{
    // Optional: filter by role/user_id jika dibutuhkan
    $query = Salary::query();

    if ($request->has('role')) {
        $query->where('role', $request->input('role'));
    }

    if ($request->has('user_id')) {
        $query->where('user_id', $request->input('user_id'));
    }

    // Ambil semua data
    $salaries = $query->orderBy('payment_date', 'desc')->get();

    return response()->json([
        'message' => 'Data gaji berhasil diambil.',
        'data' => $salaries
    ]);
}


    // public function salaryHistory(Request $request, $userId)
    // {
    //     $status = $request->query('status'); 

    //     $query = Salaries::where('user_id', $userId);

    //     if ($status) {
    //         $query->where('status', $status);
    //     }

    //     $history = $query->get();

    //     return response()->json([
    //         'salary_history' => $history
    //     ]);
    // }

    // public function updateSalaryStatus()
    // {
    //     $updated = Salaries::where('status', 'belum')->update([
    //         'status' => 'diterima',
    //         'payment_date' => now()
    //     ]);

    //     return response()->json([
    //         'message' => 'Status semua gaji yang belum diterima telah diubah menjadi diterima.',
    //         'total_updated' => $updated
    //     ]);
    // }

}