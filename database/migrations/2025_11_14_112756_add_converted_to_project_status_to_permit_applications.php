<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing check constraint
        DB::statement('ALTER TABLE permit_applications DROP CONSTRAINT IF EXISTS permit_applications_status_check');
        
        // Recreate the check constraint with the new status
        DB::statement("
            ALTER TABLE permit_applications 
            ADD CONSTRAINT permit_applications_status_check 
            CHECK (status IN (
                'draft', 'submitted', 'under_review', 'document_incomplete',
                'quoted', 'quotation_accepted', 'quotation_rejected',
                'payment_pending', 'payment_verified', 'converted_to_project',
                'in_progress', 'completed', 'cancelled'
            ))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement('ALTER TABLE permit_applications DROP CONSTRAINT IF EXISTS permit_applications_status_check');
        
        // Recreate the old constraint without converted_to_project
        DB::statement("
            ALTER TABLE permit_applications 
            ADD CONSTRAINT permit_applications_status_check 
            CHECK (status IN (
                'draft', 'submitted', 'under_review', 'document_incomplete',
                'quoted', 'quotation_accepted', 'quotation_rejected',
                'payment_pending', 'payment_verified', 'in_progress',
                'completed', 'cancelled'
            ))
        ");
    }
};
