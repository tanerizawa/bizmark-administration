@extends('mobile.layouts.app')

@section('title', 'Detail Approval')

@section('content')
<div class="pb-20" x-data="approvalDetail()">

    {{-- Item Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="p-4">
            {{-- Type Badge --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($type === 'documents') bg-purple-100 text-purple-700
                    @elseif($type === 'invoices') bg-green-100 text-green-700
                    @else bg-blue-100 text-blue-700
                    @endif">
                    @if($type === 'documents') <i class="fas fa-file-alt mr-1"></i>Dokumen
                    @elseif($type === 'invoices') <i class="fas fa-file-invoice mr-1"></i>Invoice
                    @else <i class="fas fa-receipt mr-1"></i>Expense
                    @endif
                </span>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-amber-100 text-amber-700">
                    <i class="fas fa-clock mr-1"></i>Pending
                </span>
            </div>

            {{-- Title --}}
            <h2 class="text-lg font-bold text-gray-900 mb-2">
                @if($type === 'documents')
                    {{ $item->title ?? 'Dokumen' }}
                @elseif($type === 'invoices')
                    Invoice #{{ $item->invoice_number ?? '-' }}
                @else
                    {{ $item->description ?? 'Expense' }}
                @endif
            </h2>

            {{-- Meta Info --}}
            <div class="space-y-2 text-sm">
                @if($item->project)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-folder text-gray-400 w-5 text-center"></i>
                    <span>{{ $item->project->name }}</span>
                </div>
                @endif

                @if($type === 'invoices' && $item->client)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-building text-gray-400 w-5 text-center"></i>
                    <span>{{ $item->client->name }}</span>
                </div>
                @endif

                @if($type === 'invoices' || $type === 'expenses')
                <div class="flex items-center gap-2 text-gray-900">
                    <i class="fas fa-money-bill text-gray-400 w-5 text-center"></i>
                    <span class="font-bold text-lg">Rp {{ number_format($item->total_amount ?? $item->amount ?? 0, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($item->created_at)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-calendar text-gray-400 w-5 text-center"></i>
                    <span>{{ $item->created_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>

            {{-- Description --}}
            @if($item->description)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $item->description }}</p>
            </div>
            @endif
        </div>

        {{-- Approval Actions --}}
        <div class="border-t border-gray-100 p-3 space-y-2">
            {{-- Note input --}}
            <textarea x-model="note" placeholder="Catatan (opsional)..." rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#0A66C2] focus:border-transparent resize-none"></textarea>

            <div class="flex gap-2">
                <button @click="approve()"
                        :disabled="submitting"
                        class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium active:scale-95 transition-all disabled:opacity-50">
                    <i class="fas fa-check mr-1"></i> Setujui
                </button>
                <button @click="showReject = true"
                        :disabled="submitting"
                        class="flex-1 py-2.5 bg-red-500 text-white rounded-lg text-sm font-medium active:scale-95 transition-all disabled:opacity-50">
                    <i class="fas fa-times mr-1"></i> Tolak
                </button>
            </div>
        </div>
    </div>

    {{-- Rejection Modal --}}
    <div x-show="showReject" x-transition class="fixed inset-0 bg-black/50 z-50 flex items-end">
        <div class="bg-white w-full rounded-t-2xl p-6 safe-bottom" @click.away="showReject = false">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Alasan Penolakan</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Alasan</label>
                    <select x-model="rejectReason" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih alasan...</option>
                        <option value="incomplete">Data tidak lengkap</option>
                        <option value="incorrect">Data tidak sesuai</option>
                        <option value="over_budget">Melebihi anggaran</option>
                        <option value="duplicate">Duplikat</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">Catatan</label>
                    <textarea x-model="rejectNote" rows="3" placeholder="Jelaskan alasan penolakan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none"></textarea>
                </div>
                <div class="flex gap-2">
                    <button @click="reject()" :disabled="!rejectReason || submitting"
                            class="flex-1 py-2.5 bg-red-500 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                        <i class="fas fa-times mr-1"></i> Konfirmasi Tolak
                    </button>
                    <button @click="showReject = false" class="flex-1 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-transition
         class="fixed bottom-20 left-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white"
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
        <div class="flex items-center gap-2">
            <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approvalDetail() {
    return {
        note: '',
        rejectReason: '',
        rejectNote: '',
        showReject: false,
        submitting: false,
        toast: { show: false, message: '', type: 'success' },

        async approve() {
            this.submitting = true;
            try {
                const res = await fetch(`{{ url('m/approvals') }}/{{ $type }}/{{ $item->id }}/approve`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ note: this.note })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.href = '{{ route("mobile.approvals.index") }}', 1000);
                } else {
                    this.showToast(data.message || 'Gagal menyetujui', 'error');
                }
            } catch (e) {
                this.showToast('Gagal menyetujui', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async reject() {
            this.submitting = true;
            try {
                const res = await fetch(`{{ url('m/approvals') }}/{{ $type }}/{{ $item->id }}/reject`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        note: this.rejectNote,
                        reason: this.rejectReason
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.showReject = false;
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.href = '{{ route("mobile.approvals.index") }}', 1000);
                } else {
                    this.showToast(data.message || 'Gagal menolak', 'error');
                }
            } catch (e) {
                this.showToast('Gagal menolak', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 3000);
        }
    }
}
</script>
@endpush
