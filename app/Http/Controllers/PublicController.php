<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicController extends Controller
{
    protected AuditLogService $auditLogService;

    public function __construct(AuditLogService $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

    public function home() { return view('public.home'); }
    public function features() { return view('public.features'); }
    public function pricing() { return view('public.pricing'); }
    public function docs() { return view('public.docs'); }
    public function developers() { return view('public.developers'); }
    public function about() { return view('public.about'); }
    public function security() { return view('public.security'); }
    public function compliance() { return view('public.compliance'); }
    public function contact() { return view('public.contact'); }
    public function support() { return view('public.support'); }
    public function privacy() { return view('public.privacy'); }
    public function terms() { return view('public.terms'); }
    public function status() { return view('public.status'); }

    public function requestAccessStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:merchants,email',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'monthly_volume' => 'required|string',
            'business_type' => 'required|string',
            'website' => 'nullable|url|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $enquiry = ContactRequest::create(array_merge($validator->validated(), ['status' => 'pending']));

        // Audit Log entry
        $this->auditLogService->log(
            'system',
            null,
            null,
            'enquiry_received',
            "Access request submitted by {$request->full_name} for {$request->company_name}.",
            ['enquiry_id' => $enquiry->id, 'email' => $request->email]
        );

        return back()->with('success', 'Your request has been received. Our team will review your application and contact you shortly.');
    }
}
