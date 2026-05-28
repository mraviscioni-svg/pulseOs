<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/app'): void
    {
        $app = Application::boot(dirname(__DIR__, 2));
        $viewsPath = $app->root() . '/app/views/';

        extract($data, EXTR_SKIP);

        $content = (static function () use ($template, $data, $viewsPath) {
            extract($data, EXTR_SKIP);
            ob_start();
            require $viewsPath . str_replace('.', '/', $template) . '.php';

            return ob_get_clean();
        })();

        if ($layout === null) {
            echo $content;

            return;
        }

        require $viewsPath . str_replace('.', '/', $layout) . '.php';
    }
}
