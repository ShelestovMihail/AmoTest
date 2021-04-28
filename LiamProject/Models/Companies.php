<?php
namespace LiamProject\Models;


class Companies
{
    private $createdCompanies; //массив с id

    public function getCreatedCompaniesId()
    {
        return $this->createdCompanies;
    }

    public function addCompanies($config, $accessToken, $count)
    {
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru/api/v4/companies'; //Формируем URL для запроса
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        for($i = 0; $i < $count; $i++) {
            $data[] = ['name' => 'ООО Название номер ' . $i];
        }

        CurlService::init();
        CurlService::setOpt();
        CurlService::setLink($link);
        CurlService::setMethod('POST');
        CurlService::setHeaders($headers);
        CurlService::setData($data);
        $out = CurlService::exec();
        CurlService::close();

        $response = (json_decode($out, true));

        foreach ($response['_embedded']['companies'] as $company) {
            $this->createdCompanies[] = $company['id'];
        }
    }
}