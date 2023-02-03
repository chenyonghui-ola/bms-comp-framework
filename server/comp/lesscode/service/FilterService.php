<?php

namespace Imee\Service\Lesscode;

use Imee\Service\Lesscode\Context\Filter\GetFilterContext;
use Imee\Service\Lesscode\Logic\Filter\FilterLogic;
use Imee\Service\Lesscode\Logic\Filter\Mongo\FilterLogic as MongoFilterLogic;

class FilterService extends BaseService
{
    /**
     * @var array 接收到到参数
     */
    protected $params;

    /**
     * @var string mongo / api / mysql
     */
    protected $drive = 'mysql';

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function setDrive($drive): self
    {
        $this->drive = $drive;

        return $this;
    }

    public function getFilter(): array
    {
        $context = new GetFilterContext([
            'model'  => $this->model,
            'schema' => $this->schema,
            'params' => $this->params
        ]);

        if ($this->drive == 'mongo') {
            $logic = new MongoFilterLogic($context);
        } elseif ($this->drive == 'api') {
            $logic = new FilterLogic($context);
        } else {
            $logic = new FilterLogic($context);
        }

        return $logic->handle();
    }

    /**
     * @param $field
     * @return bool
     */
    public static function isDateTimeField($field): bool
    {
        $fieldSuffix = [self::getDateSuffixStart(), self::getDateSuffixEnd(), self::getTimeSuffixStart(), self::getTimeSuffixEnd()];

        return in_array(substr($field, - 5), $fieldSuffix);
    }

    /**
     * @param $field
     * @return string
     */
    public static function getDateTimeField($field): string
    {
        return substr($field, 0, - 6);
    }

    /**
     * @param $field
     * @return bool
     */
    public static function isRangeField($field): bool
    {
        return substr($field, - 5) === self::getRangeSuffixStart() || substr($field, - 3) === self::getRangeSuffixEnd();
    }

    /**
     * @param $field
     * @return string
     */
    public static function getRangeField($field): string
    {
        $resField = '';

        if (substr($field, - 5) === self::getRangeSuffixStart()) {
            $resField = substr($field, 0,  -5);
        }

        if (substr($field, - 3) === self::getRangeSuffixEnd()) {
            $resField = substr($field, 0,  -3);
        }

        return $resField;
    }

    public static function getRangeFields($field)
    {
        return [$field . '_' . self::getRangeSuffixStart(), $field . '_' . self::getRangeSuffixEnd()];
    }

    public static function getDateSuffixStart()
    {
        return 'sdate';
    }

    public static function getDateSuffixEnd()
    {
        return 'edate';
    }

    public static function getTimeSuffixStart()
    {
        return 'stime';
    }

    public static function getTimeSuffixEnd()
    {
        return 'etime';
    }

    public static function getRangeSuffixStart()
    {
        return 'start';
    }

    public static function getRangeSuffixEnd()
    {
        return 'end';
    }


    public static function isDate($date)
    {
        if (!is_string($date)) {
            return false;
        }

        if (strlen($date) > 10) {
            $date = substr($date, 0, 10);
        }
        return $date && preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $date);
    }
}