<?php
namespace LiamProject\Models;


use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Companies
 * @package LiamProject\Models
 */
class Companies extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'companies';
    }

    /**
     * @var array
     */
    private array $createdCompanies;

    /**
     * @return array
     */
    public function getCreatedCompanies(): array
    {
        return $this->createdCompanies;
    }

    /**
     * @param int $count
     * @return array|mixed
     * @throws UnauthorizedException
     */
    public function addCompanies(int $count)
    {
        $api = '/api/v4/companies';

        $data = [];
        for ($i = 1; $i <= $count; $i++) {
            $data[] = ['name' => 'ООО Компания №  ' . $i];
        }

        $response = $this->queryToAmo($api, 'POST', $data);
        $this->createdCompanies = $response['_embedded']['companies'];
        return $this->createdCompanies;
    }
}