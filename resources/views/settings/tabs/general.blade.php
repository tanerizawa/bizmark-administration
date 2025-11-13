{{-- General Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">General Settings</h3>

    <form method="POST" action="{{ route('settings.general.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-4" style="color: #FFFFFF;">Company Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="company_name" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Company Name
                    </label>
                    <input type="text" name="company_name" id="company_name" class="input-apple w-full"
                           value="{{ old('company_name', $setting->company_name) }}" placeholder="Bizmark.ID">
                    @error('company_name')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_email" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Email
                    </label>
                    <input type="email" name="company_email" id="company_email" class="input-apple w-full"
                           value="{{ old('company_email', $setting->company_email) }}" placeholder="support@bizmark.id">
                    @error('company_email')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_phone" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Phone
                    </label>
                    <input type="text" name="company_phone" id="company_phone" class="input-apple w-full"
                           value="{{ old('company_phone', $setting->company_phone) }}" placeholder="0812-1234-5678">
                    @error('company_phone')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_website" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Website
                    </label>
                    <input type="url" name="company_website" id="company_website" class="input-apple w-full"
                           value="{{ old('company_website', $setting->company_website) }}" placeholder="https://bizmark.id">
                    @error('company_website')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="company_address" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Address
                    </label>
                    <textarea name="company_address" id="company_address" class="input-apple w-full" rows="3"
                              placeholder="Alamat kantor utama">{{ old('company_address', $setting->company_address) }}</textarea>
                    @error('company_address')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
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
                    <input type="checkbox" name="maintenance_mode" value="1"
                           {{ old('maintenance_mode', $setting->maintenance_mode) ? 'checked' : '' }}
                           class="toggle-switch">
                </label>

                <label class="flex items-center justify-between p-3 rounded-apple cursor-pointer hover:bg-opacity-50 transition-colors"
                       style="background: rgba(255, 255, 255, 0.02);">
                    <div>
                        <div class="text-sm font-medium" style="color: #FFFFFF;">Email Notifications</div>
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Send email notifications for important events</div>
                    </div>
                    <input type="checkbox" name="email_notifications" value="1"
                           {{ old('email_notifications', $setting->email_notifications) ? 'checked' : '' }}
                           class="toggle-switch">
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                    style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
