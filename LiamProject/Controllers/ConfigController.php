<?php
namespace LiamProject\Controllers;

use LiamProject\Models\IntegrationConfigService;

class ConfigController
{
    public function setIntegrationConfig(array $configData)
    {
        IntegrationConfigService::set($configData);
    }
}