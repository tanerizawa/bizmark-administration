<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->safeIndex('permit_applications', ['status'], 'idx_permit_applications_status');
        $this->safeIndex('permit_applications', ['submitted_at'], 'idx_permit_applications_submitted_at');
        $this->safeIndex('permit_applications', ['client_id'], 'idx_permit_applications_client_id');
        $this->safeIndex('permit_applications', ['permit_type_id'], 'idx_permit_applications_permit_type_id');
        $this->safeIndex('permit_applications', ['application_number'], 'idx_permit_applications_application_number');
        $this->safeIndex('permit_applications', ['status', 'submitted_at'], 'idx_permit_applications_status_submitted_at');

        $this->safeIndex('documents', ['project_id'], 'idx_documents_project_id');
        $this->safeIndex('documents', ['category'], 'idx_documents_category');
        $this->safeIndex('documents', ['created_at'], 'idx_documents_created_at');

        $this->safeIndex('projects', ['client_id'], 'idx_projects_client_id');
        $this->safeIndex('projects', ['status_id'], 'idx_projects_status_id');
        $this->safeIndex('projects', ['created_at'], 'idx_projects_created_at');

        $this->safeIndex('clients', ['status'], 'idx_clients_status');
        $this->safeIndex('clients', ['client_type'], 'idx_clients_client_type');
        $this->safeIndex('clients', ['created_at'], 'idx_clients_created_at');
    }

    public function down(): void
    {
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_status');
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_submitted_at');
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_client_id');
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_permit_type_id');
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_application_number');
        $this->safeDropIndex('permit_applications', 'idx_permit_applications_status_submitted_at');

        $this->safeDropIndex('documents', 'idx_documents_project_id');
        $this->safeDropIndex('documents', 'idx_documents_category');
        $this->safeDropIndex('documents', 'idx_documents_created_at');

        $this->safeDropIndex('projects', 'idx_projects_client_id');
        $this->safeDropIndex('projects', 'idx_projects_status_id');
        $this->safeDropIndex('projects', 'idx_projects_created_at');

        $this->safeDropIndex('clients', 'idx_clients_status');
        $this->safeDropIndex('clients', 'idx_clients_client_type');
        $this->safeDropIndex('clients', 'idx_clients_created_at');
    }

    private function safeIndex(string $table, array $columns, string $name): void
    {
        try {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                $blueprint->index($columns, $name);
            });
        } catch (Throwable $e) {
        }
    }

    private function safeDropIndex(string $table, string $name): void
    {
        try {
            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        } catch (Throwable $e) {
        }
    }
};
