<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function midtransNotif(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Notification Payload:', $payload);

        $this->midtransService->handleNotification($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification processed.'
        ]);
    }
}
