<?php

namespace LightMVC\Core;

class View
{
    private static ?View $instance = null;
    private string $layout = 'default';

    /**
     * @param string $view
     * @param array $data
     * @return mixed
     *
     * @var string $content layout content
     */
    public function render(string $view, array $data = [])
    {
        extract($data);
        ob_start();
        include view_path("$view.php");
        $content = ob_get_clean();

        return include view_path("layouts/$this->layout.php");
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
