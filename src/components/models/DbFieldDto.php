<?php

namespace andy87\yii2\dnk_file_crafter\components\models;

use andy87\yii2\dnk_file_crafter\components\models\core\BaseModel;

/**
 * Class DbField
 *
 * @package andy87\yii2\dnk_file_crafter\models
 *
 * @tag: #model #db #field
 */
class DbFieldDto extends BaseModel
{
    public const ATTR_NAME = 'name';
    public const ATTR_COMMENT = 'comment';
    public const ATTR_TYPE = 'type';
    public const ATTR_SIZE = 'size';
    public const ATTR_FOREIGN_KEYS = 'foreignKeys';
    public const ATTR_UNIQUE = 'unique';
    public const ATTR_NOT_NULL = 'notNull';



    public string $name;
    public string $comment;
    public string $type;
    public int $size;
    public bool $foreignKeys;
    public bool $unique;

    public bool $notNull;
}