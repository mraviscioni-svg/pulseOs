<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ProductModel;
use App\Services\StockService;

final class InventoryController extends Controller
{
    public function index(): void
    {
        $this->view('inventory/index', [
            'title' => 'Inventario rápido',
            'recent' => (new ProductModel())->search($this->tenantId(), null, 30),
        ]);
    }

    public function adjust(): void
    {
        $data = $this->input();
        $productId = (int) ($data['product_id'] ?? 0);
        $qty = (float) ($data['quantity'] ?? 0);
        if (!$productId) {
            $this->json(['error' => 'Producto requerido'], 422);
        }
        try {
            (new StockService())->adjust($productId, $qty, 'ajuste', $this->userId(), notes: $data['notes'] ?? 'Inventario rápido');
            $product = (new ProductModel())->find($productId);
            $this->json(['success' => true, 'stock' => $product['stock'] ?? 0]);
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function setCount(): void
    {
        $data = $this->input();
        $productId = (int) ($data['product_id'] ?? 0);
        $target = (float) ($data['target_stock'] ?? 0);
        $product = (new ProductModel())->find($productId);
        if (!$product) {
            $this->json(['error' => 'No encontrado'], 404);
        }
        $diff = $target - (float) $product['stock'];
        try {
            (new StockService())->adjust($productId, $diff, 'ajuste', $this->userId(), notes: 'Conteo inventario');
            $product = (new ProductModel())->find($productId);
            $this->json(['success' => true, 'stock' => $product['stock']]);
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }
}
