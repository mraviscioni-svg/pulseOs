<h2 style="text-align:center"><?= e($tenantName) ?></h2>
<p style="text-align:center;font-size:12px">pulseOS</p>
<hr>
<p><strong><?= e($sale['sale_number']) ?></strong></p>
<p>Vendedor: <?= e($sale['seller_name'] ?? '—') ?></p>
<p><?= e($sale['created_at']) ?></p>
<hr>
<table>
<?php foreach ($items as $item): ?>
<tr>
  <td><?= e($item['product_name']) ?> x<?= e($item['quantity']) ?></td>
  <td style="text-align:right"><?= money($item['total']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<hr>
<p><strong>Total: <?= money($sale['total']) ?></strong></p>
<p>Pago: <?= e($sale['payment_method']) ?></p>
<p style="text-align:center;margin-top:16px">¡Gracias!</p>
