<?php
namespace LiamProject\Models;


use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class CustomFields
 * @package LiamProject\Models
 */
class CustomFields extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'custom_fields';
    }

    /**
     * @var array|null
     */
    private ?array $createdField;

    /**
     * @return array
     */
    public function getCreatedField(): array
    {
        return $this->createdField;
    }

    /**
     * @return array
     * @throws UnauthorizedException
     */
    public function addMultiselectToContacts(): array
    {
        $api = '/api/v4/contacts/custom_fields';

//        Этот код позволяет избежать дублирования мультисписков в контактах. По заданию этого не требовалось
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

        $this->createdField = $this->queryToAmo($api, 'POST', $data);

        return $this->createdField;
    }

    /**
     * @return mixed
     * @throws UnauthorizedException
     */
    public function getContactsCustomFields()
    {
        $api = '/api/v4/contacts/custom_fields';

        $response = $this->queryToAmo($api);
        return $response['_embedded']['custom_fields'];
    }

    /**
     * @param $entityName
     * @return int
     * @throws UnauthorizedException
     */
    public function getOneTextTypeFieldId($entityName): int
    {
        $api = "/api/v4/$entityName/custom_fields";
        $response = $this->queryToAmo($api);

        $textTypeFieldsId = [];
        foreach ($response['_embedded']['custom_fields'] as $field) {
            if($field['type'] == 'text') {
                $textTypeFieldsId[] = $field['id'];
            }
        }

        $textFieldsCount = count($textTypeFieldsId);
        if ($textFieldsCount > 1) {
            $fieldId = array_shift($textTypeFieldsId);
            $this->deleteFieldsFromEntityById($entityName, $textTypeFieldsId);
        } elseif ($textFieldsCount == 0) {
            $fieldId = $this->addTextTypeFieldToEntity($entityName);
        } else {
            $fieldId = $textTypeFieldsId[0];
        }

        return $fieldId;
    }

    /**
     * @param $entityName
     * @param array $fieldsId
     * @throws UnauthorizedException
     */
    protected function deleteFieldsFromEntityById($entityName, array $fieldsId)
    {
        foreach ($fieldsId as $id) {
            $api = "/api/v4/$entityName/custom_fields/$id";
            $this->queryToAmo($api, 'DELETE');
        }
    }

    /**
     * @param $entityName
     * @return int
     * @throws UnauthorizedException
     */
    protected function addTextTypeFieldToEntity($entityName):int
    {
        $api = "/api/v4/$entityName/custom_fields";

        $data = [
            'name' => 'Единственное текстовое поле',
            'type' => 'text',
            'sort' => 500
        ];

        $response = $this->queryToAmo($api, 'POST', $data);
        return $response['id'];
    }
}