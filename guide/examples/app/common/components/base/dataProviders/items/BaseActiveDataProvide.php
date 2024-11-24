<?php declare(strict_types=1);

namespace app\common\components\base\dataProviders\items\core;

use yii\data\ActiveDataProvider;
use yii\db\{Connection, QueryInterface};

/**
 * < Common > Родительский класс для всех классов-провайдеров данных
 *
 * @property ?QueryInterface $query
 * @property ?callable|string $key
 * @property ?Connection|array|string| $db
 *
 * @package app\common\components\base\dataProviders\items\core
 */
class BaseActiveDataProvide extends ActiveDataProvider
{
    // {{Parent}}
}