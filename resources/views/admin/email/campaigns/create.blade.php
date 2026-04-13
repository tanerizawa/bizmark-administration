@extends('layouts.app')

@section('title', 'Buat Campaign Baru')
@section('page-title', 'Buat Campaign')

@section('content')
<div class="space-y-4">

    {{-- Page Header --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 opacity-20 blur-3xl rounded-full absolute -top-10 -right-6" style="background: var(--apple-blue);"></div>
            <div class="w-32 h-32 rounded-full absolute -bottom-10 left-8" style="background: rgba(175,82,222,0.12); filter: blur(48px);"></div>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Email Management</p>
                <h1 class="admin-hero-title text-white flex items-center gap-2">
                    <i class="fas fa-paper-plane text-apple-blue" style="font-size: 0.9rem;"></i>
                    Buat Campaign Email Baru
                </h1>
                <p class="admin-hero-desc">Isi detail campaign, tentukan penerima, dan kirim atau jadwalkan pengiriman.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}"
                   class="btn-secondary-apple text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </section>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="rounded-apple-lg px-4 py-3 flex items-start gap-3" style="background: rgba(255,59,48,0.1); border: 1px solid rgba(255,59,48,0.18);">
            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0" style="color: var(--apple-red);"></i>
            <div>
                <p class="text-sm font-semibold mb-1" style="color: var(--apple-red);">Terdapat kesalahan pada formulir:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li class="text-sm" style="color: rgba(255,59,48,0.85);">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(255,59,48,0.1); border: 1px solid rgba(255,59,48,0.18); color: var(--apple-red);">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Form --}}
    <form action="{{ route('admin.campaigns.store') }}" method="POST" id="campaignForm">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

            {{-- Left: Main Content (2/3 width) --}}
            <div class="xl:col-span-2 space-y-4">

                {{-- Basic Information --}}
                <div class="card-elevated rounded-apple-lg p-5 space-y-4">
                    <div class="flex items-center gap-2 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0" style="background: rgba(0,122,255,0.18);">
                            <i class="fas fa-info-circle text-apple-blue" style="font-size: 0.75rem;"></i>
                        </div>
                        <h2 class="text-sm font-semibold text-white" style="margin: 0;">Informasi Dasar</h2>
                    </div>

                    {{-- Campaign Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.75);">
                            Nama Campaign <span style="color: var(--apple-red);">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="cth. Newsletter Bulanan — April 2026"
                               class="input-apple w-full @error('name') border-apple-red @enderror"
                               required>
                        @error('name')
                            <p class="text-xs mt-1" style="color: var(--apple-red);">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Subject --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="subject" class="block text-sm font-medium" style="color: rgba(235,235,245,0.75);">
                                Subjek Email <span style="color: var(--apple-red);">*</span>
                            </label>
                            <span id="subject_count" class="text-xs tabular-nums" style="color: rgba(235,235,245,0.35);">0 / 80</span>
                        </div>
                        <input type="text"
                               id="subject"
                               name="subject"
                               value="{{ old('subject') }}"
                               placeholder="cth. 🎉 Update Terbaru dari Bizmark.ID"
                               class="input-apple w-full @error('subject') border-apple-red @enderror"
                               maxlength="255"
                               oninput="updateSubjectCount(this)"
                               required>
                        @error('subject')
                            <p class="text-xs mt-1" style="color: var(--apple-red);">{{ $message }}</p>
                        @enderror
                        <p class="text-xs mt-1.5 flex items-center gap-1.5" style="color: rgba(235,235,245,0.45);">
                            <i class="fas fa-lightbulb" style="color: rgba(255,149,0,0.8);"></i>
                            Gunakan emoji dan tag seperti <code class="var-chip" style="cursor:default;">@{{ name }}</code> untuk meningkatkan open rate. Optimal &le; 80 karakter.
                        </p>
                    </div>

                    {{-- Template Selection --}}
                    <div>
                        <label for="template_id" class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.75);">
                            Template Email
                            <span class="ml-1 text-xs font-normal" style="color: rgba(235,235,245,0.4);">(opsional)</span>
                        </label>
                        <select id="template_id"
                                name="template_id"
                                class="input-apple w-full"
                                onchange="loadTemplate(this.value)">
                            <option value="">— Pilih Template —</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}"
                                        data-content="{{ $template->content }}"
                                        {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ ucfirst($template->category) }})
                                </option>
                            @endforeach
                        </select>
                        @if($templates->isEmpty())
                            <p class="text-xs mt-1.5" style="color: rgba(235,235,245,0.4);">
                                <i class="fas fa-info-circle mr-1"></i>
                                Belum ada template aktif.
                                <a href="{{ route('admin.templates.create') }}" class="text-apple-blue hover:underline">Buat template</a>
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Email Content --}}
                <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                    <div class="flex items-center justify-between pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0" style="background: rgba(175,82,222,0.18);">
                                <i class="fas fa-code" style="color: #AF52DE; font-size: 0.75rem;"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-white" style="margin: 0;">Konten Email (HTML)</h2>
                        </div>
                        <button type="button"
                                class="btn-secondary-apple text-xs"
                                style="padding: 0.35rem 0.75rem;"
                                onclick="previewContent()">
                            <i class="fas fa-eye mr-1.5"></i>Preview
                        </button>
                    </div>

                    {{-- Variable chips --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        @php $varOpen = '{' . '{'; $varClose = '}' . '}'; @endphp
                        <span class="text-xs" style="color: rgba(235,235,245,0.45);">Sisipkan variabel:</span>
                        @foreach(['name', 'email', 'unsubscribe_url'] as $var)
                            <button type="button"
                                    class="var-chip"
                                    onclick="insertVariable('{{ $var }}')"
                                    title="Klik untuk sisipkan ke konten">
                                    {{ $varOpen . $var . $varClose }}
                            </button>
                        @endforeach
                    </div>

                    <textarea id="content"
                              name="content"
                              rows="18"
                              placeholder="Tulis konten HTML email di sini..."
                              class="input-apple w-full @error('content') border-apple-red @enderror"
                              style="font-family: 'Courier New', 'Consolas', monospace; font-size: 0.8125rem; line-height: 1.6; resize: vertical;"
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-xs" style="color: var(--apple-red);">{{ $message }}</p>
                    @enderror
                    <p class="text-xs" style="color: rgba(235,235,245,0.4);">
                        <i class="fas fa-code mr-1"></i>
                        Gunakan HTML penuh dengan inline CSS. Variabel tersedia: <code class="var-chip" style="cursor:default;">@{{ name }}</code> <code class="var-chip" style="cursor:default;">@{{ email }}</code> <code class="var-chip" style="cursor:default;">@{{ unsubscribe_url }}</code>
                    </p>
                </div>

            </div>

            {{-- Right: Settings Sidebar (1/3 width) --}}
            <div class="space-y-4 xl:sticky xl:top-4 xl:self-start">

                {{-- Recipients Card --}}
                <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                    <div class="flex items-center gap-2 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0" style="background: rgba(52,199,89,0.18);">
                            <i class="fas fa-users text-apple-green" style="font-size: 0.75rem;"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-white" style="margin: 0;">Penerima</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.75);">
                            Kirim Ke
                        </label>
                        <select id="recipient_type"
                                name="recipient_type"
                                class="input-apple w-full"
                                onchange="toggleTagsField()"
                                required>
                            <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>
                                Semua Subscriber
                            </option>
                            <option value="active" {{ old('recipient_type') == 'active' ? 'selected' : '' }}>
                                Hanya Aktif
                            </option>
                            <option value="tags" {{ old('recipient_type') == 'tags' ? 'selected' : '' }}>
                                Filter by Tag
                            </option>
                        </select>
                    </div>

                    {{-- Tags field --}}
                    <div id="tags_field" class="hidden">
                        <label for="recipient_tags" class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.75);">
                            Tags
                        </label>
                        <input type="text"
                               id="recipient_tags"
                               name="recipient_tags"
                               value="{{ old('recipient_tags') }}"
                               placeholder="cth. customer, vip, prospect"
                               class="input-apple w-full">
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.4);">Pisahkan dengan koma</p>
                    </div>

                    {{-- Subscriber count chip --}}
                    <div class="flex items-center gap-2.5 rounded-apple p-3" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.14);">
                        <i class="fas fa-user-check text-apple-green flex-shrink-0" style="font-size: 0.875rem;"></i>
                        <div>
                            <p class="text-xs" style="color: rgba(235,235,245,0.55); margin: 0;">Estimasi penerima</p>
                            <p class="text-sm font-semibold text-white" style="margin: 0;">
                                <span id="estimated_recipients">{{ $activeSubscribers }}</span>
                                <span class="text-xs font-normal" style="color: rgba(235,235,245,0.5);"> subscriber</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Schedule Card --}}
                <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                    <div class="flex items-center gap-2 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center justify-center w-7 h-7 rounded-lg flex-shrink-0" style="background: rgba(255,149,0,0.18);">
                            <i class="fas fa-clock text-apple-orange" style="font-size: 0.75rem;"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-white" style="margin: 0;">Jadwal Pengiriman</h3>
                    </div>

                    {{-- Radio options --}}
                    <div class="space-y-2">
                        <label class="schedule-option" id="opt_now_wrapper">
                            <input type="radio"
                                   name="schedule_type"
                                   id="send_now"
                                   value="now"
                                   onchange="toggleScheduleField()"
                                   checked>
                            <div>
                                <p class="text-sm font-medium text-white" style="margin: 0; line-height: 1.2;">Kirim Sekarang</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.5); margin: 0;">Langsung setelah disimpan</p>
                            </div>
                        </label>
                        <label class="schedule-option" id="opt_later_wrapper">
                            <input type="radio"
                                   name="schedule_type"
                                   id="schedule_later"
                                   value="later"
                                   onchange="toggleScheduleField()">
                            <div>
                                <p class="text-sm font-medium text-white" style="margin: 0; line-height: 1.2;">Jadwalkan</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.5); margin: 0;">Pilih tanggal &amp; waktu pengiriman</p>
                            </div>
                        </label>
                    </div>

                    {{-- Datetime picker (hidden until "later" selected) --}}
                    <div id="schedule_field" class="hidden">
                        <label for="scheduled_at" class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.75);">
                            Tanggal & Waktu
                        </label>
                        <input type="datetime-local"
                               id="scheduled_at"
                               name="scheduled_at"
                               value="{{ old('scheduled_at') }}"
                               class="input-apple w-full">
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.4);">Harus di masa yang akan datang</p>
                    </div>
                </div>

                {{-- Actions Card --}}
                <div class="card-elevated rounded-apple-lg p-4 space-y-2.5">
                    <h3 class="text-sm font-semibold text-white pb-3" style="margin: 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        Simpan Campaign
                    </h3>

                    <button type="submit"
                            name="action"
                            value="send"
                            class="campaign-action-btn campaign-btn-primary"
                            id="btn_send">
                        <i class="fas fa-paper-plane"></i>
                        <span>Buat &amp; Kirim</span>
                    </button>

                    <button type="submit"
                            name="action"
                            value="draft"
                            class="campaign-action-btn campaign-btn-secondary">
                        <i class="fas fa-save"></i>
                        <span>Simpan sebagai Draft</span>
                    </button>

                    <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}"
                       class="campaign-action-btn campaign-btn-ghost">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>

                    {{-- Send tip --}}
                    <p class="text-xs pt-1" style="color: rgba(235,235,245,0.38);">
                        <i class="fas fa-info-circle mr-1"></i>
                        "Buat &amp; Kirim" akan langsung mengarahkan ke halaman konfirmasi pengiriman.
                    </p>
                </div>

            </div>
        </div>{{-- end grid --}}
    </form>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: var(--dark-bg-elevated); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.05); padding: 1rem 1.25rem;">
                <div class="flex items-center gap-2">
                    <i class="fas fa-eye text-apple-blue"></i>
                    <h5 class="modal-title text-white text-sm font-semibold" id="previewModalLabel" style="margin: 0;">Preview Email</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div class="flex items-center gap-3 rounded-apple p-3 mb-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.05);">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">Subjek:</span>
                    <span id="preview_subject" class="text-sm text-white font-medium"></span>
                </div>
                <div id="preview_content" class="rounded-apple-lg overflow-hidden" style="background: #ffffff; min-height: 200px;"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ── Error border ── */
    .border-apple-red { border-color: var(--apple-red) !important; }

    /* ── Variable chips ── */
    .var-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-family: 'Courier New', 'Consolas', monospace;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        background: rgba(0,122,255,0.12);
        border: 1px solid rgba(0,122,255,0.16);
        color: rgba(0,149,255,0.9);
        letter-spacing: 0.01em;
        white-space: nowrap;
    }
    .var-chip:hover {
        background: rgba(0,122,255,0.22);
        border-color: rgba(0,122,255,0.28);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .var-chip:active { transform: translateY(0); }

    /* ── HTML textarea ── */
    #content {
        min-height: 380px;
        font-family: 'Courier New', 'Consolas', monospace;
        font-size: 0.8125rem;
        line-height: 1.65;
    }

    /* ── Radio schedule options ── */
    .schedule-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid rgba(255,255,255,0.06);
        background: rgba(255,255,255,0.03);
        user-select: none;
    }
    .schedule-option:hover {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
    }
    .schedule-option input[type="radio"] {
        accent-color: var(--apple-blue);
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
    }
    .schedule-option:has(input:checked) {
        background: rgba(0,122,255,0.10);
        border-color: rgba(0,122,255,0.22);
    }

    /* ── Action buttons ── */
    .campaign-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.65rem 1rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.18s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }
    .campaign-btn-primary {
        background: linear-gradient(135deg, var(--apple-blue), var(--apple-blue-dark, #0051D5));
        color: #ffffff;
        box-shadow: 0 2px 12px rgba(0,122,255,0.35);
    }
    .campaign-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 18px rgba(0,122,255,0.5);
        color: #ffffff;
        text-decoration: none;
    }
    .campaign-btn-secondary {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.09);
        color: rgba(235,235,245,0.85);
    }
    .campaign-btn-secondary:hover {
        background: rgba(255,255,255,0.13);
        color: #ffffff;
    }
    .campaign-btn-ghost {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.05);
        color: rgba(235,235,245,0.45);
    }
    .campaign-btn-ghost:hover {
        background: rgba(255,59,48,0.08);
        border-color: rgba(255,59,48,0.18);
        color: rgba(255,100,90,0.9);
        text-decoration: none;
    }

    /* ── Subject count color warning ── */
    .subject-count-warn { color: rgba(255,149,0,0.8) !important; }
    .subject-count-danger { color: var(--apple-red) !important; }
</style>
@endpush

@push('scripts')
<script>
function loadTemplate(templateId) {
    if (!templateId) return;
    const option = document.querySelector(`#template_id option[value="${templateId}"]`);
    if (option) {
        const content = option.getAttribute('data-content');
        if (content) {
            document.getElementById('content').value = content;
        }
    }
}

function insertVariable(varName) {
    const textarea = document.getElementById('content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const insertion = '{' + '{' + varName + '}' + '}';
    textarea.value = text.substring(0, start) + insertion + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + insertion.length;
    textarea.focus();
}

function updateSubjectCount(input) {
    const len = input.value.length;
    const counter = document.getElementById('subject_count');
    counter.textContent = len + ' / 80';
    counter.className = 'text-xs tabular-nums';
    if (len > 80) counter.classList.add('subject-count-danger');
    else if (len > 60) counter.classList.add('subject-count-warn');
    else counter.style.color = 'rgba(235,235,245,0.35)';
}

function toggleTagsField() {
    const recipientType = document.getElementById('recipient_type').value;
    const tagsField = document.getElementById('tags_field');
    tagsField.classList.toggle('hidden', recipientType !== 'tags');
    updateEstimatedRecipients();
}

function toggleScheduleField() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const scheduleField = document.getElementById('schedule_field');
    const btnSend = document.getElementById('btn_send');
    scheduleField.classList.toggle('hidden', scheduleType !== 'later');

    if (scheduleType === 'later') {
        btnSend.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Jadwalkan Campaign';
        btnSend.setAttribute('value', 'draft');
    } else {
        btnSend.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Buat &amp; Kirim';
        btnSend.setAttribute('value', 'send');
    }
}

function updateEstimatedRecipients() {
    document.getElementById('estimated_recipients').textContent = '{{ $activeSubscribers }}';
}

function previewContent() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    document.getElementById('preview_subject').textContent = subject || '(Belum ada subjek)';
    document.getElementById('preview_content').innerHTML = content || '<p style="color:#999; padding: 2rem; text-align:center;">Belum ada konten</p>';
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    toggleTagsField();
    // Init subject counter from old() value
    const subjectInput = document.getElementById('subject');
    if (subjectInput && subjectInput.value) updateSubjectCount(subjectInput);
});
</script>
@endpush
@endsection
