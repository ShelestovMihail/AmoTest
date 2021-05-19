<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Account
 * @package LiamProject\Models
 */
class Account extends AmocrmEntity
{
    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'account';
    }

    /**
     * @return mixed
     * @throws UnauthorizedException
     */
    public function checkCustomersMode()
    {
        $api = '/api/v4/account';
        $response = $this->queryToAmo($api);

        return ($response['customers_mode']);
    }
}