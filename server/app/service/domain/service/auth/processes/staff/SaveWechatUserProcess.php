<?php

namespace Imee\Service\Domain\Service\Auth\Processes\Staff;

use Imee\Models\Cms\CmsUser;
use Imee\Service\Domain\Context\Auth\Staff\SaveWechatUserContext;
use Imee\Libs\Google2FA;

class SaveWechatUserProcess
{
    private $context;

    /**
     * @var CmsUser
     */
    private $cmsUserClass = CmsUser::class;

    public function __construct(SaveWechatUserContext $context)
    {
        $this->context = $context;
    }

    public function handle()
    {
        add_tmp_log('saveWechatUser $data:');
        add_tmp_log($this->context->toArray());

        if (empty($this->context->userId)) {
            return false;
        }

        $rec = $this->cmsUserClass::findFirst([
            'conditions' => 'job_num=:job_num: and system_id=:system_id:',
            'bind' => [
                'job_num' => $this->context->userId,
                'system_id' => 1,
            ],
        ]);
        
        if (!$rec) {
            if ($this->context->email) {
                $rec = $this->cmsUserClass::findFirst([
                    'conditions' => 'user_email=:user_email: and system_id=:system_id:',
                    'bind' => [
                        'user_email' => $this->context->email,
                        'system_id' => 1,
                    ],
                ]);
                if ($rec) {
                    $rec->job_num = $this->context->userId;
                }
            }
        }

        if (!$rec) {
            $rec              = new $this->cmsUserClass;
            $rec->from_wechat = 1;
            $rec->job_num  = $this->context->userId;
            $rec->user_email  = $this->context->email ? $this->context->email : $this->context->userId;
            // 注册默认开启用户
            $rec->user_status = 1;

            $rec->salt = Google2FA::generate_secret_key();
            $rec->is_salt = 1;
        }

        if (empty($rec->user_name)) {
            if (isset($this->context->email) && !empty($this->context->email)) {
                $rec->user_name = str_replace('@olaola.chat', '', $this->context->email);
            } else {
                // 保证唯一，后面可以让管理员手动修改
                $rec->user_name = microtime(true);
            }
        }

        $rec->last_login_time = date('Y-m-d H:i:s');

        if ($rec->save()) {
            return $rec->toArray();
        }

        return [];
    }
}
