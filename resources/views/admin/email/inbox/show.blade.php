@extends('layouts.app')

@section('title', 'Detail Email')
@section('page-title', 'Detail Email')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.inbox.index', ['category' => request('category', 'inbox')]) }}"
           class="inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Inbox
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                    onclick="toggleStar({{ $email->id }}, this)"
                    data-star-button="{{ $email->is_starred ? 'true' : 'false' }}"
                    class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold transition-apple {{ $email->is_starred ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/40' : 'bg-white/5 text-white/80 border border-white/10' }}">
                <i class="fas fa-star mr-2 {{ $email->is_starred ? 'text-yellow-400' : 'text-white/60' }}" data-star-icon></i>
                <span data-star-label>{{ $email->is_starred ? 'Starred' : 'Star' }}</span>
            </button>
            <a href="{{ route('admin.inbox.reply', $email->id) }}"
               class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold text-white bg-apple-blue transition-apple">
                <i class="fas fa-reply mr-2"></i>Balas
            </a>
            <div class="relative" data-dropdown>
                <button type="button"
                        class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold text-white/80 bg-white/10 border border-white/15 hover:bg-white/15 transition-apple"
                        data-dropdown-trigger>
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="hidden absolute right-0 mt-2 w-48 card-elevated rounded-apple-lg py-2 z-20" data-dropdown-menu>
                    <button type="button" onclick="window.print()"
                            class="w-full px-4 py-2 text-left text-xs font-semibold text-white/70 hover:bg-white/5 transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak
                    </button>
                    <form action="{{ route('admin.inbox.mark-unread', $email->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 text-left text-xs font-semibold text-white/70 hover:bg-white/5 transition-colors">
                            <i class="fas fa-envelope mr-2"></i> Tandai belum dibaca
                        </button>
                    </form>
                    <hr class="border-white/10 my-1">
                    <form action="{{ route('admin.inbox.trash', $email->id) }}" method="POST" onsubmit="return confirm('Pindahkan email ini ke trash?');">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 text-left text-xs font-semibold text-red-400 hover:bg-white/5 transition-colors">
                            <i class="fas fa-trash mr-2"></i> Trash
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Email card --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-6 border-b border-white/5 space-y-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">{{ $email->subject }}</h1>
                @if($email->labels && count($email->labels) > 0)
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($email->labels as $label)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($email->from_name ?? $email->from_email, 0, 2)) }}
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest text-white/40">From</p>
                        <p class="text-sm font-semibold text-white">{{ $email->from_name ?? $email->from_email }}</p>
                        <p class="text-xs text-white/60">{{ $email->from_email }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($email->to_email, 0, 2)) }}
                    </div>
                    <div class="space-y-1 min-w-0">
                        <p class="text-xs uppercase tracking-widest text-white/40">To</p>
                        <p class="text-sm font-semibold text-white truncate">{{ $email->to_email }}</p>
                        @if($email->emailAccount)
                            <p class="text-xs text-white/60">via {{ $email->emailAccount->display_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/5 flex flex-wrap gap-4 text-sm text-white/70">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-white/40"></i>
                    <span>{{ $email->received_at->format('d M Y, H:i') }}</span>
                    <span class="text-xs text-white/40">({{ $email->received_at->diffForHumans() }})</span>
                </div>
                @if($email->has_attachments)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-paperclip text-white/40"></i>
                        <span>{{ count($email->attachments ?? []) }} lampiran</span>
                    </div>
                @endif
                @if(!$email->is_read)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                        Unread
                    </span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-6">
            {{-- Toggle view buttons --}}
            @if($email->body_html && $email->body_text)
                <div class="flex gap-2 mb-4">
                    <button type="button" onclick="showHtmlView()" id="btnHtml" class="px-4 py-2 text-xs font-semibold rounded-apple bg-apple-blue text-white">
                        <i class="fas fa-code mr-2"></i>HTML View
                    </button>
                    <button type="button" onclick="showTextView()" id="btnText" class="px-4 py-2 text-xs font-semibold rounded-apple bg-white/10 text-white/70">
                        <i class="fas fa-align-left mr-2"></i>Text View
                    </button>
                </div>
            @endif

            {{-- HTML View --}}
            @if($email->body_html)
                <div id="htmlView" class="rounded-apple-xl p-6 overflow-auto" style="background: white; border: 1px solid rgba(255,255,255,0.06); max-height: 600px;">
                    <div style="color: #000; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6;">
                        {!! $email->clean_body_html !!}
                    </div>
                </div>
            @endif

            {{-- Text View --}}
            @if($email->body_text)
                <div id="textView" class="rounded-apple-xl p-6" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); {{ $email->body_html ? 'display: none;' : '' }}">
                    <div class="text-sm text-white" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; white-space: pre-wrap;">{{ $email->clean_body_text }}</div>
                </div>
            @endif
        </div>

        <script>
        function showHtmlView() {
            document.getElementById('htmlView').style.display = 'block';
            document.getElementById('textView').style.display = 'none';
            document.getElementById('btnHtml').className = 'px-4 py-2 text-xs font-semibold rounded-apple bg-apple-blue text-white';
            document.getElementById('btnText').className = 'px-4 py-2 text-xs font-semibold rounded-apple bg-white/10 text-white/70';
        }
        function showTextView() {
            document.getElementById('htmlView').style.display = 'none';
            document.getElementById('textView').style.display = 'block';
            document.getElementById('btnHtml').className = 'px-4 py-2 text-xs font-semibold rounded-apple bg-white/10 text-white/70';
            document.getElementById('btnText').className = 'px-4 py-2 text-xs font-semibold rounded-apple bg-apple-blue text-white';
        }
        </script>

        {{-- Attachments --}}
        @if($email->attachments && count($email->attachments) > 0)
            <div class="px-6 py-5 border-t border-white/5 space-y-4">
                <h3 class="text-sm font-semibold text-white">
                    <i class="fas fa-paperclip mr-2"></i>Lampiran ({{ count($email->attachments) }})
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($email->attachments as $attachment)
                        <div class="p-4 rounded-apple border border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $attachment['filename'] ?? 'Attachment' }}</p>
                                <p class="text-xs text-white/50">{{ $attachment['content_type'] ?? 'File' }}</p>
                            </div>
                            @if(isset($attachment['download_url']))
                                <a href="{{ $attachment['download_url'] }}" class="text-xs font-semibold text-apple-blue hover:underline">
                                    Unduh
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>

<script>
function toggleStar(emailId, button) {
    fetch(`/admin/inbox/${emailId}/star`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const isStarred = button.getAttribute('data-star-button') === 'true';
            const nextState = !isStarred;
            button.setAttribute('data-star-button', nextState ? 'true' : 'false');

            button.classList.add('border');
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
        }
    })
    .catch(console.error);
}

document.querySelectorAll('[data-dropdown]').forEach(wrapper => {
    const trigger = wrapper.querySelector('[data-dropdown-trigger]');
    const menu = wrapper.querySelector('[data-dropdown-menu]');
    trigger.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });
    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
});
</script>
@endsection
