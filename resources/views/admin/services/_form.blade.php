{{--
    Partial: form fields untuk layanan (shared by create & edit)
    Variables: $service (null on create), $slug (null on create), $categories
--}}
@php $v = fn($key, $default='') => old($key, $service[$key] ?? $default); @endphp

@php
$inputStyle = 'width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
$labelStyle = 'display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px';
$textareaStyle = 'width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;resize:vertical';
@endphp

{{-- Tab navigation --}}
<div id="service-tabs" style="display:flex;gap:4px;border-bottom:1px solid var(--dark-separator);margin-bottom:22px;overflow-x:auto">
    @foreach([
        ['key'=>'basic',   'label'=>'Info Dasar',    'icon'=>'fa-info-circle'],
        ['key'=>'desc',    'label'=>'Deskripsi',     'icon'=>'fa-align-left'],
        ['key'=>'detail',  'label'=>'Detail & Harga','icon'=>'fa-tag'],
        ['key'=>'process', 'label'=>'Proses',        'icon'=>'fa-tasks'],
        ['key'=>'faq',     'label'=>'FAQ',           'icon'=>'fa-question-circle'],
        ['key'=>'seo',     'label'=>'SEO',           'icon'=>'fa-search'],
    ] as $tab)
    <button type="button" id="tab-btn-{{ $tab['key'] }}"
            onclick="switchServiceTab('{{ $tab['key'] }}')"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;font-size:0.8rem;font-weight:600;color:var(--dark-text-secondary);background:transparent;border:none;border-bottom:2px solid transparent;cursor:pointer;white-space:nowrap;transition:all .15s"
            onmouseover="if(this.dataset.active!=='1')this.style.color='var(--dark-text-primary)'"
            onmouseout="if(this.dataset.active!=='1')this.style.color='var(--dark-text-secondary)'"
            data-active="0">
        <i class="fas {{ $tab['icon'] }}" style="font-size:0.75rem"></i>{{ $tab['label'] }}
    </button>
    @endforeach
</div>

{{-- TAB: Info Dasar --}}
<div id="tab-panel-basic" style="display:block">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
            <label style="{{ $labelStyle }}">Slug <span style="color:var(--apple-red)">*</span></label>
            <input type="text" name="slug" value="{{ old('slug', $slug ?? '') }}" required placeholder="nama-layanan"
                   style="{{ $inputStyle }}" {{ $service ? 'readonly' : '' }}>
            @error('slug')<p style="color:var(--apple-red);font-size:0.75rem;margin-top:4px">{{ $message }}</p>@enderror
        </div>
        <div style="grid-column:span 2">
            <label style="{{ $labelStyle }}">Judul Layanan <span style="color:var(--apple-red)">*</span></label>
            <input type="text" name="title" value="{{ $v('title') }}" required placeholder="Contoh: Pengelolaan Limbah B3"
                   style="{{ $inputStyle }}">
            @error('title')<p style="color:var(--apple-red);font-size:0.75rem;margin-top:4px">{{ $message }}</p>@enderror
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
            <label style="{{ $labelStyle }}">Kategori</label>
            <select name="category" style="{{ $inputStyle }}">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $v('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="{{ $labelStyle }}">Icon (FontAwesome)</label>
            <input type="text" name="icon" value="{{ $v('icon', 'fa-layer-group') }}" placeholder="fa-warehouse"
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Warna Aksen</label>
            <div style="display:flex;gap:8px;align-items:center">
                <input type="color" name="color" value="{{ $v('color', '#FF9500') }}"
                       style="width:42px;height:38px;border:1px solid var(--dark-separator);border-radius:8px;background:var(--dark-bg-tertiary);cursor:pointer;padding:2px">
                <input type="text" name="color_text" id="color-text-preview" value="{{ $v('color', '#FF9500') }}" placeholder="#FF9500"
                       style="{{ $inputStyle }};flex:1">
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
            <label style="{{ $labelStyle }}">Badge (opsional)</label>
            <input type="text" name="badge" value="{{ $v('badge') }}" placeholder="Terpopuler"
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Layanan Unggulan</label>
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px">
                <input type="checkbox" name="featured" value="1" {{ $v('featured') ? 'checked' : '' }}
                       id="chk-featured" style="width:16px;height:16px;cursor:pointer;accent-color:var(--apple-blue)">
                <label for="chk-featured" style="font-size:0.85rem;color:var(--dark-text-primary);cursor:pointer;margin:0">Tampilkan sebagai unggulan</label>
            </div>
        </div>
    </div>
</div>

{{-- TAB: Deskripsi --}}
<div id="tab-panel-desc" style="display:none">
    <div style="display:flex;flex-direction:column;gap:14px">
        <div>
            <label style="{{ $labelStyle }}">Tagline</label>
            <input type="text" name="tagline" value="{{ $v('tagline') }}" placeholder="Tagline singkat layanan"
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Deskripsi Singkat</label>
            <textarea name="short_description" rows="2" placeholder="1–2 kalimat ringkas..."
                      style="{{ $textareaStyle }}">{{ $v('short_description') }}</textarea>
        </div>
        <div>
            <label style="{{ $labelStyle }}">Deskripsi Lengkap</label>
            <textarea name="long_description" rows="7" placeholder="Deskripsi detail layanan..."
                      style="{{ $textareaStyle }}">{{ $v('long_description') }}</textarea>
        </div>
    </div>
</div>

{{-- TAB: Detail & Harga --}}
<div id="tab-panel-detail" style="display:none">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
            <label style="{{ $labelStyle }}">Rentang Harga</label>
            <input type="text" name="price_range" value="{{ $v('price_range') }}" placeholder="Rp 5jt – Rp 15jt"
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Estimasi Waktu</label>
            <input type="text" name="process_time" value="{{ $v('process_time') }}" placeholder="14–30 Hari"
                   style="{{ $inputStyle }}">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
            <label style="{{ $labelStyle }}">Mulai Dari (harga terendah)</label>
            <input type="text" name="starting_price" value="{{ $v('starting_price') }}" placeholder="Mulai Rp 5.000.000"
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Target Klien</label>
            <input type="text" name="target_client" value="{{ $v('target_client') }}" placeholder="Perusahaan, UKM, ..."
                   style="{{ $inputStyle }}">
        </div>
    </div>
    <div style="margin-bottom:14px">
        <label style="{{ $labelStyle }}">Persyaratan Dokumen <span style="font-weight:400;color:var(--dark-text-tertiary)">(satu per baris)</span></label>
        <textarea name="requirements" rows="5" placeholder="SIUP&#10;NPWP&#10;Akta Pendirian"
                  style="{{ $textareaStyle }}">{{ implode("\n", old('requirements', $service['requirements'] ?? [])) }}</textarea>
    </div>
    <div>
        <label style="{{ $labelStyle }}">Output / Hasil Layanan <span style="font-weight:400;color:var(--dark-text-tertiary)">(satu per baris)</span></label>
        <textarea name="outputs" rows="4" placeholder="Sertifikat&#10;Izin Operasional"
                  style="{{ $textareaStyle }}">{{ implode("\n", old('outputs', $service['outputs'] ?? [])) }}</textarea>
    </div>
</div>

{{-- TAB: Proses --}}
<div id="tab-panel-process" style="display:none">
    <div style="margin-bottom:14px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <label style="{{ $labelStyle }};margin:0">Tahapan Proses Layanan</label>
            <button type="button" onclick="addServiceStep()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;font-size:0.75rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;cursor:pointer">
                <i class="fas fa-plus"></i>Tambah Tahapan
            </button>
        </div>
        <div id="steps-container" style="display:flex;flex-direction:column;gap:10px">
            @php $steps = old('steps', $service['steps'] ?? []); @endphp
            @if(empty($steps))
            <div class="step-item" style="display:flex;gap:8px;align-items:flex-start;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                <div style="flex-shrink:0;width:24px;height:24px;border-radius:50%;background:var(--apple-blue);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;margin-top:1px">1</div>
                <div style="flex:1;display:grid;grid-template-columns:1fr 2fr;gap:10px">
                    <input type="text" name="steps[0][title]" placeholder="Nama tahapan" style="{{ $inputStyle }}">
                    <input type="text" name="steps[0][description]" placeholder="Deskripsi singkat..." style="{{ $inputStyle }}">
                </div>
                <button type="button" onclick="this.closest('.step-item').remove()"
                        style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;margin-top:2px">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @else
            @foreach($steps as $i => $step)
            <div class="step-item" style="display:flex;gap:8px;align-items:flex-start;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                <div style="flex-shrink:0;width:24px;height:24px;border-radius:50%;background:var(--apple-blue);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;margin-top:1px">{{ $i + 1 }}</div>
                <div style="flex:1;display:grid;grid-template-columns:1fr 2fr;gap:10px">
                    <input type="text" name="steps[{{ $i }}][title]" value="{{ $step['title'] ?? '' }}" placeholder="Nama tahapan" style="{{ $inputStyle }}">
                    <input type="text" name="steps[{{ $i }}][description]" value="{{ $step['description'] ?? '' }}" placeholder="Deskripsi singkat..." style="{{ $inputStyle }}">
                </div>
                <button type="button" onclick="this.closest('.step-item').remove()"
                        style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;margin-top:2px">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>

{{-- TAB: FAQ --}}
<div id="tab-panel-faq" style="display:none">
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <label style="{{ $labelStyle }};margin:0">Pertanyaan Sering Diajukan (FAQ)</label>
            <button type="button" onclick="addServiceFaq()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;font-size:0.75rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;cursor:pointer">
                <i class="fas fa-plus"></i>Tambah FAQ
            </button>
        </div>
        <div id="faq-container" style="display:flex;flex-direction:column;gap:10px">
            @php $faqs = old('faqs', $service['faqs'] ?? []); @endphp
            @if(empty($faqs))
            <div class="faq-item" style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <div style="flex:1">
                        <input type="text" name="faqs[0][question]" placeholder="Pertanyaan..." style="{{ $inputStyle }}">
                    </div>
                    <button type="button" onclick="this.closest('.faq-item').remove()"
                            style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;flex-shrink:0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <textarea name="faqs[0][answer]" rows="2" placeholder="Jawaban..."
                          style="{{ $textareaStyle }}"></textarea>
            </div>
            @else
            @foreach($faqs as $i => $faq)
            <div class="faq-item" style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <div style="flex:1">
                        <input type="text" name="faqs[{{ $i }}][question]" value="{{ $faq['question'] ?? '' }}" placeholder="Pertanyaan..." style="{{ $inputStyle }}">
                    </div>
                    <button type="button" onclick="this.closest('.faq-item').remove()"
                            style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;flex-shrink:0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <textarea name="faqs[{{ $i }}][answer]" rows="2" placeholder="Jawaban..."
                          style="{{ $textareaStyle }}">{{ $faq['answer'] ?? '' }}</textarea>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>

{{-- TAB: SEO --}}
<div id="tab-panel-seo" style="display:none">
    <div style="display:flex;flex-direction:column;gap:14px">
        <div>
            <label style="{{ $labelStyle }}">Meta Title</label>
            <input type="text" name="meta_title" value="{{ $v('meta_title') }}" placeholder="Judul SEO halaman ini..."
                   style="{{ $inputStyle }}">
        </div>
        <div>
            <label style="{{ $labelStyle }}">Meta Description</label>
            <textarea name="meta_description" rows="3" placeholder="Deskripsi SEO..."
                      style="{{ $textareaStyle }}">{{ $v('meta_description') }}</textarea>
        </div>
        <div>
            <label style="{{ $labelStyle }}">Meta Keywords</label>
            <textarea name="meta_keywords" rows="2" placeholder="kata kunci 1, kata kunci 2, ..."
                      style="{{ $textareaStyle }}">{{ $v('meta_keywords') }}</textarea>
        </div>
        <div>
            <label style="{{ $labelStyle }}">Canonical URL (opsional)</label>
            <input type="url" name="canonical_url" value="{{ $v('canonical_url') }}" placeholder="https://bizmark.id/layanan/..."
                   style="{{ $inputStyle }}">
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // Tab switching
    const TABS = ['basic','desc','detail','process','faq','seo'];

    window.switchServiceTab = function(key) {
        TABS.forEach(function(t) {
            var panel = document.getElementById('tab-panel-' + t);
            var btn   = document.getElementById('tab-btn-' + t);
            if (!panel || !btn) return;
            if (t === key) {
                panel.style.display = 'block';
                btn.style.color          = 'var(--apple-blue)';
                btn.style.borderBottom   = '2px solid var(--apple-blue)';
                btn.dataset.active = '1';
            } else {
                panel.style.display = 'none';
                btn.style.color          = 'var(--dark-text-secondary)';
                btn.style.borderBottom   = '2px solid transparent';
                btn.dataset.active = '0';
            }
        });
    };

    // Init: show 'basic' tab on load
    document.addEventListener('DOMContentLoaded', function() {
        // Activate first tab
        switchServiceTab('basic');
    });

    // Step counter
    var stepCount = document.querySelectorAll('.step-item').length || 1;
    window.addServiceStep = function() {
        var idx = stepCount++;
        var inputStyle = 'width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid rgba(84,84,88,0.35);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
        var html = '<div class="step-item" style="display:flex;gap:8px;align-items:flex-start;background:var(--dark-bg-tertiary);border:1px solid rgba(84,84,88,0.35);border-radius:10px;padding:12px">'
            + '<div style="flex-shrink:0;width:24px;height:24px;border-radius:50%;background:var(--apple-blue);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;margin-top:1px">' + (idx+1) + '</div>'
            + '<div style="flex:1;display:grid;grid-template-columns:1fr 2fr;gap:10px">'
            + '<input type="text" name="steps[' + idx + '][title]" placeholder="Nama tahapan" style="' + inputStyle + '">'
            + '<input type="text" name="steps[' + idx + '][description]" placeholder="Deskripsi singkat..." style="' + inputStyle + '">'
            + '</div>'
            + '<button type="button" onclick="this.closest(\'.step-item\').remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;margin-top:2px"><i class="fas fa-times"></i></button>'
            + '</div>';
        document.getElementById('steps-container').insertAdjacentHTML('beforeend', html);
    };

    // FAQ counter
    var faqCount = document.querySelectorAll('.faq-item').length || 1;
    window.addServiceFaq = function() {
        var idx = faqCount++;
        var inputStyle = 'width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid rgba(84,84,88,0.35);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
        var taStyle = inputStyle + ';resize:vertical';
        var html = '<div class="faq-item" style="background:var(--dark-bg-tertiary);border:1px solid rgba(84,84,88,0.35);border-radius:10px;padding:12px">'
            + '<div style="display:flex;gap:8px;margin-bottom:8px">'
            + '<div style="flex:1"><input type="text" name="faqs[' + idx + '][question]" placeholder="Pertanyaan..." style="' + inputStyle + '"></div>'
            + '<button type="button" onclick="this.closest(\'.faq-item\').remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;flex-shrink:0"><i class="fas fa-times"></i></button>'
            + '</div>'
            + '<textarea name="faqs[' + idx + '][answer]" rows="2" placeholder="Jawaban..." style="' + taStyle + '"></textarea>'
            + '</div>';
        document.getElementById('faq-container').insertAdjacentHTML('beforeend', html);
    };
})();
</script>
@endpush
