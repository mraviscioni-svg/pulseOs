<?php

declare(strict_types=1);

namespace App\Services;

final class ListExportService
{
    /**
     * @param list<string> $headers
     * @param list<list<string|int|float>> $rows
     */
    public function csv(string $filename, array $headers, array $rows): never
    {
        $safe = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $filename) ?: 'export';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $safe . '.csv"');
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');
        if ($out === false) {
            exit;
        }
        fputcsv($out, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($out, array_map('strval', $row), ';');
        }
        fclose($out);
        exit;
    }

    /**
     * @param list<string> $headers
     * @param list<list<string|int|float>> $rows
     */
    public function pdf(string $title, array $headers, array $rows, string $subtitle = ''): never
    {
        $tenant = (string) (\App\Core\Session::get('tenant_name') ?: 'PulseOS');
        ob_start();
        require dirname(__DIR__) . '/views/export/table-pdf.php';
        $html = (string) ob_get_clean();

        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        exit;
    }
}
