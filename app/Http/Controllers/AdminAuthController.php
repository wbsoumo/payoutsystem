<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\LoginHistory;
use App\Services\GeoLocationService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
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
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            if ($admin->status !== 'active') {
                return back()->withErrors(['email' => 'This administrator account is suspended.']);
            }

            Auth::guard('admin')->login($admin, $request->boolean('remember'));

            $this->recordLoginHistory($request, $admin, 'success');

            $this->auditLogService->log(
                'admin',
                $admin->id,
                null,
                'login',
                "Administrator {$admin->email} logged in successfully."
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($admin) {
            $this->recordLoginHistory($request, $admin, 'failed');
        }

        return back()->withErrors([
            'email' => 'Invalid admin credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            $this->auditLogService->log(
                'admin',
                $admin->id,
                null,
                'logout',
                "Administrator {$admin->email} logged out."
            );
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    protected function recordLoginHistory(Request $request, Admin $admin, string $status): void
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $geo = $this->geoLocationService->resolveIpDetails($ip);
        $userAgentData = $this->geoLocationService->parseUserAgent($request->userAgent());

        LoginHistory::create([
            'user_type' => 'admin',
            'user_id' => $admin->id,
            'email' => $admin->email,
            'latitude' => $geo['latitude'],
            'longitude' => $geo['longitude'],
            'city' => $geo['city'],
            'state' => $geo['state'],
            'country' => $geo['country'],
            'timezone' => $geo['timezone'],
            'ip_address' => $ip,
            'browser' => $userAgentData['browser'],
            'operating_system' => $userAgentData['operating_system'],
            'device_type' => $userAgentData['device_type'],
            'status' => $status,
        ]);
    }
}
