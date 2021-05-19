<?php
namespace LiamProject\Views;

/**
 * Class MainView
 * @package LiamProject\Views
 */
class MainView
{
    /**
     * @param $templateName
     * @param array $vars
     * @param string $error
     */
    public function renderHtml($templateName, array $vars = [], string $error = '')
    {
        extract($vars);

        include __DIR__ . '/../../../templates/' . $templateName;
    }
}