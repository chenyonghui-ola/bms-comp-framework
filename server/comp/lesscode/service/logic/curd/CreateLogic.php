<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\ResponseInside;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Logic\Curd\Mysql\CreateLogic as MysqlCreateLogic;
use Imee\Service\Lesscode\Logic\Curd\Api\CreateLogic as ApiCreateLogic;
use Imee\Service\Lesscode\Strategys\CurdStrategy;

class CreateLogic
{
    use ResponseInside;

    private $params;

    /**
     * @var string
     */
    private $drive;

    /**
     * @var string
     */
    private $guid;

    private $classMap = [
        AdapterSchema::DRIVE_MYSQL => MysqlCreateLogic::class,
        AdapterSchema::DRIVE_API   => ApiCreateLogic::class,
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
        $point = LesscodeSchemaPoint::getInfoByGuidAndType($this->guid, AdapterSchema::POINT_CREATE);

        if (!empty($point)) {
            $point = $point->toArray();
        }

        $this->drive = isset($point['drive']) && !empty($point['drive']) ? $point['drive'] : AdapterSchema::getDriveFuncDefault();
        $logic = $this->classMap[$this->drive];
        $res   = [];

        if (isset($logic) && !empty($logic)) {
            $curdStrategy = new CurdStrategy(new $logic($this->params));
            $res = $curdStrategy->create();
        }

        if (!isset($res['success'])) {
            $res = self::success($res);
        }

        return $res;
    }
}