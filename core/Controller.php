<?php
namespace Core;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../app/views/{$view}.php";
    }

    protected function model(string $model)
    {
        $modelClass = "\\App\\Models\\$model";
        return new $modelClass;
    }
}
