<?php

namespace App\Http\Controllers;

use App\Models\ServiceInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PMAInquiryController extends Controller
{
    /**
     * Show PMA inquiry form (English)
     */
    public function create()
    {
        $services = config('services_pma');
        $countries = $this->getCountries();
        $investmentSectors = $this->getInvestmentSectors();
        
        return view('pma.inquiry.create', compact('services', 'countries', 'investmentSectors'));
    }

    /**
     * Store PMA inquiry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Contact Information
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'company_name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            
            // Investment Information
            'investment_sector' => 'required|string|max:255',
            'investment_amount_usd' => 'required|string|max:50',
            'investment_timeline' => 'required|string|max:100',
            'business_location' => 'required|string|max:255',
            
            // Services Required
            'services_needed' => 'required|array|min:1',
            'services_needed.*' => 'string',
            
            // Additional Information
            'project_description' => 'required|string|max:2000',
            'specific_questions' => 'nullable|string|max:2000',
            'preferred_contact_method' => 'required|in:email,whatsapp,phone',
            'preferred_contact_time' => 'nullable|string|max:100',
            
            // Privacy
            'privacy_consent' => 'accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inquiryNumber = 'PMA-' . strtoupper(Str::random(8));
            
            $inquiry = ServiceInquiry::create([
                'inquiry_number' => $inquiryNumber,
                'email' => $request->email,
                'company_name' => $request->company_name,
                'company_type' => 'Foreign Investment (PMA)',
                'phone' => $request->phone,
                'contact_person' => $request->full_name,
                'position' => $request->position,
                'business_activity' => $request->project_description,
                'form_data' => json_encode([
                    'country' => $request->country,
                    'investment_sector' => $request->investment_sector,
                    'investment_amount_usd' => $request->investment_amount_usd,
                    'investment_timeline' => $request->investment_timeline,
                    'business_location' => $request->business_location,
                    'services_needed' => $request->services_needed,
                    'specific_questions' => $request->specific_questions,
                    'preferred_contact_method' => $request->preferred_contact_method,
                    'preferred_contact_time' => $request->preferred_contact_time,
                    'locale' => 'en',
                    'market_segment' => 'pma',
                ]),
                'status' => 'pending',
                'priority' => $this->calculatePriority($request),
                'source' => 'pma_inquiry_form',
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
            ]);

            // Send notification email (async in production)
            // TODO: Dispatch job to send welcome email and notify admin

            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been submitted successfully!',
                'inquiry_number' => $inquiryNumber,
                'redirect_url' => route('pma.inquiry.result', $inquiryNumber),
            ]);

        } catch (\Exception $e) {
            \Log::error('PMA Inquiry submission failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again or contact us directly.'
            ], 500);
        }
    }

    /**
     * Show inquiry result/confirmation
     */
    public function result($inquiryNumber)
    {
        $inquiry = ServiceInquiry::where('inquiry_number', $inquiryNumber)->firstOrFail();
        
        return view('pma.inquiry.result', compact('inquiry'));
    }

    /**
     * Calculate inquiry priority based on investment amount and timeline
     */
    private function calculatePriority(Request $request): string
    {
        $amount = $request->investment_amount_usd;
        $timeline = $request->investment_timeline;
        
        // High priority: Large investment (>$500k) or urgent timeline
        if (str_contains($amount, '500,000') || 
            str_contains($amount, '1,000,000') || 
            str_contains($timeline, '1-3 months')) {
            return 'high';
        }
        
        // Medium priority: Mid-range investment or medium timeline
        if (str_contains($amount, '100,000') || 
            str_contains($timeline, '3-6 months')) {
            return 'medium';
        }
        
        return 'normal';
    }

    /**
     * Get list of countries for dropdown
     */
    private function getCountries(): array
    {
        return [
            'Singapore',
            'Japan',
            'South Korea',
            'China',
            'Hong Kong',
            'Taiwan',
            'Malaysia',
            'Thailand',
            'Vietnam',
            'Philippines',
            'United States',
            'United Kingdom',
            'Australia',
            'Germany',
            'France',
            'Netherlands',
            'Switzerland',
            'United Arab Emirates',
            'Saudi Arabia',
            'India',
            'Other',
        ];
    }

    /**
     * Get investment sectors
     */
    private function getInvestmentSectors(): array
    {
        return [
            'Manufacturing',
            'Technology & IT Services',
            'Financial Services',
            'Real Estate & Construction',
            'Retail & E-commerce',
            'Food & Beverage',
            'Healthcare & Pharmaceuticals',
            'Energy & Renewable Resources',
            'Transportation & Logistics',
            'Tourism & Hospitality',
            'Agriculture & Plantation',
            'Mining & Natural Resources',
            'Education & Training',
            'Telecommunications',
            'Trading & Distribution',
            'Other',
        ];
    }
}
