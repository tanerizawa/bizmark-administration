<?php

namespace Tests\Feature;

use App\Models\JobVacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HrmJobApplicationTest extends TestCase
{
    use RefreshDatabase;

    private JobVacancy $vacancy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vacancy = JobVacancy::create([
            'title' => 'Staff Konsultan Lingkungan',
            'slug' => 'staff-konsultan-lingkungan',
            'position' => 'Staff',
            'description' => 'Deskripsi posisi konsultan lingkungan.',
            'responsibilities' => 'Tanggung jawab posisi.',
            'qualifications' => 'Kualifikasi minimal S1.',
            'employment_type' => 'full-time',
            'location' => 'Karawang',
            'status' => 'open',
            'deadline' => now()->addMonths(1)->toDateString(),
        ]);
    }

    public function test_career_index_page_is_accessible(): void
    {
        $this->get(route('career.index'))
            ->assertOk();
    }

    public function test_career_show_page_displays_vacancy(): void
    {
        $this->get(route('career.show', $this->vacancy->slug))
            ->assertOk()
            ->assertSee($this->vacancy->title);
    }

    public function test_career_show_returns_404_for_unknown_slug(): void
    {
        $this->get(route('career.show', 'slug-tidak-ada'))
            ->assertNotFound();
    }

    public function test_apply_page_is_accessible(): void
    {
        $this->get(route('career.apply', $this->vacancy->id))
            ->assertOk();
    }

    public function test_submit_application_stores_record(): void
    {
        Storage::fake('public');

        $cv = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $response = $this->post(route('career.apply.store'), [
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'education_level' => 'S1',
            'major' => 'Teknik Lingkungan',
            'institution' => 'Universitas Indonesia',
            'expected_salary' => 6000000,
            'cover_letter' => 'Saya tertarik dengan posisi ini.',
            'cv' => $cv,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('job_applications', [
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);
    }

    public function test_submit_application_requires_cv(): void
    {
        $payload = [
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'education_level' => 'S1',
            'major' => 'Teknik Lingkungan',
            'institution' => 'Universitas Indonesia',
        ];

        $this->post(route('career.apply.store'), $payload)
            ->assertSessionHasErrors('cv');
    }

    public function test_submit_application_requires_valid_vacancy_id(): void
    {
        Storage::fake('public');
        $cv = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $this->post(route('career.apply.store'), [
            'job_vacancy_id' => 99999,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'education_level' => 'S1',
            'major' => 'Teknik Lingkungan',
            'institution' => 'Universitas Indonesia',
            'cv' => $cv,
        ])->assertSessionHasErrors('job_vacancy_id');
    }

    public function test_submit_application_rejects_invalid_education_level(): void
    {
        Storage::fake('public');
        $cv = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $this->post(route('career.apply.store'), [
            'job_vacancy_id' => $this->vacancy->id,
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'education_level' => 'SMA',
            'major' => 'IPA',
            'institution' => 'SMAN 1 Karawang',
            'cv' => $cv,
        ])->assertSessionHasErrors('education_level');
    }
}
