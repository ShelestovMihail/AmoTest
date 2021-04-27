<?php
namespace LiamProject\Models;


class IntegrationConfigService
{
    public static function set($config)
    {
        array_pop($config);
        $result = json_encode($config);
        file_put_contents(__DIR__ . '/../../integrationConfig.json', $result);
    }

    public static function getConfig(): ? array
    {
        $config = file_get_contents(__DIR__ . '/../../integrationConfig.json');

         return json_decode($config, true);
    }
}