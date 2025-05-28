<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use App\Models\Booking;
use App\Models\Jeep;
use Illuminate\Http\Request;
use App\Models\HistoryTicketing;

class TicketingController extends Controller
{
    public function index()
    {
        $tickets = Ticketing::with(['driver', 'jeep', 'booking'])->get();
        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'jeep_id' => 'required|exists:jeeps,jeep_id',
            'driver_id' => 'required|exists:users,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        $existingTicket = Ticketing::where('code_booking', $booking->order_id)->first();
        if ($existingTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket untuk booking ini sudah dicetak.'
            ], 422);
        }

        $jeep = Jeep::findOrFail($request->jeep_id);
        if ($jeep->driver_id != $request->driver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Driver yang dipilih tidak sesuai dengan yang terdaftar di Jeep.'
            ], 422);
        }

        $ticket = Ticketing::create([
            'code_booking' => $booking->order_id,
            'nama_pemesan' => $booking->customer_name,
            'no_handphone' => $booking->customer_phone,
            'email' => $booking->customer_email,
            'driver_id' => $request->driver_id,
            'jeep_id' => $request->jeep_id,
            'booking_id' => $booking->booking_id,
        ]);

        // Simpan histori pencetakan tiket
        HistoryTicketing::create([
            'ticketing_id' => $ticket->id,
            'code_booking' => $ticket->code_booking,
            'nama_pemesan' => $ticket->nama_pemesan,
            'no_handphone' => $ticket->no_handphone,
            'email' => $ticket->email,
            'driver_id' => $ticket->driver_id,
            'jeep_id' => $ticket->jeep_id,
            'booking_id' => $ticket->booking_id,
            'activity' => 'Tiket dicetak',
            'changed_by' => optional(\Illuminate\Support\Facades\Auth::user())->id, // opsional, sesuaikan dengan sistem login kamu
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat.',
            'ticket' => $ticket->load(['driver', 'jeep', 'booking']),
        ], 201);
    }

    public function show($id)
    {
        $ticket = Ticketing::with(['driver', 'jeep', 'booking'])->find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json($ticket);
    }

    public function destroy($id)
    {
        $ticket = Ticketing::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        $ticket->delete();

        return response()->json(['message' => 'Tiket berhasil dihapus']);
    }
}
