<?php declare(strict_types=1);

namespace app\frontend\components\dataProviders\parents;

use yii\db\Connection;
use yii\db\QueryInterface;
use app\common\components\base\dataProviders\items\source\SourceActiveDataProvider;

/**
 * < Frontend > Родительский класс для провайдеров данных в окружении: `frontend`
 *
 * @property ?QueryInterface $query
 * @property ?callable|string $key
 * @property ?Connection|array|string| $db
 *
 * @package app\frontend\components\dataProviders\parents
 *
 * @tag: #abstract #frontend #dataProvider
 */
abstract class FrontendActiveDataProvider extends SourceActiveDataProvider
{
    // {{Parent}}
}