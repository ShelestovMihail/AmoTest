<?php
namespace LiamProject\Models;


class Account extends AmocrmEntity
{
    public function checkCustomersMode()
    {
        $api = '/api/v4/account';

        $response = $this->queryToAmo($api);

        return ($response['customers_mode']);
    }
}