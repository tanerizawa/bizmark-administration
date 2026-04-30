<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Standardizes JSON API responses across all frontend-facing controllers.
 *
 * All responses follow the envelope:
 *   { "success": bool, "message": string, "data": mixed, "meta": mixed }
 *
 * HTTP status codes:
 *   200 — ok / success
 *   201 — created
 *   422 — validation error
 *   401 — unauthenticated
 *   403 — forbidden
 *   404 — not found
 *   500 — server error
 */
trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function validationError(mixed $errors, string $message = 'Validasi gagal. Mohon periksa data Anda.'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    protected function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Silakan login terlebih dahulu'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Anda tidak memiliki akses'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function serverError(string $message = 'Terjadi kesalahan server'): JsonResponse
    {
        return $this->error($message, 500);
    }
}
