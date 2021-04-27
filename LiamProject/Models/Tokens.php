<?php
namespace LiamProject\Models;

use Exception;
use LiamProject\Exceptions\EmptyRefreshToken;
use LiamProject\Exceptions\EmptyAccessToken;

class Tokens
{
    public static function checkAccessToken(): bool
    {
        if(!isset($_COOKIE['access_token'])) {
            throw new EmptyAccessToken();
        }
        return true;
    }

    private static function setAccessToken($tokenData)
    {
        setcookie('access_token', $tokenData['access_token'], time() + $tokenData['expires_in']);
        setcookie('refresh_token', $tokenData['refresh_token'], time() + ($tokenData['expires_in'] * 3));
        header('Location: index.php');
    }

    public static function getAccessToken($config)
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
            $curl = curl_init();
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
            curl_setopt($curl,CURLOPT_URL, $link);
            curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
            curl_setopt($curl,CURLOPT_HEADER, false);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
            $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
            $code = (int)$code;
            $errors = [
                400 => 'Bad request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not found',
                500 => 'Internal server error',
                502 => 'Bad gateway',
                503 => 'Service unavailable',
            ];

            try
            {
                /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
                if ($code < 200 || $code > 204) {
                    throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
                }
            }
            catch(Exception $e)
            {
                die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
            }
            $response = json_decode($out, true);
            self::setAccessToken($response);
        }

        if (!isset($_COOKIE['refreshToken']) && empty($_GET)) {
            throw new EmptyRefreshToken('Пустой refresh token');
        }
    }
}
