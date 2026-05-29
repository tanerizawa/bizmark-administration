{{--
    Partial: form fields untuk sub-layanan
    Variables: $sub (null saat create), $subSlug (null saat create)
--}}
@php
$v = fn($key, $default='') => old($key, $sub[$key] ?? $default);
$inp = 'width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
$lbl = 'display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px';
$ta  = 'width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;resize:vertical';
@endphp

<div style="display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:14px">
    <div>
        <label style="{{ $lbl }}">Judul Sub-Layanan <span style="color:var(--apple-red)">*</span></label>
        <input type="text" name="title" value="{{ $v('title') }}" required placeholder="Contoh: Pengumpulan Limbah B3"
               style="{{ $inp }}">
        @error('title')<p style="color:var(--apple-red);font-size:0.75rem;margin-top:4px">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $lbl }}">Sub-Slug <span style="color:var(--apple-red)">*</span></label>
        <div style="display:flex;align-items:center;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;overflow:hidden"
             onfocusin="this.style.borderColor='var(--apple-blue)';this.style.boxShadow='0 0 0 3px rgba(0,122,255,.1)'"
             onfocusout="this.style.borderColor='var(--dark-separator)';this.style.boxShadow='none'">
            <span style="padding:9px 10px;font-size:0.78rem;color:var(--dark-text-secondary);white-space:nowrap;border-right:1px solid var(--dark-separator);flex-shrink:0">/sub/</span>
            <input type="text" name="sub_slug" value="{{ $v('sub_slug', $subSlug ?? '') }}" required placeholder="nama-sub-layanan"
                   style="border:none;background:transparent;border-radius:0;flex:1;padding:9px 12px;font-size:0.85rem;color:var(--dark-text-primary);outline:none">
        </div>
        @error('sub_slug')<p style="color:var(--apple-red);font-size:0.75rem;margin-top:4px">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
    <div>
        <label style="{{ $lbl }}">Icon (FontAwesome)</label>
        <input type="text" name="icon" value="{{ $v('icon', 'fa-layer-group') }}" placeholder="fa-warehouse"
               style="{{ $inp }}">
        <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:4px 0 0">Cari di fontawesome.com</p>
    </div>
    <div>
        <label style="{{ $lbl }}">Durasi</label>
        <input type="text" name="duration" value="{{ $v('duration') }}" placeholder="30–60 Hari"
               style="{{ $inp }}">
    </div>
</div>

<div style="display:flex;flex-direction:column;gap:14px;margin-bottom:14px">
    <div>
        <label style="{{ $lbl }}">Deskripsi Singkat</label>
        <textarea name="short_description" rows="2" placeholder="1–2 kalimat ringkas..."
                  style="{{ $ta }}">{{ $v('short_description') }}</textarea>
    </div>
    <div>
        <label style="{{ $lbl }}">Deskripsi Lengkap</label>
        <textarea name="long_description" rows="6" placeholder="Deskripsi detail..."
                  style="{{ $ta }}">{{ $v('long_description') }}</textarea>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
    <div>
        <label style="{{ $lbl }}">Tahapan Proses <span style="font-weight:400;color:var(--dark-text-tertiary)">(satu per baris)</span></label>
        <textarea name="process_steps" rows="6" placeholder="Satu tahapan per baris"
                  style="{{ $ta }}">{{ implode("\n", old('process_steps', $sub['process_steps'] ?? [])) }}</textarea>
    </div>
    <div>
        <label style="{{ $lbl }}">Persyaratan Dokumen <span style="font-weight:400;color:var(--dark-text-tertiary)">(satu per baris)</span></label>
        <textarea name="requirements" rows="6" placeholder="Satu persyaratan per baris"
                  style="{{ $ta }}">{{ implode("\n", old('requirements', $sub['requirements'] ?? [])) }}</textarea>
    </div>
</div>

<div>
    <label style="{{ $lbl }}">Meta Keywords</label>
    <textarea name="meta_keywords" rows="2" placeholder="kata kunci 1, kata kunci 2, ..."
              style="{{ $ta }}">{{ $v('meta_keywords') }}</textarea>
</div>
