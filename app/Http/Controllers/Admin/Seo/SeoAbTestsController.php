<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use App\Models\MetaAbTest;
use App\Services\MetaAbTestService;
use Illuminate\Http\Request;

class SeoAbTestsController extends Controller
{

    /**
     * Meta A/B Tests management
     */
    public function abTests(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $query = MetaAbTest::with('article:id,title,slug,views_count');

        if ($filter === 'running') {
            $query->where('status', 'running');
        } elseif ($filter === 'completed') {
            $query->where('status', 'completed');
        }

        $tests = $query->orderByDesc('created_at')->paginate(20);

        $summary = [
            'running' => MetaAbTest::where('status', 'running')->count(),
            'completed' => MetaAbTest::where('status', 'completed')->count(),
            'b_wins' => MetaAbTest::where('winner', 'b')->count(),
            'a_wins' => MetaAbTest::where('winner', 'a')->count(),
            'inconclusive' => MetaAbTest::where('winner', 'inconclusive')->count(),
        ];

        return view('admin.seo.ab-tests', compact('tests', 'summary', 'filter'));
    }

    /**
     * Evaluate a single A/B test
     */
    public function evaluateAbTest(int $id, MetaAbTestService $service)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Test sudah selesai.');
        }

        $totalImpressions = $test->variant_a_impressions + $test->variant_b_impressions;
        if ($totalImpressions < 2) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Data belum cukup untuk evaluasi. Masukkan data impressions & clicks terlebih dahulu.');
        }

        // Force evaluate this test regardless of age
        $ctrA = $test->ctr_a;
        $ctrB = $test->ctr_b;
        $confidence = $this->calculateAbTestConfidence($test);

        if ($confidence >= 90) {
            $winner = $ctrB > $ctrA ? 'b' : 'a';
        } elseif ($confidence >= 70) {
            $winner = $ctrB > $ctrA ? 'b' : 'a';
        } else {
            $winner = 'inconclusive';
        }

        $test->update([
            'winner' => $winner,
            'confidence' => $confidence,
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        $winnerLabel = $winner === 'b' ? 'B (AI)' : ($winner === 'a' ? 'A (Original)' : 'Inconclusive');
        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$test->id} dievaluasi: Winner = {$winnerLabel} (confidence {$confidence}%)");
    }

    /**
     * Stop/cancel a running A/B test
     */
    public function stopAbTest(int $id)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Test tidak sedang berjalan.');
        }

        $test->update([
            'status' => 'completed',
            'winner' => 'inconclusive',
            'ended_at' => now(),
        ]);

        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$test->id} dihentikan.");
    }

    /**
     * Apply winning variant B to the article's meta tags
     */
    public function applyAbTestWinner(int $id)
    {
        $test = MetaAbTest::with('article')->findOrFail($id);

        if ($test->winner !== 'b') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Hanya variant B (AI) yang bisa diterapkan.');
        }

        $article = $test->article;
        if (!$article) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Artikel tidak ditemukan.');
        }

        if ($test->variant_b_title) {
            $article->meta_title = $test->variant_b_title;
        }
        if ($test->variant_b_description) {
            $article->meta_description = $test->variant_b_description;
        }
        $article->save();

        return redirect()->route('admin.seo.ab-tests')->with('success', "Meta tags variant B berhasil diterapkan ke artikel \"{$article->title}\"");
    }

    /**
     * Update impressions/clicks data for a running test (manual input)
     */
    public function updateAbTestData(Request $request, int $id)
    {
        $test = MetaAbTest::findOrFail($id);

        if ($test->status !== 'running') {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Hanya test yang running bisa diupdate datanya.');
        }

        $validated = $request->validate([
            'variant_a_impressions' => 'required|integer|min:0',
            'variant_a_clicks' => 'required|integer|min:0',
            'variant_b_impressions' => 'required|integer|min:0',
            'variant_b_clicks' => 'required|integer|min:0',
        ]);

        // Ensure clicks <= impressions
        if ($validated['variant_a_clicks'] > $validated['variant_a_impressions'] ||
            $validated['variant_b_clicks'] > $validated['variant_b_impressions']) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Clicks tidak boleh lebih besar dari impressions.');
        }

        $test->update($validated);

        return redirect()->route('admin.seo.ab-tests')->with('success', "Data test #{$test->id} berhasil diupdate.");
    }

    /**
     * Delete an A/B test
     */
    public function deleteAbTest(int $id)
    {
        $test = MetaAbTest::findOrFail($id);
        $testId = $test->id;
        $test->delete();

        return redirect()->route('admin.seo.ab-tests')->with('success', "Test #{$testId} berhasil dihapus.");
    }

    /**
     * Evaluate all running tests at once
     */
    public function evaluateAllAbTests(MetaAbTestService $service)
    {
        $results = $service->evaluateTests();
        $count = count($results);

        if ($count === 0) {
            return redirect()->route('admin.seo.ab-tests')->with('error', 'Tidak ada test yang siap dievaluasi (butuh min 7 hari & 100 impressions).');
        }

        return redirect()->route('admin.seo.ab-tests')->with('success', "{$count} test berhasil dievaluasi.");
    }

    /**
     * Calculate confidence for single test evaluation
     */
    private function calculateAbTestConfidence(MetaAbTest $test): float
    {
        $nA = max($test->variant_a_impressions, 1);
        $nB = max($test->variant_b_impressions, 1);
        $pA = $test->variant_a_clicks / $nA;
        $pB = $test->variant_b_clicks / $nB;

        $pPool = ($test->variant_a_clicks + $test->variant_b_clicks) / ($nA + $nB);
        $se = sqrt($pPool * (1 - $pPool) * (1 / $nA + 1 / $nB));

        if ($se == 0) return 0;

        $z = abs($pA - $pB) / $se;

        if ($z >= 2.576) return 99;
        if ($z >= 1.960) return 95;
        if ($z >= 1.645) return 90;
        if ($z >= 1.282) return 80;
        return round(min($z / 1.645 * 90, 89), 1);
    }

    /**
     * Run A/B test generation via web (replaces: php artisan seo:meta-ab-test --all)
     */
    public function runAbTests()
    {
        Artisan::call('seo:meta-ab-test', ['--all' => true]);
        $output = trim(Artisan::output());

        return redirect()->route('admin.seo.ab-tests')->with('success', "A/B test generation selesai.\n{$output}");
    }
}

