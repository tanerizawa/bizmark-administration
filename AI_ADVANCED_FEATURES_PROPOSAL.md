# AI Document Advanced Features - Proposal

## 🎯 Executive Summary

Proposal pengembangan fitur AI dokumen yang lebih canggih untuk sistem Bizmark.id, dengan fokus pada:
- **Version Control & Diff** - Track semua perubahan dokumen
- **AI Quality Checker** - Validasi otomatis sesuai standar
- **Collaborative Editing** - Multi-user dengan comment system
- **Smart Document Assembly** - Generate dokumen dari multiple sources
- **Compliance Checker** - Validasi sesuai regulasi pemerintah

---

## 📋 Current State Analysis

### Existing Features (Implemented)
✅ AI Paraphrasing dengan OpenRouter (Grok)
✅ Template-based document generation
✅ Draft management (CRUD)
✅ Approval workflow (draft → reviewed → approved → rejected)
✅ DOCX & PDF export
✅ Soft delete dengan audit trail
✅ Project context integration

### Gaps & Opportunities
❌ No version history
❌ No document comparison
❌ No collaborative features
❌ No quality scoring
❌ No section-level AI processing
❌ No compliance validation
❌ No change tracking

---

## 🚀 Proposed Advanced Features

### 1. **Document Version Control** ⭐⭐⭐⭐⭐
**Priority**: CRITICAL

#### Features:
- Automatic version snapshot on every save
- Version comparison (side-by-side diff)
- Rollback to previous version
- Branch & merge untuk revisi besar
- Visual diff highlighting (additions/deletions)

#### Database Schema:
```sql
CREATE TABLE document_versions (
    id BIGSERIAL PRIMARY KEY,
    draft_id BIGINT NOT NULL REFERENCES document_drafts(id),
    version_number INTEGER NOT NULL,
    title VARCHAR(255),
    content TEXT,
    sections JSONB,
    change_summary TEXT, -- AI-generated summary of changes
    changed_by BIGINT REFERENCES users(id),
    change_type VARCHAR(50), -- 'auto_save', 'manual_save', 'ai_regenerate'
    diff_stats JSONB, -- {additions: 100, deletions: 50, changes: 25}
    created_at TIMESTAMP,
    UNIQUE(draft_id, version_number)
);

CREATE TABLE document_diffs (
    id BIGSERIAL PRIMARY KEY,
    version_id BIGINT REFERENCES document_versions(id),
    section VARCHAR(255),
    old_content TEXT,
    new_content TEXT,
    diff_html TEXT, -- Pre-rendered HTML diff
    change_type VARCHAR(20), -- 'addition', 'deletion', 'modification'
    created_at TIMESTAMP
);
```

#### Implementation:
```php
// Auto-version on save
class DocumentDraft extends Model
{
    protected static function booted()
    {
        static::updated(function ($draft) {
            $draft->createVersion();
        });
    }

    public function createVersion()
    {
        $lastVersion = $this->versions()->latest()->first();
        $newVersionNumber = $lastVersion ? $lastVersion->version_number + 1 : 1;

        $version = DocumentVersion::create([
            'draft_id' => $this->id,
            'version_number' => $newVersionNumber,
            'title' => $this->title,
            'content' => $this->content,
            'sections' => $this->sections,
            'changed_by' => Auth::id(),
            'change_type' => 'manual_save',
        ]);

        // Generate diff asynchronously
        GenerateDiffJob::dispatch($version->id);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function compareWith($versionId)
    {
        $targetVersion = $this->versions()->findOrFail($versionId);
        return DocumentDiffService::compare($this, $targetVersion);
    }
}
```

#### UI Components:
```blade
<!-- Version History Sidebar -->
<div class="version-history">
    <h3>Version History</h3>
    @foreach($draft->versions as $version)
    <div class="version-item">
        <span class="version-number">v{{ $version->version_number }}</span>
        <span class="timestamp">{{ $version->created_at->diffForHumans() }}</span>
        <div class="actions">
            <button onclick="showDiff({{ $version->id }})">Compare</button>
            <button onclick="restore({{ $version->id }})">Restore</button>
        </div>
    </div>
    @endforeach
</div>

<!-- Side-by-Side Diff View -->
<div class="diff-view">
    <div class="diff-old">
        <h4>Version {{ $oldVersion->version_number }}</h4>
        {!! $diffHtml->old !!}
    </div>
    <div class="diff-new">
        <h4>Version {{ $newVersion->version_number }} (Current)</h4>
        {!! $diffHtml->new !!}
    </div>
</div>
```

---

### 2. **AI Quality Checker** ⭐⭐⭐⭐⭐
**Priority**: HIGH

#### Features:
- Grammar & spelling check (Bahasa Indonesia)
- Consistency checker (terminology, dates, numbers)
- Completeness score (semua section terisi?)
- Compliance validation (sesuai format pemerintah?)
- Readability score
- Citation validator
- Cross-reference checker

#### Implementation:
```php
class AIQualityChecker
{
    public function analyze(DocumentDraft $draft): array
    {
        return [
            'grammar' => $this->checkGrammar($draft->content),
            'consistency' => $this->checkConsistency($draft),
            'completeness' => $this->checkCompleteness($draft),
            'compliance' => $this->checkCompliance($draft),
            'readability' => $this->calculateReadability($draft->content),
            'overall_score' => $this->calculateOverallScore(),
            'suggestions' => $this->generateSuggestions(),
        ];
    }

    protected function checkGrammar(string $content): array
    {
        // Use OpenRouter AI for grammar check
        $prompt = "Periksa tata bahasa dan ejaan dalam teks berikut (Bahasa Indonesia). Berikan skor 0-100 dan list kesalahan:\n\n" . $content;
        
        $result = $this->openRouter->analyze($prompt);
        
        return [
            'score' => $result['score'],
            'errors' => $result['errors'],
            'suggestions' => $result['suggestions'],
        ];
    }

    protected function checkConsistency(DocumentDraft $draft): array
    {
        $issues = [];
        
        // Check date consistency
        preg_match_all('/\d{1,2}\s+\w+\s+\d{4}/', $draft->content, $dates);
        if (count(array_unique($dates[0])) > 3) {
            $issues[] = [
                'type' => 'date_inconsistency',
                'message' => 'Banyak format tanggal berbeda ditemukan',
                'locations' => $dates[0],
            ];
        }

        // Check project name consistency
        $projectName = $draft->project->name;
        $variations = $this->findProjectNameVariations($draft->content, $projectName);
        if (count($variations) > 1) {
            $issues[] = [
                'type' => 'project_name_variation',
                'message' => 'Nama proyek tidak konsisten',
                'variations' => $variations,
            ];
        }

        return [
            'score' => $this->calculateConsistencyScore($issues),
            'issues' => $issues,
        ];
    }

    protected function checkCompleteness(DocumentDraft $draft): array
    {
        $requiredSections = $draft->template->required_sections ?? [];
        $missingSections = [];

        foreach ($requiredSections as $section) {
            if (!$this->sectionExists($draft->content, $section)) {
                $missingSections[] = $section;
            }
        }

        return [
            'score' => (1 - count($missingSections) / count($requiredSections)) * 100,
            'missing_sections' => $missingSections,
            'total_required' => count($requiredSections),
        ];
    }

    protected function checkCompliance(DocumentDraft $draft): array
    {
        // Check compliance dengan format pemerintah
        $rules = [
            'has_cover_page' => $this->hasCoverPage($draft->content),
            'has_toc' => $this->hasTableOfContents($draft->content),
            'has_executive_summary' => $this->hasExecutiveSummary($draft->content),
            'proper_numbering' => $this->hasProperNumbering($draft->content),
            'proper_formatting' => $this->hasProperFormatting($draft->content),
        ];

        $passed = count(array_filter($rules));
        $total = count($rules);

        return [
            'score' => ($passed / $total) * 100,
            'rules' => $rules,
            'non_compliant' => array_keys(array_filter($rules, fn($v) => !$v)),
        ];
    }

    protected function calculateReadability(string $content): array
    {
        // Flesch Reading Ease (adapted for Indonesian)
        $sentences = preg_split('/[.!?]+/', $content);
        $words = str_word_count($content);
        $syllables = $this->countSyllables($content);

        $avgWordsPerSentence = $words / count($sentences);
        $avgSyllablesPerWord = $syllables / $words;

        $score = 206.835 - 1.015 * $avgWordsPerSentence - 84.6 * $avgSyllablesPerWord;

        return [
            'score' => max(0, min(100, $score)),
            'level' => $this->getReadabilityLevel($score),
            'avg_words_per_sentence' => $avgWordsPerSentence,
            'avg_syllables_per_word' => $avgSyllablesPerWord,
        ];
    }
}
```

#### UI:
```blade
<div class="quality-dashboard">
    <div class="quality-score-circle">
        <div class="score">{{ $quality->overall_score }}</div>
        <div class="label">Quality Score</div>
    </div>

    <div class="quality-metrics">
        <div class="metric">
            <span class="icon">📝</span>
            <div class="details">
                <div class="score">{{ $quality->grammar['score'] }}/100</div>
                <div class="label">Grammar</div>
            </div>
        </div>

        <div class="metric">
            <span class="icon">🎯</span>
            <div class="details">
                <div class="score">{{ $quality->consistency['score'] }}/100</div>
                <div class="label">Consistency</div>
            </div>
        </div>

        <div class="metric">
            <span class="icon">✅</span>
            <div class="details">
                <div class="score">{{ $quality->completeness['score'] }}/100</div>
                <div class="label">Completeness</div>
            </div>
        </div>

        <div class="metric">
            <span class="icon">⚖️</span>
            <div class="details">
                <div class="score">{{ $quality->compliance['score'] }}/100</div>
                <div class="label">Compliance</div>
            </div>
        </div>

        <div class="metric">
            <span class="icon">📖</span>
            <div class="details">
                <div class="score">{{ $quality->readability['score'] }}/100</div>
                <div class="label">Readability</div>
            </div>
        </div>
    </div>

    <div class="suggestions">
        <h4>AI Suggestions</h4>
        @foreach($quality->suggestions as $suggestion)
        <div class="suggestion-item">
            <span class="icon">💡</span>
            <div class="content">
                <div class="issue">{{ $suggestion->issue }}</div>
                <div class="fix">{{ $suggestion->recommendation }}</div>
                <button onclick="applyFix({{ $suggestion->id }})">Apply Fix</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
```

---

### 3. **Collaborative Editing & Comments** ⭐⭐⭐⭐
**Priority**: MEDIUM-HIGH

#### Features:
- Inline comments pada section tertentu
- Comment threads (reply & resolve)
- @mention users untuk notification
- Real-time collaboration indicator
- Activity feed (who edited what when)

#### Database Schema:
```sql
CREATE TABLE document_comments (
    id BIGSERIAL PRIMARY KEY,
    draft_id BIGINT NOT NULL REFERENCES document_drafts(id),
    parent_id BIGINT REFERENCES document_comments(id), -- for replies
    user_id BIGINT NOT NULL REFERENCES users(id),
    section VARCHAR(255), -- which section this comment is on
    quote_text TEXT, -- highlighted text being commented
    comment_text TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'open', -- 'open', 'resolved', 'archived'
    resolved_by BIGINT REFERENCES users(id),
    resolved_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE document_collaborators (
    id BIGSERIAL PRIMARY KEY,
    draft_id BIGINT NOT NULL REFERENCES document_drafts(id),
    user_id BIGINT NOT NULL REFERENCES users(id),
    role VARCHAR(20), -- 'editor', 'reviewer', 'viewer'
    invited_by BIGINT REFERENCES users(id),
    invited_at TIMESTAMP,
    last_seen_at TIMESTAMP,
    UNIQUE(draft_id, user_id)
);
```

#### Implementation:
```php
class CommentController extends Controller
{
    public function store(Request $request, $draftId)
    {
        $validated = $request->validate([
            'section' => 'nullable|string',
            'quote_text' => 'nullable|string',
            'comment_text' => 'required|string',
            'parent_id' => 'nullable|exists:document_comments,id',
        ]);

        $comment = DocumentComment::create([
            'draft_id' => $draftId,
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        // Notify mentioned users
        $this->notifyMentionedUsers($comment);

        return response()->json($comment->load('user'));
    }

    protected function notifyMentionedUsers(DocumentComment $comment)
    {
        preg_match_all('/@(\w+)/', $comment->comment_text, $mentions);
        
        foreach ($mentions[1] as $username) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->notify(new DocumentCommentMention($comment));
            }
        }
    }
}
```

---

### 4. **Section-by-Section AI Processing** ⭐⭐⭐⭐
**Priority**: MEDIUM-HIGH

#### Features:
- Process dokumen per section (BAB I, BAB II, etc)
- Independent AI calls untuk setiap section
- Progress tracking per section
- Selective regeneration (regenerate hanya 1 section)
- Section-specific prompts

#### Implementation:
```php
class SectionAIProcessor
{
    public function processDocument(DocumentDraft $draft, DocumentTemplate $template)
    {
        $sections = $this->extractSections($template->content);
        
        foreach ($sections as $index => $section) {
            ProcessSectionJob::dispatch(
                $draft->id,
                $section,
                $index
            )->onQueue('sections');
        }
    }

    protected function extractSections(string $content): array
    {
        // Extract BAB I, BAB II, etc
        $pattern = '/(?:BAB\s+[IVX]+|CHAPTER\s+\d+|SECTION\s+\d+).*?(?=BAB\s+[IVX]+|CHAPTER\s+\d+|SECTION\s+\d+|$)/s';
        preg_match_all($pattern, $content, $matches);
        
        return $matches[0];
    }

    public function regenerateSection(DocumentDraft $draft, string $sectionName)
    {
        $template = $draft->template;
        $sections = $this->extractSections($template->content);
        
        $sectionContent = collect($sections)->first(function($section) use ($sectionName) {
            return str_contains($section, $sectionName);
        });

        if (!$sectionContent) {
            throw new \Exception("Section not found: $sectionName");
        }

        $result = $this->openRouter->paraphraseDocument(
            $sectionContent,
            $draft->project->context
        );

        // Update only this section in the draft
        $this->updateDraftSection($draft, $sectionName, $result['full_text']);
    }
}
```

---

### 5. **Smart Document Assembly** ⭐⭐⭐⭐
**Priority**: MEDIUM

#### Features:
- Combine multiple documents into one
- Smart merge dengan conflict resolution
- Extract sections from multiple sources
- Template mixing (use BAB I from template A, BAB II from template B)
- Reference management

---

### 6. **AI-Powered Document Analysis** ⭐⭐⭐
**Priority**: MEDIUM

#### Features:
- Summarization (executive summary generation)
- Key points extraction
- Risk identification
- Cost estimation extraction
- Timeline extraction
- Stakeholder identification

#### Use Case:
```php
$analysis = AIAnalyzer::analyze($draft);

// Output:
[
    'summary' => '...',
    'key_points' => ['Point 1', 'Point 2', ...],
    'risks' => [
        ['type' => 'environmental', 'description' => '...', 'severity' => 'high'],
        ...
    ],
    'timeline' => [
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
        'milestones' => [...],
    ],
    'costs' => [
        'total_estimated' => 5000000000,
        'breakdown' => [...],
    ],
]
```

---

## 🎨 Enhanced UI/UX Features

### 1. **Rich Text Editor**
- Upgrade to TipTap or Quill.js
- Inline formatting toolbar
- Image upload
- Table editor
- Code blocks
- Equation editor

### 2. **Split View**
- Template on left, draft on right
- Synchronized scrolling
- Click template section to edit corresponding draft section

### 3. **AI Assistant Sidebar**
- Contextual suggestions
- Quick actions (expand, shorten, rephrase)
- Translation (English ↔ Indonesian)
- Style improvements

### 4. **Smart Search**
- Full-text search across all drafts
- Filter by status, date, author
- Search within comments
- AI-powered semantic search

---

## 📊 Implementation Roadmap

### Phase 1: Foundation (Week 1-2)
- [ ] Document version control
- [ ] Basic diff viewer
- [ ] Version restore functionality

### Phase 2: Quality & Validation (Week 3-4)
- [ ] AI Quality Checker
- [ ] Compliance validator
- [ ] Completeness checker

### Phase 3: Collaboration (Week 5-6)
- [ ] Comment system
- [ ] Collaborator management
- [ ] Notification system

### Phase 4: Advanced AI (Week 7-8)
- [ ] Section-by-section processing
- [ ] AI document analysis
- [ ] Smart suggestions

### Phase 5: Polish & Optimize (Week 9-10)
- [ ] Performance optimization
- [ ] UI/UX improvements
- [ ] Testing & bug fixes

---

## 💰 Cost Estimation

### Development Time: 10 weeks
### Team Size: 2-3 developers

### API Costs (OpenRouter):
- Grammar check: ~$0.001 per document
- Quality analysis: ~$0.002 per document
- Section regeneration: ~$0.005 per section
- **Estimated monthly cost**: $50-200 (depending on usage)

---

## 🎯 Expected Benefits

### For Users:
✅ Save 60% time on document editing
✅ Reduce errors by 80%
✅ Improve compliance rate to 95%+
✅ Enable real-time collaboration

### For Business:
✅ Faster project turnaround
✅ Higher client satisfaction
✅ Reduced rework
✅ Competitive advantage

---

## 🔧 Technical Requirements

### Backend:
- Laravel 12+
- PostgreSQL with JSONB support
- Redis for caching & queues
- Websockets for real-time features (Laravel Echo + Pusher)

### Frontend:
- Alpine.js or Vue.js for interactivity
- TipTap editor
- Diff viewer library (jsdiff)

### AI:
- OpenRouter API (Grok or Claude)
- Fallback to local NLP for basic checks

---

## 📝 Next Steps

1. **Review & Approve** proposal ini
2. **Prioritize** features (mana yang paling urgent?)
3. **Prototype** version control & diff viewer (proof of concept)
4. **Test** dengan real UKL-UPL document
5. **Iterate** based on feedback

---

**Status**: 📋 Awaiting Approval
**Created**: November 3, 2025
**Last Updated**: November 3, 2025
