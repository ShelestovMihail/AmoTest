<?php
require_once 'autoload.php';

use LiamProject\Controllers\MainController;
use LiamProject\Exceptions\EmptyRefreshToken;
use LiamProject\Controllers\ConfigController;
use LiamProject\Exceptions\EmptyAccessToken;

session_start();
$controller = new MainController();

if (!empty($_POST['setConfig'])) {
    $configController = new ConfigController();
    $configController->setIntegrationConfig($_POST);
}

if (!empty($_POST['newContacts'])) {
    $controller->addContacts();
}

try {
    $controller->checkAccessToken();
} catch (EmptyAccessToken $e) {
    try {
        $controller->getAccessToken();
    } catch (EmptyRefreshToken $e) {
        $controller->viewButton();
        return;
    }
}

var_dump($_SESSION);
$controller->viewPage();