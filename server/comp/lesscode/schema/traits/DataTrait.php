<?php


namespace Imee\Schema\Traits;


use Imee\Models\Cms\Lesscode\LesscodeSchemaConfig;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPointConfig;

trait DataTrait
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $points;

    /**
     * @var array
     */
    private $pointsConfig;

    protected function setData()
    {
        $this->config = LesscodeSchemaConfig::findFirstByGuid($this->guid)->toArray();
        $this->model = $this->config['model'];
        $this->points = LesscodeSchemaPoint::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ])->toArray();

        $this->points = array_column($this->points, null, 'id');
        $pointsConfig = LesscodeSchemaPointConfig::find([
            'conditions' => 'guid = :guid:',
            'bind' => ['guid' => $this->guid]
        ])->toArray();

        foreach ($pointsConfig as $item)
        {
            $this->pointsConfig[$item['point_id']][] = $item;
        }

        unset($pointsConfig, $item);

        foreach ($this->points as $point)
        {
            $method = 'set' . ucfirst($point['type']) . 'Data';

            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $point);
            } else {
                $this->setOperateData($point);
            }
        }
    }

    private function setListData($point)
    {
        if (!empty($point['logic'])) {
            $this->logics[static::POINT_LIST] = $point['logic'];
        }

        // 暂时默认去第一条数据
        $config = current($this->pointsConfig[$point['id']]);

        if (!isset($config['config']) || empty($config['config'])) {
            return;
        }

        $configList = json_decode($config['config'], true);

        if (!empty($this->config) && !empty($this->config['table_config'])) {
            $this->table = json_decode($this->config['table_config'], true);
        }

        if (isset($configList['list']) && !empty($configList['list'])) {
            $this->list = $configList['list'];
        }

        if (isset($configList['filter']) && !empty($configList['filter'])) {
            $this->listFilter = $configList['filter'];
        }

        if (isset($configList['fields']) && !empty($configList['fields'])) {
            $this->listFields = $configList['fields'];
        }

        // 是否多选
        $this->listMultiple = $configList['multiple'] ?? false;

        // 是否缓存
        $this->listFilterCache = $configList['filterCache'] ?? false;

        // 是否支持挂件
        $this->listPatch = $configList['patch'] ?? [];
    }

    private function setCreateData($point)
    {
        if (!empty($point['logic'])) {
            $this->logics[static::POINT_CREATE] = $point['logic'];
        }

        // 暂时默认去第一条数据
        $config = current($this->pointsConfig[$point['id']]);

        if (!isset($config['config']) || empty($config['config'])) {
            return;
        }

        $create = json_decode($config['config'], true);
        $this->create = $create;
        unset($create['fields']);

        if (!empty($create)) {
            $this->listExtra['form'][static::POINT_CREATE] = $create;
        }

        $this->setActionData($point);
    }

    private function setModifyData($point)
    {
        if (!empty($point['logic'])) {
            $this->logics[static::POINT_MODIFY] = $point['logic'];
        }

        // 暂时默认去第一条数据
        $config = current($this->pointsConfig[$point['id']]);

        if (!isset($config['config']) || empty($config['config'])) {
            return;
        }

        $modify = json_decode($config['config'], true);
        $this->modify = $modify;
        unset($modify['fields']);

        if (!empty($create)) {
            $this->listExtra['form'][static::POINT_MODIFY] = $modify;
        }

        $this->setOperateData($point, ['icon' => 'icon-xiugai']);
    }

    private function setDeleteData($point)
    {
        if (!empty($point['logic'])) {
            $this->logics[static::POINT_DELETE] = $point['logic'];
        }

        $this->setOperateData($point, ['icon' => 'icon-jinrongxianxingge-']);
    }

    private function setExportData($point)
    {
        $this->setActionData($point);
    }

    private function setOperateData($point, $attach = [])
    {
        if (!empty($point['logic'])) {
            $this->logics[$point['type']] = $point['logic'];
        }

        // 暂时默认去第一条数据
        $config = current($this->pointsConfig[$point['id']]);

        if (!isset($config['config']) || empty($config['config'])) {
            return;
        }

        $operate = (array) json_decode($config['config'], true);
        $this->operate[] = array_merge(
            ['title' => $point['title'], 'type' => $point['type'], 'state' => $point['state']],
            $this->mergeConfigData($operate, $attach)
        );
    }

    private function setActionData($point, $attach = [])
    {
        if (!empty($point['logic'])) {
            $this->logics[$point['type']] = $point['logic'];
        }

        // 暂时默认去第一条数据
        $config = current($this->pointsConfig[$point['id']] ?? []);

        if (!isset($config['config']) || empty($config['config'])) {
            return;
        }

        $action = (array) json_decode($config['config'], true);
        $this->listAction[] = array_merge(
            ['title' => $point['title'], 'type' => $point['type'], 'state' => $point['state']],
            $this->mergeConfigData($action, $attach)
        );
    }

    private function mergeConfigData($data, $attach)
    {
        if (empty($data) && empty($attach)) {
            return [];
        }

        if (!empty($data) && empty($attach)) {
            return $data;
        }

        if (empty($data) && !empty($attach)) {
            return $attach;
        }

        foreach ($data as $key => $item)
        {
            if (empty($item) && isset($attach[$key]) && !empty($attach[$key])) {
                $data[$key] = $attach[$key];
                unset($attach[$key]);
            }
        }

        return array_merge($data, $attach);
    }

}