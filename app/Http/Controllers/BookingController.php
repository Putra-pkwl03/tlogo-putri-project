<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingService;
use Carbon\Carbon;

class BookingController extends Controller
{   

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $order = Booking::all();

        return response()->json($order, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->bookingService->createBooking($request);

        return response()->json([
            'success' => true,
            'snap_token' => $data['snap_token'],
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $data['snap_token'],
            'order' => $data['order'],
        ], 201);   
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $data = Booking::find($booking->booking_id);

        if(!$data){
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json($data, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        if (now()->diffInDays($booking->tour_date, false) < 2) {
            return response()->json([
                'message' => 'Rescheduling can only be done a maximum of H-2 before departure'
            ], 403);
        }

        $validated = $request->validate([
            'tour_date' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::now()->addDays(2)->format('Y-m-d'),
            ],
            'start_time' => 'nullable|date_format:H:i',
        ]);
        
        if ($booking->payment_status === 'paid') {
            $booking->tour_date = $validated['tour_date'];
            $booking->start_time = $validated['start_time'] ?? $booking->start_time;

            $booking->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => 'Booking is not fully paid'
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $data = Booking::find($booking->booking_id);

        if(!$data){
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
