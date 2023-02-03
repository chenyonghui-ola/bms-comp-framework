<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Helper\Traits\ResponseInside;
use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Logic\Curd\Mysql\ExportLogic as MysqlExportLogic;
use Imee\Service\Lesscode\Logic\Curd\Api\ExportLogic as ApiExportLogic;
use Imee\Service\Lesscode\Strategys\CurdStrategy;

class ExportLogic
{
    use ResponseInside;

    private $params;

    /**
     * @var string
     */
    private $drive;

    private $classMap = [
        AdapterSchema::DRIVE_MYSQL => MysqlExportLogic::class,
        AdapterSchema::DRIVE_API   => ApiExportLogic::class,
        AdapterSchema::DRIVE_MONGO => '', // 暂不支持mongo
    ];

    public function __construct($params)
    {
        $this->params = $params;
        $this->drive = AdapterSchema::getDriveFuncDefault();
    }

    public function handle()
    {
        $point = LesscodeSchemaPoint::getInfoByGuidAndType($this->params['guid'], AdapterSchema::POINT_EXPORT);

        if (!empty($point)) {
            $point = $point->toArray();
        }

        $this->drive = isset($point['drive']) && !empty($point['drive']) ? $point['drive'] : AdapterSchema::getDriveFuncDefault();
        $logic = $this->classMap[$this->drive];
        $res   = [];

        if (isset($logic) && !empty($logic)) {
            $curdStrategy = new CurdStrategy(new $logic($this->params));
            $res = $curdStrategy->export();
        }

        if (!isset($res['success'])) {
            $res = self::success($res);
        }


        return $res;
    }
}