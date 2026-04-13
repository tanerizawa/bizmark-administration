@php
    $activeFolder = $activeFolder ?? 'inbox';
    $selectedEmailId = $selectedEmail?->id;
    $resetInboxQuery = ['tab' => 'inbox', 'folder' => 'inbox'];
    $closeDetailQuery = array_merge(request()->except('email'), ['tab' => 'inbox']);
    $baseInboxQuery = array_merge(request()->except(['folder', 'is_read', 'email', 'inbox_page']), ['tab' => 'inbox']);
    $mailboxCounts = $mailboxCounts ?? [];
    $statusCounts = $statusCounts ?? ['all' => 0, 'read' => 0, 'unread' => 0];
    $mailboxSummaryCards = [
        ['label' => 'Inbox belum dibaca', 'value' => number_format($unreadEmails ?? 0), 'icon' => 'envelope-open', 'tone' => 'rgba(255,159,10,0.16)', 'color' => '#FF9F0A'],
        ['label' => 'Draft campaign', 'value' => number_format($notifications['campaigns'] ?? 0), 'icon' => 'bullhorn', 'tone' => 'rgba(10,132,255,0.16)', 'color' => '#0A84FF'],
        ['label' => 'Akun aktif', 'value' => number_format($totalAccounts ?? 0), 'icon' => 'at', 'tone' => 'rgba(52,199,89,0.16)', 'color' => '#34C759'],
    ];
    $folderLinks = [
        ['key' => 'inbox', 'label' => 'Inbox', 'icon' => 'inbox', 'count' => $mailboxCounts['inbox'] ?? 0],
        ['key' => 'sent', 'label' => 'Terkirim', 'icon' => 'paper-plane', 'count' => $mailboxCounts['sent'] ?? 0],
        ['key' => 'starred', 'label' => 'Berbintang', 'icon' => 'star', 'count' => $mailboxCounts['starred'] ?? 0],
        ['key' => 'trash', 'label' => 'Trash', 'icon' => 'trash', 'count' => $mailboxCounts['trash'] ?? 0],
    ];
    $statusLinks = [
        ['key' => null, 'label' => 'Semua', 'count' => $statusCounts['all'] ?? 0],
        ['key' => '0', 'label' => 'Belum dibaca', 'count' => $statusCounts['unread'] ?? 0],
        ['key' => '1', 'label' => 'Sudah dibaca', 'count' => $statusCounts['read'] ?? 0],
    ];
    $selectedHtmlDocument = null;

    if ($selectedEmail?->body_html) {
        $selectedHtmlDocument = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<base target="_blank">'
            . '<style>'
            . 'html,body{margin:0;padding:0;background:#eef2f7;color:#111827;}'
            . 'body{font:14px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;overflow-wrap:anywhere;padding:18px;}'
            . '@media (min-width: 768px){body{padding:24px;}}'
            . '*{box-sizing:border-box;}'
            . '.email-frame-inner{max-width:100%;margin:0 auto;background:#ffffff;border-radius:18px;box-shadow:0 12px 30px rgba(15,23,42,0.08);padding:18px;overflow:visible;}'
            . '@media (min-width: 768px){.email-frame-inner{padding:24px;}}'
            . 'img{max-width:100%;height:auto;}'
            . 'table{max-width:100% !important;}'
            . 'body table{width:auto;}'
            . 'a{color:#0a66c2;}'
            . 'pre{white-space:pre-wrap;word-break:break-word;}'
            . '</style></head><body><div class="email-frame-inner">'
            . $selectedEmail->clean_body_html
            . '</div></body></html>';
    }
@endphp

<div class="space-y-4" data-mailbox-root data-active-inbox-folder="{{ $activeFolder }}" data-inbox-close-url="{{ route('admin.email-management.index', $closeDetailQuery) }}" data-mailbox-current-url="{{ request()->fullUrl() }}">
    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(255,59,48,0.12); border: 1px solid rgba(255,59,48,0.3); color: rgba(255,59,48,1);">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    @if(!$selectedEmail)
        <section class="card-elevated rounded-apple-xl p-4 space-y-4 mailbox-shell">
            <div class="mailbox-topbar">
                <div class="min-w-0">
                    <p class="mailbox-kicker">Mailbox Workspace</p>
                    <div class="mailbox-title-row">
                        <h2 class="mailbox-title">Kotak surat operasional tim</h2>
                        <span class="mailbox-folder-badge">
                            <i class="fas fa-folder-open"></i>{{ ucfirst($activeFolder) }}
                        </span>
                    </div>
                    <div class="mailbox-inline-metrics">
                        <span><i class="fas fa-layer-group"></i>{{ number_format($statusCounts['all'] ?? 0) }} email aktif</span>
                        <span><i class="fas fa-circle"></i>{{ number_format($statusCounts['unread'] ?? 0) }} perlu dibaca</span>
                        <span><i class="fas fa-at"></i>{{ number_format($totalAccounts ?? 0) }} akun aktif</span>
                    </div>
                </div>
                <div class="mailbox-actions">
                    <button id="deleteSelectedBtn" class="btn-apple-sm px-3 py-2 hidden" style="background: rgba(255,69,58,0.2); color: rgba(255,69,58,1);" onclick="deleteSelected()">
                        <i class="fas fa-trash mr-2"></i>Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                    <button type="button" class="btn-apple-sm px-3 py-2" data-mailbox-refresh>
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <a href="{{ route('admin.inbox.compose') }}" class="btn-apple-primary-sm px-3 py-2">
                        <i class="fas fa-plus mr-2"></i>Compose
                    </a>
                </div>
            </div>

            <div class="mailbox-summary-grid">
                @foreach($mailboxSummaryCards as $card)
                    <article class="mailbox-summary-card compact">
                        <div class="mailbox-summary-icon" style="background: {{ $card['tone'] }}; color: {{ $card['color'] }};">
                            <i class="fas fa-{{ $card['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="mailbox-summary-label">{{ $card['label'] }}</p>
                            <p class="mailbox-summary-value">{{ $card['value'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mailbox-toolbar-shell">
                <form method="GET" action="{{ route('admin.email-management.index') }}" class="mailbox-search-row" data-mailbox-async>
                    <input type="hidden" name="tab" value="inbox">
                    <input type="hidden" name="folder" value="{{ $activeFolder }}">
                    @if(request()->filled('is_read'))
                        <input type="hidden" name="is_read" value="{{ request('is_read') }}">
                    @endif
                    <div class="mailbox-search-input-shell">
                        <i class="fas fa-search"></i>
                        <input id="inbox-search" type="text" name="search" placeholder="Cari subjek, isi email, atau pengirim"
                               value="{{ request('tab') === 'inbox' ? request('search') : '' }}">
                    </div>
                    <div class="mailbox-search-actions">
                        <button type="submit" class="btn-apple-primary-sm px-4 py-2">Cari</button>
                        <a href="{{ route('admin.email-management.index', $resetInboxQuery) }}" class="btn-apple-sm px-4 py-2">Reset</a>
                    </div>
                </form>

                <div class="mailbox-filter-stack">
                    <section class="mailbox-filter-section">
                        <div class="mailbox-filter-heading">
                            <p class="mailbox-toolbar-label">Folder</p>
                            <span class="mailbox-filter-note">Navigasi utama mailbox</span>
                        </div>
                        <div class="mailbox-chip-row mailbox-chip-row-single">
                            @foreach($folderLinks as $folderLink)
                                <a href="{{ route('admin.email-management.index', array_merge($baseInboxQuery, ['folder' => $folderLink['key']])) }}"
                                   class="mailbox-chip {{ $activeFolder === $folderLink['key'] ? 'mailbox-chip-active' : '' }}">
                                    <i class="fas fa-{{ $folderLink['icon'] }}"></i>
                                    <span class="mailbox-chip-label">{{ $folderLink['label'] }}</span>
                                    <span class="mailbox-chip-count">{{ number_format($folderLink['count']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <section class="mailbox-filter-section">
                        <div class="mailbox-filter-heading">
                            <p class="mailbox-toolbar-label">Status</p>
                            <span class="mailbox-filter-note">Filter pembacaan cepat</span>
                        </div>
                        <div class="mailbox-chip-row mailbox-chip-row-single mailbox-chip-row-status">
                            @foreach($statusLinks as $statusLink)
                                @php
                                    $statusQuery = array_merge($baseInboxQuery, ['folder' => $activeFolder]);
                                    if ($statusLink['key'] !== null) {
                                        $statusQuery['is_read'] = $statusLink['key'];
                                    }
                                @endphp
                                <a href="{{ route('admin.email-management.index', $statusQuery) }}"
                                   class="mailbox-chip {{ (request('is_read') === $statusLink['key']) || ($statusLink['key'] === null && !request()->filled('is_read')) ? 'mailbox-chip-active' : '' }}">
                                    <span class="mailbox-chip-label">{{ $statusLink['label'] }}</span>
                                    <span class="mailbox-chip-count">{{ number_format($statusLink['count']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="mailbox-selection-row">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="rounded text-apple-blue focus:ring-apple-blue" onchange="toggleSelectAll()">
                        <span class="text-sm text-dark-text-secondary">Pilih semua email di halaman ini</span>
                    </label>
                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">
                        {{ isset($emails) ? number_format($emails->total()) : 0 }} email di {{ ucfirst($activeFolder) }}
                    </p>
                </div>
            </div>
        </section>
    @endif

    <form id="batchDeleteForm" action="{{ route('admin.inbox.batch-delete') }}" method="POST" style="display: none;" data-mailbox-async>
        @csrf
        @method('DELETE')
    </form>

    @if(!$selectedEmail)
        <div class="space-y-2">
            @if(isset($emails) && $emails->count() > 0)
                @foreach($emails as $email)
                    @php
                        $isSentCategory = ($activeFolder === 'sent') || $email->category === 'sent';
                        $senderEmail = $isSentCategory ? $email->to_email : $email->from_email;
                        $senderName = $isSentCategory ? null : trim((string) $email->from_name);

                        if (!$isSentCategory && (!$senderName || strcasecmp($senderName, (string) $senderEmail) === 0 || str_contains($senderName, '@'))) {
                            $senderName = $senderEmail
                                ? (string) Str::of(strstr($senderEmail, '@', true) ?: $senderEmail)->replace(['.', '_', '-'], ' ')->title()
                                : null;
                        }

                        $senderPrimary = $isSentCategory
                            ? ($email->to_email ?: 'Penerima tidak diketahui')
                            : ($senderName ?: ($senderEmail ?: 'Pengirim tidak diketahui'));

                        $senderSecondary = !$isSentCategory && $senderEmail && strcasecmp($senderPrimary, $senderEmail) !== 0
                            ? $senderEmail
                            : null;

                        $senderInitials = collect(preg_split('/\s+/', trim($senderPrimary)) ?: [])
                            ->filter()
                            ->take(2)
                            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                            ->implode('');

                        $senderInitials = $senderInitials !== '' ? $senderInitials : Str::upper(Str::substr($senderPrimary, 0, 2));
                        $previewText = $email->preview ?: 'Tidak ada ringkasan isi email.';
                        $emailDetailUrl = route('admin.email-management.index', array_merge(request()->query(), ['tab' => 'inbox', 'folder' => $activeFolder, 'email' => $email->id]));
                        $accountEmail = $email->emailAccount?->email ?? $email->to_email;
                    @endphp

                    <div class="card-elevated rounded-apple-lg px-3 py-2.5 transition-apple email-management-item {{ $email->is_read ? 'is-read' : 'is-unread' }}" data-email-id="{{ $email->id }}">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 pt-0.5">
                                <input type="checkbox" class="email-checkbox rounded text-apple-blue focus:ring-apple-blue"
                                       value="{{ $email->id }}"
                                       onchange="updateSelectedCount()"
                                       onclick="event.stopPropagation()">
                            </div>

                            <div class="flex-shrink-0 cursor-pointer" data-mailbox-nav="{{ $emailDetailUrl }}">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-semibold text-[11px]"
                                     style="background: linear-gradient(135deg, rgba(10,132,255,0.88), rgba(94,92,230,0.8));">
                                    {{ $senderInitials }}
                                </div>
                            </div>

                            <div class="flex-1 min-w-0 cursor-pointer" data-mailbox-nav="{{ $emailDetailUrl }}">
                                <div class="mailbox-list-row">
                                    <div class="mailbox-sender-column">
                                        <div class="mailbox-sender-line">
                                            <h3 class="mailbox-sender-name {{ !$email->is_read ? 'is-unread' : '' }}">
                                                {{ $senderPrimary }}
                                            </h3>
                                            @if(!$email->is_read)
                                                <span class="mailbox-row-dot"></span>
                                            @endif
                                            @if($email->is_starred)
                                                <i class="fas fa-star text-[11px] flex-shrink-0" style="color: var(--neuro-warning);"></i>
                                            @endif
                                            @if($email->has_attachments)
                                                <i class="fas fa-paperclip text-[11px] flex-shrink-0" style="color: rgba(235,235,245,0.6);"></i>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mailbox-message-column">
                                        <p class="mailbox-row-subject {{ !$email->is_read ? 'is-unread' : '' }}">
                                            {{ $email->subject ?: '(No subject)' }}
                                        </p>
                                        <span class="mailbox-row-divider">-</span>
                                        <p class="mailbox-row-preview">{{ $previewText }}</p>
                                    </div>

                                    <div class="mailbox-meta-column">
                                        <span class="mailbox-row-time">{{ $email->received_at?->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="mailbox-secondary-line">
                                    @if($senderSecondary)
                                        <span class="truncate">{{ $senderSecondary }}</span>
                                    @endif
                                    @if($accountEmail)
                                        <span class="mailbox-inline-tag"><i class="fas fa-at"></i>{{ $accountEmail }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(method_exists($emails, 'hasPages') && $emails->hasPages())
                    <div class="pt-4">
                        {{ $emails->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="email-empty-state">
                    <i class="fas fa-inbox"></i>
                    <p class="text-base font-medium text-white mb-2">Tidak ada email ditemukan</p>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                        Folder ini kosong atau filter pencarian belum menemukan email yang sesuai.
                    </p>
                </div>
            @endif
        </div>
    @else
        <section class="card-elevated rounded-apple-xl overflow-hidden email-detail-panel">
            <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('admin.email-management.index', $closeDetailQuery) }}" class="inline-flex items-center px-3 py-2 rounded-apple bg-white/5 text-white/75 text-xs font-semibold hover:bg-white/10 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke mailbox
                    </a>
                    <div>
                        <p class="text-xs uppercase tracking-[0.28em]" style="color: rgba(235,235,245,0.45);">Mailbox Detail</p>
                        <p class="text-sm font-semibold text-white">Detail email operasional</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="mailbox-inline-tag mailbox-inline-tag-muted"><i class="fas fa-folder-open"></i>{{ ucfirst($selectedEmail->category) }}</span>
                    <span class="mailbox-inline-tag mailbox-inline-tag-muted"><i class="fas fa-clock"></i>{{ $selectedEmail->received_at?->diffForHumans() }}</span>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-white/5 space-y-4">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="space-y-2 min-w-0">
                        <h3 class="text-lg font-semibold text-white break-words">{{ $selectedEmail->subject ?: '(No subject)' }}</h3>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.95);">
                                <i class="fas fa-folder-open mr-1.5"></i>{{ ucfirst($selectedEmail->category) }}
                            </span>
                            @if($selectedEmail->has_attachments)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.7);">
                                    <i class="fas fa-paperclip mr-1.5"></i>{{ count($selectedEmail->attachments ?? []) }} lampiran
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button"
                                onclick="toggleInboxStar({{ $selectedEmail->id }}, this)"
                                data-starred="{{ $selectedEmail->is_starred ? 'true' : 'false' }}"
                                class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-semibold transition-apple {{ $selectedEmail->is_starred ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/40' : 'bg-white/5 text-white/80 border border-white/10' }}">
                            <i class="fas fa-star mr-2 {{ $selectedEmail->is_starred ? 'text-yellow-400' : 'text-white/60' }}" data-star-icon></i>
                            <span data-star-label>{{ $selectedEmail->is_starred ? 'Starred' : 'Star' }}</span>
                        </button>
                        <a href="{{ route('admin.inbox.reply', $selectedEmail->id) }}" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-semibold text-white bg-apple-blue transition-apple">
                            <i class="fas fa-reply mr-2"></i>Balas
                        </a>
                        @if($selectedEmail->category === 'trash')
                            <form action="{{ route('admin.inbox.delete', $selectedEmail->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus email ini secara permanen?');" data-mailbox-async>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-semibold text-red-300 bg-red-500/10 border border-red-400/30 transition-apple">
                                    <i class="fas fa-times-circle mr-2"></i>Hapus permanen
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.inbox.trash', $selectedEmail->id) }}" method="POST" onsubmit="return confirm('Pindahkan email ini ke trash?');" data-mailbox-async>
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-semibold text-red-300 bg-red-500/10 border border-red-400/30 transition-apple">
                                    <i class="fas fa-trash mr-2"></i>Pindahkan ke trash
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 rounded-apple-lg border border-white/8 bg-white/3">
                        <p class="text-[10px] uppercase tracking-[0.24em] text-white/40 mb-2">From</p>
                        <p class="text-sm font-semibold text-white break-words">{{ $selectedEmail->from_name ?: $selectedEmail->from_email }}</p>
                        <p class="text-xs text-white/55 break-all mt-1">{{ $selectedEmail->from_email }}</p>
                    </div>
                    <div class="p-3 rounded-apple-lg border border-white/8 bg-white/3">
                        <p class="text-[10px] uppercase tracking-[0.24em] text-white/40 mb-2">To</p>
                        <p class="text-sm font-semibold text-white break-words">{{ $selectedEmail->to_email }}</p>
                        @if($selectedEmail->emailAccount)
                            <p class="text-xs text-white/55 break-words mt-1">via {{ $selectedEmail->emailAccount->display_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-5 py-5 space-y-4">
                @if($selectedEmail->body_html || $selectedEmail->body_text)
                    <div class="flex gap-2 flex-wrap">
                        @if($selectedEmail->body_html)
                            <button type="button" onclick="showManagementHtmlView()" id="managementBtnHtml" class="px-4 py-2 text-xs font-semibold rounded-apple bg-apple-blue text-white">
                                <i class="fas fa-code mr-2"></i>HTML View
                            </button>
                        @endif
                        @if($selectedEmail->body_text)
                            <button type="button" onclick="showManagementTextView()" id="managementBtnText" class="px-4 py-2 text-xs font-semibold rounded-apple {{ !$selectedEmail->body_html ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70' }}">
                                <i class="fas fa-align-left mr-2"></i>Text View
                            </button>
                        @endif
                        <button type="button" onclick="showManagementRawView()" id="managementBtnRaw" class="px-4 py-2 text-xs font-semibold rounded-apple bg-white/10 text-white/70">
                            <i class="fas fa-file-code mr-2"></i>Raw View
                        </button>
                    </div>
                @endif

                @if($selectedEmail->body_html)
                    <div id="managementHtmlView" class="rounded-apple-xl overflow-hidden email-html-shell">
                        <div class="email-html-meta">
                            <span class="email-html-badge">
                                <i class="fas fa-envelope-open-text mr-2"></i>Rendered HTML Email
                            </span>
                            <span class="text-xs text-slate-500">Preview tetap berada di tab mailbox</span>
                        </div>
                        <iframe
                            id="managementEmailHtmlFrame"
                            class="email-html-frame"
                            title="Email HTML content"
                            sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin"
                            referrerpolicy="no-referrer"
                            loading="lazy"
                            scrolling="no"
                        ></iframe>
                        <script type="application/json" id="managementEmailHtmlPayload">@json($selectedHtmlDocument)</script>
                    </div>
                @endif

                @if($selectedEmail->body_text)
                    <div id="managementTextView" class="rounded-apple-xl p-6" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); {{ $selectedEmail->body_html ? 'display: none;' : '' }}">
                        <div class="text-sm text-white" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.7; white-space: pre-wrap;">{{ $selectedEmail->clean_body_text }}</div>
                    </div>
                @endif

                <div id="managementRawView" class="rounded-apple-xl p-6" style="background: rgba(28,28,30,1); border: 1px solid rgba(255,255,255,0.06); display: none; max-height: 600px; overflow: auto;">
                    <div class="mb-3">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Debug View - Raw MIME Content
                        </span>
                    </div>
                    <pre class="text-xs text-white/70" style="font-family: 'Courier New', monospace; line-height: 1.4; white-space: pre-wrap; word-break: break-all;">{{ $selectedEmail->raw_body }}</pre>
                </div>

                @if($selectedEmail->attachments && count($selectedEmail->attachments) > 0)
                    <div class="space-y-3 pt-1">
                        <h4 class="text-sm font-semibold text-white">
                            <i class="fas fa-paperclip mr-2"></i>Lampiran ({{ count($selectedEmail->attachments) }})
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($selectedEmail->attachments as $attachment)
                                <div class="p-4 rounded-apple border border-white/10 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-white break-words">{{ $attachment['filename'] ?? 'Attachment' }}</p>
                                        <p class="text-xs text-white/50">{{ $attachment['content_type'] ?? 'File' }}</p>
                                    </div>
                                    @if(isset($attachment['download_url']))
                                        <a href="{{ $attachment['download_url'] }}" class="text-xs font-semibold text-apple-blue hover:underline">Unduh</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>

@push('styles')
<style>
.mailbox-shell {
    border: 1px solid rgba(255,255,255,0.05);
}

.mailbox-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.mailbox-kicker {
    margin: 0 0 0.25rem;
    font-size: 0.72rem;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: rgba(235,235,245,0.45);
}

.mailbox-title-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.mailbox-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
}

.mailbox-folder-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.34rem 0.58rem;
    border-radius: 999px;
    background: rgba(10,132,255,0.14);
    color: rgba(10,132,255,0.96);
    font-size: 0.72rem;
    font-weight: 700;
}

.mailbox-inline-metrics {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.5rem;
    color: rgba(235,235,245,0.56);
    font-size: 0.76rem;
}

.mailbox-inline-metrics span {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.mailbox-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mailbox-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.65rem;
}

.mailbox-summary-card {
    display: flex;
    align-items: center;
    gap: 0.72rem;
    padding: 0.78rem 0.85rem;
    border-radius: 1rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.06);
    min-height: 66px;
}

.mailbox-summary-icon {
    width: 2.35rem;
    height: 2.35rem;
    border-radius: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.92rem;
    flex-shrink: 0;
}

.mailbox-summary-label {
    margin: 0;
    font-size: 0.68rem;
    letter-spacing: 0.04em;
    color: rgba(235,235,245,0.58);
}

.mailbox-summary-value {
    margin: 0.16rem 0 0;
    font-size: 0.98rem;
    font-weight: 700;
    color: #fff;
}

.mailbox-toolbar-shell {
    padding: 0.85rem 0.95rem;
    border-radius: 1rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    space-y: 0.75rem;
}

.mailbox-search-row {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.mailbox-search-actions {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.mailbox-search-input-shell {
    min-width: 0;
    flex: 1 1 360px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.78rem 1rem;
    border-radius: 999px;
    background: rgba(15,23,42,0.45);
    border: 1px solid rgba(255,255,255,0.08);
}

.mailbox-search-input-shell i {
    color: rgba(235,235,245,0.45);
}

.mailbox-search-input-shell input {
    width: 100%;
    border: 0;
    outline: 0;
    background: transparent;
    color: #fff;
    font-size: 0.92rem;
}

.mailbox-search-input-shell input::placeholder {
    color: rgba(235,235,245,0.35);
}

.mailbox-filter-stack {
    display: grid;
    gap: 0.7rem;
    margin-top: 0.78rem;
}

.mailbox-filter-section {
    display: grid;
    gap: 0.38rem;
}

.mailbox-filter-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.mailbox-toolbar-label {
    margin: 0;
    font-size: 0.68rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(235,235,245,0.45);
}

.mailbox-filter-note {
    color: rgba(235,235,245,0.38);
    font-size: 0.68rem;
    line-height: 1.2;
}

.mailbox-chip-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.mailbox-chip-row-single {
    flex-wrap: nowrap;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 0.15rem;
}

.mailbox-chip-row-single::-webkit-scrollbar {
    display: none;
}

.mailbox-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.42rem;
    padding: 0.48rem 0.72rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    color: rgba(235,235,245,0.72);
    font-size: 0.74rem;
    font-weight: 600;
    transition: all 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.mailbox-chip:hover {
    background: rgba(255,255,255,0.08);
    color: #fff;
}

.mailbox-chip-active {
    background: rgba(10,132,255,0.16);
    border-color: rgba(10,132,255,0.32);
    color: #fff;
}

.mailbox-chip-label {
    line-height: 1;
}

.mailbox-chip-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.18rem;
    height: 1.18rem;
    padding: 0 0.3rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    font-size: 0.63rem;
    color: rgba(255,255,245,0.86);
}

.mailbox-chip-row-status .mailbox-chip {
    min-width: 0;
}

.mailbox-selection-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 0.72rem;
    padding-top: 0.72rem;
    border-top: 1px solid rgba(255,255,255,0.06);
}

.mailbox-selection-row label span {
    font-size: 0.79rem;
}

.email-management-item {
    border: 1px solid rgba(255,255,255,0.05);
    position: relative;
    transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.email-management-item.is-unread {
    background: linear-gradient(180deg, rgba(10,132,255,0.075), rgba(255,255,255,0.03));
    border-color: rgba(10,132,255,0.18);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
}

.email-management-item.is-read {
    background: rgba(255,255,255,0.018);
    border-color: rgba(255,255,255,0.04);
}

.email-management-item:hover {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
    transform: translateY(-1px);
}

.email-management-item.is-unread .mailbox-row-preview {
    color: rgba(235,235,245,0.66);
}

.email-management-item.is-unread .mailbox-row-time {
    color: rgba(10,132,255,0.92);
    font-weight: 700;
}

.email-management-item.is-read .mailbox-row-time {
    color: rgba(235,235,245,0.38);
}

.mailbox-list-row {
    display: grid;
    grid-template-columns: minmax(152px, 0.34fr) minmax(0, 1fr) auto;
    align-items: center;
    gap: 0.8rem;
    min-width: 0;
}

.mailbox-sender-column,
.mailbox-message-column {
    min-width: 0;
}

.mailbox-sender-line {
    display: flex;
    align-items: center;
    gap: 0.36rem;
    min-width: 0;
}

.mailbox-sender-name {
    margin: 0;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.79rem;
    line-height: 1.2;
    font-weight: 500;
    color: rgba(235,235,245,0.84);
}

.mailbox-sender-name.is-unread {
    font-weight: 650;
    color: #fff;
}

.mailbox-message-column {
    display: flex;
    align-items: baseline;
    gap: 0.42rem;
    min-width: 0;
}

.mailbox-row-subject,
.mailbox-row-preview {
    margin: 0;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.78rem;
    line-height: 1.25;
}

.mailbox-row-subject {
    flex: 0 1 auto;
    max-width: 36%;
    font-weight: 500;
    color: rgba(235,235,245,0.78);
}

.mailbox-row-subject.is-unread {
    font-weight: 620;
    color: rgba(255,255,255,0.94);
}

.mailbox-row-divider {
    flex-shrink: 0;
    color: rgba(235,235,245,0.24);
    font-size: 0.75rem;
}

.mailbox-row-preview {
    flex: 1 1 auto;
    color: rgba(235,235,245,0.5);
}

.mailbox-meta-column {
    flex-shrink: 0;
    text-align: right;
}

.mailbox-row-dot {
    width: 0.45rem;
    height: 0.45rem;
    border-radius: 999px;
    flex-shrink: 0;
    background: var(--neuro-primary);
}

.mailbox-row-meta {
    color: rgba(235,235,245,0.48);
    font-size: 0.72rem;
}

.mailbox-row-time {
    display: inline-flex;
    align-items: center;
    color: rgba(235,235,245,0.46);
    font-size: 0.68rem;
    line-height: 1;
}

.mailbox-secondary-line {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    min-width: 0;
    margin-top: 0.22rem;
    padding-left: calc(152px + 0.8rem);
    color: rgba(235,235,245,0.42);
    font-size: 0.7rem;
}

.mailbox-secondary-line > span:first-child {
    min-width: 0;
}

.mailbox-inline-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.32rem;
    padding: 0.16rem 0.42rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    color: rgba(235,235,245,0.68);
    line-height: 1;
}

.mailbox-inline-tag-muted {
    background: rgba(255,255,255,0.05);
    color: rgba(235,235,245,0.58);
}

#content-inbox {
    position: relative;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

#content-inbox.mailbox-is-loading {
    opacity: 0.58;
    transform: translateY(2px);
    pointer-events: none;
}

.email-detail-panel {
    border: 1px solid rgba(255,255,255,0.06);
}

.email-html-shell {
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
}

.email-html-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.email-html-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #0f172a;
    background: rgba(10, 132, 255, 0.12);
}

.email-html-frame {
    display: block;
    width: 100%;
    min-height: 220px;
    border: 0;
    background: #ffffff;
    overflow: hidden;
}

@media (max-width: 900px) {
    .mailbox-summary-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .mailbox-search-row {
        align-items: stretch;
    }

    .mailbox-search-row > * {
        width: 100%;
    }

    .mailbox-search-actions {
        width: 100%;
    }

    .mailbox-search-actions > * {
        flex: 1 1 0;
        text-align: center;
        justify-content: center;
    }

    .mailbox-filter-heading {
        align-items: flex-start;
    }

    .mailbox-list-row {
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 0.55rem;
    }

    .mailbox-sender-column {
        grid-column: 1 / 2;
    }

    .mailbox-message-column {
        grid-column: 1 / 2;
        display: block;
        margin-top: 0.1rem;
    }

    .mailbox-meta-column {
        grid-column: 2 / 3;
        grid-row: 1 / span 2;
        align-self: start;
    }

    .mailbox-row-subject {
        max-width: 100%;
        display: inline;
        margin-right: 0.3rem;
    }

    .mailbox-row-divider {
        display: none;
    }

    .mailbox-row-preview {
        display: inline;
    }

    .mailbox-secondary-line {
        padding-left: 0;
        margin-top: 0.25rem;
        flex-wrap: wrap;
    }
}
</style>
@endpush

@push('scripts')
<script>
let mailboxNavigationController = null;

function getMailboxContentRoot() {
    return document.getElementById('content-inbox');
}

function getMailboxStateRoot() {
    return document.querySelector('#content-inbox [data-mailbox-root]');
}

function getActiveInboxFolder() {
    return getMailboxStateRoot()?.dataset.activeInboxFolder || 'inbox';
}

function getInboxCloseSelectionUrl() {
    return getMailboxStateRoot()?.dataset.inboxCloseUrl || window.location.href;
}

function getCurrentMailboxUrl() {
    return getMailboxStateRoot()?.dataset.mailboxCurrentUrl || window.location.href;
}

function setMailboxLoadingState(isLoading) {
    const root = getMailboxContentRoot();
    if (!root) {
        return;
    }

    root.classList.toggle('mailbox-is-loading', isLoading);
}

function syncEmailManagementSummary(documentNode) {
    const nextSummary = documentNode.querySelector('#email-management-summary-stats');
    const currentSummary = document.querySelector('#email-management-summary-stats');

    if (nextSummary && currentSummary) {
        currentSummary.innerHTML = nextSummary.innerHTML;
    }

    const nextInboxTab = documentNode.querySelector('#tab-inbox');
    const currentInboxTab = document.querySelector('#tab-inbox');

    if (nextInboxTab && currentInboxTab) {
        currentInboxTab.innerHTML = nextInboxTab.innerHTML;
        currentInboxTab.className = nextInboxTab.className;
        if (!currentInboxTab.classList.contains('active')) {
            currentInboxTab.classList.add('active');
        }
    }
}

function initializeManagementEmailFrame() {
    const managementEmailFrame = document.getElementById('managementEmailHtmlFrame');
    const managementEmailPayloadNode = document.getElementById('managementEmailHtmlPayload');

    if (!managementEmailFrame || !managementEmailPayloadNode) {
        return;
    }

    function applyManagementEmailFrameHeight(nextHeight) {
        if (!Number.isFinite(nextHeight)) {
            return;
        }

        managementEmailFrame.style.height = `${Math.max(220, Math.ceil(nextHeight) + 2)}px`;
    }

    function measureManagementEmailFrameHeight() {
        if (!managementEmailFrame.contentDocument) {
            return null;
        }

        const doc = managementEmailFrame.contentDocument;
        const body = doc.body;
        const html = doc.documentElement;

        if (!body || !html) {
            return null;
        }

        const lastElement = body.lastElementChild;
        const lastElementBottom = lastElement
            ? lastElement.getBoundingClientRect().bottom + body.getBoundingClientRect().top
            : 0;

        return Math.max(
            body.scrollHeight,
            body.offsetHeight,
            body.clientHeight,
            html.scrollHeight,
            html.offsetHeight,
            html.clientHeight,
            lastElementBottom
        );
    }

    function syncManagementEmailFrameHeight() {
        const measuredHeight = measureManagementEmailFrameHeight();
        if (measuredHeight) {
            applyManagementEmailFrameHeight(measuredHeight);
        }
    }

    function scheduleManagementEmailFrameResizes() {
        [0, 60, 180, 360, 700, 1200, 2200, 4000].forEach((delay) => {
            window.setTimeout(syncManagementEmailFrameHeight, delay);
        });
    }

    function bindManagementEmailFrameObservers() {
        if (!managementEmailFrame.contentDocument) {
            return;
        }

        const doc = managementEmailFrame.contentDocument;

        doc.querySelectorAll('img').forEach((img) => {
            if (!img.complete) {
                img.addEventListener('load', syncManagementEmailFrameHeight);
                img.addEventListener('error', syncManagementEmailFrameHeight);
            }
        });

        if (doc.fonts?.ready) {
            doc.fonts.ready.then(syncManagementEmailFrameHeight).catch(() => {});
        }

        if (window.ResizeObserver && doc.body) {
            const resizeObserver = new ResizeObserver(syncManagementEmailFrameHeight);
            resizeObserver.observe(doc.body);
            if (doc.documentElement) {
                resizeObserver.observe(doc.documentElement);
            }
        }
    }

    const payload = JSON.parse(managementEmailPayloadNode.textContent || 'null');
    if (!payload) {
        return;
    }

    applyManagementEmailFrameHeight(220);
    managementEmailFrame.addEventListener('load', () => {
        bindManagementEmailFrameObservers();
        syncManagementEmailFrameHeight();
        scheduleManagementEmailFrameResizes();
    }, { once: true });
    managementEmailFrame.srcdoc = payload;
}

function initializeMailboxContent() {
    updateSelectedCount();
    initializeManagementEmailFrame();
}

async function loadMailboxContent(url, options = {}) {
    const {
        method = 'GET',
        body = null,
        pushHistory = true,
        preserveScroll = false,
        headers = {},
    } = options;

    const requestUrl = new URL(url, window.location.origin);
    if (!requestUrl.searchParams.has('tab')) {
        requestUrl.searchParams.set('tab', 'inbox');
    }

    if (mailboxNavigationController) {
        mailboxNavigationController.abort();
    }

    mailboxNavigationController = new AbortController();
    setMailboxLoadingState(true);

    try {
        const response = await fetch(requestUrl.toString(), {
            method,
            body,
            signal: mailboxNavigationController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Mailbox-Partial': 'inbox',
                ...headers,
            },
        });

        if (!response.ok) {
            throw new Error(`Mailbox request failed with status ${response.status}`);
        }

        const html = await response.text();
        const nextDocument = new DOMParser().parseFromString(html, 'text/html');
        const nextInboxContent = nextDocument.querySelector('#content-inbox');
        const currentInboxContent = getMailboxContentRoot();

        if (!nextInboxContent || !currentInboxContent) {
            window.location.assign(requestUrl.toString());
            return;
        }

        currentInboxContent.innerHTML = nextInboxContent.innerHTML;
        syncEmailManagementSummary(nextDocument);
        initializeMailboxContent();

        if (pushHistory) {
            window.history.pushState({ tab: 'inbox' }, '', response.url || requestUrl.toString());
        }

        if (!preserveScroll) {
            currentInboxContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    } catch (error) {
        if (error.name !== 'AbortError') {
            console.error(error);
            window.location.assign(requestUrl.toString());
        }
    } finally {
        setMailboxLoadingState(false);
    }
}

function shouldHandleMailboxLink(anchor) {
    if (!anchor || anchor.target || anchor.hasAttribute('download')) {
        return false;
    }

    const nextUrl = new URL(anchor.href, window.location.origin);
    return nextUrl.pathname === window.location.pathname && nextUrl.searchParams.get('tab') === 'inbox';
}

function submitMailboxForm(form) {
    const formMethod = (form.querySelector('input[name="_method"]')?.value || form.getAttribute('method') || 'GET').toUpperCase();
    const formData = new FormData(form);

    if (formMethod === 'GET') {
        const requestUrl = new URL(form.action, window.location.origin);
        const searchParams = new URLSearchParams(formData);
        requestUrl.search = searchParams.toString();
        return loadMailboxContent(requestUrl.toString());
    }

    return loadMailboxContent(form.action, {
        method: 'POST',
        body: formData,
        preserveScroll: true,
    });
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.email-checkbox');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.email-checkbox:checked');
    const allCheckboxes = document.querySelectorAll('.email-checkbox');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const countSpan = document.getElementById('selectedCount');
    const selectAll = document.getElementById('selectAll');

    if (countSpan) {
        countSpan.textContent = checked.length;
    }

    if (deleteBtn) {
        deleteBtn.classList.toggle('hidden', checked.length === 0);
    }

    if (selectAll) {
        selectAll.checked = allCheckboxes.length > 0 && checked.length === allCheckboxes.length;
    }
}

function deleteSelected() {
    const checked = document.querySelectorAll('.email-checkbox:checked');
    if (checked.length === 0) {
        return;
    }

    if (!confirm(`Yakin ingin memproses ${checked.length} email yang dipilih?`)) {
        return;
    }

    const form = document.getElementById('batchDeleteForm');
    form.querySelectorAll('input[name="email_ids[]"]').forEach((node) => node.remove());

    checked.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'email_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    submitMailboxForm(form);
}

function toggleInboxStar(emailId, button) {
    fetch(`/admin/inbox/${emailId}/star`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then((response) => response.json())
    .then((data) => {
        if (!data.success) {
            return;
        }

        const nextState = !!data.starred;
        button.dataset.starred = nextState ? 'true' : 'false';
        button.classList.toggle('bg-yellow-500/20', nextState);
        button.classList.toggle('text-yellow-400', nextState);
        button.classList.toggle('border-yellow-500/40', nextState);
        button.classList.toggle('border-white/10', !nextState);
        button.classList.toggle('bg-white/5', !nextState);
        button.classList.toggle('text-white/80', !nextState);

        const icon = button.querySelector('[data-star-icon]');
        const label = button.querySelector('[data-star-label]');

        if (icon) {
            icon.classList.toggle('text-yellow-400', nextState);
            icon.classList.toggle('text-white/60', !nextState);
        }

        if (label) {
            label.textContent = nextState ? 'Starred' : 'Star';
        }

        if (getActiveInboxFolder() === 'starred' && !nextState) {
            loadMailboxContent(getInboxCloseSelectionUrl(), { preserveScroll: true });
            return;
        }

        loadMailboxContent(getCurrentMailboxUrl(), { preserveScroll: true, pushHistory: false });
    })
    .catch(console.error);
}

function setManagementView(activeView) {
    const views = {
        html: document.getElementById('managementHtmlView'),
        text: document.getElementById('managementTextView'),
        raw: document.getElementById('managementRawView'),
    };

    const buttons = {
        html: document.getElementById('managementBtnHtml'),
        text: document.getElementById('managementBtnText'),
        raw: document.getElementById('managementBtnRaw'),
    };

    Object.entries(views).forEach(([key, node]) => {
        if (!node) {
            return;
        }

        node.style.display = key === activeView ? 'block' : 'none';
    });

    Object.entries(buttons).forEach(([key, node]) => {
        if (!node) {
            return;
        }

        node.className = key === activeView
            ? 'px-4 py-2 text-xs font-semibold rounded-apple bg-apple-blue text-white'
            : 'px-4 py-2 text-xs font-semibold rounded-apple bg-white/10 text-white/70';
    });
}

function showManagementHtmlView() {
    setManagementView('html');
}

function showManagementTextView() {
    setManagementView('text');
}

function showManagementRawView() {
    setManagementView('raw');
}

if (!window.__mailboxAsyncBindingsAttached) {
    window.__mailboxAsyncBindingsAttached = true;

    document.addEventListener('click', (event) => {
        const refreshButton = event.target.closest('[data-mailbox-refresh]');
        if (refreshButton && refreshButton.closest('#content-inbox')) {
            event.preventDefault();
            loadMailboxContent(getCurrentMailboxUrl(), { pushHistory: false, preserveScroll: true });
            return;
        }

        const rowTarget = event.target.closest('[data-mailbox-nav]');
        if (rowTarget && rowTarget.closest('#content-inbox')) {
            if (event.target.closest('input, button, a, form, label')) {
                return;
            }

            event.preventDefault();
            loadMailboxContent(rowTarget.dataset.mailboxNav);
            return;
        }

        const anchor = event.target.closest('#content-inbox a[href]');
        if (anchor && shouldHandleMailboxLink(anchor)) {
            event.preventDefault();
            loadMailboxContent(anchor.href);
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-mailbox-async]');
        if (!form) {
            return;
        }

        if (!form.closest('#content-inbox') && form.id !== 'batchDeleteForm') {
            return;
        }

        event.preventDefault();
        submitMailboxForm(form);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        if (url.searchParams.get('tab') === 'inbox') {
            loadMailboxContent(url.toString(), { pushHistory: false, preserveScroll: true });
        }
    });
}

initializeMailboxContent();
</script>
@endpush
