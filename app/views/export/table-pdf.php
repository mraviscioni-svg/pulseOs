<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= e($title ?? 'Listado') ?></title>
<style>
  * { box-sizing: border-box; }
  body { font-family: system-ui, sans-serif; margin: 1.5rem; color: #111; font-size: 12px; }
  h1 { font-size: 18px; margin: 0 0 4px; }
  .meta { color: #555; margin-bottom: 16px; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
  th { background: #f3f4f6; font-weight: 600; }
  tr:nth-child(even) td { background: #fafafa; }
  @media print { body { margin: 0.5cm; } }
</style>
</head>
<body>
  <h1><?= e($title ?? 'Listado') ?></h1>
  <p class="meta"><?= e($tenant) ?> · <?= e($subtitle) ?> · <?= date('d/m/Y H:i') ?></p>
  <table>
    <thead><tr><?php foreach ($headers as $h): ?><th><?= e((string) $h) ?></th><?php endforeach; ?></tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
    <tr><?php foreach ($row as $cell): ?><td><?= e((string) $cell) ?></td><?php endforeach; ?></tr>
    <?php endforeach; ?>
    <?php if ($rows === []): ?>
    <tr><td colspan="<?= count($headers) ?>">Sin registros</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
  <script>window.onload = function () { window.print(); };</script>
</body>
</html>
