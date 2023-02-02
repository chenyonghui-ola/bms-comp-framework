<?php

namespace Imee\Service\Lesscode;


use Imee\Service\Lesscode\Logic\Init\BaseMenuLogic;
use Imee\Service\Lesscode\Logic\Init\GuidExtendLogic;

/**
 *  初始化数据
 */
class InitService
{
   public function baseMenu()
   {
       $logic = new BaseMenuLogic();
       return $logic->handle();
   }

   public function guidExtend()
   {
       $logic = new GuidExtendLogic();
       return $logic->handle();
   }
}