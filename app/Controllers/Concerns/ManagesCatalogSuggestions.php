<?php

declare(strict_types=1);

namespace App\Controllers\Concerns;

use App\Core\Session;
use App\Services\BusinessCatalogService;

trait ManagesCatalogSuggestions
{
    /** @return array{businessType: string, businessTypeLabel: string, suggestions: list<array<string, mixed>>, hidden: bool} */
    protected function suggestionContext(string $kind, array $tenantRows): array
    {
        $businessType = (string) Session::get('business_type', 'otro');
        $catalog = new BusinessCatalogService();
        $catalogRows = $kind === 'category'
            ? $catalog->categoriesForType($businessType)
            : $catalog->brandsForType($businessType);

        return [
            'businessType' => $businessType,
            'businessTypeLabel' => config('business_types')[$businessType]['label'] ?? $businessType,
            'suggestions' => $catalog->suggestionsNotInTenant($catalogRows, $tenantRows),
            'hidden' => (bool) Session::get('catalog_hide_' . $kind . '_suggestions'),
        ];
    }

    protected function dismissSuggestions(string $kind): void
    {
        Session::set('catalog_hide_' . $kind . '_suggestions', true);
    }

    protected function showSuggestions(string $kind): void
    {
        Session::forget('catalog_hide_' . $kind . '_suggestions');
    }

    protected function resetCatalogSuggestions(string $kind): void
    {
        $businessType = (string) Session::get('business_type', 'otro');
        (new BusinessCatalogService())->resetCatalogForType($businessType);
        $this->showSuggestions($kind);
    }
}
