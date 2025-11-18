@extends('mobile.layouts.app')

@section('title', 'Approvals')

@section('content')
<div class="mobile-page pb-20" x-data="approvalsPage()">
    <!-- Stats Header -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 safe-top">
        <h1 class="text-2xl font-bold mb-4">Pending Approvals</h1>
        
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['expenses'] }}</div>
                <div class="text-xs text-purple-100 mt-0.5">Expenses</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['documents'] }}</div>
                <div class="text-xs text-purple-100 mt-0.5">Dokumen</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['invoices'] }}</div>
                <div class="text-xs text-purple-100 mt-0.5">Invoice</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="sticky top-14 z-10 bg-white border-b border-gray-200">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button @click="filterType('all')" 
                    :class="currentType === 'all' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Semua ({{ $stats['total'] }})
            </button>
            <button @click="filterType('expenses')" 
                    :class="currentType === 'expenses' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Expenses ({{ $stats['expenses'] }})
            </button>
            <button @click="filterType('documents')" 
                    :class="currentType === 'documents' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Dokumen ({{ $stats['documents'] }})
            </button>
            <button @click="filterType('invoices')" 
                    :class="currentType === 'invoices' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Invoice ({{ $stats['invoices'] }})
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar (when items selected) -->
    <div x-show="selectedItems.length > 0" 
         x-transition
         class="sticky top-[106px] z-10 bg-[#f0f7fa] border-b border-[#caccce] p-3">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-[#000000]">
                <span x-text="selectedItems.length"></span> item dipilih
            </span>
            <div class="flex gap-2">
                <button @click="bulkApprove()" 
                        class="px-4 py-1.5 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 active:bg-green-700">
                    <i class="fas fa-check mr-1"></i>Approve All
                </button>
                <button @click="showBulkRejectModal = true" 
                        class="px-4 py-1.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 active:bg-red-700">
                    <i class="fas fa-times mr-1"></i>Reject All
                </button>
                <button @click="clearSelection()" 
                        class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Approvals List -->
    <div class="p-4">
        <template x-if="loading">
            <div class="py-12 text-center">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="mt-3 text-gray-500">Loading...</p>
            </div>
        </template>

        <template x-if="!loading && approvals.length === 0">
            <div class="py-12 text-center">
                <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Semua Sudah Disetujui!</h3>
                <p class="text-gray-500">Tidak ada approval yang pending</p>
            </div>
        </template>

        <!-- Swipeable Approval Cards -->
        <div class="space-y-3">
            <template x-for="approval in approvals" :key="approval.id + approval.type">
                <div class="approval-card" 
                     x-data="swipeableCard(approval)" 
                     @touchstart="touchStart($event)"
                     @touchmove="touchMove($event)"
                     @touchend="touchEnd($event)">
                    
                    <!-- Swipe Actions Background -->
                    <div class="absolute inset-0 flex">
                        <!-- Left: Approve -->
                        <div class="w-1/2 bg-green-500 flex items-center justify-start pl-6">
                            <i class="fas fa-check text-white text-2xl"></i>
                        </div>
                        <!-- Right: Reject -->
                        <div class="w-1/2 bg-red-500 flex items-center justify-end pr-6">
                            <i class="fas fa-times text-white text-2xl"></i>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="relative bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden"
                         :style="`transform: translateX(${swipeX}px); transition: ${dragging ? 'none' : 'transform 0.3s ease-out'}`">
                        
                        <!-- Selection Checkbox -->
                        <div class="absolute top-3 left-3 z-10">
                            <input type="checkbox" 
                                   :checked="isSelected(approval)"
                                   @change="toggleSelection(approval)"
                                   class="w-5 h-5 rounded border-gray-300 text-[#0077b5] focus:ring-[#0077b5]">
                        </div>

                        <a :href="`/m/approvals/${approval.type}/${approval.id}`" 
                           class="block p-4 pl-12">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0 pr-3">
                                    <h3 class="font-semibold text-gray-900 mb-0.5" x-text="approval.title"></h3>
                                    <p class="text-sm text-gray-600 truncate" x-text="approval.subtitle"></p>
                                </div>
                                <span :class="getIconColor(approval.type)" 
                                      class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                                    <i :class="`fas fa-${approval.icon}`"></i>
                                </span>
                            </div>

                            <!-- Amount (if applicable) -->
                            <template x-if="approval.amount">
                                <div class="text-xl font-bold text-gray-900 mb-2" x-text="formatCurrency(approval.amount)"></div>
                            </template>

                            <!-- Footer -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span x-text="formatDate(approval.date)"></span>
                                <span :class="getTypeBadge(approval.type)" class="px-2 py-1 rounded-full font-medium">
                                    <span x-text="approval.type"></span>
                                </span>
                            </div>
                        </a>

                        <!-- Quick Actions -->
                        <div class="border-t border-gray-100 px-4 py-2 flex gap-2">
                            <button @click.prevent="quickApprove(approval)" 
                                    class="flex-1 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 active:bg-green-200">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <button @click.prevent="showRejectModal(approval)" 
                                    class="flex-1 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 active:bg-red-200">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="currentRejectItem !== null" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-4"
         @click.self="currentRejectItem = null">
        
        <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full max-w-md p-6 safe-bottom"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0"
             x-transition:enter-end="translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="translate-y-0 sm:scale-100"
             x-transition:leave-end="translate-y-full sm:scale-95 sm:translate-y-0">
            
            <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Approval</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <select x-model="rejectReason" class="w-full p-2.5 border border-gray-300 rounded-lg">
                    <option value="">Pilih alasan...</option>
                    <option value="incomplete">Informasi Tidak Lengkap</option>
                    <option value="incorrect">Data Tidak Sesuai</option>
                    <option value="budget">Melebihi Budget</option>
                    <option value="policy">Tidak Sesuai Kebijakan</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea x-model="rejectNote" 
                          rows="3" 
                          placeholder="Tambahkan catatan..."
                          class="w-full p-2.5 border border-gray-300 rounded-lg resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button @click="currentRejectItem = null" 
                        class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg">
                    Batal
                </button>
                <button @click="confirmReject()" 
                        :disabled="!rejectReason"
                        :class="rejectReason ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-300 cursor-not-allowed'"
                        class="flex-1 py-2.5 text-white font-medium rounded-lg">
                    Reject
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.approval-card {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
function approvalsPage() {
    return {
        approvals: @json($approvals),
        currentType: '{{ $currentType }}',
        loading: false,
        selectedItems: [],
        currentRejectItem: null,
        showBulkRejectModal: false,
        rejectReason: '',
        rejectNote: '',

        async filterType(type) {
            if (this.currentType === type) return;
            
            this.currentType = type;
            this.loading = true;
            this.clearSelection();
            
            try {
                const response = await fetch(`/m/approvals?type=${type}`);
                const html = await response.text();
                // Extract approvals data from response
                // For simplicity, reload page
                window.location.href = `/m/approvals?type=${type}`;
            } catch (error) {
                console.error('Filter error:', error);
                this.loading = false;
            }
        },

        toggleSelection(approval) {
            const index = this.selectedItems.findIndex(
                item => item.id === approval.id && item.type === approval.type
            );
            
            if (index === -1) {
                this.selectedItems.push(approval);
            } else {
                this.selectedItems.splice(index, 1);
            }
        },

        isSelected(approval) {
            return this.selectedItems.some(
                item => item.id === approval.id && item.type === approval.type
            );
        },

        clearSelection() {
            this.selectedItems = [];
        },

        async quickApprove(approval) {
            if (!confirm('Approve item ini?')) return;

            try {
                const response = await fetch(`/m/approvals/${approval.type}/${approval.id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Remove from list
                    this.approvals = this.approvals.filter(
                        a => !(a.id === approval.id && a.type === approval.type)
                    );
                    
                    // Show success feedback
                    this.showToast('Berhasil disetujui!', 'success');
                }
            } catch (error) {
                console.error('Approve error:', error);
                this.showToast('Gagal menyetujui', 'error');
            }
        },

        showRejectModal(approval) {
            this.currentRejectItem = approval;
            this.rejectReason = '';
            this.rejectNote = '';
        },

        async confirmReject() {
            if (!this.rejectReason || !this.currentRejectItem) return;

            try {
                const response = await fetch(`/m/approvals/${this.currentRejectItem.type}/${this.currentRejectItem.id}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reason: this.rejectReason,
                        note: this.rejectNote
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Remove from list
                    this.approvals = this.approvals.filter(
                        a => !(a.id === this.currentRejectItem.id && a.type === this.currentRejectItem.type)
                    );
                    
                    this.currentRejectItem = null;
                    this.showToast('Berhasil ditolak', 'success');
                }
            } catch (error) {
                console.error('Reject error:', error);
                this.showToast('Gagal menolak', 'error');
            }
        },

        async bulkApprove() {
            if (!confirm(`Approve ${this.selectedItems.length} items?`)) return;

            try {
                const response = await fetch('/m/approvals/bulk-approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: this.selectedItems
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Remove approved items
                    this.selectedItems.forEach(item => {
                        this.approvals = this.approvals.filter(
                            a => !(a.id === item.id && a.type === item.type)
                        );
                    });
                    
                    this.clearSelection();
                    this.showToast('Bulk approve berhasil!', 'success');
                }
            } catch (error) {
                console.error('Bulk approve error:', error);
                this.showToast('Bulk approve gagal', 'error');
            }
        },

        getIconColor(type) {
            const colors = {
                'expenses': 'bg-[#e7f3f8] text-[#0077b5]',
                'documents': 'bg-purple-100 text-purple-600',
                'invoices': 'bg-green-100 text-green-600'
            };
            return colors[type] || 'bg-gray-100 text-gray-600';
        },

        getTypeBadge(type) {
            const badges = {
                'expenses': 'bg-[#e7f3f8] text-[#0077b5]',
                'documents': 'bg-purple-100 text-purple-700',
                'invoices': 'bg-green-100 text-green-700'
            };
            return badges[type] || 'bg-gray-100 text-gray-700';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'short',
                year: 'numeric'
            });
        },

        showToast(message, type = 'info') {
            // Simple toast implementation
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }
    };
}

function swipeableCard(approval) {
    return {
        swipeX: 0,
        startX: 0,
        dragging: false,

        touchStart(e) {
            this.startX = e.touches[0].clientX;
            this.dragging = true;
        },

        touchMove(e) {
            if (!this.dragging) return;
            
            const currentX = e.touches[0].clientX;
            const diff = currentX - this.startX;
            
            // Limit swipe distance
            this.swipeX = Math.max(-150, Math.min(150, diff));
        },

        async touchEnd(e) {
            if (!this.dragging) return;
            this.dragging = false;

            // Swipe threshold
            if (this.swipeX > 100) {
                // Approve
                await this.$root.quickApprove(approval);
            } else if (this.swipeX < -100) {
                // Reject
                this.$root.showRejectModal(approval);
            }

            // Reset position
            this.swipeX = 0;
        }
    };
}
</script>
@endpush
