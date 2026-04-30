@extends('layouts.app')

@section('title', 'Compose Email')

@section('content')
<div class="px-4 py-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-400"></i>Compose Email
            </h1>
            <p class="text-gray-400 mt-1">Kirim email baru</p>
        </div>
        <a href="{{ route('admin.inbox.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Inbox
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-5">
        <i class="fas fa-check-circle flex-shrink-0"></i><span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-5">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Compose Form --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow mb-4">
        <div class="p-6">
            <form action="{{ route('admin.inbox.send') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-at mr-2 text-gray-400"></i>From Account
                        </label>
                        <select name="from_account_id" id="from_account_id"
                                class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('from_account_id') border-red-500 @enderror">
                            <option value="">Default ({{ config('mail.from.address') }})</option>
                            @foreach(($fromAccounts ?? collect()) as $account)
                            <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} ({{ $account->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('from_account_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Pilih akun pengirim untuk korespondensi klien.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>To <span class="text-red-400">*</span>
                        </label>
                        <input type="email" name="to_email" id="to_email" value="{{ old('to_email') }}"
                               placeholder="recipient@example.com" required
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('to_email') border-red-500 @enderror">
                        @error('to_email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-tag mr-2 text-gray-400"></i>Subject <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                               placeholder="Email subject" required
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                        @error('subject')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-align-left mr-2 text-gray-400"></i>Message <span class="text-red-400">*</span>
                        </label>
                        <textarea name="body_html" id="body_html" rows="15" required
                                  placeholder="Write your message here..."
                                  class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('body_html') border-red-500 @enderror">{{ old('body_html') }}</textarea>
                        @error('body_html')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>You can use HTML formatting if needed</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-paperclip mr-2 text-gray-400"></i>Attachments
                        </label>
                        <input type="file" name="attachments[]" id="attachments" multiple
                               class="w-full text-sm text-gray-300 file:bg-gray-700 file:border-0 file:rounded file:px-3 file:py-1" />
                        <p class="text-xs text-gray-500 mt-1">Optional. Max 10MB per file.</p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-paper-plane mr-2"></i>Send Email
                            </button>
                            <button type="button" id="generateAiBtn"
                                    class="inline-flex items-center px-4 py-2.5 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-robot mr-2"></i>Generate with AI
                            </button>
                            <button type="button" id="saveDraftBtn" onclick="saveDraft()"
                                    class="inline-flex items-center px-4 py-2.5 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-save mr-2"></i>Save Draft
                            </button>
                        </div>
                        <a href="{{ route('admin.inbox.index') }}"
                           class="inline-flex items-center px-4 py-2.5 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick Tips --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <h6 class="text-white font-medium flex items-center gap-2 mb-3">
            <i class="fas fa-lightbulb text-yellow-400"></i>Email Tips
        </h6>
        <ul class="text-gray-400 text-sm space-y-1 pl-4 list-disc">
            <li>Pastikan email penerima valid dan aktif</li>
            <li>Tulis subject yang jelas dan deskriptif</li>
            <li>Gunakan format HTML untuk tampilan yang lebih menarik</li>
            <li>Email akan tersimpan di folder "Sent" setelah terkirim</li>
        </ul>
    </div>
</div>

<script>
function saveDraft() {
    const btn = document.getElementById('saveDraftBtn');
    localStorage.setItem('email_draft', JSON.stringify({
        to: document.getElementById('to_email').value,
        subject: document.getElementById('subject').value,
        body: document.getElementById('body_html').value,
        saved_at: new Date().toISOString()
    }));
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Draft tersimpan';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
    const draft = localStorage.getItem('email_draft');
    if (draft) {
        const data = JSON.parse(draft);
        if (confirm('Found saved draft from ' + new Date(data.saved_at).toLocaleString() + '. Restore?')) {
            document.getElementById('to_email').value = data.to || '';
            document.getElementById('subject').value = data.subject || '';
            document.getElementById('body_html').value = data.body || '';
        }
    }
});

@if(session('success'))
    localStorage.removeItem('email_draft');
@endif

// Generate with AI
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('generateAiBtn');
    if (!btn) return;
    btn.addEventListener('click', async function () {
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghasilkan...';

        const payload = new FormData();
        payload.append('to_email', document.getElementById('to_email').value || '');
        payload.append('subject', document.getElementById('subject').value || '');
        payload.append('body_html', document.getElementById('body_html').value || '');

        try {
            const res = await fetch('{{ route('admin.inbox.generate') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                body: payload,
            });
            const data = await res.json();
            if (data.success && data.data) {
                if (data.data.email_subject) document.getElementById('subject').value = data.data.email_subject;
                if (data.data.email_html) document.getElementById('body_html').value = data.data.email_html;
                alert('Konten AI berhasil dimasukkan. Periksa dan sesuaikan sebelum mengirim.');
            } else {
                alert('Gagal menghasilkan konten: ' + (data.error || 'unknown'));
            }
        } catch (e) {
            alert('Gagal menghubungi layanan AI.');
        }

        btn.innerHTML = orig;
        btn.disabled = false;
    });
});
</script>
@endsection
