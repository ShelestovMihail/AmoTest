<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Contacts
 * @package LiamProject\Models
 */
class Contacts extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'contacts';
    }

    /**
     * @var array
     */
    private array $createdContacts;

    /**
     * @return array
     */
    public function getCreatedContacts(): array
    {
        return $this->createdContacts;
    }

    /**
     * @param int $count
     * @return array
     * @throws UnauthorizedException
     */
    public function addContacts(int $count): array
    {
        $api = '/api/v4/contacts';

        $data = [];
        for ($i = 1; $i <= $count; $i++) {
            $data["contact$i"] = ['first_name' => 'Имя номер ' . $i, 'last_name' => 'Фамилия номер ' . $i];
        }

        $response = $this->queryToAmo($api, 'POST', $data);

        $responseContacts = $response['_embedded']['contacts'];
        $contactsId = [];
        $num = 1;
        foreach ($responseContacts as $contact) {
            $contactsId["contact$num"] = ['id' => $contact['id']];
            $num++;
        }

        $this->createdContacts = array_merge_recursive($contactsId, $data);
        return $this->createdContacts;
    }

    /**
     * @param array $companies
     * @return array
     * @throws UnauthorizedException
     */
    public function addLinksToCompanies(array $companies): array
    {
        $api = '/api/v4/contacts/link';
        $companiesId = [];

        foreach ($companies as $company) {
            $companiesId[] = $company['id'];
        }

        $data = [];
        foreach ($this->createdContacts as $contact) {
            $data[] = [
                "entity_id" => $contact['id'],
                "to_entity_id" => $companiesId[array_rand($companiesId)],
                "to_entity_type" => "companies",
            ];
        }

        return $this->queryToAmo($api, 'POST', $data);
    }

    /**
     * @param array $field
     * @return array|null
     * @throws UnauthorizedException
     */
    public function fillMultiselectField(array $field): ?array
    {
        $api = '/api/v4/contacts';

        $enumsId = [];
        foreach ($field['enums'] as $enum) {
            $enumsId[] = ['enum_id' => $enum['id']];
        }

        $data = [];
        foreach ($this->createdContacts as $contact) {
            shuffle($enumsId);
            $fieldValues = array_slice($enumsId, 0, rand(1, count($enumsId)));
            $contact['custom_fields_values'][] =
                [
                    'field_id' => $field['id'],
                    'values' => $fieldValues
                ];
            $data[] = $contact;
        }

        return $this->queryToAmo($api, 'PATCH', $data);
    }
}