<?php

namespace LiamProject\Controllers;

use LiamProject\Exceptions\WrongEntityIdException;
use LiamProject\Models\Account;
use LiamProject\Models\AmocrmEntity;
use LiamProject\Models\Catalogs;
use LiamProject\Models\Companies;
use LiamProject\Models\Customers;
use LiamProject\Models\CustomFields;
use LiamProject\Models\Leads;
use LiamProject\Models\Notes;
use LiamProject\Models\Segments;
use LiamProject\Models\Tasks;
use LiamProject\Models\Users;
use LiamProject\Views\MainView;
use LiamProject\Models\Tokens;
use LiamProject\Models\IntegrationConfigService;
use LiamProject\Models\Contacts;

class MainController
{
    private $view;
    private $token;
    private $integrationConfigData;

    public function __construct()
    {
        $this->view = new MainView();
        $this->token = new Tokens();
        $this->integrationConfigData = IntegrationConfigService::getConfig();
    }

    public function checkAccessToken(): bool
    {
        return $this->token->checkTokens();
    }

    public function refreshToken()
    {
        $this->token->refreshToken($this->integrationConfigData);
    }

    public function addAccessToken()
    {
        $this->token->addAccessToken($this->integrationConfigData);
    }

    public function addEntities($realCount)
    {
        for ($count = min(200, $realCount); $realCount > 0; $realCount -= $count) {

            $company = new Companies();
            $contact = new Contacts();
            $customer = new Customers();
            $lead = new Leads();
            $customField = new CustomFields();

            $company->addCompanies($count);
            $contact->addContacts($count);
            $lead->addLeads($count);
            $customer->addCustomers($count);
            $field = $customField->addMultiselectToContacts();

            $contact->addLinksToCompanies($company->getCreatedCompanies());
            $lead->addLinksToContacts($contact->getCreatedContacts());
            $lead->addLinksToCompanies($company->getCreatedCompanies());
            $customer->addLinksToContacts($contact->getCreatedContacts());
            $customer->addLinksToCompanies($company->getCreatedCompanies());

            $contact->fillMultiselectField($field);
        }
    }

    public function setValueToEntityField($entityId, $fieldValue)
    {
        $field = new CustomFields();
        $entity = $this->getEntityWithCustomFieldById($entityId);

        $field = $field->getOneTextTypeFieldId($entity->getEntityName());

        $entity->setCustomField($field, $fieldValue);
    }

    public function setNoteToEntity($entityId, $noteType)
    {
        $note = new Notes();
        $entity = $this->getNotableEntitiesById($entityId);

        $note->setNoteToEntityById($entity, $noteType);
    }

    public function setTaskToEntity($completeTill, $taskText, $responsibleUserId, $entityId)
    {
        $task = new Tasks();
        $user = new Users();
        if ($responsibleUserId !== '') {
            $user->getUserIdById($responsibleUserId);

        }
        $entity = $this->getNotableEntitiesById($entityId);

        $task->addTaskToEntity($completeTill, $taskText, $responsibleUserId, $entity);
    }

    public function completeTask($taskId)
    {
        $task = new Tasks();
        $task->completeTaskById($taskId);
    }

    protected function getNotableEntitiesById($entityId): ?AmocrmEntity
    {
        $lead = new Leads();
        $entity = $lead->getEntityById($entityId);
        if ($entity !== null) {
            return $lead;
        }

        $contact = new Contacts();
        $entity = $contact->getEntityById($entityId);
        if ($entity !== null) {
            return $contact;
        }

        $company = new Companies();
        $entity = $company->getEntityById($entityId);
        if ($entity !== null) {
            return $company;
        }

        $customer = new Customers();
        $entity = $customer->getEntityById($entityId);
        if ($entity !== null) {
            return $customer;
        }
        throw new WrongEntityIdException('Сущности с таким id не существует');
    }

    public function getEntityWithCustomFieldById($entityId): AmocrmEntity
    {
        $entity = $this->getNotableEntitiesById($entityId);
        if ($entity !== null) {
            return $entity;
        }

        $catalog = new Catalogs();
        $entity = $catalog->getEntityById($entityId);
        if ($entity !== null) {
            return $catalog;
        }

        $account = new Account();
        if ($account->checkCustomersMode() == 'segments') {
            $segment = new Segments();
            $entity = $segment->getEntityById($entityId);
            if ($entity !== null) {
                return $segment;
            }
        }
        throw new WrongEntityIdException('Сущности с таким id не существует');
    }

    public function viewPage($error = '')
    {
        $this->refreshConfig();
        $this->view->renderHtml('main.php', $this->integrationConfigData ?? [], $error);
    }

    public function viewButton()
    {
        $this->refreshConfig();
        $this->view->renderHtml('button.php', $this->integrationConfigData ?? []);
    }

    private function refreshConfig()
    {
        $this->integrationConfigData = IntegrationConfigService::getConfig();
    }

    public function clearTokens()
    {
        $this->token->clearTokens();
    }
}