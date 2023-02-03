<?php


namespace Imee\Service\Lesscode\Logic\Curd;


use Imee\Models\Cms\Lesscode\LesscodeSchemaPoint;
use Imee\Schema\AdapterSchema;
use Imee\Service\Lesscode\Interfaces\HandleInterface;
use Imee\Service\Lesscode\Logic\Curd\Api\ListLogic as ApiListLogic;
use Imee\Service\Lesscode\Logic\Curd\Mysql\ListLogic as MysqlListLogic;
use Imee\Service\Lesscode\Logic\Curd\Mongo\ListLogic as MongoListLogic;
use Imee\Service\Lesscode\Strategys\CurdStrategy;

class ListLogic implements HandleInterface
{
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
        AdapterSchema::DRIVE_MYSQL => MysqlListLogic::class,
        AdapterSchema::DRIVE_API   => ApiListLogic::class,
        AdapterSchema::DRIVE_MONGO => MongoListLogic::class,
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
        $point = LesscodeSchemaPoint::getInfoByGuidAndType($this->guid, AdapterSchema::POINT_LIST);

        if (!empty($point)) {
            $point = $point->toArray();
        }

        $this->drive = isset($point['drive']) && !empty($point['drive']) ? $point['drive'] : AdapterSchema::getDriveFuncDefault();

        $logic        = $this->classMap[$this->drive];
        $curdStrategy = new CurdStrategy(new $logic($this->params));

        return $curdStrategy->getList();
    }
}