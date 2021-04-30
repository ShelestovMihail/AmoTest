<?php
namespace LiamProject\Models;


class Segments extends AmocrmEntity
{
    public function getSegmentById($id): ?array
    {
        $api = "/api/v4/customers/segments/$id";
        return $this->queryToAmo($api);
    }
}