<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Inquiry - Bizmark.ID</title>
    <meta name="description" content="Request a free consultation for your investment in Indonesia. Get personalized guidance from our expert team.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<!-- Navbar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing.en') }}" class="text-xl font-bold text-blue-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>Bizmark.ID
            </a>
            <div class="flex items-center gap-4">
                <x-locale-switcher />
                <a href="{{ route('landing.en') }}" class="text-sm text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="min-h-screen py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-semibold mb-4">
                    <i class="fas fa-handshake mr-2"></i>Free Consultation
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">Investment Inquiry Form</h1>
                <p class="text-xl text-gray-600">
                    Share your investment plans and receive a personalized consultation within 24 hours
                </p>
            </div>

            <!-- Progress Indicator -->
            <div x-data="{ step: 1 }" class="mb-12">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex-1 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm transition-colors"
                             :class="step >= 1 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">1</div>
                        <div class="text-xs mt-2 font-medium" :class="step >= 1 ? 'text-blue-900' : 'text-gray-500'">Contact</div>
                    </div>
                    <div class="flex-1 h-1 mx-2" :class="step >= 2 ? 'bg-blue-900' : 'bg-gray-200'"></div>
                    <div class="flex-1 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm transition-colors"
                             :class="step >= 2 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">2</div>
                        <div class="text-xs mt-2 font-medium" :class="step >= 2 ? 'text-blue-900' : 'text-gray-500'">Investment</div>
                    </div>
                    <div class="flex-1 h-1 mx-2" :class="step >= 3 ? 'bg-blue-900' : 'bg-gray-200'"></div>
                    <div class="flex-1 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm transition-colors"
                             :class="step >= 3 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">3</div>
                        <div class="text-xs mt-2 font-medium" :class="step >= 3 ? 'text-blue-900' : 'text-gray-500'">Services</div>
                    </div>
                    <div class="flex-1 h-1 mx-2" :class="step >= 4 ? 'bg-blue-900' : 'bg-gray-200'"></div>
                    <div class="flex-1 text-center">
                        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-bold text-sm transition-colors"
                             :class="step >= 4 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">4</div>
                        <div class="text-xs mt-2 font-medium" :class="step >= 4 ? 'text-blue-900' : 'text-gray-500'">Details</div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="pmaInquiryForm" 
                  x-data="pmaInquiryForm()" 
                  @submit.prevent="submitForm"
                  class="bg-white rounded-2xl shadow-lg p-8">
                
                <!-- Step 1: Contact Information -->
                <div x-show="step === 1" x-cloak class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="full_name" x-model="formData.full_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" x-model="formData.email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="phone" x-model="formData.phone" required placeholder="+1 234 567 8900"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Country *</label>
                            <select name="country" x-model="formData.country" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select your country</option>
                                @foreach($countries as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name *</label>
                            <input type="text" name="company_name" x-model="formData.company_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Position/Title</label>
                            <input type="text" name="position" x-model="formData.position"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="button" @click="step = 2"
                                class="px-8 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                            Next: Investment Details <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Investment Information -->
                <div x-show="step === 2" x-cloak class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Investment Information</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Investment Sector *</label>
                            <select name="investment_sector" x-model="formData.investment_sector" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select sector</option>
                                @foreach($investmentSectors as $sector)
                                <option value="{{ $sector }}">{{ $sector }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Investment Amount (USD) *</label>
                            <select name="investment_amount_usd" x-model="formData.investment_amount_usd" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select range</option>
                                <option value="Under $50,000">Under $50,000</option>
                                <option value="$50,000 - $100,000">$50,000 - $100,000</option>
                                <option value="$100,000 - $250,000">$100,000 - $250,000</option>
                                <option value="$250,000 - $500,000">$250,000 - $500,000</option>
                                <option value="$500,000 - $1,000,000">$500,000 - $1,000,000</option>
                                <option value="Over $1,000,000">Over $1,000,000</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Investment Timeline *</label>
                            <select name="investment_timeline" x-model="formData.investment_timeline" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select timeline</option>
                                <option value="1-3 months">1-3 months (Urgent)</option>
                                <option value="3-6 months">3-6 months</option>
                                <option value="6-12 months">6-12 months</option>
                                <option value="Over 12 months">Over 12 months</option>
                                <option value="Just exploring">Just exploring options</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Business Location *</label>
                            <input type="text" name="business_location" x-model="formData.business_location" required
                                   placeholder="e.g., Jakarta, Bali, Surabaya"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 1"
                                class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Previous
                        </button>
                        <button type="button" @click="step = 3"
                                class="px-8 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                            Next: Services <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Services Needed -->
                <div x-show="step === 3" x-cloak class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Services Needed</h2>
                    
                    <p class="text-gray-600 mb-4">Select all services you're interested in:</p>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($services as $slug => $service)
                        <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer transition">
                            <input type="checkbox" name="services_needed[]" value="{{ $slug }}"
                                   x-model="formData.services_needed"
                                   class="mt-1 w-5 h-5 text-blue-900 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $service['title'] }}</div>
                                <div class="text-sm text-gray-600">{{ Str::limit($service['short_description'], 60) }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 2"
                                class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Previous
                        </button>
                        <button type="button" @click="step = 4"
                                class="px-8 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                            Next: Final Details <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Additional Details -->
                <div x-show="step === 4" x-cloak class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Additional Details</h2>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Project Description *</label>
                        <textarea name="project_description" x-model="formData.project_description" required rows="6"
                                  placeholder="Please describe your investment project, business model, and objectives..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <div class="text-sm text-gray-500 mt-1">Minimum 50 characters</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Specific Questions or Concerns</label>
                        <textarea name="specific_questions" x-model="formData.specific_questions" rows="4"
                                  placeholder="Any specific questions about regulations, permits, or processes?"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Contact Method *</label>
                            <select name="preferred_contact_method" x-model="formData.preferred_contact_method" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="phone">Phone Call</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Best Time to Contact</label>
                            <input type="text" name="preferred_contact_time" x-model="formData.preferred_contact_time"
                                   placeholder="e.g., Weekdays 9-5 EST"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="privacy_consent" x-model="formData.privacy_consent" required
                                   class="mt-1 w-5 h-5 text-blue-900 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-900 underline">Privacy Policy</a> and consent to be contacted by Bizmark.ID regarding my investment inquiry. *
                            </span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 3"
                                class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Previous
                        </button>
                        <button type="submit" :disabled="loading"
                                class="px-8 py-4 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Inquiry
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Submitting...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Error Messages -->
                <div x-show="errorMessage" x-cloak class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700" x-text="errorMessage"></p>
                </div>
            </form>

            <!-- Benefits Section -->
            <div class="grid md:grid-cols-3 gap-6 mt-12">
                <div class="text-center p-6 bg-white rounded-xl shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">24-Hour Response</h3>
                    <p class="text-sm text-gray-600">Get expert feedback within one business day</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-gift text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Free Consultation</h3>
                    <p class="text-sm text-gray-600">No obligation, personalized guidance</p>
                </div>
                
                <div class="text-center p-6 bg-white rounded-xl shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Confidential</h3>
                    <p class="text-sm text-gray-600">Your information is secure and private</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pmaInquiryForm() {
    return {
        step: 1,
        loading: false,
        errorMessage: '',
        formData: {
            full_name: '',
            email: '',
            phone: '',
            country: '',
            company_name: '',
            position: '',
            investment_sector: '',
            investment_amount_usd: '',
            investment_timeline: '',
            business_location: '',
            services_needed: [],
            project_description: '',
            specific_questions: '',
            preferred_contact_method: 'email',
            preferred_contact_time: '',
            privacy_consent: false,
        },
        
        async submitForm() {
            this.loading = true;
            this.errorMessage = '';
            
            try {
                const response = await fetch('{{ route("pma.inquiry.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    this.errorMessage = data.message || 'An error occurred. Please try again.';
                }
            } catch (error) {
                this.errorMessage = 'Network error. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

</body>
</html>
