<?php
namespace LiamProject\Controllers;

use LiamProject\Models\Companies;
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

    public function addEntities()
    {
        $count = $_POST['entityCount'];
        $accessToken = $this->token->getAccessToken();
        $company = new Companies();
        $contact = new Contacts();
        $company->addCompanies($this->integrationConfigData, $accessToken, $count);
        $contact->addContacts($this->integrationConfigData, $accessToken, $count);

        $contact->addLinksToCompanies($this->integrationConfigData, $accessToken, $company->getCreatedCompaniesId());
        sleep(1);
    }

    public function viewPage()
    {
        $this->view->renderHtml('main.php', $this->integrationConfigData ?? []);
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