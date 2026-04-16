@extends('layouts.app')

@section('title', 'Detail Permohonan - ' . $serviceRequest->request_number)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}" class="text-apple-blue hover:text-blue-400 mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-dark-text-primary">
                    {{ $serviceRequest->request_number }}
                </h1>
                <div class="flex items-center space-x-3 text-xs mt-1 text-dark-text-secondary">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>{{ $serviceRequest->created_at->format('d M Y H:i') }}
                    </span>
                    @php
                        $statusColors = [
                            'pending' => 'text-yellow-400',
                            'reviewing' => 'text-blue-400',
                            'quoted' => 'text-indigo-400',
                            'accepted' => 'text-green-400',
                            'rejected' => 'text-red-400',
                            'cancelled' => 'text-gray-400',
                        ];
                    @endphp
                    <span class="flex items-center {{ $statusColors[$serviceRequest->status] ?? 'text-gray-400' }}">
                        <i class="fas fa-info-circle mr-1.5"></i>{{ $serviceRequest->status_label }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            @if($serviceRequest->status !== 'cancelled')
                <button onclick="document.getElementById('archiveModal').classList.remove('hidden')" 
                        class="px-3 py-2 rounded-apple text-sm font-medium transition-colors inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white">
                    <i class="fas fa-archive mr-1.5"></i>Arsip
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3" style="color: var(--apple-green);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-green);">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-green); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mr-3 mt-0.5" style="color: var(--apple-red);"></i>
                <div class="text-sm" style="color: var(--apple-red);">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3" style="color: var(--apple-red);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-red);">{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="text-xs text-dark-text-secondary mb-1">Status</div>
            <div class="text-sm font-semibold text-dark-text-primary">{{ $serviceRequest->status_label }}</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="text-xs text-dark-text-secondary mb-1">Nilai Quote</div>
            <div class="text-sm font-semibold text-green-400">
                {{ $serviceRequest->quoted_price ? 'Rp ' . number_format($serviceRequest->quoted_price, 0, ',', '.') : 'Belum tersedia' }}
            </div>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="text-xs text-dark-text-secondary mb-1">Email Tujuan</div>
            <div class="text-sm font-semibold text-dark-text-primary break-all">{{ $serviceRequest->email }}</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="text-xs text-dark-text-secondary mb-1">Dikirim Pada</div>
            <div class="text-sm font-semibold text-dark-text-primary">{{ $serviceRequest->responded_at ? $serviceRequest->responded_at->format('d M Y H:i') : 'Belum dikirim' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Left Column -->
        <div class="space-y-4">
            <!-- Contact Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-user mr-2 text-apple-blue"></i>Informasi Kontak
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Tipe Pemohon</label>
                        <p class="text-sm text-dark-text-primary">
                            {{ $serviceRequest->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Pemohon</label>
                        <p class="text-sm text-dark-text-primary">{{ $serviceRequest->display_name }}</p>
                    </div>
                    @if($serviceRequest->applicant_type === 'badan' && $serviceRequest->company_name)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Perusahaan</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceRequest->company_name }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Email</label>
                        <p class="text-sm text-dark-text-primary">
                            <a href="mailto:{{ $serviceRequest->email }}" class="text-apple-blue hover:text-blue-400">
                                {{ $serviceRequest->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Telepon</label>
                        <p class="text-sm text-dark-text-primary">
                            <a href="tel:{{ $serviceRequest->phone }}" class="text-apple-blue hover:text-blue-400">
                                {{ $serviceRequest->phone }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-briefcase mr-2 text-apple-blue"></i>Informasi Layanan
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-dark-text-secondary block mb-1">Kategori Layanan</label>
                        <p class="text-sm text-dark-text-primary">
                            {{ \App\Models\ServiceCostRequest::getServiceCategories()[$serviceRequest->service_category] ?? $serviceRequest->service_category }}
                        </p>
                    </div>
                    @if(!empty($serviceRequest->services_requested) && is_array($serviceRequest->services_requested))
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Layanan Dipilih</label>
                            <div class="space-y-1">
                                @foreach($serviceRequest->services_requested as $service)
                                    <p class="text-sm text-dark-text-primary flex items-start">
                                        <i class="fas fa-check text-green-400 mr-2 mt-1 text-xs"></i>
                                        <span>{{ $service }}</span>
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($serviceRequest->project_description)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Deskripsi Kebutuhan</label>
                            <p class="text-sm text-dark-text-primary whitespace-pre-wrap">{{ $serviceRequest->project_description }}</p>
                        </div>
                    @endif
                    @if($serviceRequest->project_location)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Lokasi Proyek</label>
                            <p class="text-sm text-dark-text-primary">{{ $serviceRequest->project_location }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quote Management -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-receipt mr-2 text-apple-blue"></i>Manajemen Quote
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    @if($serviceRequest->quoted_at)
                        @php
                            $defaultEmailSubject = $serviceRequest->quote_details['email_subject']
                                ?? ('Penawaran Jasa - ' . $serviceRequest->request_number . ' - Bizmark.ID');
                            $defaultEmailBody = $serviceRequest->quote_details['email_body']
                                ?? ('Yth. Bapak/Ibu ' . $serviceRequest->display_name . "\n\n" .
                                    'Terima kasih atas kepercayaan Anda kepada Bizmark.ID. Bersama ini kami sampaikan penawaran jasa untuk permohonan ' . $serviceRequest->request_number . '. ' .
                                    'Nilai penawaran saat ini sebesar Rp ' . number_format((float) $serviceRequest->quoted_price, 0, ',', '.') .
                                    ($serviceRequest->quoted_timeline ? ' dengan estimasi timeline ' . $serviceRequest->quoted_timeline . '.' : '.') . "\n\n" .
                                    'Apabila diperlukan penyesuaian ruang lingkup atau klarifikasi tambahan, silakan balas email ini dan tim kami akan menindaklanjuti.\n\n' .
                                    'Hormat kami,\nTim Konsultan\ninfo@bizmark.id');
                            $defaultEmailHtmlBody = $serviceRequest->quote_details['email_html_body'] ?? '';
                            $signatureMeta = $serviceRequest->quote_details['digital_signature'] ?? [];
                        @endphp

                        <div class="p-4 rounded-apple-lg" style="background: linear-gradient(145deg, rgba(15,23,42,0.95), rgba(30,64,175,0.82)); border: 1px solid rgba(148,163,184,0.35);">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-wide text-blue-100/80 mb-1">Quote Aktif</p>
                                    <p class="text-2xl font-semibold text-white">Rp {{ number_format($serviceRequest->quoted_price, 0, ',', '.') }}</p>
                                    <p class="text-xs text-blue-100/85 mt-1">Dikutip pada {{ $serviceRequest->quoted_at->format('d M Y H:i') }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.service-cost-requests.regenerate-content', $serviceRequest->request_number) }}" class="w-64 space-y-2">
                                    @csrf
                                    <textarea name="regen_notes" rows="2" class="w-full px-2.5 py-2 rounded-apple text-xs" placeholder="Arahan regenerate (opsional)" style="background: rgba(15,23,42,0.45); border: 1px solid rgba(191,219,254,0.35); color: #e2e8f0;"></textarea>
                                    <button type="submit" class="w-full px-3 py-2 rounded-apple text-xs font-semibold text-white" style="background: rgba(14,165,233,0.85);">
                                        <i class="fas fa-rotate-right mr-1"></i>Regenerate AI
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($serviceRequest->quoted_timeline)
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-1">Timeline Proses</label>
                                <p class="text-sm text-dark-text-primary">{{ $serviceRequest->quoted_timeline }}</p>
                            </div>
                        @endif

                        @if(!empty($serviceRequest->quote_details['offer_text']))
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-dark-text-secondary">Naskah Penawaran Formal</label>
                                    @if(!empty($serviceRequest->quote_details['generated_by_ai']))
                                        <span class="text-[11px] px-2 py-0.5 rounded border border-blue-500 text-blue-400">Generated by AI</span>
                                    @endif
                                </div>
                                <textarea id="offerText" rows="8" readonly class="w-full px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">{{ $serviceRequest->quote_details['offer_text'] }}</textarea>
                                <button type="button" onclick="copyField('offerText')" class="btn-secondary px-3 py-2 rounded-apple text-xs font-medium mt-2">
                                    <i class="fas fa-copy mr-1"></i>Copy Naskah Penawaran
                                </button>
                            </div>
                        @endif

                        @if($defaultEmailHtmlBody !== '')
                            <div class="rounded-apple-lg p-3" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-medium text-dark-text-secondary">Preview Typography Email</label>
                                    <span class="text-[11px] text-blue-400">Rich Text</span>
                                </div>
                                <div class="rounded-apple p-3 text-sm" style="background: #ffffff; color: #0f172a; border:1px solid #dbe2ef; max-height:240px; overflow:auto;">
                                    {!! $defaultEmailHtmlBody !!}
                                </div>
                            </div>
                        @endif

                        @if(!empty($signatureMeta))
                            <div class="rounded-apple-lg p-3" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Digital Signature</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                    <div><span class="text-dark-text-secondary">Signer:</span> <span class="text-dark-text-primary">{{ $signatureMeta['signer_name'] ?? 'Tim Konsultan' }}</span></div>
                                    <div><span class="text-dark-text-secondary">Issued:</span> <span class="text-dark-text-primary">{{ $signatureMeta['issued_at'] ?? '-' }}</span></div>
                                    <div class="md:col-span-2"><span class="text-dark-text-secondary">Signature ID:</span> <span class="text-dark-text-primary font-mono">{{ $signatureMeta['signature_id'] ?? '-' }}</span></div>
                                    <div class="md:col-span-2"><span class="text-dark-text-secondary">Verification Hash:</span> <span class="text-dark-text-primary font-mono">{{ $signatureMeta['signature_hash'] ?? '-' }}</span></div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.service-cost-requests.send-email', $serviceRequest->request_number) }}" class="pt-2" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                            @csrf
                            <label class="text-xs font-medium text-dark-text-secondary block mb-2">Template Email Manual</label>
                            <div class="mb-2">
                                <label class="text-[11px] text-dark-text-secondary block mb-1">Subject</label>
                                <input id="emailSubject" name="email_subject" type="text" value="{{ $defaultEmailSubject }}" class="w-full px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                            </div>
                            <div>
                                <label class="text-[11px] text-dark-text-secondary block mb-1">Body</label>
                                <textarea id="emailBody" name="email_body" rows="10" class="w-full px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">{{ $defaultEmailBody }}</textarea>
                            </div>
                            <div class="flex gap-2 mt-2">
                                <button type="button" onclick="copyField('emailSubject')" class="btn-secondary px-3 py-2 rounded-apple text-xs font-medium">
                                    <i class="fas fa-copy mr-1"></i>Copy Subject
                                </button>
                                <button type="button" onclick="copyField('emailBody')" class="btn-secondary px-3 py-2 rounded-apple text-xs font-medium">
                                    <i class="fas fa-copy mr-1"></i>Copy Body
                                </button>
                                <button type="submit" class="btn-primary px-3 py-2 rounded-apple text-xs font-medium">
                                    <i class="fas fa-paper-plane mr-1"></i>Kirim via info@bizmark.id
                                </button>
                            </div>
                            <p class="text-xs text-dark-text-secondary mt-2">Email akan dikirim melalui sistem aplikasi dengan pengirim <strong>Tim Konsultan &lt;info@bizmark.id&gt;</strong>.</p>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.service-cost-requests.generate-quote', $serviceRequest->request_number) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Estimasi Biaya (Rp) <span class="text-apple-red">*</span></label>
                                <input type="number" 
                                       name="quoted_price" 
                                       class="w-full px-3 py-2 rounded-apple text-sm" 
                                       placeholder="Contoh: 5000000"
                                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                                       min="0" 
                                       step="1000" 
                                       required>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Timeline Proses</label>
                                <input type="text" 
                                       name="quoted_timeline" 
                                       class="w-full px-3 py-2 rounded-apple text-sm" 
                                       placeholder="Contoh: 5-7 hari kerja"
                                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-dark-text-secondary block mb-2">Catatan Quote</label>
                                <textarea name="quote_notes" 
                                          rows="3" 
                                          class="w-full px-3 py-2 rounded-apple text-sm" 
                                          placeholder="Catatan tambahan untuk quote..."
                                          style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"></textarea>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-dark-text-primary">
                                <input type="checkbox" name="generate_ai_content" value="1" checked>
                                <span>Generate naskah penawaran dan template email formal dengan AI</span>
                            </label>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>Buat Quote
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Status & Priority Management -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-tasks mr-2 text-apple-blue"></i>Status Management
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Update Status -->
                    <form method="POST" action="{{ route('admin.service-cost-requests.update-status', $serviceRequest->request_number) }}">
                        @csrf
                        @method('PATCH')
                        <label class="text-xs font-medium text-dark-text-secondary block mb-2">Update Status</label>
                        <div class="flex gap-2">
                            <select name="status" class="flex-1 px-3 py-2 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $serviceRequest->status == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium">
                                Update
                            </button>
                        </div>
                    </form>

                    <!-- Reviewed Info -->
                    @if($serviceRequest->reviewed_at)
                        <div>
                            <label class="text-xs font-medium text-dark-text-secondary block mb-1">Direview Pada</label>
                            <p class="text-sm text-dark-text-primary">
                                {{ $serviceRequest->reviewed_at->format('d M Y H:i') }}
                                @if($serviceRequest->reviewer)
                                    <span class="text-dark-text-secondary">oleh {{ $serviceRequest->reviewer->name ?? 'Admin' }}</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Completion Status -->
                    @if($serviceRequest->status === 'accepted' || $serviceRequest->status === 'quoted')
                        @if(!$serviceRequest->completed_at)
                            <form method="POST" action="{{ route('admin.service-cost-requests.complete', $serviceRequest->request_number) }}">
                                @csrf
                                <button type="submit" class="btn-success px-4 py-2 rounded-apple text-sm font-medium w-full">
                                    <i class="fas fa-check mr-2"></i>Tandai Selesai
                                </button>
                            </form>
                        @else
                            <div class="p-3 rounded-apple text-center" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <p class="text-sm text-green-400 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>Selesai pada {{ $serviceRequest->completed_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
            <!-- Documents -->
            @if(!empty($serviceRequest->documents) && is_array($serviceRequest->documents))
                <div class="card-elevated rounded-apple-lg">
                    <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                        <h3 class="text-base font-semibold text-white flex items-center">
                            <i class="fas fa-file-upload mr-2 text-apple-blue"></i>Dokumen ({{ count($serviceRequest->documents) }})
                        </h3>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($serviceRequest->documents as $doc)
                            <div class="p-3 rounded-apple flex items-center justify-between" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <div class="flex items-center flex-1 min-w-0">
                                    <i class="fas fa-file-pdf text-red-400 mr-2 text-lg flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-dark-text-primary truncate">{{ $doc['name'] ?? 'Document' }}</p>
                                        <p class="text-xs text-dark-text-secondary">{{ round(($doc['size'] ?? 0) / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                @if(isset($doc['path']))
                                    <a href="{{ asset('storage/' . $doc['path']) }}" 
                                       target="_blank" 
                                       class="text-apple-blue hover:text-blue-400 text-sm ml-2 flex-shrink-0">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Admin Notes -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-apple-blue"></i>Catatan Admin
                    </h3>
                </div>
                <div class="p-4">
                    <!-- Add Note Form -->
                    <form method="POST" action="{{ route('admin.service-cost-requests.add-note', $serviceRequest->request_number) }}" class="mb-4">
                        @csrf
                        <textarea name="note" 
                                  rows="3" 
                                  class="w-full px-3 py-2 rounded-apple text-sm mb-2" 
                                  placeholder="Tambahkan catatan..."
                                  style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"></textarea>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                            <i class="fas fa-plus mr-2"></i>Tambah Catatan
                        </button>
                    </form>

                    <!-- Existing Notes -->
                    @if($serviceRequest->admin_notes)
                        <div class="space-y-2">
                            @foreach(array_filter(explode("\n\n", $serviceRequest->admin_notes)) as $note)
                                <div class="p-3 rounded-apple text-sm" style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                    <p class="text-dark-text-primary whitespace-pre-wrap">{{ $note }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-dark-text-secondary text-center py-4">Belum ada catatan</p>
                    @endif
                </div>
            </div>

            <!-- Technical Info -->
            <div class="card-elevated rounded-apple-lg">
                <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <h3 class="text-base font-semibold text-white flex items-center">
                        <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Teknis
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Nomor Permohonan:</span>
                        <span class="text-dark-text-primary font-mono">{{ $serviceRequest->request_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">IP Address:</span>
                        <span class="text-dark-text-primary font-mono">{{ $serviceRequest->ip_address ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Source:</span>
                        <span class="text-dark-text-primary">{{ $serviceRequest->source ?? 'website' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark-text-secondary">Dibuat:</span>
                        <span class="text-dark-text-primary">{{ $serviceRequest->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyField(fieldId) {
    const el = document.getElementById(fieldId);
    if (!el) return;

    const value = el.value || el.textContent || '';
    navigator.clipboard.writeText(value).then(() => {
        alert('Konten berhasil dicopy');
    }).catch(() => {
        alert('Gagal copy otomatis. Silakan copy manual.');
    });
}
</script>

<!-- Archive Modal -->
<div id="archiveModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-lg max-w-md w-full p-6">
        <h3 class="text-base font-semibold text-white mb-4">Arsipkan Permohonan</h3>
        <p class="text-sm text-dark-text-secondary mb-4">
            Apakah Anda yakin ingin mengarsipkan permohonan ini? Permohonan yang diarsipkan akan tersembunyi dari daftar utama.
        </p>
        <form method="POST" action="{{ route('admin.service-cost-requests.archive', $serviceRequest->request_number) }}">
            @csrf
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('archiveModal').classList.add('hidden')" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    Batal
                </button>
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    Arsipkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
