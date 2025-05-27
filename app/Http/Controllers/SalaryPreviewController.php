<?php

namespace App\Http\Controllers;

use App\Models\SalaryPreview;
use App\Models\Ticketing;
use App\Models\User;
use App\Models\Salaries;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalaryPreviewController extends Controller
{
    public function generatePreviews()
    {
        $tickets = Ticketing::with([
            'booking.package',
            'jeep.driver',
            'jeep.owner',
        ])->get();

        $created = 0;

        foreach ($tickets as $ticket) {
            $booking = $ticket->booking;
            $jeep = $ticket->jeep;

            if (!$booking || !$jeep) continue;

            foreach (['driver', 'owner'] as $type) {
                $user = $jeep->$type;
                if (!$user) continue;

                // Cek apakah sudah ada preview
                $existing = SalaryPreview::where('user_id', $user->id)
                    ->where('ticketing_id', $ticket->id)
                    ->first();

                if ($existing) continue;

                SalaryPreview::create([
                    'user_id' => $user->id,
                    'ticketing_id' => $ticket->id,
                    'nama' => $user->name,
                    'role' => $user->role,
                    'status' => 'belum',
                ]);

                $created++;
            }
        }

        // Tambahkan Front Office (sekali untuk setiap FO)
        $frontOffices = User::where('role', 'Front Office')->get();

        foreach ($frontOffices as $fo) {
            $exists = SalaryPreview::where('user_id', $fo->id)->exists();
            if ($exists) continue;

            SalaryPreview::create([
                'user_id' => $fo->id,
                'ticketing_id' => null,
                'nama' => $fo->name,
                'role' => $fo->role,
                'status' => 'belum',
            ]);

            $created++;
        }

        return response()->json([
            'message' => 'Salary previews generated.',
            'total_created' => $created
        ]);
    }

    public function index()
    {
        $previews = SalaryPreview::with('user', 'ticketing')->get();

        return response()->json([
            'previews' => $previews
        ]);
    }

}