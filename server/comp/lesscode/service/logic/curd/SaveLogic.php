<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\ResponseInside;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Logic\Curd\Mysql\SaveLogic as MysqlSaveLogic;
use Imee\Service\Lesscode\Logic\Curd\Api\SaveLogic as ApiSaveLogic;
use Imee\Service\Lesscode\Strategys\CurdStrategy;

class SaveLogic
{
    use ResponseInside;

    private $params;

    /**
     * @var string
     */
    private $drive;

    private $guid;

    private $classMap = [
        AdapterSchema::DRIVE_MYSQL => MysqlSaveLogic::class,
        AdapterSchema::DRIVE_API   => ApiSaveLogic::class,
        AdapterSchema::DRIVE_MONGO => '', // 暂不支持mongo
    ];

    public function __construct($params)
    {
        $this->params = $params;
        $this->drive  = AdapterSchema::getDriveFuncDefault();
        $this->guid   = AdapterSchema::getRequestGuid();
        $this->guid   = !empty($this->guid) ? $this->guid : $this->params['guid'];
    }

    public function handle()
    {
        $point = LesscodeSchemaPoint::getInfoByGuidAndType($this->guid, AdapterSchema::POINT_MODIFY);

        if (!empty($point)) {
            $point = $point->toArray();
        }

        $this->drive = isset($point['drive']) && !empty($point['drive']) ? $point['drive'] : AdapterSchema::getDriveFuncDefault();
        $logic = $this->classMap[$this->drive];
        $res   = [];

        if (isset($logic) && !empty($logic)) {
            $curdStrategy = new CurdStrategy(new $logic($this->params));
            $res = $curdStrategy->modify();
        }

        if (!isset($res['success'])) {
            $res = self::success($res);
        }

        return $res;
    }
}