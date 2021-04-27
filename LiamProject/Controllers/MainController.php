<?php
namespace LiamProject\Controllers;

use \LiamProject\Views\MainView;
use \LiamProject\Models\Tokens;
use LiamProject\Models\IntegrationConfigService;
use LiamProject\Models\Contacts;

class MainController
{
    private $view;
    private $integrationConfigData;

    public function __construct()
    {
        $this->view = new MainView();
        $this->integrationConfigData = IntegrationConfigService::getConfig();
    }

    public function checkAccessToken(): bool
    {
        return Tokens::checkAccessToken();
    }

    public function getAccessToken()
    {
        Tokens::getAccessToken($this->integrationConfigData);
    }

    public function addContacts()
    {
        Contacts::addContacts($this->integrationConfigData);
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