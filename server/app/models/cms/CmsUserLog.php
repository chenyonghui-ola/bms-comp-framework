<?php

namespace Imee\Models\Cms;

class CmsUserLog extends BaseModel
{
    public const ACTION_MODIFY = 'modify';
    public const ACTION_CREATE = 'create';
    public const ACTION_DELETE = 'delete';
    public const ACTION_LOGIN = 'login';
}
