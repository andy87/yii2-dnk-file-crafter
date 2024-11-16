<?php declare(strict_types=1);

namespace common\components\base\moels\items\core;

use yii\db\ActiveRecord;

/**
 * Родительский класс для всех моделей базы данных
 *
 * @package app\common\components\base\models\items
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @tag: #base #model
 */
abstract class BaseModel extends ActiveRecord
{

}