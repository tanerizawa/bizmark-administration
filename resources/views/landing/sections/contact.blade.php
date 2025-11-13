@php
    $contactChannels = config('landing.contact_channels', []);
@endphp

<section id="contact" class="py-24 lg:py-32 bg-white">
    <div class="container-wide space-y-14">
        <div class="max-w-3xl text-center mx-auto space-y-5" data-aos="fade-up">
            <span class="pill pill-brand mx-auto justify-center">Hubungi Kami</span>
            <h2 class="text-3xl lg:text-4xl font-semibold text-slate-900 leading-tight">
                Kanal komunikasi tunggal untuk seluruh dokumen, koordinasi, dan tindak lanjut perizinan Anda.
            </h2>
            <p class="text-lg text-slate-500">
                Tim customer success mencatat setiap permintaan dan memastikan respon maksimal dalam 24 jam kerja.
            </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-10" data-aos="fade-up" data-aos-delay="120">
            <div class="card h-full space-y-6">
                @foreach($contactChannels as $channel)
                    <div class="flex items-start gap-4">
                        <div class="icon-ring" style="--icon-color: #0f172a; --icon-sheen: rgba(15,23,42,0.12); --icon-border: rgba(15,23,42,0.2);">
                            <i class="{{ $channel['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ $channel['label'] }}</p>
                            @if($channel['href'])
                                <a href="{{ $channel['href'] }}" class="text-2xl font-semibold text-slate-900 hover:text-primary transition">{{ $channel['value'] }}</a>
                            @else
                                <p class="text-2xl font-semibold text-slate-900">{{ $channel['value'] }}</p>
                            @endif
                            @if($channel['sub'])
                                <p class="text-sm text-slate-500">{{ $channel['sub'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="rounded-2xl border border-dashed border-slate-200 px-5 py-4 text-sm text-slate-500">
                    Tertarik demo workspace dokumen? Hubungi kami via WhatsApp dan tim akan menjadwalkan sesi virtual dalam 1x24 jam.
                </div>
            </div>
            
            <div class="card shadow-[0_25px_80px_rgba(15,23,42,0.08)]">
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
                               class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
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
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   placeholder="+62 812 3456 7890" 
                                   required 
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
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
                                  class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none resize-none transition"></textarea>
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
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none transition">
                        </div>
                        <div>
                            <label for="industry" class="block text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">
                                Industri <span class="text-slate-300">(opsional)</span>
                            </label>
                            <select id="industry" 
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none transition">
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
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none transition">
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
