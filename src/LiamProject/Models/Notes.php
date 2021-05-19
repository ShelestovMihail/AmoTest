<?php
namespace LiamProject\Models;

use LiamProject\Exceptions\UnauthorizedException;

/**
 * Class Notes
 * @package LiamProject\Models
 */
class Notes extends AmocrmEntity
{

    /**
     * @return string
     */
    protected function setEntityName(): string
    {
        return 'notes';
    }

    /**
     * @param AmocrmEntity $entity
     * @param $noteType
     * @throws UnauthorizedException
     */
    public function setNoteToEntityById(AmocrmEntity $entity, $noteType)
    {
        $entityType = $entity->entityName;
        $entityId = $entity->foundedElement['id'];
            $api = "/api/v4/$entityType/$entityId/notes";

        $commonParams = ['text' => 'Текст Примечания'];
        $callInParams = [
            "uniq" => "8f52d38a-5fb3-406d-93a3-a4832dc28f8b",
            "duration" => 60,
            "source" => "onlinePBX",
            "link" => "https://example.com",
            "phone" => "+79999999999"
        ];

        $params = ($noteType == 'call_in') ? $callInParams : $commonParams;

        $data[] = [
            'note_type' => $noteType,
            "params" => $params
        ];

        $this->queryToAmo($api, 'POST', $data);
    }
}