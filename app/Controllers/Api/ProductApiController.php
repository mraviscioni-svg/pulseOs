<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\ProductModel;

final class ProductApiController extends Controller
{
    public function search(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $products = (new ProductModel())->search($this->tenantId(), $q ?: null, 20);
        $this->json(['data' => $products]);
    }

    public function barcode(): void
    {
        $code = trim((string) ($_GET['code'] ?? ''));
        if ($code === '') {
            $this->json(['data' => null], 404);
        }

        $product = (new ProductModel())->findByBarcode($this->tenantId(), $code);
        if (!$product) {
            $this->json(['data' => null], 404);
        }

        $this->json(['data' => $product]);
    }
}
