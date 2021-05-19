<?php
namespace LiamProject\Models;


/**
 * Class Customers
 * @package LiamProject\Models
 */
class Customers extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'customers';
    }

    /**
     * @var array
     */
    public array $createdCustomers;

    /**
     * @return array|null
     */
    public function getCreatedCustomers(): ?array
    {
        return $this->createdCustomers;
    }

    /**
     * @param $count
     * @return array|mixed
     * @throws \LiamProject\Exceptions\UnauthorizedException
     */
    public function addCustomers($count)
    {
        $api = '/api/v4/customers';

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'name' => 'Имя покупателя №  ' . $i,
                'next_price' => rand(100, 20000),
                'next_date' => (time() + (60 * 60 * 24 * rand(1, 14)))
            ];
        }

        $response = $this->queryToAmo($api, 'POST', $data);
        $this->createdCustomers = $response['_embedded']['customers'];
        return $this->createdCustomers;
    }

    /**
     * @param $contacts
     * @return array|null
     * @throws \LiamProject\Exceptions\UnauthorizedException
     */
    public function addLinksToContacts($contacts): ?array
    {
        $api = '/api/v4/customers/link';

        $contactsId = [];
        $data = [];
        foreach ($contacts as $contact) {
            $contactsId[] = $contact['id'];
        }

        $contactsId = array_flip($contactsId);
        foreach ($this->createdCustomers as $customer) {

            $contactsCount = rand(1, min(3, count($contactsId)));
            $contactsList = (array)array_rand($contactsId, $contactsCount);

            foreach ($contactsList as $contactId) {
                $data[] = [
                    "entity_id" => $customer['id'],
                    "to_entity_id" => $contactId,
                    "to_entity_type" => "contacts",
                ];
            }
        }

        return $this->queryToAmo($api, 'POST', $data);
    }

    /**
     * @param $companies
     * @return array|null
     * @throws \LiamProject\Exceptions\UnauthorizedException
     */
    public function addLinksToCompanies($companies): ?array
    {
        $api = '/api/v4/customers/link';

        $companiesId = [];
        foreach ($companies as $company) {
            $companiesId[] = $company['id'];
        }

        $data = [];
        foreach ($this->createdCustomers as $customer) {
            $data[] = [
                "entity_id" => $customer['id'],
                "to_entity_id" => $companiesId[array_rand($companiesId)],
                "to_entity_type" => "companies",
            ];
        }

        return $this->queryToAmo($api, 'POST', $data);
    }
}