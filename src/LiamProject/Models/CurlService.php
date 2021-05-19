<?php
namespace LiamProject\Models;

/**
 * Class CurlService
 * @package LiamProject\Models
 */
class CurlService
{
    /**
     * @var
     */
    protected static $curl;

    /**
     *
     */
    public static function init()
    {
        if (empty(static::$curl)) {
            static::$curl = curl_init();
        }
    }

    /**
     *
     */
    public static function setOpt()
    {
          curl_setopt(static::$curl,CURLOPT_RETURNTRANSFER, true);
          curl_setopt(static::$curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
          curl_setopt(static::$curl,CURLOPT_HEADER, false);
          curl_setopt(static::$curl,CURLOPT_SSL_VERIFYPEER, 1);
          curl_setopt(static::$curl,CURLOPT_SSL_VERIFYHOST, 2);
    }

    /**
     * @param $link
     */
    public static function setLink($link)
    {
        curl_setopt(static::$curl,CURLOPT_URL, $link);
    }

    /**
     * @param $method
     */
    public static function setMethod($method)
    {
        curl_setopt(static::$curl,CURLOPT_CUSTOMREQUEST, $method);
    }

    /**
     * @param array|string[] $headers
     */
    public static function setHeaders(array $headers = ['Content-Type:application/json'])
    {
        curl_setopt(static::$curl,CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @param $data
     */
    public static function setData($data)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt(static::$curl,CURLOPT_POSTFIELDS, $data);
    }

    /**
     * @return bool|string
     */
    public static function exec()
    {
         return curl_exec(static::$curl);
    }

    /**
     *
     */
    public static function close()
    {
        curl_close(static::$curl);
    }
}