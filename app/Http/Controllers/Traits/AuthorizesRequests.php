<?php

namespace App\Http\Controllers\Traits;

trait AuthorizesRequests
{
    /**
     * Apply permission middleware to controller actions
     * 
     * @param string $permission Base permission name (e.g., 'projects', 'tasks')
     * @return void
     */
    protected function authorizePermissions(string $permission)
    {
        $this->middleware(function ($request, $next) use ($permission) {
            if (!auth()->user()->can("{$permission}.view")) {
                abort(403, "Anda tidak memiliki akses untuk melihat {$this->getResourceName($permission)}.");
            }
            return $next($request);
        })->only(['index', 'show']);

        $this->middleware(function ($request, $next) use ($permission) {
            if (!auth()->user()->can("{$permission}.create")) {
                abort(403, "Anda tidak memiliki akses untuk membuat {$this->getResourceName($permission)}.");
            }
            return $next($request);
        })->only(['create', 'store']);

        $this->middleware(function ($request, $next) use ($permission) {
            if (!auth()->user()->can("{$permission}.edit")) {
                abort(403, "Anda tidak memiliki akses untuk mengubah {$this->getResourceName($permission)}.");
            }
            return $next($request);
        })->only(['edit', 'update']);

        $this->middleware(function ($request, $next) use ($permission) {
            if (!auth()->user()->can("{$permission}.delete")) {
                abort(403, "Anda tidak memiliki akses untuk menghapus {$this->getResourceName($permission)}.");
            }
            return $next($request);
        })->only(['destroy']);
    }

    /**
     * Apply single permission check to all controller actions
     * 
     * @param string $permission Permission name (e.g., 'settings.manage')
     * @param string $message Error message
     * @return void
     */
    protected function authorizePermission(string $permission, string $message = null)
    {
        $this->middleware(function ($request, $next) use ($permission, $message) {
            if (!auth()->user()->can($permission)) {
                $msg = $message ?? "Anda tidak memiliki akses untuk halaman ini.";
                abort(403, $msg);
            }
            return $next($request);
        });
    }

    /**
     * Get user-friendly resource name in Indonesian
     * 
     * @param string $permission
     * @return string
     */
    private function getResourceName(string $permission): string
    {
        $names = [
            'projects' => 'proyek',
            'tasks' => 'tugas',
            'documents' => 'dokumen',
            'clients' => 'klien',
            'institutions' => 'institusi',
            'invoices' => 'invoice',
            'finances' => 'keuangan',
            'users' => 'pengguna',
            'content' => 'konten',
            'recruitment' => 'rekrutmen',
            'email' => 'email',
            'master_data' => 'master data',
        ];

        return $names[$permission] ?? $permission;
    }
}
