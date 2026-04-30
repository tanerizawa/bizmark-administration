<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleTopicController extends Controller
{
    public function index(Request $request)
    {
        $query = ArticleTopic::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'ILIKE', '%'.$request->search.'%');
        }

        $topics = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => ArticleTopic::count(),
            'available' => ArticleTopic::where('status', 'pending')->whereNull('scheduled_for')->count(),
            'scheduled' => ArticleTopic::where('status', 'pending')->whereNotNull('scheduled_for')->count(),
            'used' => ArticleTopic::where('status', 'published')->count(),
        ];

        return view('admin.auto-post.topics.index', compact('topics', 'stats'));
    }

    public function create()
    {
        return view('admin.auto-post.topics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:tips,guide,case-study,news,regulation,general',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'language' => 'nullable|in:id,en',
            'target_market' => 'nullable|in:local,pma,both',
            'generation_notes' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:1|max:10',
        ]);

        if (($validated['category'] ?? null) === 'guide') {
            $validated['category'] = 'tips';
        }

        $validated['language'] = $validated['language'] ?? 'id';
        $validated['target_market'] = $validated['target_market'] ?? 'both';
        $validated['status'] = 'pending';

        $validated = $this->normalizeTopicPayload($validated);

        ArticleTopic::create($validated);

        return redirect()
            ->route('auto-post.topics.index')
            ->with('success', 'Topic berhasil ditambahkan');
    }

    public function edit(ArticleTopic $topic)
    {
        return view('admin.auto-post.topics.edit', compact('topic'));
    }

    public function update(Request $request, ArticleTopic $topic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:tips,guide,case-study,news,regulation,general',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'language' => 'nullable|in:id,en',
            'target_market' => 'nullable|in:local,pma,both',
            'generation_notes' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:1|max:10',
        ]);

        if (($validated['category'] ?? null) === 'guide') {
            $validated['category'] = 'tips';
        }

        $validated['language'] = $validated['language'] ?? $topic->language ?? 'id';
        $validated['target_market'] = $validated['target_market'] ?? $topic->target_market ?? 'both';
        $validated = $this->normalizeTopicPayload($validated);

        $topic->update($validated);

        return redirect()
            ->route('auto-post.topics.index')
            ->with('success', 'Topic berhasil diperbarui');
    }

    public function destroy(ArticleTopic $topic)
    {
        if ($topic->status === 'processing' || ($topic->status === 'pending' && $topic->scheduled_for)) {
            return back()->with('error', 'Tidak dapat menghapus topic yang sedang diproses atau dijadwalkan');
        }

        $topic->delete();

        return redirect()
            ->route('auto-post.topics.index')
            ->with('success', 'Topic berhasil dihapus');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,reset,change_priority,normalize_ecosystem',
            'topics' => 'nullable|array',
            'topics.*' => 'exists:article_topics,id',
            'priority' => 'required_if:action,change_priority|integer|min:1|max:10',
            'scope' => 'nullable|in:selected,filtered,all',
            'status' => 'nullable|string',
            'category' => 'nullable|string',
            'market' => 'nullable|in:local,pma,both',
            'search' => 'nullable|string',
        ]);

        $scope = $validated['scope'] ?? 'selected';
        if ($scope === 'selected') {
            if (empty($validated['topics'])) {
                return back()->with('error', 'Pilih minimal 1 topic untuk diproses.');
            }
            $topics = ArticleTopic::whereIn('id', $validated['topics']);
        } elseif ($scope === 'filtered') {
            $topics = ArticleTopic::query();
            if (! empty($validated['status'])) {
                $topics->where('status', $validated['status']);
            }
            if (! empty($validated['category'])) {
                $topics->where('category', $validated['category']);
            }
            if (! empty($validated['market'])) {
                $topics->where('target_market', $validated['market']);
            }
            if (! empty($validated['search'])) {
                $topics->where(function ($q) use ($validated) {
                    $q->where('title', 'ILIKE', '%'.$validated['search'].'%')
                        ->orWhere('description', 'ILIKE', '%'.$validated['search'].'%')
                        ->orWhereRaw("COALESCE(keywords::text, '') ILIKE ?", ['%'.$validated['search'].'%']);
                });
            }
        } else {
            $topics = ArticleTopic::query();
        }

        switch ($validated['action']) {
            case 'delete':
                $topics->where('status', '!=', 'processing')->delete();
                $message = 'Topics berhasil dihapus';
                break;

            case 'reset':
                $topics->update(['status' => 'pending', 'scheduled_for' => null]);
                $message = 'Topics berhasil direset';
                break;

            case 'change_priority':
                $topics->update(['priority' => $validated['priority']]);
                $message = 'Priority berhasil diubah';
                break;

            case 'normalize_ecosystem':
                $processed = 0;
                $updated = 0;
                $topics->chunkById(100, function ($chunk) use (&$processed, &$updated) {
                    foreach ($chunk as $topic) {
                        $processed++;
                        $payload = [
                            'title' => $topic->title,
                            'description' => $topic->description,
                            'category' => $topic->category,
                            'keywords' => $topic->keywords ?? [],
                            'target_market' => $topic->target_market ?? 'both',
                            'language' => $topic->language ?? 'id',
                            'priority' => $topic->priority ?? 5,
                            'generation_notes' => $topic->generation_notes,
                        ];

                        $normalized = $this->normalizeTopicPayload($payload, true);
                        $dirty = [];
                        foreach ($normalized as $key => $value) {
                            if ($topic->{$key} != $value) {
                                $dirty[$key] = $value;
                            }
                        }

                        if (! empty($dirty)) {
                            $topic->update($dirty);
                            $updated++;
                        }
                    }
                });

                $message = "Normalisasi selesai: {$updated} dari {$processed} topic diperbarui.";
                break;
        }

        return back()->with('success', $message);
    }

    private function normalizeTopicPayload(array $payload, bool $autoDetect = false): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $titleLower = Str::lower($title);

        $detectedCategory = $this->detectCategoryFromTitle($titleLower);
        if ($autoDetect || empty($payload['category']) || $payload['category'] === 'general') {
            $payload['category'] = $detectedCategory;
        }

        $payload['target_market'] = $this->detectTargetMarketFromTitle(
            $titleLower,
            (string) ($payload['target_market'] ?? 'both'),
            $autoDetect
        );

        if (empty($payload['description'])) {
            $payload['description'] = $this->buildDescription($title, $payload['category'], $payload['target_market']);
        }

        $keywords = is_array($payload['keywords'] ?? null) ? $payload['keywords'] : [];
        $payload['keywords'] = $this->enrichKeywords($keywords, $title, $payload['category']);

        if (empty($payload['generation_notes'])) {
            $payload['generation_notes'] = $this->buildGenerationNotes($payload['category'], $payload['target_market']);
        }

        if (empty($payload['priority']) || (int) $payload['priority'] < 1) {
            $payload['priority'] = $this->defaultPriorityByCategory($payload['category']);
        }

        if (($payload['category'] ?? null) === 'guide') {
            $payload['category'] = 'tips';
        }

        return $payload;
    }

    private function detectCategoryFromTitle(string $titleLower): string
    {
        if (Str::contains($titleLower, ['studi kasus', 'case study'])) {
            return 'case-study';
        }

        if (Str::contains($titleLower, ['breaking news', 'berita', 'tren '])) {
            return 'news';
        }

        if (Str::contains($titleLower, ['regulasi', 'peraturan', 'omnibus', 'pp ', 'permen'])) {
            return 'regulation';
        }

        if (Str::contains($titleLower, ['panduan', 'cara ', 'checklist', 'tips', 'strategi', 'optimalisasi', 'perbedaan'])) {
            return 'tips';
        }

        return 'general';
    }

    private function detectTargetMarketFromTitle(string $titleLower, string $current, bool $autoDetect): string
    {
        if (Str::contains($titleLower, ['umkm', 'iumk', 'daerah'])) {
            return 'local';
        }

        if (Str::contains($titleLower, ['pma', 'investasi asing', 'foreign'])) {
            return 'pma';
        }

        return $autoDetect ? 'both' : $current;
    }

    private function buildDescription(string $title, string $category, string $market): string
    {
        $marketLabel = $market === 'local' ? 'pasar lokal/UMKM' : ($market === 'pma' ? 'investor PMA' : 'pasar lokal dan PMA');

        return "Artikel {$category} tentang {$title} dengan fokus praktis untuk {$marketLabel}.";
    }

    private function enrichKeywords(array $keywords, string $title, string $category): array
    {
        $result = collect($keywords)
            ->filter(fn ($k) => is_string($k) && trim($k) !== '')
            ->map(fn ($k) => trim($k))
            ->values();

        $titleTokens = collect(preg_split('/[^\p{L}\p{N}]+/u', Str::lower($title)))
            ->filter(fn ($w) => mb_strlen($w) >= 4)
            ->reject(fn ($w) => in_array($w, ['yang', 'untuk', 'dengan', 'dalam', 'pada', 'dari', 'dan', 'atau']))
            ->take(6)
            ->values();

        $categoryKeywords = collect([
            'tips' => ['panduan praktis', 'langkah perizinan'],
            'regulation' => ['update regulasi', 'kepatuhan usaha'],
            'news' => ['berita perizinan', 'tren kebijakan'],
            'case-study' => ['studi kasus bisnis', 'best practice'],
            'general' => ['informasi perizinan', 'edukasi bisnis'],
        ][$category] ?? ['informasi perizinan']);

        $result = $result
            ->merge($titleTokens)
            ->merge($categoryKeywords)
            ->unique(fn ($v) => Str::lower($v))
            ->take(8)
            ->values();

        if ($result->count() < 3) {
            $result = $result->merge(['perizinan usaha', 'konsultasi bisnis'])->unique()->take(5)->values();
        }

        return $result->all();
    }

    private function buildGenerationNotes(string $category, string $market): string
    {
        $tone = $category === 'regulation' ? 'formal, akurat, dan berbasis aturan terbaru' : 'praktis, jelas, dan actionable';
        $audience = $market === 'local' ? 'UMKM dan bisnis lokal' : ($market === 'pma' ? 'investor dan perusahaan PMA' : 'UMKM dan PMA');

        return "Gunakan gaya {$tone}; target pembaca: {$audience}; sertakan checklist, estimasi waktu, dan risiko umum.";
    }

    private function defaultPriorityByCategory(string $category): int
    {
        return [
            'regulation' => 8,
            'news' => 7,
            'tips' => 6,
            'case-study' => 6,
            'general' => 5,
        ][$category] ?? 5;
    }
}
