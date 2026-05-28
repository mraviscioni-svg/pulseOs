<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'PulseOS') ?> — PulseOS</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        pulse: { 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
      }
    }
  }
}
</script>
