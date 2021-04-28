<?php
namespace LiamProject\Models;


class Leads
{
    private $createdLeads; //массив с id

    public function addLeads($config, $accessToken, $count)
    {
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru/api/v4/leads'; //Формируем URL для запроса
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        for($i = 0; $i < $count; $i++) {
            $data[] = ['name' => 'Название сделки номер' . $i, 'price' => rand(100, 20000) . $i];
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

        foreach ($response['_embedded']['leads'] as $lead) {
            $this->createdLeads[] = $lead['id'];
        }
    }
}