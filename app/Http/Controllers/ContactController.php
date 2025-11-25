<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display contact page
     */
    public function index(Request $request)
    {
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        return view('contact.index', compact('isMobile'));
    }
    
    /**
     * Submit contact form
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'phone.required' => 'Nomor telepon wajib diisi',
            'subject.required' => 'Subjek wajib diisi',
            'message.required' => 'Pesan wajib diisi',
            'message.max' => 'Pesan maksimal 2000 karakter',
        ]);
        
        try {
            // Send email notification
            Mail::send('emails.contact-notification', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'subject' => $validated['subject'],
                'messageContent' => $validated['message'],
            ], function ($message) use ($validated) {
                $message->to(config('mail.contact_email', 'info@bizmark.id'))
                    ->subject('Contact Form: ' . $validated['subject'])
                    ->replyTo($validated['email'], $validated['name']);
            });
            
            // Log the contact submission
            Log::info('Contact form submitted', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
            ]);
            
            return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
            
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);
            
            return back()
                ->with('error', 'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi kami via WhatsApp.')
                ->withInput();
        }
    }
}
