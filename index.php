<?php
require_once 'autoload.php';

use LiamProject\Controllers\MainController;
use LiamProject\Exceptions\ExpiredRefreshTokenException;
use LiamProject\Controllers\ConfigController;
use LiamProject\Exceptions\ExpiredAccessTokenException;
use LiamProject\Exceptions\EmptyTokensException;

ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);
header('Content-type: text/html; charset=UTF-8');

session_start();

try {
    $controller = new MainController();
    $controller->checkAccessToken();
} catch (ExpiredAccessTokenException $e) {
    $controller->refreshToken();
} catch (ExpiredRefreshTokenException | EmptyTokensException $e) {
    $controller->addAccessToken();
    $controller->viewButton();
    return;
}

if (!empty($_POST['setConfig'])) {
    $configController = new ConfigController();
    $configController->setIntegrationConfig();
}

if (!empty($_POST['newEntities'])) {
    $controller->addEntities($_POST['entityCount']);
}

if (!empty($_POST['setValueToEntityField'])) {
    try {
        $controller->setValueToEntityField($_POST['entityId'], $_POST['fieldValue']);
    } catch (\LiamProject\Exceptions\WrongEntityIdException $e) {
        $controller->viewPage($e->getMessage());
        return;
    }
}

$controller->viewPage();