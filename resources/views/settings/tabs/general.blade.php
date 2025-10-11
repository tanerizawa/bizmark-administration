{{-- General Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">General Settings</h3>
    
    <div class="space-y-6">
        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-4" style="color: #FFFFFF;">Company Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Company Name
                    </label>
                    <input type="text" class="input-apple w-full" placeholder="Your Company Name">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Email
                    </label>
                    <input type="email" class="input-apple w-full" placeholder="company@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Phone
                    </label>
                    <input type="text" class="input-apple w-full" placeholder="+62 xxx xxx xxx">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Website
                    </label>
                    <input type="url" class="input-apple w-full" placeholder="https://example.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Address
                    </label>
                    <textarea class="input-apple w-full" rows="3" placeholder="Company address"></textarea>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-4" style="color: #FFFFFF;">Application Settings</h4>
            <div class="space-y-3">
                <label class="flex items-center justify-between p-3 rounded-apple cursor-pointer hover:bg-opacity-50 transition-colors"
                       style="background: rgba(255, 255, 255, 0.02);">
                    <div>
                        <div class="text-sm font-medium" style="color: #FFFFFF;">Maintenance Mode</div>
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Temporarily disable access to the application</div>
                    </div>
                    <input type="checkbox" class="toggle-switch">
                </label>
                
                <label class="flex items-center justify-between p-3 rounded-apple cursor-pointer hover:bg-opacity-50 transition-colors"
                       style="background: rgba(255, 255, 255, 0.02);">
                    <div>
                        <div class="text-sm font-medium" style="color: #FFFFFF;">Email Notifications</div>
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Send email notifications for important events</div>
                    </div>
                    <input type="checkbox" class="toggle-switch" checked>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                    style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </div>
</div>
