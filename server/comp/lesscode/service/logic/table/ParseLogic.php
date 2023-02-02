<?php


namespace Imee\Service\Lesscode\Logic\Table;


use Imee\Service\Helper;
use Imee\Service\Lesscode\Context\Table\ParseContext;
use Imee\Service\Lesscode\Exception\FormException;
use Phalcon\Di;

class ParseLogic
{
    private $context;
    private $table;

    private $schemaMap = [
        'db'            => 'cms',
        'cms'           => 'cms',
        'cmsdb'         => 'cms',
        'bms'           => 'bmsdb',
        'bmsdb'         => 'bmsdb',
        'xs'            => 'xsdb',
        'xianshi'       => 'xsdb',
        'xspldb'        => 'xsdb',
        'slavedb'       => 'xsdb',
        'xsdb'          => 'xsdb',
        'readonly_db'   => 'xsdb',
        'bbc'           => 'bbcdb',
        'bbcdb'         => 'bbcdb',
        'config'        => 'bbcdb',
        'configdb'      => 'bbcdb',
        'bbc_slavedb'   => 'bbcdb',
        'xss'           => 'xssdb',
        'xssdb'         => 'xssdb',
        'xsst'          => 'xsstdb',
        'xsstdb'        => 'xsstdb',
        'xsstdbs2'      => 'xsstdb',
        'banban'        => 'banbandb',
        'banbandb'      => 'banbandb',
        'gaia'          => 'gaiadb',
        'gaiadb'        => 'gaiadb',
        'union'         => 'union_db',
        'union_db'      => 'union_db',
        'uniondb'       => 'union_db',
        'lemon'         => 'lemondb',
        'lemondb'       => 'lemondb',
        'lemon_slavedb' => 'lemondb',
        'recharge'      => 'rechargedb',
        'rechargedb'    => 'rechargedb',
        'broker'        => 'broker',
        'brokerdb'      => 'broker',
        'rush'          => 'rush_db',
        'rushdb'        => 'rush_db',
        'rush_db'       => 'rush_db',
        'activity'      => 'activity',
        'activitydb'    => 'activity',
        'game'          => 'gamedb',
        'gamedb'        => 'gamedb',
        'game_slavedb'  => 'gamedb',
    ];

    private $schema;

    private $defaultSchema = 'cms';

    private $tableName;

    public function __construct(ParseContext $context)
    {
        $this->context = $context;
    }

    public function handle(): array
    {
        $res = [
            'table'     => [],
            'parse_msg' => '',
        ];

        try {

            $this->validation();

            if (!empty($this->context->model)) {
                $this->parseModel();
            }

            if (!empty($this->context->tableName)) {
                $this->parseTableName();
            }

            if ($this->isParse()) {
                $this->createParseTable();
            } else {
                FormException::throwException(FormException::TABLE_PARSE_ERROR);
            }

            $res['table'] = $this->table;

        } catch (FormException $e) {
            $res['parse_msg'] = $e->getMessage();
        }

        return $res;
    }

    private function validation(): void
    {
        if (empty($this->context->model) && empty($this->context->tableName)) {
            FormException::throwException(FormException::TABLE_NOT_PARSE_ERROR);
        }

        if (empty($this->context->tableName) && !empty($this->context->model) && !class_exists($this->context->model)) {
            FormException::throwException(FormException::TABLE_PARSE_MODEL_NO_EXIST_ERROR);
        }
    }

    private function parseModel(): void
    {
        if ($this->isParse() || !class_exists($this->context->model)) {
            return;
        }

        $model = new $this->context->model;
        $this->tableName = $model->getSource();
        $this->schema    = $model::SCHEMA;
    }

    private function parseTableName(): void
    {
        if ($this->isParse()) {
            return;
        }

        if (stripos($this->context->tableName, '.') !== false) {
            [$schema, $tableName] = explode('.', $this->context->tableName);

            if (!isset($this->schemaMap[$schema])) {
                FormException::throwException(FormException::TABLE_PARSE_SCHEMA_NOT_EXIST_ERROR);
            }

            $this->schema = $this->schemaMap[$schema];
            $this->tableName = $tableName;

        } else {
            $this->schema = $this->defaultSchema;
            $this->tableName = $this->context->tableName;
        }
    }

    private function isParse(): bool
    {
        return !empty($this->tableName) && !empty($this->schema);
    }

    private function createParseTable()
    {
        $databases = Di::getDefault()->getShared('config')->database;

        if (!isset($databases[$this->schema])) {
            FormException::throwException(FormException::TABLE_PARSE_DATABASE_NOT_EXIST_ERROR);
        }

        $dbName = $databases[$this->schema]['dbname'] ?? '';
        $sql = "select * from information_schema.columns where `table_schema` = '{$dbName}' and `table_name` = '{$this->tableName}';";
        $sqlInfo = "select * from information_schema.tables where `table_schema` = '{$dbName}' and `table_name` = '{$this->tableName}';";

        try {
            $list = (array) Helper::fetch($sql, null, $this->schema);
            $info = (array) Helper::fetchOne($sqlInfo, null, $this->schema);
        } catch (\Exception $e) {
            throw new FormException($e->getMessage());
        }

        if (empty($list)) {
            FormException::throwException(FormException::TABLE_PARSE_MYSQL_GET_INFO_ERROR);
        }

        $this->formatList($list, $info);
    }

    private function formatList($list, $info)
    {
        try {
            $fields = [];

            $this->table['table_name'] = $info['TABLE_NAME'];
            $this->table['comment']    = $info['TABLE_COMMENT'] ?? '';
            $this->table['engine']     = $info['ENGINE'] ?? 'InnoDB';

            foreach ($list as $item)
            {
                $this->table['schema']     = $this->schema;

                $field = $item['COLUMN_NAME'];
                $pk    = strtolower($item['COLUMN_KEY']);

                $tmp = [
                    'name'             => strtolower($item['COLUMN_NAME']),
                    'type'             => strtolower($item['COLUMN_TYPE']),
                    'data_type'        => strtolower($item['DATA_TYPE']),
                    'str_length'       => $item['CHARACTER_MAXIMUM_LENGTH'],
                    'num_length'       => $item['NUMERIC_PRECISION'],
                    'num_scale_length' => $item['NUMERIC_SCALE'],
                    'comment'          => $item['COLUMN_COMMENT'],
                    'pk'               => $pk === 'pri' || $pk === 'primary' || $pk === 'primary key',
                ];

                $fields[$field] = $tmp;
            }

            $this->table['fields']  = $fields;

        } catch (\Exception $e) {
            throw new FormException($e->getMessage());
        }
    }
}