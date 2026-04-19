@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin bertanya tentang lamaran untuk posisi: ' . $vacancy->title;
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <a href="{{ route('career.show', $vacancy->slug) }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke detail lowongan</a>
        <span class="section-badge mb-4">Lamaran</span>
        <h1 class="section-title mb-3">Lamar Posisi: {{ $vacancy->title }}</h1>
        <p class="section-description mb-0" style="margin-left:0;">Isi data di bawah ini. Field bertanda * wajib diisi.</p>
    </div>
</section>

<section class="section pt-0">
    <div class="container-wide">
        @if(session('error'))
            <div class="card mb-6" style="border-color:rgba(220,38,38,.35);box-shadow:0 0 0 4px rgba(220,38,38,.08);">
                <div class="text-sm" style="color:var(--text-secondary);">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="card mb-6" style="border-color:rgba(220,38,38,.35);">
                <div class="text-sm font-semibold mb-2" style="color:var(--text-primary);">Mohon periksa kembali input yang ditandai.</div>
                <ul class="text-sm m-0 pl-5" style="color:var(--text-secondary);">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('career.apply.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-12 gap-8">
            @csrf
            <input type="hidden" name="job_vacancy_id" value="{{ $vacancy->id }}">

            <div class="lg:col-span-8">
                <div class="card mb-6">
                    <h2 class="text-lg font-bold mb-4" style="color:var(--text-primary);">Data Pribadi</h2>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="full_name" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required autocomplete="name"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="email" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Email <span class="text-red-600">*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="phone" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Nomor Telepon <span class="text-red-600">*</span></label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required autocomplete="tel"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="birth_date" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Tanggal Lahir</label>
                            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="gender" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Gender</label>
                            <select id="gender" name="gender"
                                    class="w-full rounded-lg border px-4 py-3 text-base"
                                    style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                                <option value="">-</option>
                                <option value="Pria" @selected(old('gender') === 'Pria')>Pria</option>
                                <option value="Wanita" @selected(old('gender') === 'Wanita')>Wanita</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full rounded-lg border px-4 py-3 text-base"
                                      style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-6">
                    <h2 class="text-lg font-bold mb-4" style="color:var(--text-primary);">Pendidikan</h2>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="education_level" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Jenjang Pendidikan <span class="text-red-600">*</span></label>
                            <select id="education_level" name="education_level" required
                                    class="w-full rounded-lg border px-4 py-3 text-base"
                                    style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                                <option value="">Pilih</option>
                                @foreach(['D3','S1','S2','S3'] as $lvl)
                                    <option value="{{ $lvl }}" @selected(old('education_level') === $lvl)>{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="major" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Jurusan <span class="text-red-600">*</span></label>
                            <input id="major" name="major" type="text" value="{{ old('major') }}" required
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="institution" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Institusi <span class="text-red-600">*</span></label>
                            <input id="institution" name="institution" type="text" value="{{ old('institution') }}" required
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="graduation_year" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Tahun Lulus</label>
                            <input id="graduation_year" name="graduation_year" type="number" value="{{ old('graduation_year') }}"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                        <div>
                            <label for="gpa" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">IPK</label>
                            <input id="gpa" name="gpa" type="number" step="0.01" value="{{ old('gpa') }}"
                                   class="w-full rounded-lg border px-4 py-3 text-base"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="text-lg font-bold mb-4" style="color:var(--text-primary);">Dokumen</h2>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="cv" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">CV <span class="text-red-600">*</span></label>
                            <input id="cv" name="cv" type="file" required accept=".pdf,.doc,.docx"
                                   class="w-full rounded-lg border px-4 py-3 text-sm"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                            <div class="text-xs mt-2" style="color:var(--text-tertiary);">PDF/DOC/DOCX, max 2MB</div>
                        </div>
                        <div>
                            <label for="portfolio" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Portfolio</label>
                            <input id="portfolio" name="portfolio" type="file" accept=".pdf,.doc,.docx,.zip"
                                   class="w-full rounded-lg border px-4 py-3 text-sm"
                                   style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">
                            <div class="text-xs mt-2" style="color:var(--text-tertiary);">PDF/DOC/DOCX/ZIP, max 5MB</div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="cover_letter" class="text-sm font-semibold block mb-2" style="color:var(--text-primary);">Cover Letter</label>
                            <textarea id="cover_letter" name="cover_letter" rows="5"
                                      class="w-full rounded-lg border px-4 py-3 text-base"
                                      style="border-color:var(--border-light);background:var(--surface);color:var(--text-primary);">{{ old('cover_letter') }}</textarea>
                            <div class="text-xs mt-2" style="color:var(--text-tertiary);">Maksimal 2000 karakter</div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto"><i class="fas fa-paper-plane"></i> Kirim Lamaran</button>
                        <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-lg w-full sm:w-auto"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-4">
                <div class="card">
                    <div class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:var(--text-tertiary);">Ringkasan Lowongan</div>
                    <div class="text-sm font-bold mb-1" style="color:var(--text-primary);">{{ $vacancy->title }}</div>
                    <div class="text-sm mb-5" style="color:var(--text-secondary);">{{ $vacancy->location }} • {{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}</div>
                    <div class="text-sm" style="color:var(--text-secondary);">
                        Kirim lamaran Anda dan tim kami akan menghubungi Anda jika sesuai.
                    </div>
                    <div class="mt-6 pt-5 border-t" style="border-color:var(--border-light);">
                        <a href="{{ route('career.show', $vacancy->slug) }}" class="link-primary text-sm inline-flex items-center"><i class="fas fa-arrow-left mr-2"></i>Lihat Detail</a>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</section>

