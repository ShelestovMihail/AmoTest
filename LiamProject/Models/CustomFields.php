<?php

namespace LiamProject\Models;


class CustomFields extends AmocrmEntity
{
    private array $createdField;

    public function getCreatedField(): array
    {
        return $this->createdField;
    }

    public function addMultiselectToContacts(): array
    {
        $api = '/api/v4/contacts/custom_fields';

        //Этот код позволяет избежать дублирования мультисписков в контактах. По заданию этого не требовалось
        $fieldsList = $this->getContactsCustomFields();
        foreach ($fieldsList as $field) {
            if ($field['name'] == 'Мультисписок для контактов') {
                return $field;
            }
        }

        $enums = [];
        for ($i = 1; $i <= 10; $i++) {
            $enums[] = [
                'value' => 'Случайное значение ' . $i,
                'sort' => $i
            ];
        }

        $data = [
            'name' => 'Мультисписок для контактов',
            'type' => 'multiselect',
            'enums' => $enums
        ];

        $this->createdField = $this->queryToAmo($api, $data);
        var_dump($this->createdField);
echo "<hr>";
        return $this->createdField;
    }

    public function getContactsCustomFields()
    {
        $api = '/api/v4/contacts/custom_fields';

        $response = $this->queryToAmo($api);
        return $response['_embedded']['custom_fields'];
    }
}