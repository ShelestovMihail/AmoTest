<?php
declare(strict_types=1);
namespace LiamProject\Controllers;

use LiamProject\Models\IntegrationConfigService;

/**
 * Class ConfigController
 * @package LiamProject\Controllers
 */
class ConfigController
{
    public function setIntegrationConfig()
    {
        IntegrationConfigService::set($_POST);
    }
}