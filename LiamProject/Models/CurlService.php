<?php
namespace LiamProject\Models;


use Exception;

class CurlService
{
    protected static $curl;

    public static function init()
    {
        if (empty(static::$curl)) {
            static::$curl = curl_init();
        }
    }

    public static function setOpt()
    {
          curl_setopt(static::$curl,CURLOPT_RETURNTRANSFER, true);
          curl_setopt(static::$curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
          curl_setopt(static::$curl,CURLOPT_HEADER, false);
          curl_setopt(static::$curl,CURLOPT_SSL_VERIFYPEER, 1);
          curl_setopt(static::$curl,CURLOPT_SSL_VERIFYHOST, 2);
    }

    public static function setLink($link)
    {
        curl_setopt(static::$curl,CURLOPT_URL, $link);
    }

    public static function setMethod($method)
    {
        curl_setopt(static::$curl,CURLOPT_CUSTOMREQUEST, $method);
    }

    public static function setHeaders(array $headers = ['Content-Type:application/json'])
    {
        curl_setopt(static::$curl,CURLOPT_HTTPHEADER, $headers);
    }

    public static function setData($data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt(static::$curl,CURLOPT_POSTFIELDS, $data);
    }

    public static function exec()
    {
        $out = curl_exec(static::$curl);
        return $out;
    }

    public static function close()
    {
        curl_close(static::$curl);
    }
}