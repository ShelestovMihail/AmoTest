<?php

namespace LiamProject\Controllers;

use LiamProject\Exceptions\WrongEntityIdException;
use LiamProject\Models\Account;
use LiamProject\Models\Catalogs;
use LiamProject\Models\Companies;
use LiamProject\Models\Customers;
use LiamProject\Models\CustomFields;
use LiamProject\Models\Leads;
use LiamProject\Models\Segments;
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
        $entity = $this->getEntityWithCustomFieldById($entityId);
        var_dump($entity);

    }

    public function getEntityWithCustomFieldById($entityId): object
    {
        $lead = new Leads();
        $entity = $lead->getLeadById($entityId);
        if ($entity !== null) {
            return $lead;
        }

        $contact = new Contacts();
        $entity = $contact->getContactById($entityId);
        if ($entity !== null) {
            return $contact;
        }

        $company = new Companies();
        $entity = $company->getCompanyById($entityId);
        if ($entity !== null) {
            return $company;
        }

        $customer = new Customers();
        $entity = $customer->getCustomerById($entityId);
        if ($entity !== null) {
            return $customer;
        }

        $catalog = new Catalogs();
        $entity = $catalog->getCatalogById($entityId);
        if ($entity !== null) {
            return $catalog;
        }

        $account = new Account();
        if ($account->checkCustomersMode() == 'segments') {
            $segment = new Segments();
            $entity = $segment->getSegmentById($entityId);
            if ($entity !== null) {
                return $segment;
            }
        }
        throw new WrongEntityIdException('Ошибка id');
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
}