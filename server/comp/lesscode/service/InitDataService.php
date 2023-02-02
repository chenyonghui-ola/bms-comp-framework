<?php

namespace Imee\Service\Lesscode;

use Imee\Models\Cms\CmsModules;
use Imee\Models\Cms\Lesscode\LesscodeMenu;
use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;
use Imee\Schema\AdapterSchema;
use Imee\Service\Helper;
use Imee\Service\Lesscode\Exception\CommonException;

/**
 *  初始化数据
 */
class InitDataService
{
    /**
     * @var AdapterSchema
     */
    private $schema = AdapterSchema::class;


    private $op;
    private $guid;

    /**
     * @var array 新老菜单映射 old => new
     */
    private $menuMap = [];

    /**
     * @var array 存储guid和菜单的关系
     */
    private $guidMenu = [];

    /**
     * @var array point config map  old => new
     */
    private $pointConfigMap = [];

    public function __construct($guid, $op)
    {
        $this->guid = $guid;
        $this->op   = $op;
    }

    public function handle()
    {
        // 本地使用 拿出所有需要处理的数据
        if (empty($this->guid)) {
            [$code, $msg] = CommonException::FILTER_NO_TEXT;
            throw new CommonException(sprintf($msg, 'guid') . "\n", $code);
        }

        $this->parseGuid();

        if ($this->op == 1) {
            $this->selectData();
            return true;
        }

        $this->updateData();

        return true;
    }

    private function parseGuid()
    {
        $this->guid = array_map('trim', explode(',', $this->guid));
    }

    private function selectData()
    {
        $list = [];

        foreach ($this->guid as $guid)
        {
            // 根据不同表查询数据
            $list[$guid] = [
//                CmsModules::class => $this->selectCmsModules($guid),
//                LesscodeMenu::class => $this->selectLesscodeMenu($guid),
                LesscodeSchemaConfig::class => $this->selectLesscodeSchemaConfig($guid),
                LesscodeSchemaPoint::class => $this->selectLesscodeSchemaPoint($guid),
                LesscodeSchemaPointConfig::class => $this->selectLesscodeSchemaPointConfig($guid),
            ];
        }

        $jsonData = json_encode($list, JSON_UNESCAPED_UNICODE);

        Helper::console(
            'json data:'
            . PHP_EOL
            . $jsonData
        );

        $this->saveFile($list);
    }

    private function selectCmsModules($guid)
    {
        return CmsModules::find([
            'conditions' => 'controller like :controller:',
            'bind' => ['controller' => '%/' . $guid . '%']
        ])->toArray();
    }

    private function selectLesscodeMenu($guid)
    {
        return LesscodeMenu::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $guid]
        ])->toArray();
    }

    private function selectLesscodeSchemaConfig($guid)
    {
        return LesscodeSchemaConfig::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $guid]
        ])->toArray();
    }

    private function selectLesscodeSchemaPoint($guid)
    {
        return LesscodeSchemaPoint::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $guid]
        ])->toArray();
    }

    private function selectLesscodeSchemaPointConfig($guid)
    {
        return LesscodeSchemaPointConfig::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $guid]
        ])->toArray();
    }

    private function saveFile($data)
    {
        foreach ($data as $guid => $config)
        {
            if ($this->schema::isSystemGuid($guid)) {
                $dir = ROOT . '/lesscode/tmp/';
                $fileName = 'initSystemData_' . $guid . '.json';
            } else {
                $dir = ROOT . '/public/tmp/';
                $fileName = 'initData_' . $guid . '.json';
            }
            is_dir($dir) || mkdir($dir, 0777, true);
            file_put_contents($dir . $fileName, json_encode($config, JSON_UNESCAPED_UNICODE));
        }
    }

    private function getFile()
    {
        $guids = $this->guid;
        foreach ($guids as $guid)
        {
            if ($this->schema::isSystemGuid($guid)) {
                $dir = ROOT . '/lesscode/tmp/';
                $fileName = 'initSystemData_' . $guid . '.json';
            } else {
                $dir = ROOT . '/public/tmp/';
                $fileName = 'initData_' . $guid . '.json';
            }

            $fileStr = @file_get_contents($dir . $fileName);

            yield [$guid, !empty($fileStr) ? $fileStr : '{}'];
        }
    }

    private function updateData()
    {
        foreach ($this->getFile() as $fileStr)
        {
            [$guid, $fileStr] = $fileStr;

            if (empty($fileStr)) {
                Helper::console("guid:{$guid}, 无数据需要处理");
                continue;
            }

            $data = json_decode($fileStr, true);
            unset($fileStr);

            if (empty($data)) {
                Helper::console("guid:{$guid}, json数据解析为空");
                continue;
            }

            $funMap = [
                LesscodeSchemaConfig::class      => 'setLesscodeSchemaConfig',
                LesscodeSchemaPoint::class       => 'setLesscodeSchemaPoint',
                LesscodeSchemaPointConfig::class => 'setLesscodeSchemaPointConfig',
            ];

            $this->guid = $guid;
            Helper::console('guid:' . $guid);
            foreach ($data as $modelName => $value)
            {
                Helper::console("modelName:{$modelName} start");
                isset($funMap[$modelName]) && call_user_func([$this, $funMap[$modelName]], $value);
                Helper::console("modelName:{$modelName} end");
            }
        }
    }

    private function setCmsModules($data)
    {
        if (empty($data)) {
            return;
        }

        $info = CmsModules::findFirst([
            'conditions' => 'controller like :controller:',
            'bind' => ['controller' => '%/' . $this->guid . '%']
        ]);

        if (!empty($info)) {
            return;
        }

        foreach ($data as $item)
        {
            $id  = $item['module_id'];
            $pid = $item['parent_module_id'];
            unset($item['module_id']);

            $model = new CmsModules();

            foreach ($item as $field => $value) {
                if ($field == 'parent_module_id') {
                    if (isset($this->menuMap[$this->guid][$value])) {
                        $value = $this->menuMap[$this->guid][$value];
                    }
                }
                $model->{$field} = $value;
            }

            $model->save();

            if ($model->module_id <= 0) {
                Helper::console('setCmsModules err, id:' . $id);
                continue;
            }

            $this->menuMap[$this->guid][$id] = $model->module_id;

            if (in_array($model->action, [$this->schema::POINT_MAIN, $this->schema::POINT_LIST, $this->schema::POINT_CREATE, $this->schema::POINT_MODIFY, $this->schema::POINT_DELETE, $this->schema::POINT_EXPORT])) {
                $this->guidMenu[$this->guid][$model->action] = $model->module_id;
            }

        }

        Helper::console('menuMap ===> ' . print_r($this->menuMap[$this->guid], true));
        Helper::console('guidMenu ===> ' . print_r($this->guidMenu[$this->guid], true));
    }

    private function setLesscodeMenu($data)
    {
        if (empty($data)) {
            return;
        }

        $info = LesscodeMenu::findFirst([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ]);

        if (!empty($info)) {
            return;
        }

        foreach ($data as $key => $item)
        {
            $id = $item['id'];
            unset($item['id']);

            if ($key == 0) {
                $guidMenu = current($this->guidMenu[$this->guid]);
            } else {
                $guidMenu = next($this->guidMenu[$this->guid]);
            }

            $model = new LesscodeMenu();

            foreach ($item as $field => $value)
            {
                if ($field == 'menu_id') {
                    $value = $guidMenu;
                }

                $model->{$field} = $value;
            }

            $model->save();
        }
    }

    private function setLesscodeSchemaConfig($data)
    {
        if (empty($data)) {
            return;
        }

        $info = LesscodeSchemaConfig::findFirst([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ]);

        $this->log($info, 'LesscodeSchemaConfig');

        foreach ($data as $key => $item)
        {
            $id = $item['id'];
            unset($item['id']);

            if (empty($info)) {
                $info = new LesscodeSchemaConfig();
            }

            foreach ($item as $field => $value)
            {
                $info->{$field} = $value;
            }

            $info->save();
        }
    }

    private function setLesscodeSchemaPoint($data)
    {
        if (empty($data)) {
            return;
        }

        $list = LesscodeSchemaPoint::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ]);

        if ($list->valid()) {
            $this->log($list, 'LesscodeSchemaPoint');
            $list->delete();
        }

        foreach ($data as $key => $item)
        {
            $id = $item['id'];
            unset($item['id']);

            $model = new LesscodeSchemaPoint();

            foreach ($item as $field => $value)
            {
                $model->{$field} = $value;
            }

            $model->save();

            if ($model->id > 0) {
                $this->pointConfigMap[$this->guid][$id] = $model->id;
            }
        }
    }

    private function setLesscodeSchemaPointConfig($data)
    {
        if (empty($data)) {
            return;
        }

        $list = LesscodeSchemaPointConfig::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ]);

        if ($list->valid()) {
            $this->log($list, 'LesscodeSchemaPointConfig');
            $list->delete();
        }

        foreach ($data as $key => $item)
        {
            $id = $item['id'];
            unset($item['id']);

            $model = new LesscodeSchemaPointConfig();

            foreach ($item as $field => $value)
            {
                if ($field == 'point_id') {
                    $value = $this->pointConfigMap[$this->guid][$value];
                }
                $model->{$field} = $value;
            }

            $model->save();
        }
    }

    private function log($model, $type = '')
    {
        if (ENV !== 'dev') {
            return;
        }

        if (empty($model)) {
            return;
        }

        $arr = $model->toArray();

        if (empty($arr)) {
            return;
        }

        $date = date('Y-m-d H:i:s');
        $text = "[{$date}] guid:{$this->guid} type:{$type}" . PHP_EOL;
        $file = '/tmp/tmp_init_data_import_' . date('Y-m') . '.log';

        @file_put_contents($file, $text . @json_encode($arr, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}