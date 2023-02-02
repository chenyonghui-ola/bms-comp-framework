<?php


namespace Imee\Service\Lesscode\Logic\Curd\Mysql;

use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Exception\CurdException;
use Imee\Service\Lesscode\Schema\FieldService;
use Imee\Service\Lesscode\Logic\Curd\BaseLogic;

class DeleteLogic extends BaseLogic
{
    protected $opType = AdapterSchema::POINT_DELETE;
    protected $drive = AdapterSchema::DRIVE_MYSQL;

    /**
     * @var string delete|deleteBatch
     */
    private $action = 'delete';

    public function handle()
    {
        parent::handle();

		$this->hookService->onSetParams($this->params);

        if (true === $this->hookService->onRewriteDelete()) {
            return $this->rewriteDelete();
        }

        $this->fieldService = new FieldService(new $this->model, $this->schema);
        $pk                 = $this->fieldService->getPk();

        if (!isset($this->params[$pk]) || empty($pk)) {
            [$code, $msg] = CurdException::ILLEGAL_ERROR;
            throw new CurdException($msg, $code);
        }

        $pkVal = $this->params[$pk];
        unset($this->params[$pk]);

        // todo lesscode 数据校验 validations

        $model = $this->getData($pk, $pkVal);

        if (!$model) {
            [$code, $msg] = CurdException::NO_DATA_ERROR;
            throw new CurdException($msg, $code);
        }

        $this->hookService->onBeforeDelete($this->params, $model);

        $bool = $this->delete($model);

        if (false === $bool) {
            [$code, $msg] = CurdException::DELETE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterDelete($this->params, $model);

        return $res;
    }


    public function rewriteDelete()
    {
        $this->hookService->onBeforeDelete($this->params, []);

        $bool = $this->hookService->onDelete($this->params);

        if (false === $bool) {
            [$code, $msg] = CurdException::SAVE_ERROR;
            throw new CurdException($msg, $code);
        }

        $res = $this->hookService->onAfterDelete($this->params, []);

        return $res;
    }

    /**
     * 支持批量操作
     * @param $pk
     * @param $pkVal
     * @return mixed
     */
    protected function getData($pk, $pkVal)
    {
        if (is_array($pkVal)) {
            $this->action = 'deleteBatch';
            $info         = $this->model::find([
                'conditions' => $pk . ' IN ({pk:array})',
                'bind'       => ['pk' => $pkVal]
            ]);
        } else {
            $info = $this->model::findFirst([
                'conditions' => $pk . ' = :pk:',
                'bind'       => ['pk' => $pkVal]
            ]);
        }

        return $info;
    }

    protected function delete($model)
    {
        if ($this->action === 'deleteBatch') {
            $bool = true;
            foreach ($model as $item)
            {
                if (method_exists($item, 'setLogAttr')) {
                    $item->setLogAttr([
                        'op_uid' => $this->params['admin_uid'] ?? 0,
                        'action' => $this->action
                    ]);
                }
                $bool = $item->delete();
            }

            return $bool;

        } else {
            if (method_exists($model, 'setLogAttr')) {
                $model->setLogAttr([
                    'op_uid' => $this->params['admin_uid'] ?? 0,
                    'action' => $this->action
                ]);
            }

            return $model->delete();
        }

    }
}