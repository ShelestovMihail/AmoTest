<?php require __DIR__ . '/header.php' ?>
    <h4><?= $error ?></h4>
    <a href="https://www.amocrm.ru/oauth?client_id=<?= $integrationId; ?>&state=test&mode=popup">
        Сслыка для получения кода авторизации
    </a>
<?php require __DIR__ . '/footer.php' ?>