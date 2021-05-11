<?php

namespace LiamProject\Models;


use LiamProject\Exceptions\UnauthorizedException;

abstract class AmocrmEntity
{
    protected string $entityName;
    protected ?array $foundedElement;

    abstract protected function setEntityName(): string;

    public function __construct()
    {
        $this->entityName = $this->setEntityName();
    }

    public function getEntityById($id): ?array
    {
        $api = "/api/v4/$this->entityName/$id";

        $response = $this->queryToAmo($api);
        $this->foundedElement = $response;
        return $response;
    }

    public function setCustomField($fieldId, $value)
    {
        $id = $this->foundedElement['id'];

        $api = "/api/v4/$this->entityName/$id";


        $this->foundedElement['custom_fields_values'] = [[
            'field_id' => $fieldId,
            'values' => [
                ['value' => $value]
            ]
        ]];

        if (isset($this->foundedElement['_embedded'])) {
            unset($this->foundedElement['_embedded']);
        }

        $this->queryToAmo($api, 'PATCH', $this->foundedElement);
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    protected function queryToAmo(string $api, string $method = 'GET', $data = []): ?array
    {
        $config = IntegrationConfigService::getConfig();
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru' . $api;

        CurlService::init();
        CurlService::setOpt();
        CurlService::setHeaders($this->getHeaders());
        CurlService::setLink($link);
        if ($data !== []) {
            CurlService::setData($data);
        }
        CurlService::setMethod($method);
        $out = CurlService::exec();

        $response = json_decode($out, true);

        if (isset($response['status']) && $response['status'] === 401) {
            throw new UnauthorizedException();
        }
        return $response;
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