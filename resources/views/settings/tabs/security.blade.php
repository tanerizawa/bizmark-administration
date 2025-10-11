{{-- Security Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Security Settings</h3>
    
    <div class="space-y-4">
        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-lock mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Password Policies
            </h4>
            <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.6);">
                Configure password requirements and expiration
            </p>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #FFFFFF;">Minimum Password Length</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Require at least 8 characters</p>
                    </div>
                    <input type="number" value="8" min="6" max="32" 
                           class="w-20 px-3 py-2 rounded-apple text-sm text-center"
                           style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #FFFFFF;">
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #FFFFFF;">Require Special Characters</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Include symbols in passwords</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                             style="background: rgba(255, 255, 255, 0.15);"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #FFFFFF;">Password Expiration</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Force password change every 90 days</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                             style="background: rgba(255, 255, 255, 0.15);"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-shield-alt mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Two-Factor Authentication
            </h4>
            <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.6);">
                Enhance security with 2FA
            </p>
            <div class="text-center py-8">
                <i class="fas fa-mobile-alt text-3xl mb-2" style="color: rgba(235, 235, 245, 0.25);"></i>
                <p class="text-sm" style="color: rgba(235, 235, 245, 0.5);">Coming soon</p>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-clock mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Session Management
            </h4>
            <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.6);">
                Configure session timeout and concurrent sessions
            </p>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #FFFFFF;">Session Timeout</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Automatically logout after inactivity</p>
                    </div>
                    <select class="px-3 py-2 rounded-apple text-sm"
                            style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #FFFFFF;">
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: #FFFFFF;">Concurrent Sessions</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Allow multiple active sessions</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                             style="background: rgba(255, 255, 255, 0.15);"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-history mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Activity Log
            </h4>
            <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.6);">
                Track user actions and system events
            </p>
            <div class="text-center py-8">
                <i class="fas fa-list-ul text-3xl mb-2" style="color: rgba(235, 235, 245, 0.25);"></i>
                <p class="text-sm" style="color: rgba(235, 235, 245, 0.5);">Coming soon</p>
            </div>
        </div>
    </div>
</div>
