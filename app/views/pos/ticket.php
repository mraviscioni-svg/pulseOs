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
<p>Pago: <?= e(str_replace('_', ' ', $sale['payment_method'])) ?></p>
<?php if ($sale['payment_method'] === 'mixto' && !empty($sale['payment_details'])): ?>
<?php $mix = json_decode((string)$sale['payment_details'], true) ?: []; ?>
<ul style="font-size:12px;margin-top:8px">
<?php foreach ($mix as $k => $v): if ((float)$v > 0): ?>
<li><?= e(ucfirst(str_replace('_', ' ', $k))) ?>: <?= money($v) ?></li>
<?php endif; endforeach; ?>
</ul>
<?php endif; ?>
<p style="text-align:center;margin-top:16px">¡Gracias!</p>
