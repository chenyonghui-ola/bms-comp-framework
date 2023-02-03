<?php


namespace Imee\Service\Lesscode\Logic\Table;


use Imee\Service\Helper;
use Imee\Service\Lesscode\Constant\FieldTypeConstant;
use Imee\Service\Lesscode\Context\ModelDiffContext;
use Imee\Service\Lesscode\Context\Schema\SchemaDiffContext;
use Imee\Service\Lesscode\FileService;

class CreateLogic extends BaseLogic
{
    /**
     * @var int 操作类型 1：创建 2：更新
     */
    private $opType = 1;

    public function handle()
    {
        $sql = $this
            ->parseFields()
            ->setCharset()
            ->setEngine()
            ->createSql();

        // sql生成文件
        $this->createFile();

        return $sql;
    }

    /**
     * 创建sql
     */
    public function createSql(): string
    {
        // todo lesscode 判断表是否存在，存在的话进入修改逻辑
        if (false === $this->checkCreate()) {
            return $this->updateSql();
        }

        $exec = $this->context->execSql;

        $sql = <<<SQL
CREATE TABLE `{tableName}` (
{fields},
PRIMARY KEY ({pk}){uks}{indexs}
) ENGINE={engint} DEFAULT CHARSET={charset} COMMENT='{comment}';
SQL;

        $fields = $this->fields;

        foreach ($fields as $k => &$field) {
            $field[0] = $this->addApostrophe($field[0]);
            $field    = implode(' ', $field);
        }

        $fieldStr = implode(',' . PHP_EOL, $fields);

        $pk = array_map(function ($val)
        {
            return $this->addApostrophe($val);
        }, $this->pk);

        $data = [
            'tableName' => $this->getTableName(),
            'fields'    => $fieldStr,
            'pk'        => implode(',', $pk),
            'uks'       => '',
            'indexs'    => '',
            'engint'    => $this->engine,
            'charset'   => $this->charset,
            'comment'   => $this->comment,
        ];

        if (!empty($this->uks)) {
            // todo lesscode 唯一索引处理
        }

        if (!empty($this->indexs)) {
            // todo lesscode 普通索引处理
        }

        foreach ($data as $key => $value) {
            $sql = str_replace("{{$key}}", $value, $sql);
        }

        $exec && Helper::exec($sql, $this->model::SCHEMA);

        $this->sql = $sql;

        return $sql;
    }

    public function updateSql(): string
    {
        $exec = $this->context->execSql;

        $fileService = new FileService();

        $diff = $fileService->getDiff();

        // 只需要处理 schema diff里的改动
        if (!$diff->schemaDiffContext instanceof SchemaDiffContext) {
            return '';
        }

        $beforeClass = $diff->schemaDiffContext->beforeClass;
        $afterClass  = $diff->schemaDiffContext->afterClass;

        // 对比表字段
        $beforeFields = $beforeClass->getTableFields();
        $afterFields  = $afterClass->getTableFields();

        $diffFields = array_diff(array_keys($afterFields), array_keys($beforeFields));

        if (empty($diffFields)) {
            return '';
        }

        $fields = [];

        foreach ($afterFields as $key => $value) {
            if (in_array($key, $diffFields)) {
                $fields[$key] = $value;
            }
        }

        // 重置数据
        $this->fields = [];

        foreach ($fields as $name => $field) {
            $this->addField($name, $field);
        }

        $fields = $this->fields;

        foreach ($fields as $k => &$field) {
            $field[0] = $this->addApostrophe($field[0]);
            $field    = 'ADD COLUMN ' . implode(' ', $field);
        }

        $fieldStr = implode(',' . PHP_EOL, $fields);

        $data = [
            'tableName' => $this->getTableName(),
            'fields'    => $fieldStr,
        ];

        $sql = <<<SQL
ALTER TABLE `{tableName}` 
{fields}
SQL;

        foreach ($data as $key => $value) {
            $sql = str_replace("{{$key}}", $value, $sql);
        }

        $exec && Helper::exec($sql, $this->model::SCHEMA);

        $this->sql = $sql;

        return $sql;
    }

    public function checkCreate(): bool
    {
        $tableName = $this->getTableName();

        $sql  = "show tables like '{$tableName}'";
        $name = Helper::fetchColumn($sql, $this->model::SCHEMA);

        $bool = empty($name);

        if (false === $bool) {
            $this->opType = 2;
        }

        return $bool;
    }

    public function parseFields()
    {
        $table = $this->schema->getTable();

        if (empty($table)) {
            throw new \Exception('schema lack table field');
        }

        if (!isset($table['fields'])) {
            throw new \Exception('schema lack table fields');
        }

        $fields   = $table['fields'];
        $this->pk = is_array($table['pk']) ? $table['pk'] : [$table['pk']];

        foreach ($fields as $name => $field) {
            $this->addField($name, $field);
        }

        // 解析主键
        if (!isset($table['pk'])) {
            throw new \Exception('PRIMARY KEY is require');
        }

        if (!isset($table['index'])) {
            $this->indexs = [];
        } else {
            $this->indexs = is_array($table['index']) ? $table['index'] : [$table['index']];
        }

        if (!isset($table['unique_key'])) {
            $this->uks = [];
        } else {
            $this->uks = is_array($table['unique_key']) ? $table['unique_key'] : [$table['unique_key']];
        }

        $this->comment = $table['comment'] ?? '';

        return $this;
    }

    public function addField($name, $field)
    {
        if (empty($name)) {
            throw new \Exception('field name is require');
        }

        $isPk  = (current($this->pk) == $name);
        $fdata = [];

        $type     = $field['type'] ?? FieldTypeConstant::DEFAULT_TYPE;
        $length   = $field['length'] ?? FieldTypeConstant::DEFAULT_TYPE_LENGTH;
        $unsigned = (isset($field['unsigned']) && $field['unsigned'] == true) ? 'unsigned' : '';
        $notNull  = $this->isTypeText($type) ? '' : 'NOT NULL';
        $default  = $this->isTypeText($type) ? '' : 'DEFAULT ' . '\'' . $this->getFieldValueDefault($type) . '\'';
        $comment  = $field['comment'] ?? '';

        array_push($fdata, $name);
        array_push($fdata, $type . "({$length})");
        array_push($fdata, $unsigned);
        array_push($fdata, $notNull);

        if ($isPk) {
            array_push($fdata, 'AUTO_INCREMENT');
        } else {
            array_push($fdata, $default);
        }

        array_push($fdata, 'COMMENT \'' . $comment . '\'');

        $this->fields[] = $fdata;

        return $this;
    }

    protected function createFile()
    {
        if (empty($this->sql)) {
            return;
        }

        $path      = ROOT . '/lesscode/service/sql/';
        $opType    = $this->opType == 2 ? 'update' : 'create';
        $tableName = $this->getTableName();
        $fileName  = $tableName . '_' . $opType . '_' . date('YmdHis') . '_' . substr(Helper::createId(), 0, 8) . '.sql';

        file_put_contents($path . $fileName, $this->sql);
    }
}