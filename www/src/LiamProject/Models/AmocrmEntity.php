<?php
declare(strict_types=1);
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class AmocrmEntity
 * @package LiamProject\Models
 */
abstract class AmocrmEntity
{
    /**
     * @var array
     */
    private array $queriesTimestamp;
    /**
     * @var string
     */
    protected string $entityName;
    /**
     * @var array|null
     */
    protected ?array $foundedElement;

    /**
     * @return string
     */
    abstract protected function setEntityName(): string;

    /**
     * AmocrmEntity constructor.
     */
    public function __construct()
    {
        $this->entityName = $this->setEntityName();
    }

    /**
     * @param $id
     * @return array|null
     * @throws UnauthorizedException
     */
    public function getEntityById($id): ?array
    {
        $api = "/api/v4/$this->entityName/$id";

        $response = $this->queryToAmo($api);
        $this->foundedElement = $response;
        return $response;
    }

    /**
     * @param $fieldId
     * @param $value
     * @throws UnauthorizedException
     */
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

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @param string $api
     * @param string $method
     * @param array $data
     * @return array|null
     * @throws UnauthorizedException
     */
    protected function queryToAmo(string $api, string $method = 'GET', array $data = []): ?array
    {
        $config = IntegrationConfigService::getConfig();
        $link = 'https://' . $config['subdomain'] . '.amocrm' . $config['domainZone'] . $api;

        CurlService::init();
        CurlService::setOpt();
        CurlService::setHeaders($this->getHeaders());
        CurlService::setLink($link);
        if ($data !== []) {
            CurlService::setData($data);
        }
        CurlService::setMethod($method);
        $this->checkQueriesPerSecond();
        $out = CurlService::exec();
        var_dump($out);

        $response = json_decode($out, true);

        if (isset($response['status']) && $response['status'] === 401) {
            throw new UnauthorizedException();
        }
        return $response;
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        $accessToken = $_SESSION['access_token'];
        return [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
    }

    /**
     *
     */
    private function checkQueriesPerSecond(): void
    {
        $lastQueryTime = microtime(true);
        $this->queriesTimestamp[] = $lastQueryTime;
        if(count($this->queriesTimestamp) > 3) {
            $firstQueryTime = array_shift($this->queriesTimestamp);

            if($lastQueryTime - $firstQueryTime < 1) {
                sleep(1);
            }
        }
    }
}