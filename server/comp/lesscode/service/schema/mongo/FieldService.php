<?php

namespace Imee\Service\Lesscode\Schema\Mongo;

use Imee\Service\Lesscode\Schema\FieldService as NormalFieldService;

class FieldService extends NormalFieldService
{
    public function setAttachGetData($attachModel, $attach, $joinFields, $masterIds)
    {
        $conditions = [
            'conditions' => [
                $joinFields => ['$in' => $masterIds]
            ]
        ];

        return $attachModel::aggFind($conditions);
    }
}