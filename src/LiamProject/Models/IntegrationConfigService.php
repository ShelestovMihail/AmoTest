<?php
namespace LiamProject\Models;

/**
 * Class IntegrationConfigService
 * @package LiamProject\Models
 */
class IntegrationConfigService
{
    /**
     * @param array $form
     */
    public static function set(array $form)
    {
        $config = static::prepareConfigFromPost($form);
        $config = array_map('trim', $config);
        $result = json_encode($config);
        file_put_contents(__DIR__ . '/../../../integrationConfig.json', $result);
    }

    /**
     * @return array|null
     */
    public static function getConfig(): ? array
    {
        $config = file_get_contents(__DIR__ . '/../../../integrationConfig.json');

         return json_decode($config, true);
    }

    /**
     * @param array $form
     * @return array
     */
    protected static function prepareConfigFromPost(array $form): array
    {
        //Убирает input:submit из формы
        array_pop($form);
        $config = self::validateFormData($form);
        preg_match('|https://(?<subdomain>.*)\.amocrm(?<domainZone>\..*)|', $form['domain'], $matches);
        $config['subdomain'] = $matches['subdomain'];
        $config['domainZone'] = $matches['domainZone'];
        return $config;
    }

    /**
     * @param array $form
     * @return array
     */
    protected static function validateFormData(array $form): array
    {
        foreach ($form as $key => $data) {
            $form[$key] = htmlentities($data);
        }
        return $form;
    }
}