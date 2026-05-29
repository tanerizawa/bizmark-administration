<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceDataController extends Controller
{
    protected string $dataPath;

    public function __construct()
    {
        $this->dataPath = resource_path('data/services_data.json');
    }

    // ─────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────

    protected function load(): array
    {
        if (! file_exists($this->dataPath)) {
            return [];
        }

        return json_decode(file_get_contents($this->dataPath), true) ?? [];
    }

    protected function save(array $data): void
    {
        file_put_contents(
            $this->dataPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // Clear config cache so frontend immediately picks up changes
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->dataPath, true);
        }
    }

    protected function categories(): array
    {
        return [
            'LINGKUNGAN',
            'PERIZINAN USAHA',
            'BANGUNAN',
            'INDUSTRI',
            'TEKNOLOGI',
            'K3',
            'PMA & INVESTASI',
            'HALAL & PANGAN',
            'PERPAJAKAN',
            'HAKI & MEREK',
            'KETENAGAKERJAAN',
        ];
    }

    /**
     * Parse a newline-delimited textarea into an array, removing blank lines.
     */
    protected function parseLines(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", $text)))
        ));
    }

    /**
     * Build the service array from validated request data.
     * Does NOT handle sub_services (managed separately).
     */
    protected function buildService(Request $request, array $existing = []): array
    {
        // Process steps: [{title, desc}]
        $stepTitles = $request->input('step_titles', []);
        $stepDescs = $request->input('step_descs', []);
        $steps = [];
        foreach ($stepTitles as $i => $t) {
            $t = trim($t);
            if ($t !== '') {
                $steps[] = ['title' => $t, 'desc' => trim($stepDescs[$i] ?? '')];
            }
        }

        // FAQ: [{q, a}]
        $faqQs = $request->input('faq_q', []);
        $faqAs = $request->input('faq_a', []);
        $faqs = [];
        foreach ($faqQs as $i => $q) {
            $q = trim($q);
            if ($q !== '') {
                $faqs[] = ['q' => $q, 'a' => trim($faqAs[$i] ?? '')];
            }
        }

        return [
            'title' => $request->input('title', ''),
            'slug' => $request->input('slug', ''),
            'short_description' => $request->input('short_description', ''),
            'long_description' => $request->input('long_description', ''),
            'icon' => $request->input('icon', 'fa-layer-group'),
            'color' => $request->input('color', '#B45309'),
            'meta_keywords' => $request->input('meta_keywords', ''),
            'category' => $request->input('category', ''),
            'price_range' => $request->input('price_range', ''),
            'price' => $request->input('price', ''),
            'process_time' => $request->input('process_time', ''),
            'badge' => $request->input('badge', ''),
            'featured' => (bool) $request->input('featured', false),
            'key_features' => $this->parseLines($request->input('key_features')),
            'documents_required' => $this->parseLines($request->input('documents_required')),
            'process_steps_detail' => $steps,
            'faq' => $faqs,
            // Preserve sub_services from existing data
            'sub_services' => $existing['sub_services'] ?? [],
        ];
    }

    // ─────────────────────────────────────────────
    //  CRUD: SERVICES
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $services = $this->load();

        // Filter by category
        if ($cat = $request->query('category')) {
            $services = array_filter($services, fn ($s) => ($s['category'] ?? '') === $cat);
        }

        // Search by title
        if ($q = $request->query('q')) {
            $q = strtolower($q);
            $services = array_filter($services, fn ($s) => str_contains(strtolower($s['title'] ?? ''), $q));
        }

        return view('admin.services.index', [
            'services' => $services,
            'categories' => $this->categories(),
            'filterCat' => $request->query('category', ''),
            'filterQ' => $request->query('q', ''),
        ]);
    }

    public function create()
    {
        return view('admin.services.create', [
            'categories' => $this->categories(),
            'service' => null,
            'slug' => null,
        ]);
    }

    public function aiGenerate(Request $request)
    {
        $request->validate(['topic' => 'required|string|max:300']);

        $topic = $request->input('topic');
        $apiKey = config('services.openrouter.api_key');
        $model = config('services.openrouter.free_primary_model', 'google/gemini-2.5-flash');
        $cats = implode(', ', $this->categories());

        $prompt = <<<PROMPT
Kamu adalah konsultan perizinan bisnis Indonesia dan penulis konten profesional untuk website Bizmark.ID (jasa pengurusan perizinan).

Buat konten halaman layanan untuk topik: "{$topic}"

Kategori yang tersedia: {$cats}

Balas HANYA dengan JSON valid (tanpa teks tambahan, tanpa markdown code block) dalam format persis ini:
{
  "title": "Judul layanan resmi dan menarik (max 80 karakter)",
  "category": "Salah satu dari kategori yang tersedia persis",
  "slug": "slug-url-lowercase-dengan-tanda-hubung",
  "icon": "fa-nama-ikon-fontawesome",
  "color": "#HEXWARNA",
  "badge": "Label badge opsional seperti Populer atau kosong string",
  "short_description": "1-2 kalimat deskripsi singkat untuk kartu layanan (max 200 karakter)",
  "long_description": "Deskripsi lengkap minimal 4 paragraf detail menjelaskan layanan, manfaat, regulasi yang relevan, dan keunggulan Bizmark.ID",
  "price_range": "Estimasi harga tampilan misalnya Mulai Rp 25 Juta",
  "price": "Angka harga numerik saja misalnya 25000000",
  "process_time": "Estimasi durasi misalnya 30–90 Hari",
  "key_features": ["Fitur 1", "Fitur 2", "Fitur 3", "Fitur 4", "Fitur 5", "Fitur 6"],
  "documents_required": ["Dokumen 1", "Dokumen 2", "Dokumen 3", "Dokumen 4", "Dokumen 5"],
  "process_steps_detail": [
    {"title": "Judul Tahapan 1", "desc": "Deskripsi detail tahapan pertama"},
    {"title": "Judul Tahapan 2", "desc": "Deskripsi detail tahapan kedua"},
    {"title": "Judul Tahapan 3", "desc": "Deskripsi detail tahapan ketiga"},
    {"title": "Judul Tahapan 4", "desc": "Deskripsi detail tahapan keempat"},
    {"title": "Judul Tahapan 5", "desc": "Deskripsi detail tahapan kelima"}
  ],
  "faq": [
    {"q": "Pertanyaan umum 1?", "a": "Jawaban lengkap 1"},
    {"q": "Pertanyaan umum 2?", "a": "Jawaban lengkap 2"},
    {"q": "Pertanyaan umum 3?", "a": "Jawaban lengkap 3"},
    {"q": "Pertanyaan umum 4?", "a": "Jawaban lengkap 4"},
    {"q": "Pertanyaan umum 5?", "a": "Jawaban lengkap 5"}
  ],
  "meta_keywords": "kata kunci 1, kata kunci 2, kata kunci 3, kata kunci 4, kata kunci 5"
}
PROMPT;

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(90)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Kamu adalah asisten AI yang menghasilkan konten perizinan bisnis Indonesia dalam Bahasa Indonesia. Selalu balas dengan JSON valid saja tanpa markdown atau teks tambahan.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ]);

            if (! $response->successful()) {
                return response()->json(['error' => 'AI API error: '.$response->status().' '.$response->body()], 502);
            }

            $content = $response->json('choices.0.message.content', '');
            // Strip markdown code blocks if AI adds them
            $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
            $content = preg_replace('/\s*```$/i', '', $content);
            $content = trim($content);

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'AI returned invalid JSON: '.json_last_error_msg()], 422);
            }

            return response()->json(['data' => $data]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'slug' => 'required|string|regex:/^[a-z0-9\-]+$/',
        ]);

        $services = $this->load();
        $slug = $request->input('slug');

        if (isset($services[$slug])) {
            return back()->withInput()->withErrors(['slug' => 'Slug sudah digunakan oleh layanan lain.']);
        }

        $services[$slug] = $this->buildService($request);
        $this->save($services);

        // Bust config cache
        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(string $slug)
    {
        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        return view('admin.services.edit', [
            'service' => $services[$slug],
            'slug' => $slug,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'slug' => 'required|string|regex:/^[a-z0-9\-]+$/',
        ]);

        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        $newSlug = $request->input('slug');

        // If slug changed, move the record
        if ($newSlug !== $slug) {
            if (isset($services[$newSlug])) {
                return back()->withInput()->withErrors(['slug' => 'Slug sudah digunakan oleh layanan lain.']);
            }
            unset($services[$slug]);
        }

        $services[$newSlug] = $this->buildService($request, $services[$slug] ?? $services[$newSlug] ?? []);
        $this->save($services);

        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(string $slug)
    {
        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        unset($services[$slug]);
        $this->save($services);

        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    //  CRUD: SUB-SERVICES
    // ─────────────────────────────────────────────

    public function subIndex(string $slug)
    {
        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        return view('admin.services.sub.index', [
            'parent' => $services[$slug],
            'parentSlug' => $slug,
            'subServices' => $services[$slug]['sub_services'] ?? [],
        ]);
    }

    public function subCreate(string $slug)
    {
        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        return view('admin.services.sub.create', [
            'parent' => $services[$slug],
            'parentSlug' => $slug,
            'sub' => null,
            'subSlug' => null,
        ]);
    }

    public function subStore(Request $request, string $slug)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'sub_slug' => 'required|string|regex:/^[a-z0-9\-]+$/',
        ]);

        $services = $this->load();

        if (! isset($services[$slug])) {
            abort(404);
        }

        $subSlug = $request->input('sub_slug');

        if (isset($services[$slug]['sub_services'][$subSlug])) {
            return back()->withInput()->withErrors(['sub_slug' => 'Sub-slug sudah digunakan.']);
        }

        $services[$slug]['sub_services'][$subSlug] = $this->buildSubService($request);
        $this->save($services);

        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.sub.index', $slug)
            ->with('success', 'Sub-layanan berhasil ditambahkan.');
    }

    public function subEdit(string $slug, string $subSlug)
    {
        $services = $this->load();

        if (! isset($services[$slug]['sub_services'][$subSlug])) {
            abort(404);
        }

        return view('admin.services.sub.edit', [
            'parent' => $services[$slug],
            'parentSlug' => $slug,
            'sub' => $services[$slug]['sub_services'][$subSlug],
            'subSlug' => $subSlug,
        ]);
    }

    public function subUpdate(Request $request, string $slug, string $subSlug)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'sub_slug' => 'required|string|regex:/^[a-z0-9\-]+$/',
        ]);

        $services = $this->load();

        if (! isset($services[$slug]['sub_services'][$subSlug])) {
            abort(404);
        }

        $newSubSlug = $request->input('sub_slug');

        if ($newSubSlug !== $subSlug) {
            if (isset($services[$slug]['sub_services'][$newSubSlug])) {
                return back()->withInput()->withErrors(['sub_slug' => 'Sub-slug sudah digunakan.']);
            }
            unset($services[$slug]['sub_services'][$subSlug]);
        }

        $services[$slug]['sub_services'][$newSubSlug] = $this->buildSubService($request);
        $this->save($services);

        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.sub.index', $slug)
            ->with('success', 'Sub-layanan berhasil diperbarui.');
    }

    public function subDestroy(string $slug, string $subSlug)
    {
        $services = $this->load();

        if (! isset($services[$slug]['sub_services'][$subSlug])) {
            abort(404);
        }

        unset($services[$slug]['sub_services'][$subSlug]);
        $this->save($services);

        \Artisan::call('cache:clear');

        return redirect()->route('admin.services.sub.index', $slug)
            ->with('success', 'Sub-layanan berhasil dihapus.');
    }

    protected function buildSubService(Request $request): array
    {
        return [
            'title' => $request->input('title', ''),
            'short_description' => $request->input('short_description', ''),
            'long_description' => $request->input('long_description', ''),
            'icon' => $request->input('icon', 'fa-layer-group'),
            'meta_keywords' => $request->input('meta_keywords', ''),
            'duration' => $request->input('duration', ''),
            'process_steps' => $this->parseLines($request->input('process_steps')),
            'requirements' => $this->parseLines($request->input('requirements')),
        ];
    }
}
