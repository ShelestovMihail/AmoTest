<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Leads
 * @package LiamProject\Models
 */
class Leads extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'leads';
    }

    /**
     * @var array
     */
    private array $createdLeads;

    /**
     * @return array
     */
    public function getCreatedLids(): array
    {
        return $this->createdLeads;
    }


    /**
     * @param $count
     * @return array
     * @throws UnauthorizedException
     */
    public function addLeads($count): array
    {
        $api = '/api/v4/leads';

        $data = [];
        for($i = 1; $i <= $count; $i++) {
            $data[] = [
                'name' => 'Название сделки номер ' . $i,
                'price' => rand(100, 20000)
            ];
        }

        $response = $this->queryToAmo($api, 'POST', $data);

        $this->createdLeads = $response['_embedded']['leads'];
        return $this->createdLeads;
    }

    /**
     * @param $contacts
     * @return array
     * @throws UnauthorizedException
     */
    public function addLinksToContacts($contacts): array
    {
        $api = '/api/v4/leads/link';

        $contactsId = [];
        $data = [];
        foreach ($contacts as $contact) {
            $contactsId[] = $contact['id'];
        }

        $contactsId = array_flip($contactsId);
        foreach ($this->createdLeads as $lead) {

            $contactsCount = rand(1, min(3, count($contactsId)));
            $contactsList = (array)array_rand($contactsId, $contactsCount);

            foreach ($contactsList as $contactId) {
                $data[] = [
                    "entity_id" => $lead['id'],
                    "to_entity_id" => $contactId,
                    "to_entity_type" => "contacts",
                ];
            }
        }

        return $this->queryToAmo($api, 'POST', $data);
    }

    /**
     * @param $companies
     * @return array
     * @throws UnauthorizedException
     */
    public function addLinksToCompanies($companies): array
    {
        $api = '/api/v4/leads/link';

        $companiesId = [];
        $data = [];
        foreach ($companies as $company) {
            $companiesId[] = $company['id'];
        }

        foreach ($this->createdLeads as $lead) {
            $data[] = [
                "entity_id" => $lead['id'],
                "to_entity_id" => $companiesId[array_rand($companiesId)],
                "to_entity_type" => "companies",
            ];
        }

        return $this->queryToAmo($api, 'POST', $data);
    }
}