<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error — PulseOS</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #020617; color: #e2e8f0; margin: 0; padding: 2rem; }
    .box { max-width: 32rem; margin: 2rem auto; padding: 1.5rem; border: 1px solid #334155; border-radius: 1rem; background: #0f172a; }
    a { color: #818cf8; }
    pre { overflow: auto; font-size: 12px; background: #020617; padding: 1rem; border-radius: 0.5rem; }
  </style>
</head>
<body>
  <div class="box">
    <h1>Error del servidor</h1>
    <p>Algo falló al procesar la solicitud.</p>
    <?php if (!empty($message) && !empty($debug)): ?>
    <pre><?= htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8') ?></pre>
    <?php endif; ?>
    <p><a href="<?= htmlspecialchars(url('/login'), ENT_QUOTES, 'UTF-8') ?>">Volver al login</a></p>
    <p><a href="<?= htmlspecialchars(url('/health'), ENT_QUOTES, 'UTF-8') ?>">Diagnóstico /health</a></p>
  </div>
</body>
</html>
