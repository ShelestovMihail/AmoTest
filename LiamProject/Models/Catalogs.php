<?php
namespace LiamProject\Models;


class Catalogs extends AmocrmEntity
{
    public function getCatalogById($id): ?array
    {
        $api = "/api/v4/catalogs/$id";

        return $this->queryToAmo($api);
    }
}