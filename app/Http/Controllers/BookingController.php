<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\BookingService;

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

        return response()->json($order);
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
        ]);   
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
