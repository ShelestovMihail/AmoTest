<?php

namespace LiamProject\Models;


class Companies extends AmocrmEntity
{
    protected function setEntityName(): string
    {
        return 'companies';
    }

    private array $createdCompanies;

    public function getCreatedCompanies(): array
    {
        return $this->createdCompanies;
    }

    public function addCompanies($count)
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