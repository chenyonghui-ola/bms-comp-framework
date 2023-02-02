<?php

namespace Imee\Models\Cms;

use Imee\Models\Traits\ChangeTrait;
use Imee\Models\Traits\ModelLogTrait;
use Imee\Models\Logcontext\LogBaseContext;

class CmsUser extends BaseModel
{
    use ChangeTrait, ModelLogTrait;

    protected static $primaryKey = 'user_id';

    /**
     * 日志类的全路径
     * @var string
     */
    private $recordLog = CmsUserLog::class;

    /**
     * 日志context类的全路径
     * @var string
     */
    private $logContext = LogBaseContext::class;

    private $logPrimaryKey = 'user_id';

    const USER_STATUS_INVALID = 0;
    const USER_STATUS_VALID = 1;

    const IS_SALT_YES = 1;
    const IS_SALT_NO = 0;
    public static $userStatusDisplay = [
        self::USER_STATUS_INVALID => '无效',
        self::USER_STATUS_VALID   => '有效',
    ];

    public static $isSaltDisplay = [
        self::IS_SALT_YES => '有二次验证',
        self::IS_SALT_NO  => '无二次验证',
    ];

    public function initialize()
    {
        parent::initialize();
        $this->setLogEventsManager();
    }

    /**
     * 获取用户名list
     * @param $adminIds
     * @return array
     */
    public static function getUserNameList($adminIds): array
    {
        if (!$adminIds) {
            return [];
        }
        $adminIds = array_filter($adminIds);
        $adminIds = array_unique($adminIds);
        $adminIds = array_values($adminIds);
        $list = self::getListByWhere([['user_id', 'in', $adminIds]], 'user_id,user_name');
        return array_column($list, 'user_name', 'user_id');
    }

    /**
     * @desc 根据user_id批量获取用户数据，尽量避免循环查询单条.
     * @param array $uidArr 用户id数组，比如[1，2]
     * @param array $fieldArr 字段数组，需要查询的字段
     * @return array
     */
    public static function getAdminUserBatch(array $uidArr = [], array $fieldArr = ['user_id', 'user_name']): array
    {
        if (empty($uidArr)) {
            return [];
        }

        $model = self::query();
        $model->columns(implode(',', $fieldArr));
        $bindValue = $uidArr;
        $model->andWhere("user_id IN ({user_id:array})", ['user_id' => $bindValue]);

        $data = $model->execute()->toArray();
        return array_column($data, null, 'user_id');
    }

    /**
     * @param array $condition
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public static function queryBuilder(array $condition=[])
    {
        $query = static::baseQueryBuilder($condition);
        foreach ($condition as $key => $value) {
            switch ($key) {
                case 'user_name_like':
                    $query->andWhere('user_name like :user_name_like:', ['user_name_like' => '%'.$value.'%']);
                    break;
                case 'user_id':
                    $query->andWhere('user_id = :user_id:', ['user_id' => $value]);
                    break;
                case 'user_id_array':
                    $query->inWhere('user_id', $value);
                    break;
                case 'system_id':
                    $query->andWhere('system_id = :system_id:', ['system_id' => $value]);
                    break;
                case 'columns':
                    // 查询的字段
                    $query->columns($value);
                    break;
                default:
                    break;
            }
        }
        return $query;
    }

	public static function getKfNameById($kfId = 0)
	{
		static $_kfMap = [];

		if (empty($kfId)) {
			return '';
		}

		if (isset($_kfMap[$kfId])) {
			return $_kfMap[$kfId];
		}

		$res = self::findFirst($kfId);

		$_kfMap[$kfId] = empty($res) ? '' : $res->user_name;

		return $_kfMap[$kfId];
	}
}
