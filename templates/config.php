<form action="" method="post">
    <label>Id Интеграции: <input type="text" name="integrationId" value="<?= $integrationId ?? 'integration id' ?>"></label>
    <label>Домен: <input type="text" name="domain" value="<?= $domain ?? ' domain' ?>"></label>
    <label>Secret Key: <input type="text" name="secretKey" value="<?= $secretKey ?? 'secret key' ?>"></label>
    <label>url для редиректа: <input type="text" name="url" value="<?= $url ?? 'url' ?>"></label>
    <input type="submit" name="setConfig" value="Задать конфиг">
</form>
<hr>