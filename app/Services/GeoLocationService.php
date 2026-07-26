<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    public function resolveIpDetails(string $ip): array
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return [
                'city' => 'Localhost',
                'state' => 'Local',
                'country' => 'Local',
                'timezone' => 'UTC',
                'latitude' => 0.0,
                'longitude' => 0.0,
            ];
        }

        try {
            // Fetch from ip-api.com (non-secure for free version, secure available)
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'success') {
                    return [
                        'city' => $data['city'] ?? null,
                        'state' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'latitude' => (float) ($data['lat'] ?? 0),
                        'longitude' => (float) ($data['lon'] ?? 0),
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("IP Geolocation resolution failed for IP: {$ip}. Error: " . $e->getMessage());
        }

        return [
            'city' => null,
            'state' => null,
            'country' => null,
            'timezone' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    public function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'browser' => 'Unknown',
                'operating_system' => 'Unknown',
                'device_type' => 'desktop',
            ];
        }

        $browser = 'Unknown';
        $os = 'Unknown';
        $device = 'desktop';

        // Direct simple OS detection
        if (preg_match('/iphone/i', $userAgent)) {
            $os = 'iOS';
            $device = 'mobile';
        } elseif (preg_match('/ipad/i', $userAgent)) {
            $os = 'iOS';
            $device = 'tablet';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
            $device = 'mobile';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        // Direct simple Browser detection
        if (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/edge|edg/i', $userAgent) && !preg_match('/opr/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/edge|edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr/i', $userAgent)) {
            $browser = 'Opera';
        }

        return [
            'browser' => $browser,
            'operating_system' => $os,
            'device_type' => $device,
        ];
    }
}
