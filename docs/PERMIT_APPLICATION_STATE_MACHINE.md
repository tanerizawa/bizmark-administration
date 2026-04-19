# Permit Application State Machine

Dokumen ini mendefinisikan state machine untuk `PermitApplication.status`, termasuk aturan bisnis dan transisi yang diizinkan. Semua perubahan status wajib melalui [PermitApplicationWorkflowService.php](file:///home/bizmark/bizmark.id/app/Services/PermitApplicationWorkflowService.php).

## Status

- `draft`
- `submitted`
- `under_review`
- `document_incomplete`
- `quoted`
- `quotation_accepted`
- `payment_pending`
- `payment_verified`
- `converted_to_project`
- `in_progress`
- `completed`
- `cancelled`

## Diagram (Mermaid)

```mermaid
stateDiagram-v2
    [*] --> draft
    draft --> submitted
    draft --> cancelled

    submitted --> under_review
    submitted --> document_incomplete
    submitted --> cancelled

    under_review --> document_incomplete
    under_review --> quoted
    under_review --> cancelled

    document_incomplete --> under_review
    document_incomplete --> cancelled

    quoted --> quotation_accepted
    quoted --> under_review
    quoted --> cancelled

    quotation_accepted --> payment_pending
    quotation_accepted --> payment_verified
    quotation_accepted --> cancelled

    payment_pending --> payment_verified
    payment_pending --> cancelled

    payment_verified --> converted_to_project
    payment_verified --> in_progress
    payment_verified --> cancelled

    converted_to_project --> in_progress
    converted_to_project --> cancelled

    in_progress --> completed
    in_progress --> cancelled

    completed --> [*]
    cancelled --> [*]
```

## Aturan bisnis per status (ringkas)

- `draft`: dibuat oleh client; bisa diedit; belum dianggap sebagai permohonan resmi.
- `submitted`: permohonan resmi masuk; admin boleh mulai review.
- `under_review`: admin melakukan pemeriksaan data/dokumen.
- `document_incomplete`: admin meminta revisi/kelengkapan dokumen.
- `quoted`: quotation sudah dibuat; menunggu keputusan client.
- `quotation_accepted`: client menerima quotation; dapat lanjut ke payment (manual/gateway).
- `payment_pending`: client mengunggah bukti manual / menunggu settlement gateway.
- `payment_verified`: pembayaran terverifikasi (manual/gateway success).
- `converted_to_project`: aplikasi sudah dibuatkan `Project` dan terhubung ke `project_id`.
- `in_progress`: tim menjalankan pekerjaan.
- `completed`: layanan selesai.
- `cancelled`: permohonan dibatalkan (oleh client/admin/system sesuai aturan).

