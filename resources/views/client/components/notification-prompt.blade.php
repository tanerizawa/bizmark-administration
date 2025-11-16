<!-- Notification Permission Prompt Component -->
<div x-data="notificationPrompt()" x-show="showPrompt" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all" @click.away="dismiss()">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bell text-3xl text-indigo-600"></i>
            </div>
        </div>
        
        <!-- Title -->
        <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
            Dapatkan Update Real-time
        </h3>
        
        <!-- Description -->
        <p class="text-center text-gray-600 mb-6">
            Aktifkan notifikasi untuk mendapat update langsung tentang status izin Anda
        </p>
        
        <!-- Benefits -->
        <div class="space-y-3 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Update Status Langsung</p>
                    <p class="text-sm text-gray-600">Ketahui perubahan status izin secara real-time</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Reminder Deadline</p>
                    <p class="text-sm text-gray-600">Jangan lewatkan deadline penting</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <p class="font-medium text-gray-900">Pesan dari Tim Kami</p>
                    <p class="text-sm text-gray-600">Terima pemberitahuan dokumen yang dibutuhkan</p>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex gap-3">
            <button @click="dismiss()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                Nanti Saja
            </button>
            <button @click="enable()" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg font-medium hover:from-indigo-700 hover:to-blue-700 transition">
                Aktifkan
            </button>
        </div>
        
        <!-- Don't show again -->
        <button @click="dismissPermanently()" class="w-full mt-3 text-sm text-gray-500 hover:text-gray-700">
            Jangan tampilkan lagi
        </button>
    </div>
</div>

<script>
function notificationPrompt() {
    return {
        showPrompt: false,
        
        init() {
            // Check if should show prompt
            this.checkShouldShow();
        },
        
        checkShouldShow() {
            // Don't show if:
            // 1. Already dismissed permanently
            if (localStorage.getItem('notification-prompt-dismissed') === 'true') {
                return;
            }
            
            // 2. Not in PWA mode
            if (!window.isPWA || !window.isPWA()) {
                return;
            }
            
            // 3. Already subscribed
            if (localStorage.getItem('push-subscribed') === 'true') {
                return;
            }
            
            // 4. Permission already denied
            if (Notification.permission === 'denied') {
                return;
            }
            
            // 5. Permission already granted
            if (Notification.permission === 'granted') {
                return;
            }
            
            // 6. No applications yet (wait for user to be engaged)
            const hasApplications = document.querySelector('[data-has-applications]')?.dataset.hasApplications === 'true';
            if (!hasApplications) {
                return;
            }
            
            // Show prompt after 5 seconds delay
            setTimeout(() => {
                this.showPrompt = true;
            }, 5000);
        },
        
        async enable() {
            try {
                if (window.subscribeToPushNotifications) {
                    const success = await window.subscribeToPushNotifications();
                    
                    if (success) {
                        this.showPrompt = false;
                        
                        // Show success message
                        this.showSuccessMessage();
                    } else {
                        // Show error message
                        this.showErrorMessage();
                    }
                }
            } catch (error) {
                console.error('Error enabling notifications:', error);
                this.showErrorMessage();
            }
        },
        
        dismiss() {
            this.showPrompt = false;
        },
        
        dismissPermanently() {
            localStorage.setItem('notification-prompt-dismissed', 'true');
            this.showPrompt = false;
        },
        
        showSuccessMessage() {
            // Create temporary success message
            const message = document.createElement('div');
            message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
            message.innerHTML = '<i class="fas fa-check-circle"></i> Notifikasi berhasil diaktifkan!';
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 3000);
        },
        
        showErrorMessage() {
            // Create temporary error message
            const message = document.createElement('div');
            message.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
            message.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal mengaktifkan notifikasi';
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 3000);
        }
    }
}
</script>
