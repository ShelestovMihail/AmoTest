<?php
require_once 'autoload.php';

use LiamProject\Controllers\MainController;
use LiamProject\Exceptions\ExpiredRefreshTokenException;
use LiamProject\Controllers\ConfigController;
use LiamProject\Exceptions\ExpiredAccessTokenException;
use LiamProject\Exceptions\EmptyTokensException;
use LiamProject\Exceptions\UserNotFoundException;
use LiamProject\Exceptions\WrongEntityIdException;

ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);

session_start();

//Проверка токена
try {
    $controller = new MainController();
    $controller->checkAccessToken();
} catch (ExpiredAccessTokenException $e) {
    $controller->refreshToken();
} catch (ExpiredRefreshTokenException | EmptyTokensException  $e) {
    $controller->addAccessToken();
    $controller->viewButton();
    return;
}

//Редактирование конфига - id интеграции, секретный ключ, субдомен
if (!empty($_POST['setConfig'])) {
    $configController = new ConfigController();
    $configController->setIntegrationConfig();
}

//Запросы к API
try {
    if (!empty($_POST['newEntities'])) {
        $controller->addEntities($_POST['entityCount']);
    }

    if (!empty($_POST['setValueToEntityField'])) {
        $controller->setValueToEntityField($_POST['entityId'], $_POST['fieldValue']);
    }

    if (!empty($_POST['setNoteToEntity'])) {
        $controller->setNoteToEntity($_POST['entityId'], $_POST['noteType']);
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
} catch (\LiamProject\Exceptions\UnauthorizedException $e) {
    $controller->clearTokens();
    $controller->viewButton();
    return;
} catch (Error $e) {
    $controller->viewPage('Произошла фатальная ошибка: ' . $e->getMessage());
    return;
} catch (WrongEntityIdException | UserNotFoundException $e) {
    $controller->viewPage($e->getMessage());
    return;
}

//Отрисовка страницы с формами
$controller->viewPage();
