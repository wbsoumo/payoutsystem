<?php

namespace App\Http\Controllers;

use App\Models\MerchantUser;
use App\Models\LoginHistory;
use App\Services\GeoLocationService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MerchantAuthController extends Controller
{
    protected GeoLocationService $geoLocationService;
    protected AuditLogService $auditLogService;

    public function __construct(GeoLocationService $geoLocationService, AuditLogService $auditLogService)
    {
        $this->geoLocationService = $geoLocationService;
        $this->auditLogService = $auditLogService;
    }

    public function loginForm()
    {
        if (Auth::guard('merchant')->check()) {
            return redirect()->route('merchant.dashboard');
        }
        return view('merchant.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Find user
        $user = MerchantUser::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check status
            if ($user->status !== 'active') {
                return back()->withErrors(['email' => 'Your merchant account has been suspended. Please contact support.']);
            }

            // Log successful login and record history
            Auth::guard('merchant')->login($user, $request->boolean('remember'));

            $this->recordLoginHistory($request, $user, 'success');

            $this->auditLogService->log(
                'merchant_user',
                $user->id,
                $user->merchant_id,
                'login',
                "Merchant user {$user->email} logged in successfully."
            );

            return redirect()->intended(route('merchant.dashboard'));
        }

        // Record failed login
        if ($user) {
            $this->recordLoginHistory($request, $user, 'failed');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('merchant')->user();
        if ($user) {
            $this->auditLogService->log(
                'merchant_user',
                $user->id,
                $user->merchant_id,
                'logout',
                "Merchant user {$user->email} logged out."
            );
        }

        Auth::guard('merchant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('merchant.login');
    }

    protected function recordLoginHistory(Request $request, MerchantUser $user, string $status): void
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $geo = $this->geoLocationService->resolveIpDetails($ip);
        $userAgentData = $this->geoLocationService->parseUserAgent($request->userAgent());

        // Read geolocation parameters passed from browser permissions (latitude, longitude, accuracy)
        $lat = $request->input('latitude') ? (float) $request->input('latitude') : $geo['latitude'];
        $lon = $request->input('longitude') ? (float) $request->input('longitude') : $geo['longitude'];
        $accuracy = $request->input('accuracy') ? (float) $request->input('accuracy') : null;

        LoginHistory::create([
            'user_type' => 'merchant_user',
            'user_id' => $user->id,
            'email' => $user->email,
            'latitude' => $lat,
            'longitude' => $lon,
            'accuracy' => $accuracy,
            'city' => $request->input('city') ?? $geo['city'],
            'state' => $request->input('state') ?? $geo['state'],
            'country' => $request->input('country') ?? $geo['country'],
            'timezone' => $request->input('timezone') ?? $geo['timezone'],
            'ip_address' => $ip,
            'browser' => $userAgentData['browser'],
            'operating_system' => $userAgentData['operating_system'],
            'device_type' => $userAgentData['device_type'],
            'screen_resolution' => $request->input('screen_resolution'),
            'language' => $request->input('language') ?? $request->header('Accept-Language'),
            'status' => $status,
        ]);
    }
}
