<?php
namespace LiamProject\Views;

class MainView
{
    public function renderHtml($templateName, array $vars = [], $error = '')
    {
        extract($vars);

        include __DIR__ . '/../../templates/' . $templateName;
    }
}