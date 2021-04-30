<?php

namespace LiamProject\Models;

class Contacts extends AmocrmEntity
{
    private array $createdContacts;

    public function getCreatedContacts(): array
    {
        return $this->createdContacts;
    }

    public function getContactById($id): ?array
    {
        $api = "/api/v4/contacts/$id";

        return $this->queryToAmo($api);

    }

    public function addContacts($count): array
    {
        $api = '/api/v4/contacts';

        $data = [];
        for ($i = 1; $i <= $count; $i++) {
            $data["contact{$i}"] = ['first_name' => 'Имя номер ' . $i, 'last_name' => 'Фамилия номер ' . $i];
        }

        $response = $this->queryToAmo($api, $data);

        $responseContacts = $response['_embedded']['contacts'];
        $contactsId = [];
        $num = 1;
        foreach ($responseContacts as $contact) {
            $contactsId["contact{$num}"] = ['id' => $contact['id']];
            $num++;
        }

        $this->createdContacts = array_merge_recursive($contactsId, $data);
        return $this->createdContacts;
    }

    public function addLinksToCompanies($companies): array
    {
        $api = '/api/v4/contacts/link';
        $companiesId = [];

        foreach ($companies as $company) {
            $companiesId[] = $company['id'];
        }

        foreach ($this->createdContacts as $contact) {
            $data[] = [
                "entity_id" => $contact['id'],
                "to_entity_id" => $companiesId[array_rand($companiesId)],
                "to_entity_type" => "companies",
            ];
        }

        return $this->queryToAmo($api, $data, 'POST');
    }

    public function fillMultiselectField($field)
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

        return $this->queryToAmo($api, $data, 'PATCH');
    }
}