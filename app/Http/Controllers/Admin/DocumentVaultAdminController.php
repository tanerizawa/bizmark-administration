<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentVaultAdminController extends Controller
{
    /**
     * Toggle visibilitas dokumen di client vault.
     */
    public function toggleVisibility(Document $document)
    {
        $document->update(['client_visible' => ! $document->client_visible]);

        $status = $document->client_visible ? 'ditampilkan' : 'disembunyikan';

        return back()->with('success', "Dokumen \"{$document->title}\" berhasil {$status} dari vault klien.");
    }

    /**
     * Update metadata vault (kategori, nomor, tanggal terbit/expire).
     */
    public function updateMeta(Request $request, Document $document)
    {
        $validated = $request->validate([
            'vault_category' => ['nullable', 'in:izin_utama,dokumen_pendukung,laporan,sertifikat,lainnya'],
            'document_number' => ['nullable', 'string', 'max:150'],
            'document_issued_at' => ['nullable', 'date'],
            'document_expires_at' => ['nullable', 'date'],
        ]);

        $document->update($validated);

        return back()->with('success', 'Metadata vault dokumen berhasil diperbarui.');
    }
}
