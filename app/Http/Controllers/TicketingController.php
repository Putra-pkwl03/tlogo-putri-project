<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use App\Models\Booking;
use App\Models\Jeep;
use Illuminate\Http\Request;

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
    
        // Cek apakah tiket untuk kode booking ini sudah dibuat
        $existingTicket = Ticketing::where('code_booking', $booking->order_id)->first();
        if ($existingTicket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket untuk booking ini sudah dicetak.'
            ], 422);
        }
    
        // Cek apakah jeep memang dimiliki oleh driver yang dipilih
        $jeep = Jeep::findOrFail($request->jeep_id);
        if ($jeep->users_id != $request->driver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Driver yang dipilih tidak sesuai dengan pemilik Jeep.'
            ], 422);
        }
    
        // Buat tiket
        $ticket = Ticketing::create([
            'code_booking' => $booking->order_id,
            'nama_pemesan' => $booking->customer_name,
            'no_handphone' => $booking->customer_phone,
            'email' => $booking->customer_email,
            'driver_id' => $request->driver_id,
            'jeep_id' => $request->jeep_id,
            'booking_id' => $booking->booking_id,
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
}