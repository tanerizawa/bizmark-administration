@extends('client.layouts.app')

@section('title', 'Persetujuan Syarat & Ketentuan')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-[#0a66c2] to-blue-600 px-6 py-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold" id="pageTitle">{{ config('terms.id.title') }}</h1>
                    <!-- Language Toggle -->
                    <div class="flex gap-2 bg-white/20 backdrop-blur-sm rounded-lg p-1">
                        <button 
                            onclick="switchLanguage('id')"
                            id="btnLangId"
                            class="px-4 py-2 rounded-md text-sm font-semibold transition-all bg-white text-[#0a66c2] shadow-sm">
                            🇮🇩 Indonesia
                        </button>
                        <button 
                            onclick="switchLanguage('en')"
                            id="btnLangEn"
                            class="px-4 py-2 rounded-md text-sm font-semibold transition-all text-white hover:bg-white/10">
                            🇬🇧 English
                        </button>
                    </div>
                </div>
                <p class="text-blue-100" id="pageSubtitle">{{ config('terms.id.subtitle') }}</p>
                <div class="flex items-center gap-4 mt-4 text-sm text-blue-100">
                    <span><i class="fas fa-calendar-alt mr-2"></i>Versi: {{ config('terms.version') }}</span>
                    <span><i class="fas fa-clock mr-2"></i>Berlaku: {{ \Carbon\Carbon::parse(config('terms.effective_date'))->isoFormat('D MMMM YYYY') }}</span>
                </div>
            </div>

            <!-- Application Info -->
            <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-[#0a66c2] rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Permohonan #{{ $application->application_number }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $application->permitType->name }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            <i class="fas fa-clock mr-1"></i> Menunggu Persetujuan
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-file-contract text-[#0a66c2] mr-2"></i>
                        <span id="termsContentTitle">Syarat dan Ketentuan Layanan</span>
                    </h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400" id="scrollHint">
                        <i class="fas fa-arrow-down mr-1"></i>Scroll untuk membaca selengkapnya
                    </span>
                </div>
            </div>

            <!-- Terms Scrollable Content -->
            <div id="termsContent" class="px-6 py-6 max-h-[500px] overflow-y-auto prose prose-sm dark:prose-invert max-w-none">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>

        <!-- Acceptance Form -->
        <form action="{{ route('client.applications.submit', $application->id) }}" method="POST" id="acceptanceForm">
            @csrf
            <input type="hidden" name="terms_accepted" value="1">
            <input type="hidden" name="terms_version" value="{{ config('terms.version') }}">
            <input type="hidden" name="terms_language" id="termsLanguage" value="id">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/10">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2" id="acceptanceTitle">
                        {{ config('terms.id.acceptance.title') }}
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-3" id="acceptanceText">
                        {{ config('terms.id.acceptance.text') }}
                    </p>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300" id="acceptanceStatements">
                        @foreach(config('terms.id.acceptance.statements') as $statement)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"></i>
                            <span>{{ $statement }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="px-6 py-6">
                    <!-- Checkbox Agreement -->
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input 
                            type="checkbox" 
                            name="agree" 
                            id="agreeCheckbox"
                            required
                            class="mt-1 w-5 h-5 text-[#0a66c2] border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700">
                        <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white" id="checkboxLabel">
                            {{ config('terms.id.acceptance.checkbox') }}
                        </span>
                    </label>

                    <!-- Scroll Progress Indicator -->
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800" id="scrollAlert">
                        <div class="flex items-start gap-2 text-sm text-blue-800 dark:text-blue-300">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span id="scrollAlertText">Harap scroll dan baca seluruh syarat dan ketentuan di atas sebelum menyetujui.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <a href="{{ route('client.applications.show', $application->id) }}" 
                   class="flex-1 px-6 py-4 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition active:scale-95 text-center">
                    <i class="fas fa-arrow-left mr-2"></i><span id="btnCancel">Kembali</span>
                </a>
                <button 
                    type="submit"
                    id="submitButton"
                    disabled
                    class="flex-1 px-6 py-4 bg-gradient-to-r from-[#0a66c2] to-blue-600 hover:from-[#004182] hover:to-blue-700 text-white font-semibold rounded-lg transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-[#0a66c2] disabled:hover:to-blue-600">
                    <i class="fas fa-check-circle mr-2"></i><span id="btnSubmit">Saya Setuju dan Ajukan Permohonan</span>
                </button>
            </div>
        </form>

        <!-- Footer Info -->
        <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-shield-alt text-[#0a66c2] mt-0.5"></i>
                <p id="footerInfo">
                    Data Anda dilindungi dan dienkripsi. Persetujuan ini akan dicatat dengan timestamp, IP address, dan versi syarat & ketentuan yang Anda setujui.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const terms = @json(config('terms'));
    let currentLang = 'id';
    let hasScrolledToBottom = false;
    let termsContent = document.getElementById('termsContent');
    let submitButton = document.getElementById('submitButton');
    let agreeCheckbox = document.getElementById('agreeCheckbox');
    let scrollAlert = document.getElementById('scrollAlert');

    // Load initial content
    loadTermsContent('id');

    // Language switcher
    function switchLanguage(lang) {
        currentLang = lang;
        document.getElementById('termsLanguage').value = lang;
        
        // Update button states
        document.getElementById('btnLangId').className = lang === 'id' 
            ? 'px-4 py-2 rounded-md text-sm font-semibold transition-all bg-white text-[#0a66c2] shadow-sm'
            : 'px-4 py-2 rounded-md text-sm font-semibold transition-all text-white hover:bg-white/10';
        
        document.getElementById('btnLangEn').className = lang === 'en' 
            ? 'px-4 py-2 rounded-md text-sm font-semibold transition-all bg-white text-[#0a66c2] shadow-sm'
            : 'px-4 py-2 rounded-md text-sm font-semibold transition-all text-white hover:bg-white/10';
        
        // Load content
        loadTermsContent(lang);
        updateUILanguage(lang);
    }

    function loadTermsContent(lang) {
        const content = terms[lang];
        let html = '';
        
        content.sections.forEach((section, index) => {
            html += `
                <div class="mb-6 ${index === 0 ? '' : 'pt-6 border-t border-gray-200 dark:border-gray-700'}">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">${section.title}</h3>
                    <ul class="space-y-3">
            `;
            
            section.content.forEach(item => {
                html += `<li class="text-gray-700 dark:text-gray-300 leading-relaxed">${item}</li>`;
            });
            
            html += `</ul></div>`;
        });
        
        // Add contact info
        html += `
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 -mx-6 px-6 py-6 mt-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">${content.contact.title}</h3>
                <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <p><strong>${content.contact.company}</strong></p>
                    <p><i class="fas fa-map-marker-alt w-5 text-[#0a66c2]"></i> ${content.contact.address}</p>
                    <p><i class="fas fa-envelope w-5 text-[#0a66c2]"></i> <a href="mailto:${content.contact.email}" class="text-[#0a66c2] hover:underline">${content.contact.email}</a></p>
                    <p><i class="fas fa-phone w-5 text-[#0a66c2]"></i> <a href="https://wa.me/${content.contact.phone.replace(/\+| /g, '')}" class="text-[#0a66c2] hover:underline">${content.contact.phone}</a></p>
                    <p><i class="fas fa-globe w-5 text-[#0a66c2]"></i> <a href="${content.contact.website}" target="_blank" class="text-[#0a66c2] hover:underline">${content.contact.website}</a></p>
                </div>
            </div>
        `;
        
        termsContent.innerHTML = html;
        termsContent.scrollTop = 0;
        hasScrolledToBottom = false;
        updateScrollIndicator();
    }

    function updateUILanguage(lang) {
        const content = terms[lang];
        
        document.getElementById('pageTitle').textContent = content.title;
        document.getElementById('pageSubtitle').textContent = content.subtitle;
        document.getElementById('acceptanceTitle').textContent = content.acceptance.title;
        document.getElementById('acceptanceText').textContent = content.acceptance.text;
        document.getElementById('checkboxLabel').textContent = content.acceptance.checkbox;
        
        // Update statements
        let statementsHtml = '';
        content.acceptance.statements.forEach(statement => {
            statementsHtml += `
                <li class="flex items-start gap-2">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mt-0.5"></i>
                    <span>${statement}</span>
                </li>
            `;
        });
        document.getElementById('acceptanceStatements').innerHTML = statementsHtml;
        
        // Update button texts
        if (lang === 'id') {
            document.getElementById('scrollHint').innerHTML = '<i class="fas fa-arrow-down mr-1"></i>Scroll untuk membaca selengkapnya';
            document.getElementById('scrollAlertText').textContent = 'Harap scroll dan baca seluruh syarat dan ketentuan di atas sebelum menyetujui.';
            document.getElementById('btnCancel').textContent = 'Kembali';
            document.getElementById('btnSubmit').textContent = 'Saya Setuju dan Ajukan Permohonan';
            document.getElementById('footerInfo').textContent = 'Data Anda dilindungi dan dienkripsi. Persetujuan ini akan dicatat dengan timestamp, IP address, dan versi syarat & ketentuan yang Anda setujui.';
        } else {
            document.getElementById('scrollHint').innerHTML = '<i class="fas fa-arrow-down mr-1"></i>Scroll to read more';
            document.getElementById('scrollAlertText').textContent = 'Please scroll and read all terms and conditions above before agreeing.';
            document.getElementById('btnCancel').textContent = 'Back';
            document.getElementById('btnSubmit').textContent = 'I Agree and Submit Application';
            document.getElementById('footerInfo').textContent = 'Your data is protected and encrypted. This acceptance will be recorded with timestamp, IP address, and the version of terms & conditions you agreed to.';
        }
    }

    // Track scroll
    termsContent.addEventListener('scroll', function() {
        const scrollPercentage = (termsContent.scrollTop / (termsContent.scrollHeight - termsContent.clientHeight)) * 100;
        
        if (scrollPercentage > 95 && !hasScrolledToBottom) {
            hasScrolledToBottom = true;
            updateScrollIndicator();
        }
    });

    // Checkbox change
    agreeCheckbox.addEventListener('change', function() {
        updateSubmitButton();
    });

    function updateScrollIndicator() {
        if (hasScrolledToBottom) {
            scrollAlert.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200', 'dark:border-blue-800');
            scrollAlert.classList.add('bg-green-50', 'dark:bg-green-900/20', 'border-green-200', 'dark:border-green-800');
            scrollAlert.querySelector('i').classList.remove('fa-info-circle', 'text-blue-800', 'dark:text-blue-300');
            scrollAlert.querySelector('i').classList.add('fa-check-circle', 'text-green-800', 'dark:text-green-300');
            scrollAlert.querySelector('span').classList.remove('text-blue-800', 'dark:text-blue-300');
            scrollAlert.querySelector('span').classList.add('text-green-800', 'dark:text-green-300');
            
            if (currentLang === 'id') {
                scrollAlert.querySelector('span').textContent = 'Terima kasih telah membaca seluruh syarat dan ketentuan.';
            } else {
                scrollAlert.querySelector('span').textContent = 'Thank you for reading all terms and conditions.';
            }
        }
        updateSubmitButton();
    }

    function updateSubmitButton() {
        submitButton.disabled = !(agreeCheckbox.checked && hasScrolledToBottom);
    }

    // Form submission
    document.getElementById('acceptanceForm').addEventListener('submit', function(e) {
        if (!hasScrolledToBottom) {
            e.preventDefault();
            alert(currentLang === 'id' 
                ? 'Harap scroll dan baca seluruh syarat dan ketentuan terlebih dahulu.' 
                : 'Please scroll and read all terms and conditions first.');
            return false;
        }
        
        if (!agreeCheckbox.checked) {
            e.preventDefault();
            alert(currentLang === 'id' 
                ? 'Anda harus menyetujui syarat dan ketentuan untuk melanjutkan.' 
                : 'You must agree to the terms and conditions to continue.');
            return false;
        }
        
        // Show loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + 
            (currentLang === 'id' ? 'Memproses...' : 'Processing...');
    });
</script>
@endpush
@endsection
