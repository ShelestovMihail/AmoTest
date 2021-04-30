<?php
namespace LiamProject\Models;


class AmocrmEntity
{

    public function getCompanyById($id): ?array
    {
        $api = "/api/v4/companies/$id";

        return $this->queryToAmo($api);
    }

    protected function queryToAmo(string $api, $data = [], string $method = 'POST'): ?array
    {
        $config = IntegrationConfigService::getConfig();
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru' . $api;

        CurlService::init();
        CurlService::setOpt();
        CurlService::setHeaders($this->getHeaders());
        CurlService::setLink($link);
        if ($data !== []) {
            CurlService::setMethod($method);
            CurlService::setData($data);
        } else {
            CurlService::setMethod('GET');
        }
        $out = CurlService::exec();

        return json_decode($out, true);
    }

    private function getHeaders(): array
    {
        $accessToken = $_SESSION['access_token'];
        return [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
    }
}