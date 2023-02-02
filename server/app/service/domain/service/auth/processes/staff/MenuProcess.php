<?php


namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Exception\Auth\StaffException;
use Imee\Models\Cms\CmsModules;
use Imee\Service\Domain\Context\Auth\Staff\ModuleContext;
use Phalcon\Di;
use Imee\Service\Domain\Service\Auth\Processes\Traits\MenuFormatTrait;

class MenuProcess extends BaseProcess
{
    use MenuFormatTrait;
    private $default_menu = [
        'menus' => [],
        'pages' => [],
        'points' => []
    ];

    public function handleMenu(ModuleContext $moduleContext): array
    {
        $parentModuleId = intval($moduleContext->parentModuleId);
        // if ($parentModuleId <= 0) {
        //     list($code, $msg) = StaffException::PARAM_PARENT_MODULE_ERROR;
        //     throw new StaffException($code, $msg);
        // }

        $session = Di::getDefault()->getShared('session');
        $userInfo = $session->get('userinfo');
        
        return $this->getMenu($userInfo, $parentModuleId, $moduleContext->lang);
    }

    public function handPermission(): array
    {
        $session = Di::getDefault()->getShared('session');
        $userInfo = $session->get('userinfo');
        return $this->getPermission($userInfo);
    }

    private function getPermission($userInfo): array
    {
        $user_id = intval($userInfo['user_id']);
        if ($user_id < 1) {
            return [];
        }
        $permissions = $userInfo['super'] ? $this->getSuperPermission() : $this->getNormalPermission($userInfo['user_id']);
        return $this->formatPermissionList($permissions);
    }

    private function getSuperPermission()
    {
        return CmsModules::findModules([], -1, 1);
    }

    private function getNormalPermission($uid)
    {
        $moduleIds = $this->getModuleIdsByUid($uid);
        if (empty($moduleIds)) {
            return [];
        }
        return CmsModules::findModules($moduleIds, -1, 1);
    }

    private function getMenu($userInfo, $parentModuleId, $lang)
    {
        $user_id = intval($userInfo['user_id']);
        
        if ($user_id < 1) {
            return $this->default_menu;
        }
        $allMenus = $userInfo['super'] ? $this->getSuperMenu($parentModuleId) : $this->getNormalMenu($userInfo['user_id'], $parentModuleId);
        return $this->formatMenuList($allMenus, $lang);
    }

    private function getLeftMenu($userInfo)
    {
        return $userInfo['super'] ? $this->getSuperRootMenu() : $this->getNormalRootMenu($userInfo['user_id']);
    }
}
