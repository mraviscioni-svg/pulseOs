<?php

declare(strict_types=1);

namespace App\Controllers\Concerns;

use App\Services\ListExportService;

trait ExportableList
{
    /**
     * @param list<string> $headers
     * @param list<list<string|int|float>> $rows
     */
    protected function maybeExportList(string $title, array $headers, array $rows, string $filename): bool
    {
        $export = strtolower((string) ($_GET['export'] ?? ''));
        if ($export === '') {
            return false;
        }

        $service = new ListExportService();
        $subtitle = 'Exportado desde PulseOS';

        if ($export === 'csv' || $export === 'excel') {
            $service->csv($filename, $headers, $rows);
        }

        if ($export === 'pdf') {
            $service->pdf($title, $headers, $rows, $subtitle);
        }

        return false;
    }
}
