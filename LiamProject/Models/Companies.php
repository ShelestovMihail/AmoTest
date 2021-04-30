<?php

namespace LiamProject\Models;


class Companies extends AmocrmEntity
{
    private $createdCompanies;

    public function getCreatedCompanies()
    {
        return $this->createdCompanies;
    }

    public function getCompanyById($id): ?array
    {
        $api = "/api/v4/companies/$id";

        return $this->queryToAmo($api);
    }

    public function addCompanies($count)
    {
        $api = '/api/v4/companies';

        for ($i = 1; $i <= $count; $i++) {
            $data[] = ['name' => 'ООО Компания №  ' . $i];
        }

        $response = $this->queryToAmo($api, $data);
        $this->createdCompanies = $response['_embedded']['companies'];
        return $this->createdCompanies;
    }
}