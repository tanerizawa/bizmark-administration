@php
    $contactChannels = config('landing.contact_channels', []);
@endphp

<section id="contact" class="py-12 lg:py-20 bg-gradient-to-b from-white to-gray-50">
    <div class="container-wide space-y-8">
        <div class="max-w-3xl text-center mx-auto space-y-4" data-aos="fade-up">
            <span class="pill pill-brand mx-auto justify-center">Hubungi Kami</span>
            <h2 class="text-3xl lg:text-4xl font-semibold text-slate-900 leading-tight">
                Hubungi Kami dan Dapatkan Update Terbaru
            </h2>
            <p class="text-lg text-slate-500">
                Tim customer success kami siap membantu Anda. Respon maksimal dalam 24 jam kerja atau berlangganan newsletter untuk update regulasi perizinan terbaru.
            </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-6" data-aos="fade-up" data-aos-delay="120">
            {{-- Contact Channels --}}
            <div class="flex flex-col gap-4 h-full">
                <div class="card space-y-4 flex-1">
                    <h3 class="text-xl font-bold text-slate-900">Kanal Komunikasi</h3>
                    @foreach($contactChannels as $channel)
                        <div class="flex items-start gap-4">
                            <div class="icon-ring" style="--icon-color: #0077B5; --icon-sheen: rgba(0,119,181,0.12); --icon-border: rgba(0,119,181,0.2);">
                                <i class="{{ $channel['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ $channel['label'] }}</p>
                                @if($channel['href'])
                                    <a href="{{ $channel['href'] }}" class="text-lg font-semibold text-slate-900 hover:text-primary transition">{{ $channel['value'] }}</a>
                                @else
                                    <p class="text-lg font-semibold text-slate-900">{{ $channel['value'] }}</p>
                                @endif
                                @if($channel['sub'])
                                    <p class="text-sm text-slate-500">{{ $channel['sub'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Newsletter Subscription --}}
                <div class="card bg-gradient-to-br from-[#0077B5]/10 to-[#005582]/10 border-[#0077B5]/20">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center">
                                <i class="fas fa-envelope text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Newsletter</h3>
                                <p class="text-xs text-slate-600">Update regulasi & tips perizinan</p>
                            </div>
                        </div>
                        
                        <form id="newsletterForm" class="space-y-2">
                            <input 
                                type="email" 
                                id="subscriberEmail"
                                name="email"
                                placeholder="email@perusahaan.com" 
                                required
                                class="w-full px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-900 placeholder:text-slate-400 focus:border-[#0077B5] focus:outline-none focus:ring-2 focus:ring-[#0077B5]/20 transition text-sm"
                            >
                            <button 
                                type="submit"
                                class="w-full px-3 py-2.5 bg-gradient-to-r from-[#0077B5] to-[#005582] text-white font-semibold rounded-xl hover:from-[#005582] hover:to-[#003d5c] transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl text-sm"
                            >
                                <span class="flex items-center justify-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    Subscribe Newsletter
                                </span>
                            </button>
                        </form>
                        
                        <div id="subscribeMessage" class="hidden"></div>
                        
                        <p class="text-xs text-slate-500 flex items-center gap-2">
                            <i class="fas fa-lock"></i>
                            Data email tidak akan dibagikan ke pihak ketiga
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Contact Form --}}
            <div class="card shadow-[0_25px_80px_rgba(15,23,42,0.08)]">
                <h3 class="text-xl font-bold text-slate-900 mb-4">Kirim Pertanyaan</h3>
                <form class="space-y-4" 
                      x-data="{ showOptional: false }"
                      onsubmit="trackEvent('CTA', 'form_submit', 'contact_form'); alert('{{ app()->getLocale() == 'id' ? 'Pengiriman form akan segera diimplementasikan' : 'Form submission will be implemented soon' }}'); return false;">
                    
                    {{-- Required Fields --}}
                    <div>
                        <label for="name" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               placeholder="Nama lengkap" 
                               required 
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   placeholder="email@perusahaan.com" 
                                   required 
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   placeholder="+62 838 7960 2855" 
                                   required 
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                        </div>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" 
                                  rows="3" 
                                  placeholder="Ceritakan kebutuhan perizinan Anda..." 
                                  required 
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none resize-none transition"></textarea>
                    </div>
                    
                    {{-- Progressive Disclosure: Optional Fields --}}
                    <div x-show="showOptional" 
                         x-collapse
                         class="space-y-4 pt-4 border-t border-slate-200">
                        <div>
                            <label for="company" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Nama Perusahaan <span class="text-slate-300">(opsional)</span>
                            </label>
                            <input type="text" 
                                   id="company" 
                                   placeholder="PT Contoh Indonesia" 
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                        </div>
                        <div>
                            <label for="industry" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Industri <span class="text-slate-300">(opsional)</span>
                            </label>
                            <select id="industry" 
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                                <option value="">Pilih industri...</option>
                                <option value="manufacturing">Manufaktur</option>
                                <option value="chemical">Kimia & Farmasi</option>
                                <option value="food">Makanan & Minuman</option>
                                <option value="automotive">Otomotif</option>
                                <option value="textile">Tekstil</option>
                                <option value="metal">Logam & Elektronik</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="permit_type" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Jenis Perizinan <span class="text-slate-300">(opsional)</span>
                            </label>
                            <select id="permit_type" 
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                                <option value="">Pilih perizinan...</option>
                                <option value="lb3">Limbah B3</option>
                                <option value="amdal">AMDAL</option>
                                <option value="ukl_upl">UKL-UPL</option>
                                <option value="oss">OSS</option>
                                <option value="multiple">Beberapa perizinan</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Toggle Optional Fields Button --}}
                    <button type="button" 
                            @click="showOptional = !showOptional; trackEvent('Form', 'toggle_optional_fields', showOptional ? 'show' : 'hide')" 
                            class="w-full text-sm text-slate-500 hover:text-primary transition flex items-center justify-center gap-2 py-2">
                        <i class="fas" :class="showOptional ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        <span x-text="showOptional ? 'Sembunyikan detail tambahan' : 'Tambahkan detail (opsional)'"></span>
                    </button>
                    
                    <button type="submit" class="btn btn-primary w-full justify-center group" data-cta="contact_form_submit">
                        <span>Kirim Pertanyaan</span>
                        <i class="fas fa-paper-plane text-sm opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </button>
                    <p class="text-xs text-center text-slate-400">Kami akan merespon setiap permintaan dalam waktu 24 jam kerja.</p>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Newsletter Script --}}
<script>
document.getElementById('newsletterForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('subscriberEmail').value;
    const submitBtn = this.querySelector('button[type="submit"]');
    const messageDiv = document.getElementById('subscribeMessage');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><i class="fas fa-spinner fa-spin"></i>Mengirim...</span>';
    
    try {
        const response = await fetch('{{ route("newsletter.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            messageDiv.classList.remove('hidden');
            messageDiv.className = 'p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm';
            messageDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
            this.reset();
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'newsletter_subscription', {
                    'event_category': 'engagement',
                    'event_label': 'newsletter_form'
                });
            }
        } else if (response.status === 422) {
            messageDiv.classList.remove('hidden');
            messageDiv.className = 'p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-sm';
            messageDiv.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Email ini sudah terdaftar di newsletter kami.';
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm';
        messageDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Terjadi kesalahan. Silakan coba lagi.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><i class="fas fa-paper-plane"></i>Subscribe Newsletter</span>';
    }
});
</script>
