<?php

namespace Imee\Service\Lesscode\Logic\Schema;


use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Service\Lesscode\Traits\Curd\ListTrait;
use Phalcon\Di;

class GuidPointListLogic
{
    use ListTrait;

    /**
     * @var LesscodeSchemaConfig
     */
    protected $masterModel = LesscodeSchemaConfig::class;

    /**
     * @var LesscodeSchemaPointConfig
     */
    protected $pointConfigModel = LesscodeSchemaPointConfig::class;

    public function onGetFilter(&$filter)
    {
        $request = Di::getDefault()->get('request');
        $id = $request->getQuery('id') ?? 0;

        $config = $this->masterModel::findFirstById($id);

        // 只能看到不是系统级的功能
        !empty($config) && $filter['guid'] = $config->guid;
//        $filter['is_system'] = 0;
    }

    public function onListFormat(&$item)
    {
        $config = $this->pointConfigModel::findFirst([
            'conditions' => 'point_id = :point_id:',
            'bind' => ['point_id' => $item['id']]
        ]);

        if ($config) {
            $item['config'] = (string) json_encode(json_decode($config->config, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $item['config'] = '';
        }
    }

    public function onAfterList($list): array
    {
        return $list;
    }
}