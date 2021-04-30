<?php
require_once 'autoload.php';

use LiamProject\Controllers\MainController;
use LiamProject\Exceptions\ExpiredRefreshTokenException;
use LiamProject\Controllers\ConfigController;
use LiamProject\Exceptions\ExpiredAccessTokenException;
use LiamProject\Exceptions\EmptyTokensException;
use LiamProject\Exceptions\WrongEntityIdException;

ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);
ini_set("max_execution_time", 2);
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
    } catch (WrongEntityIdException $e) {
        $controller->viewPage($e->getMessage());
        return;
    }
}

if (!empty($_POST['setNoteToEntity'])) {
    try {
        $controller->setNoteToEntity($_POST['entityId'], $_POST['noteType']);
    } catch (WrongEntityIdException $e) {
        $controller->viewPage($e->getMessage());
        return;
    }
}

if (!empty($_POST['setTaskToEntity'])) {
    $controller->setTaskToEntity(
        $_POST['completeTill'],
        $_POST['taskText'],
        $_POST['responsibleUserId'],
        $_POST['entityId']
    );
}

if (!empty($_POST['completeTask'])) {
    $controller->completeTask($_POST['taskId']);
}

$controller->viewPage();