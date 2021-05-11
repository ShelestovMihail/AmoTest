<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\EmptyTokensException;
use LiamProject\Exceptions\ExpiredRefreshTokenException;
use LiamProject\Exceptions\ExpiredAccessTokenException;

class Tokens
{
    private $accessToken;
    private $refreshToken;
    private $pathToTokens = __DIR__ . '/../../../tokens.json';

    public function __construct()
    {
        $tokens = json_decode(file_get_contents($this->pathToTokens), true);
        if ($tokens === null) {
            return;
        }

        $this->accessToken = $tokens['accessToken'];
        $this->refreshToken = $tokens['refreshToken'];
    }

    public function checkTokens(): bool
    {
        $tokens = json_decode(file_get_contents($this->pathToTokens), true);
        if ($tokens === null) {
            throw new EmptyTokensException('Токены не были получены');
        }
        if ($this->refreshToken['expireTime'] < time()) {
            throw new ExpiredRefreshTokenException();
        }
        if ($this->accessToken['expireTime'] < time()) {
            throw new ExpiredAccessTokenException();
        }

        $_SESSION['access_token'] = $this->accessToken['token'];
        return true;
    }

    private function setAccessToken($tokenData)
    {
        $tokens = [
            'accessToken' => [
                'token' => $tokenData['access_token'],
                'expireTime' => (time() + $tokenData['expires_in'])
            ],
            'refreshToken' => [
                'token' => $tokenData['refresh_token'],
                'expireTime' => (time() + ($tokenData['expires_in'] * 90)),
            ]
        ];
        file_put_contents($this->pathToTokens, json_encode($tokens));
        header('Location: index.php');
    }

    public function addAccessToken($config)
    {
        if (!empty($_GET['code'])) {
            $link = 'https://' . $config['subdomain'] . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса
            $data = [
                'client_id' => $config['integrationId'],
                'client_secret' => $config['secretKey'],
                'grant_type' => 'authorization_code',
                'code' => $_GET['code'],
                'redirect_uri' => $config['url'],
            ];

            CurlService::init();
            CurlService::setOpt();
            CurlService::setLink($link);
            CurlService::setMethod('POST');
            CurlService::setHeaders();
            CurlService::setData($data);
            $out = CurlService::exec();
            CurlService::close();

            $response = json_decode($out, true);
            $this->setAccessToken($response);
        }
    }

    public function refreshToken($config)
    {
        $link = 'https://' . $config['subdomain'] . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса
        $data = [
            'client_id' => $config['integrationId'],
            'client_secret' => $config['secretKey'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken['token'],
            'redirect_uri' => $config['url'],
        ];

        CurlService::init();
        CurlService::setOpt();
        CurlService::setLink($link);
        CurlService::setMethod('POST');
        CurlService::setHeaders();
        CurlService::setData($data);
        $out = CurlService::exec();

        $response = json_decode($out, true);
        $this->setAccessToken($response);
    }

    public function clearTokens()
    {
        unset($_SESSION['access_token']);
        file_put_contents($this->pathToTokens, '');
    }
}
