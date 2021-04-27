<?php
namespace LiamProject\Views;

class MainView
{
    public function renderHtml($templateName, array $vars = [])
    {
        extract($vars);

        include __DIR__ . '/../../templates/' . $templateName;
    }
}