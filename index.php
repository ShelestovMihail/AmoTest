<?php
declare(strict_types=1);

use LiamProject\Controllers\MainController;
use LiamProject\Exceptions\ExpiredRefreshTokenException;
use LiamProject\Controllers\ConfigController;
use LiamProject\Exceptions\ExpiredAccessTokenException;
use LiamProject\Exceptions\EmptyTokensException;
use LiamProject\Exceptions\UnauthorizedException;
use LiamProject\Exceptions\UserNotFoundException;
use LiamProject\Exceptions\WrongEntityIdException;

ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

require_once 'autoload.php';

session_start();
$errorMessage = '';

//Редактирование конфига - id интеграции, секретный ключ, субдомен
if (!empty($_POST['setConfig'])) {
    try {
        $configController = new ConfigController();
        $configController->setIntegrationConfig();
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

$controller = new MainController();

//Проверка токена
try {
    $controller->checkAccessToken();
} catch (ExpiredAccessTokenException $e) {
    $controller->refreshToken();
} catch (ExpiredRefreshTokenException | EmptyTokensException  $e) {
    $controller->addAccessToken();
    $controller->viewButton($errorMessage);
    return;
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
} catch (UnauthorizedException $e) {
    $controller->clearTokens();
    $controller->viewButton($e->getMessage());
    return;
} catch (WrongEntityIdException | UserNotFoundException | Exception | Error $e) {
    $errorMessage = $e->getMessage();
}

//Отрисовка страницы с формами
$controller->viewPage($errorMessage);
