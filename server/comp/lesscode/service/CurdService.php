<?php

namespace Imee\Service\Lesscode;

use Imee\Service\Lesscode\Logic\Curd\{ListLogic, CreateLogic, SaveLogic, DeleteLogic, ExportLogic};

class CurdService
{
    public function getlist($params)
    {
        $logic = new ListLogic($params);

        return $logic->handle();
    }

    public function create($params)
    {
        $logic = new CreateLogic($params);

        return $logic->handle();
    }

    public function modify($params)
    {
        $logic = new SaveLogic($params);

        return $logic->handle();
    }

    public function delete($params)
    {
        $logic = new DeleteLogic($params);

        return $logic->handle();
    }

    public function export($params)
    {
        $logic = new ExportLogic($params);

        return $logic->handle();
    }
}