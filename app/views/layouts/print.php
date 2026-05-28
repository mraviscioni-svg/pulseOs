<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= e($title ?? 'Ticket') ?></title>
<style>
body{font-family:system-ui,sans-serif;max-width:320px;margin:2rem auto;font-size:14px}
table{width:100%;border-collapse:collapse} td{padding:4px 0}
hr{border:none;border-top:1px dashed #999;margin:12px 0}
</style>
</head>
<body>
<?= $content ?>
<script>window.print()</script>
</body>
</html>
