<?php

namespace LiamProject\Models;

class Contacts
{
    private $createdContacts; //массив с id

    public function addContacts($config, $accessToken, $count)
    {
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru/api/v4/contacts'; //Формируем URL для запроса
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        for ($i = 0; $i < $count; $i++) {
            $data[] = ['first_name' => 'Имя номер ' . $i, 'last_name' => 'Фамилия номер ' . $i];
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

        foreach ($response['_embedded']['contacts'] as $contact) {
            $this->createdContacts[] = $contact['id'];
        }
    }

    public function addLinksToCompanies($config, $accessToken, $companiesId)
    {
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru/api/v4/contacts/link'; //Формируем URL для запроса
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $companiesId = array_flip($companiesId);
        foreach ($this->createdContacts as $contactId) {
            $data[] = [
                "entity_id" => $contactId,
                "to_entity_id" => array_rand($companiesId),
                "to_entity_type" => "companies",
            ];
        }

        CurlService::init();
        CurlService::setOpt();
        CurlService::setLink($link);
        CurlService::setMethod('POST');
        CurlService::setHeaders($headers);
        CurlService::setData($data);
        $out = CurlService::exec();
        CurlService::close();
    }
}