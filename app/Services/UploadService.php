<?php

declare(strict_types=1);

namespace App\Services;

final class UploadService
{
    private const MAX_BYTES = 3_145_728; // 3 MB

    /** @param array<string, mixed> $file */
    public function storeImage(array $file, string $folder = 'products'): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Error al subir el archivo.');
        }

        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('La imagen no puede superar 3 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            throw new \RuntimeException('Formato no permitido. Usá JPG, PNG o WebP.');
        }

        $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
        $destDir = dirname(__DIR__, 2) . '/public/uploads/' . $folder;
        if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
            throw new \RuntimeException('No se pudo crear la carpeta de uploads.');
        }

        $dest = $destDir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('No se pudo guardar la imagen.');
        }

        return 'uploads/' . $folder . '/' . $name;
    }

    public function delete(?string $relativePath): void
    {
        if (!$relativePath || str_contains($relativePath, '..')) {
            return;
        }
        $full = dirname(__DIR__, 2) . '/public/' . ltrim($relativePath, '/');
        if (is_file($full)) {
            unlink($full);
        }
    }
}
