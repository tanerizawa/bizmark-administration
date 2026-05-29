@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = true; // PMA inquiry is English-only by product decision
    $pageTitle = 'Investment Inquiry — Bizmark.ID';
    $pageDescription = 'Request a free consultation for your foreign investment in Indonesia. Get personalized guidance from our PMA-specialist team within 24 hours.';

    $metrics = config('landing_metrics');
    $expYears = data_get($metrics, 'experience.years', 12);
    $clientsActive = data_get($metrics, 'stats.clients_active_label', '138+');

    // Recent anonymized PMA inquiries (range agregat, no PII) — Decision #2 default
    $recentPma = [
        ['country' => 'Singapore',   'sector' => 'Manufacturing',     'amount' => '$250K–$500K', 'when' => '2 days ago'],
        ['country' => 'Japan',       'sector' => 'Logistics & Trade', 'amount' => '$500K–$1M',   'when' => '4 days ago'],
        ['country' => 'South Korea', 'sector' => 'Renewable Energy',  'amount' => '$1M+',        'when' => '1 week ago'],
        ['country' => 'Australia',   'sector' => 'F&B / Retail',      'amount' => '$100K–$250K', 'when' => '1 week ago'],
    ];
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)

@section('content')

{{-- HERO --}}
<section class="section-v2-sm bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="eyebrow mb-4">PMA Inquiry · Free Consultation</span>
            <h1 class="display-xl mt-2 mb-5">
                Investment inquiry — get a tailored PMA roadmap.
            </h1>
            <p class="text-lg leading-relaxed max-w-2xl text-gray-600">
                Share your investment plan in 3 short steps. Our PMA-specialist team responds within
                <strong style="color: var(--accent-text);">24 business hours</strong> with a clear permit roadmap, timeline, and cost estimate.
            </p>
        </div>
    </div>
</section>

{{-- FORM + SIDE PROOF --}}
<section class="section-v2">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-8 items-start">

            {{-- Form column --}}
            <div class="lg:col-span-8 order-2 lg:order-1">
                <form id="pmaInquiryForm"
                      x-data="pmaInquiryForm()"
                      @submit.prevent="submitForm"
                      class="premium-card">

                    {{-- 3-step progress indicator --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-700">
                                Step <span x-text="step" style="color: var(--accent-text);"></span> of 3
                            </span>
                            <span class="text-sm font-bold" style="color: var(--accent-text);" x-text="Math.round((step / 3) * 100) + '%'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-for="n in 3" :key="n">
                                <div class="flex-1 h-1.5 rounded-full transition-colors"
                                     :style="step >= n ? 'background: var(--accent);' : 'background: rgba(0,0,0,.08);'"></div>
                            </template>
                        </div>
                        <div class="flex items-center justify-between mt-3 text-xs font-medium text-gray-500">
                            <span :class="step === 1 ? 'font-bold' : ''" :style="step === 1 ? 'color: var(--accent-text);' : ''">1. About you</span>
                            <span :class="step === 2 ? 'font-bold' : ''" :style="step === 2 ? 'color: var(--accent-text);' : ''">2. Investment plan</span>
                            <span :class="step === 3 ? 'font-bold' : ''" :style="step === 3 ? 'color: var(--accent-text);' : ''">3. Project details</span>
                        </div>
                    </div>

                    {{-- STEP 1 — About you --}}
                    <div x-show="step === 1" x-cloak class="space-y-5">
                        <div>
                            <h2 class="font-display font-bold text-2xl mb-1">About you</h2>
                            <p class="text-sm text-gray-600">Who should we contact, and where are you based?</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full name *</label>
                                <input type="text" x-model="formData.full_name" required autocomplete="name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                                <input type="email" x-model="formData.email" required autocomplete="email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone (with country code) *</label>
                                <input type="tel" x-model="formData.phone" required placeholder="+1 234 567 8900" autocomplete="tel"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Country of origin *</label>
                                <select x-model="formData.country" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm bg-white">
                                    <option value="">Select your country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Company name *</label>
                                <input type="text" x-model="formData.company_name" required autocomplete="organization"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your role</label>
                                <input type="text" x-model="formData.position" placeholder="e.g., Director, Founder" autocomplete="organization-title"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="button" @click="next()" class="btn btn-gold">
                                <span>Continue</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2 — Investment plan + services --}}
                    <div x-show="step === 2" x-cloak class="space-y-5">
                        <div>
                            <h2 class="font-display font-bold text-2xl mb-1">Investment plan</h2>
                            <p class="text-sm text-gray-600">Tell us the shape of your investment and which services you'll need.</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sector *</label>
                                <select x-model="formData.investment_sector" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm bg-white">
                                    <option value="">Select sector</option>
                                    @foreach($investmentSectors as $sector)
                                        <option value="{{ $sector }}">{{ $sector }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Investment amount (USD) *</label>
                                <select x-model="formData.investment_amount_usd" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm bg-white">
                                    <option value="">Select range</option>
                                    <option value="Under $50,000">Under $50,000</option>
                                    <option value="$50,000 - $100,000">$50,000 – $100,000</option>
                                    <option value="$100,000 - $250,000">$100,000 – $250,000</option>
                                    <option value="$250,000 - $500,000">$250,000 – $500,000</option>
                                    <option value="$500,000 - $1,000,000">$500,000 – $1,000,000</option>
                                    <option value="Over $1,000,000">Over $1,000,000</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Timeline *</label>
                                <select x-model="formData.investment_timeline" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm bg-white">
                                    <option value="">Select timeline</option>
                                    <option value="1-3 months">1–3 months (urgent)</option>
                                    <option value="3-6 months">3–6 months</option>
                                    <option value="6-12 months">6–12 months</option>
                                    <option value="Over 12 months">Over 12 months</option>
                                    <option value="Just exploring">Just exploring options</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Preferred location *</label>
                                <input type="text" x-model="formData.business_location" required
                                       placeholder="e.g., Jakarta, Bali, Surabaya"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Services you're interested in</label>
                            <p class="text-xs text-gray-500 mb-3">Optional — pick any that apply. We'll recommend the rest.</p>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($services as $slug => $service)
                                    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-amber-300">
                                        <input type="checkbox" value="{{ $slug }}"
                                               x-model="formData.services_needed"
                                               class="mt-0.5 w-4 h-4 border-gray-300 rounded">
                                        <div class="flex-1">
                                            <div class="font-semibold text-sm text-gray-900">{{ $service['title'] }}</div>
                                            <div class="text-xs text-gray-600 mt-0.5">{{ Str::limit($service['short_description'], 70) }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-between pt-2">
                            <button type="button" @click="prev()" class="btn btn-ghost">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Back</span>
                            </button>
                            <button type="button" @click="next()" class="btn btn-gold">
                                <span>Continue</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3 — Project details + submit --}}
                    <div x-show="step === 3" x-cloak class="space-y-5">
                        <div>
                            <h2 class="font-display font-bold text-2xl mb-1">Project details</h2>
                            <p class="text-sm text-gray-600">A short description so our consultant can prepare for the call.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Project description *</label>
                            <textarea x-model="formData.project_description" required rows="5"
                                      placeholder="Briefly describe your investment project, business model, and main goals…"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm"></textarea>
                            <div class="text-xs text-gray-500 mt-1">Minimum 50 characters</div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Specific questions (optional)</label>
                            <textarea x-model="formData.specific_questions" rows="3"
                                      placeholder="Anything specific about regulations, KBLI, permits, or process?"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm"></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Preferred contact *</label>
                                <select x-model="formData.preferred_contact_method" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm bg-white">
                                    <option value="email">Email</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="phone">Phone Call</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Best time to contact</label>
                                <input type="text" x-model="formData.preferred_contact_time"
                                       placeholder="e.g., Weekdays 9–5 SGT"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                            </div>
                        </div>

                        <label class="flex items-start gap-3 p-4 rounded-lg cursor-pointer" style="background: var(--accent-glow); border: 1px solid rgba(var(--accent-rgb), .2);">
                            <input type="checkbox" x-model="formData.privacy_consent" required
                                   class="mt-0.5 w-4 h-4 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">
                                I agree to the <a href="#" class="underline" style="color: var(--accent-text);">Privacy Policy</a> and consent to be contacted by Bizmark.ID regarding this inquiry. *
                            </span>
                        </label>

                        <div class="flex justify-between pt-2">
                            <button type="button" @click="prev()" class="btn btn-ghost">
                                <i class="fas fa-arrow-left text-xs"></i>
                                <span>Back</span>
                            </button>
                            <button type="submit" :disabled="loading" class="btn btn-gold">
                                <span x-show="!loading"><i class="fas fa-paper-plane text-xs"></i> Submit inquiry</span>
                                <span x-show="loading"><i class="fas fa-spinner fa-spin text-xs"></i> Submitting…</span>
                            </button>
                        </div>
                    </div>

                    {{-- Error banner --}}
                    <div x-show="errorMessage" x-cloak class="mt-5 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700" x-text="errorMessage"></p>
                    </div>
                </form>
            </div>

            {{-- Side proof column --}}
            <aside class="lg:col-span-4 order-1 lg:order-2 lg:sticky lg:top-24 space-y-5">
                {{-- Recent inquiries --}}
                <div class="premium-card">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[11px] font-bold uppercase tracking-[.15em] text-emerald-700">Live</span>
                    </div>
                    <h3 class="font-display font-bold text-lg mb-1">Recent PMA inquiries handled</h3>
                    <p class="text-xs text-gray-500 mb-4">Aggregated range, country/sector only — no PII.</p>
                    <ul class="space-y-3">
                        @foreach($recentPma as $r)
                            <li class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full flex-shrink-0 text-sm font-bold" style="background: var(--accent-glow); color: var(--accent-text);">
                                    {{ substr($r['country'], 0, 2) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-900">{{ $r['country'] }} · {{ $r['sector'] }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $r['amount'] }} · {{ $r['when'] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Why this consultation --}}
                <div class="premium-card">
                    <h3 class="font-display font-bold text-lg mb-3">Why this inquiry?</h3>
                    <ul class="space-y-2.5 text-sm text-gray-700">
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0" style="color: var(--accent-text);"></i>
                            <span><strong>24-hour response</strong> from a PMA specialist</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0" style="color: var(--accent-text);"></i>
                            <span><strong>Tailored permit roadmap</strong> — KBLI, OSS-RBA, BKPM, sector permits</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0" style="color: var(--accent-text);"></i>
                            <span><strong>Cost &amp; timeline estimate</strong> upfront — no surprises</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0" style="color: var(--accent-text);"></i>
                            <span><strong>Confidential</strong> — your data is encrypted and never shared</span>
                        </li>
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                        <i class="fas fa-award mr-1" style="color: var(--accent);"></i>
                        <strong>{{ $expYears }}+ years</strong> · <strong>{{ $clientsActive }} clients</strong> · ISO 9001:2015
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>

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
        next() {
            if (this.step === 1) {
                if (!this.formData.full_name || !this.formData.email || !this.formData.phone || !this.formData.country || !this.formData.company_name) {
                    this.errorMessage = 'Please fill in all required fields.';
                    return;
                }
            }
            if (this.step === 2) {
                if (!this.formData.investment_sector || !this.formData.investment_amount_usd || !this.formData.investment_timeline || !this.formData.business_location) {
                    this.errorMessage = 'Please complete the investment plan fields.';
                    return;
                }
            }
            this.errorMessage = '';
            if (this.step < 3) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prev() {
            this.errorMessage = '';
            if (this.step > 1) this.step--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
            } catch (e) {
                this.errorMessage = 'Network error. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

@endsection
