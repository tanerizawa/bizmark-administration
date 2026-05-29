{{-- Hero --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-1">
            <i class="fas fa-key text-xl opacity-90"></i>
            <h1 class="text-2xl font-bold tracking-tight">B2B API Keys</h1>
        </div>
        <p class="text-white/80 text-sm">Akses data KBLI, cost estimate, dan compliance Bizmark via API.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 space-y-5"
     x-data="{
         createModal: false,
         newKeyName: '',
         newKeyPlan: 'free',
         copyOnce: false,
         generatedKey: null,
         copied: false,
         copyKey() {
             if (!this.generatedKey) return;
             navigator.clipboard.writeText(this.generatedKey).then(() => { this.copied = true; setTimeout(()=>this.copied=false, 2000); });
         }
     }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-sm text-red-700 dark:text-red-300">
        <i class="fas fa-circle-xmark text-red-500 flex-shrink-0"></i> {{ $errors->first() }}
    </div>
    @endif

    {{-- Plan comparison table --}}
    @php
        $plans = [
            ['key'=>'free',       'name'=>'Free',       'price'=>'Gratis',      'limit'=>'100',    'period'=>'/bln',   'endpoints'=>'KBLI Search',                        'featured'=>false],
            ['key'=>'starter',    'name'=>'Starter',    'price'=>'Rp 299rb',    'limit'=>'5.000',  'period'=>'/bln',   'endpoints'=>'KBLI + Cost Estimate',               'featured'=>true],
            ['key'=>'pro',        'name'=>'Pro',         'price'=>'Rp 999rb',    'limit'=>'50.000', 'period'=>'/bln',   'endpoints'=>'Semua endpoint + Webhook',            'featured'=>false],
            ['key'=>'enterprise', 'name'=>'Enterprise',  'price'=>'Custom',      'limit'=>'∞',      'period'=>'',       'endpoints'=>'SLA + Dedicated Support',            'featured'=>false],
        ];
        $currentPlan = $keys->first()?->plan ?? null;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($plans as $p)
        <div class="relative flex flex-col gap-2 p-4 rounded-2xl border-2 transition
            {{ ($p['featured'] ?? false) ? 'border-[#0a66c2] bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">
            @if($p['featured'] ?? false)
            <span class="absolute -top-2.5 left-4 text-[10px] font-bold px-2 py-0.5 bg-[#0a66c2] text-white rounded-full">Populer</span>
            @endif
            @if($currentPlan === $p['key'])
            <span class="absolute -top-2.5 right-4 text-[10px] font-bold px-2 py-0.5 bg-green-500 text-white rounded-full">Aktif</span>
            @endif
            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $p['name'] }}</p>
            <p class="text-xl font-extrabold text-[#0a66c2]">{{ $p['price'] }}<span class="text-xs text-gray-400 font-normal">{{ $p['period'] }}</span></p>
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $p['limit'] }} req{{ $p['period'] }}</p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500 flex-1">{{ $p['endpoints'] }}</p>
            @if($currentPlan !== $p['key'])
            <a href="https://wa.me/{{ preg_replace('/\D/', '', config('landing_metrics.whatsapp_number', '6281234567890')) }}?text={{ rawurlencode('Halo Bizmark, saya ingin upgrade ke plan ' . $p['name'] . ' untuk API.') }}"
               target="_blank" rel="noopener"
               class="mt-1 block text-center text-xs font-semibold px-3 py-1.5 rounded-xl transition active:scale-95
               {{ ($p['featured'] ?? false) ? 'bg-[#0a66c2] hover:bg-[#004182] text-white' : 'border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-[#0a66c2] hover:text-[#0a66c2]' }}">
                Upgrade →
            </a>
            @else
            <span class="mt-1 block text-center text-xs font-semibold px-3 py-1.5 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                <i class="fas fa-check text-[9px] mr-0.5"></i> Plan Anda
            </span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- API Keys list + create --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-key text-[#0a66c2] text-sm"></i> API Keys Aktif
            </h2>
            <button type="button" @click="createModal=true"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                <i class="fas fa-plus text-[9px]"></i> Buat Key
            </button>
        </div>
        @if($keys->isEmpty())
        <div class="px-5 py-8">
            @include('client.components.empty-state', [
                'icon'     => 'fa-key',
                'title'    => 'Belum Ada API Key',
                'message'  => 'Buat API key pertama Anda untuk mulai mengakses Bizmark API.',
                'size'     => 'sm',
                'color'    => 'blue',
            ])
        </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($keys as $key)
            @php $pct = $key->monthly_limit > 0 ? min(100, ($key->usage_this_month / $key->monthly_limit) * 100) : 0; @endphp
            <div class="px-4 sm:px-5 py-4"
                 x-data="{ visible: false, copied: false }">
                <div class="flex items-start gap-3">
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $key->name }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                {{ match($key->plan) {
                                    'starter'    => 'bg-[#0a66c2]/10 text-[#0a66c2]',
                                    'pro'        => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                                    'enterprise' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                    default      => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                } }}">{{ ucfirst($key->plan) }}</span>
                            <span class="status-badge status-badge--{{ $key->is_active ? 'active' : 'inactive' }} !text-[10px]">
                                {{ $key->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        {{-- Key value + copy --}}
                        <div class="flex items-center gap-2">
                            <code class="text-xs font-mono bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-lg text-gray-700 dark:text-gray-300 truncate max-w-xs"
                                  x-text="visible ? '{{ $key->key }}' : '{{ substr($key->key, 0, 8) }}' + '•'.repeat(16)"></code>
                            <button type="button" @click="visible=!visible"
                                    class="text-[10px] font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 px-2 py-1 border border-gray-200 dark:border-gray-600 rounded-lg transition">
                                <i class="fas" :class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                            <button type="button" @click="navigator.clipboard.writeText('{{ $key->key }}').then(()=>{copied=true;setTimeout(()=>copied=false,2000)})"
                                    class="text-[10px] font-medium px-2 py-1 border rounded-lg transition"
                                    :class="copied ? 'border-green-400 text-green-600 bg-green-50' : 'border-gray-200 dark:border-gray-600 text-gray-400 hover:text-[#0a66c2] hover:border-[#0a66c2]'">
                                <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Disalin!' : 'Salin'"></span>
                            </button>
                        </div>
                        {{-- Usage bar --}}
                        <div>
                            <div class="flex items-center justify-between text-[10px] text-gray-500 dark:text-gray-400 mb-1">
                                <span>{{ number_format($key->usage_this_month) }} / {{ number_format($key->monthly_limit) }} req bulan ini</span>
                                <span class="{{ $pct >= 90 ? 'text-red-500 font-bold' : ($pct >= 70 ? 'text-amber-500 font-semibold' : '') }}">{{ round($pct) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-[#0a66c2]') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        @if($key->last_used_at)
                        <p class="text-xs text-gray-400 dark:text-gray-500">Terakhir digunakan {{ $key->last_used_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    {{-- Actions --}}
                    <div class="flex flex-col gap-1.5 flex-shrink-0">
                        <form method="POST" action="{{ route('client.api-keys.toggle', $key) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full text-xs font-medium px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-[#0a66c2] hover:text-[#0a66c2] rounded-xl transition active:scale-95">
                                {{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('client.api-keys.destroy', $key) }}"
                              onsubmit="return confirm('Hapus key ini? Aplikasi yang menggunakannya akan berhenti berfungsi.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full text-xs font-medium px-3 py-1.5 border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition active:scale-95">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Webhook Configuration --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <i class="fas fa-webhook text-[#0a66c2] text-sm"></i>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Konfigurasi Webhook</h2>
            <span class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">Pro+</span>
        </div>
        <div class="px-4 sm:px-5 py-4">
            @if(!in_array($currentPlan, ['pro', 'enterprise']))
            <div class="flex items-center gap-3 p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-xl text-sm">
                <i class="fas fa-lock text-purple-400 flex-shrink-0"></i>
                <p class="text-purple-700 dark:text-purple-300 text-xs">Webhook tersedia mulai plan <strong>Pro</strong>. Upgrade untuk mengaktifkan notifikasi real-time.</p>
            </div>
            @else
            <div x-data="{webhookUrl: '{{ $webhookUrl ?? '' }}', saving: false}" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">URL Endpoint Webhook</label>
                    <div class="flex gap-2">
                        <input type="url" x-model="webhookUrl" placeholder="https://yourapp.com/webhook"
                               class="flex-1 px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                        <button type="button"
                                @click="saving=true; window.apiFetch('{{ route('client.api-keys.webhook') }}', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({url: webhookUrl})}).then(()=>{saving=false; alert('Webhook disimpan!')})"
                                :disabled="saving"
                                class="px-4 py-2.5 text-sm font-semibold bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                            <span x-text="saving ? 'Menyimpan…' : 'Simpan'"></span>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">Event yang dikirim: <code>permit.status_changed</code>, <code>payment.verified</code>, <code>document.ready</code></p>
            </div>
            @endif
        </div>
    </div>

    {{-- API Quick Ref --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-code text-[#0a66c2] text-sm"></i> Referensi API
            </h2>
        </div>
        <div class="px-4 sm:px-5 py-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Kirim header <code class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs">Authorization: Bearer {api_key}</code> di setiap request.</p>
            <div class="space-y-2">
                @foreach([
                    ['POST','/api/v2/kbli/search','KBLI semantic search','{"query":"usaha kuliner"}'],
                    ['GET', '/api/v2/kbli/{code}', 'Detail KBLI','path param: code (e.g. 56101)'],
                    ['POST','/api/v2/cost-estimate','Estimasi biaya perizinan','{"kbli_code":"56101","location":"jakarta"}'],
                    ['POST','/api/v2/checklist','Generate checklist dokumen','{"kbli_code":"56101","permit_type":"nib"}'],
                ] as [$method, $path, $desc, $params])
                <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-3 py-2.5">
                    <span class="flex-shrink-0 text-[10px] font-bold font-mono px-2 py-0.5 rounded-md {{ $method === 'GET' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-[#0a66c2]/10 text-[#0a66c2]' }}">{{ $method }}</span>
                    <div class="min-w-0 flex-1">
                        <code class="text-xs text-gray-800 dark:text-gray-200">{{ $path }}</code>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">— {{ $desc }}</span>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 font-mono">{{ $params }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Create Key Modal --}}
    <div x-show="createModal" x-transition
         style="display:none"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm"
         @click.self="createModal=false" @keydown.escape.window="createModal=false">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="font-semibold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-plus text-[#0a66c2] text-xs"></i> Buat API Key Baru
                </p>
                <button @click="createModal=false" class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>
            {{-- Copy-once warning --}}
            <div class="mx-4 mt-4 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl text-xs text-amber-700 dark:text-amber-300">
                <i class="fas fa-triangle-exclamation flex-shrink-0 mt-0.5"></i>
                <span>Key hanya ditampilkan <strong>sekali</strong> setelah dibuat. Simpan di tempat yang aman segera.</span>
            </div>
            <form method="POST" action="{{ route('client.api-keys.store') }}" class="px-4 py-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Key <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="newKeyName" placeholder="Misal: My App Key" required maxlength="80"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plan</label>
                    <select name="plan" x-model="newKeyPlan"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                        <option value="free">Free — 100 req/bln</option>
                        <option value="starter">Starter — 5.000 req/bln</option>
                        <option value="pro">Pro — 50.000 req/bln</option>
                        <option value="enterprise">Enterprise — Unlimited</option>
                    </select>
                </div>
                <div class="flex gap-2 justify-end pt-1">
                    <button type="button" @click="createModal=false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!newKeyName"
                            :class="!newKeyName ? 'opacity-50 pointer-events-none' : ''"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                        <i class="fas fa-key text-xs"></i> Buat Key
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
