<?php
namespace LiamProject\Controllers;

use LiamProject\Models\IntegrationConfigService;

class ConfigController
{
    public function setIntegrationConfig()
    {
        IntegrationConfigService::set($_POST);
    }
}