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
        $orders = Booking::with(['package:id,package_name,destination,price'])->get();
        return response()->json($orders, 200);
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
    public function show($id)
    {
        $data = Booking::with(['package:id,package_name,destination,price'])->find($id);

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
    public function update(Request $request, $id)
    {

        $data = Booking::find($id);
    
        if (!$data) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $validated = $request->validate([
            'booking_status' => 'nullable|in:settlement,capture,cancel,expired',
            'tour_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
        ]);
        
        if ($data->payment_status === 'paid') {
            
            $data->booking_status = $validated['booking_status'] ?? $data->booking_status;
            $data->tour_date = $validated['tour_date'] ?? $data->tour_date;
            $data->start_time = $validated['start_time'] ?? $data->start_time;

            $data->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $data
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
    public function destroy($order_id)
    {
        $data = Booking::where('order_id', $order_id)->first();

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
