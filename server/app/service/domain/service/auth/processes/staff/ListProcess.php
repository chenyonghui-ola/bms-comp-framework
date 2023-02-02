<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Service\Domain\Context\Auth\Staff\ListContext;
use Imee\Models\Cms\CmsUser;
use Imee\Models\Cms\CmsUserRoles;
use Phalcon\Di;
use Imee\Service\Helper;
use Imee\Models\Xs\XsBigarea;
use Imee\Service\Domain\Service\Abstracts\NormalListAbstract;

/**
 * 后台用户列表
 */
class ListProcess extends NormalListAbstract
{
    protected $context;
    protected $masterClass;
    protected $query;

    public function __construct(ListContext $context)
    {
        $this->context = $context;
        $this->masterClass = CmsUser::class;
        $this->query = CmsUser::query();
    }

    protected function setColumns()
    {
        $this->query->columns(array(
            "$this->masterClass.user_id",
            "$this->masterClass.user_email",
            "$this->masterClass.user_name",
            "$this->masterClass.user_status",
            "$this->masterClass.last_login_time",
            "$this->masterClass.user_status",
            "$this->masterClass.is_salt",
            "$this->masterClass.language",
            "$this->masterClass.bigarea",
        ));
    }

    protected function buildWhere()
    {
        $where = [];
        $where['condition'][] = 'system_id=:system_id:';
        $where['bind']['system_id'] = 1;
        if (!empty($this->context->userName)) {
            $where['condition'][] = '(user_name like :user_name: or user_email like :user_email:)';
            $where['bind']['user_name'] = '%' . $this->context->userName . '%';
            $where['bind']['user_email'] = '%' . $this->context->userName . '%';
        }
        if (!empty($this->context->userId)) {
            $where['condition'][] = 'user_id = :user_id:';
            $where['bind']['user_id'] = $this->context->userId;
        }

        if (is_numeric($this->context->userStatus)) {
            $where['condition'][] = 'user_status = :user_status:';
            $where['bind']['user_status'] = $this->context->userStatus;
        }

        if (is_numeric($this->context->isSalt)) {
            $where['condition'][] = 'is_salt = :is_salt:';
            $where['bind']['is_salt'] = $this->context->isSalt;
        }

        $this->where = $where;
    }

    protected function formatList($items)
    {
        $format = [];
        if (empty($items)) {
            return $format;
        }

        $uids = [];
        
        foreach ($items as $item) {
            $tmp = $item->toArray();
            $uids[] = $item->user_id;
            // unset($tmp['password']);
            // unset($tmp['salt']);

            $tmp['display_user_status'] = isset(CmsUser::$userStatusDisplay[$item->user_status]) ?
                CmsUser::$userStatusDisplay[$item->user_status] : '';

            $tmp['display_is_salt'] = isset(CmsUser::$isSaltDisplay[$item->is_salt]) ?
                CmsUser::$isSaltDisplay[$item->is_salt] : '';

            $tmp['display_language'] = $item->language ? array_map(function ($v) {
                return Helper::getLanguageName($v);
            }, explode(',', $item->language)) : [];

            
            $tmp['display_roles'] = [];
            $format[] = $tmp;
        }

        if (empty($format)) {
            return $format;
        }

        $areaMap = XsBigarea::getAllNewBigArea();
        
        $userRoleMap = CmsUserRoles::getUserRolesByUserIds($uids);

        foreach ($format as &$v) {
            $v['display_roles'] = isset($userRoleMap[$v['user_id']]) ?
                array_column($userRoleMap[$v['user_id']], 'role_name') : $v['display_roles'];
            
            $v['display_bigarea'] = $v['bigarea'] ? array_map(function ($v) use ($areaMap) {
                return isset($areaMap[$v]) ? $areaMap[$v] : '';
            }, explode(',', $v['bigarea'])) : [];
        }

        return $format;
    }
}
