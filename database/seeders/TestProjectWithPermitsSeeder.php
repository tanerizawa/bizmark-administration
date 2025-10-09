<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Institution;
use App\Models\PermitType;
use App\Models\ProjectPermit;
use App\Models\ProjectPermitDependency;

class TestProjectWithPermitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create test project
        $status = ProjectStatus::where('name', 'Proses di DLH')->first();
        $institution = Institution::first();
        
        $project = Project::create([
            'name' => 'Test Project - Pembangunan Pabrik Kelapa Sawit',
            'client_name' => 'PT Nusantara Agro Resources',
            'client_contact' => '0812-3456-7890',
            'description' => 'Pembangunan pabrik pengolahan kelapa sawit kapasitas 60 ton/jam di Kalimantan Tengah',
            'start_date' => now(),
            'budget' => 500000000, // Rp 500 juta
            'status_id' => $status->id,
            'institution_id' => $institution->id,
        ]);

        // Get permit types
        $permitTypes = PermitType::limit(5)->get();
        
        if ($permitTypes->count() < 5) {
            $this->command->error('Not enough permit types. Please run PermitTypeSeeder first.');
            return;
        }

        // Create permits with sequence
        $permit1 = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitTypes[0]->id,
            'sequence_order' => 1,
            'status' => 'approved',
            'is_goal_permit' => false,
            'notes' => $permitTypes[0]->name . ' sudah disetujui',
        ]);

        $permit2 = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitTypes[1]->id,
            'sequence_order' => 2,
            'status' => 'approved',
            'is_goal_permit' => false,
            'notes' => $permitTypes[1]->name . ' telah disahkan',
        ]);

        $permit3 = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitTypes[2]->id,
            'sequence_order' => 3,
            'status' => 'in_progress',
            'is_goal_permit' => false,
            'notes' => 'Dokumen teknis ' . $permitTypes[2]->name . ' sedang disiapkan',
        ]);

        $permit4 = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitTypes[3]->id,
            'sequence_order' => 4,
            'status' => 'not_started',
            'is_goal_permit' => true, // Goal permit
            'notes' => 'Ini adalah izin utama yang menjadi tujuan akhir',
        ]);

        $permit5 = ProjectPermit::create([
            'project_id' => $project->id,
            'permit_type_id' => $permitTypes[4]->id,
            'sequence_order' => 5,
            'status' => 'not_started',
            'is_goal_permit' => false,
            'notes' => null,
        ]);

        // Create dependencies
        // Permit 2 depends on Permit 1 (MANDATORY)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit2->id,
            'depends_on_permit_id' => $permit1->id,
            'can_proceed_without' => false, // MANDATORY
        ]);

        // Permit 3 depends on Permit 2 (MANDATORY)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit3->id,
            'depends_on_permit_id' => $permit2->id,
            'can_proceed_without' => false,
        ]);

        // Permit 4 depends on Permit 1 (MANDATORY)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit4->id,
            'depends_on_permit_id' => $permit1->id,
            'can_proceed_without' => false,
        ]);

        // Permit 4 depends on Permit 3 (MANDATORY)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit4->id,
            'depends_on_permit_id' => $permit3->id,
            'can_proceed_without' => false,
        ]);

        // Permit 5 depends on Permit 4 (MANDATORY)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit5->id,
            'depends_on_permit_id' => $permit4->id,
            'can_proceed_without' => false,
        ]);

        // Permit 5 depends on Permit 2 (OPTIONAL)
        ProjectPermitDependency::create([
            'project_permit_id' => $permit5->id,
            'depends_on_permit_id' => $permit2->id,
            'can_proceed_without' => true, // OPTIONAL
        ]);

        $this->command->info('✅ Test project created with ' . $project->permits->count() . ' permits and dependencies');
        $this->command->info('📍 Project: ' . $project->name);
        $this->command->info('🔗 Dependencies created for testing canStart() logic');
    }
}
