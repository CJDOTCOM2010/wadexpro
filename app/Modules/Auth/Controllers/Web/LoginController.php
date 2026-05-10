<?php

namespace App\Modules\Auth\Controllers\Web;

use App\Modules\Auth\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class LoginController extends Controller
{
    private $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the high-fidelity login page.
     */
    public function show(string $country, string $lang): View
    {
        return view('login', [
            'country' => $country,
            'lang'    => $lang,
        ]);
    }

    public function authenticate(Request $request, string $country, string $lang)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = $request->input('identifier');

        // Generate a real OTP
        $otp = $this->otpService->generate($identifier, 'sms', 'login');

        // In a real staging/prod environment, we would trigger an SMS/Email here.
        // For now, we log it so the developer can see it.
        \Log::info("OTP for {$identifier}: {$otp['code']}");

        return response()->json([
            'status' => 'success',
            'redirect' => route('login.challenge', [
                'country' => $country, 
                'lang' => $lang, 
                'identifier' => urlencode($identifier)
            ])
        ]);
    }

    /**
     * Display the OTP Challenge view.
     */
    public function challenge(Request $request, string $country, string $lang): View
    {
        $identifier = urldecode($request->query('identifier', ''));

        return view('login-challenge', [
            'country'    => $country,
            'lang'       => $lang,
            'identifier' => $identifier
        ]);
    }

    /**
     * Verify the OTP challenge and log the user in.
     */
    public function verify(Request $request, string $country, string $lang)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code'       => 'required|string|size:6',
        ]);

        $identifier = $request->input('identifier');
        $code = $request->input('code');

        $verification = $this->otpService->verify($identifier, $code, 'login');

        if (!$verification['valid']) {
            return response()->json([
                'status'  => 'error',
                'message' => $verification['message']
            ], 422);
        }

        // Find the user by email or phone
        $user = \App\Models\User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User account not found.'
            ], 404);
        }

        // Explicitly log into the 'web' guard
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('home', ['country' => $country, 'lang' => $lang])
        ]);
    }
}
