<?php declare(strict_types=1);

namespace app\common\components\repository\items;

use yii\db\{ActiveQuery, Connection};
use app\common\components\base\repository\items\cote\BaseRepository;

/**
 * < Common > Родительский класс для репозиториев: console/frontend/backend
 *
 * @property ?Connection $connection
 * @property array $criteriaActive
 *
 * @method ActiveQuery|null find( mixed $where = null )
 * @method ActiveQuery|null findActive( array $where = [] )
 * @method self setConnection( Connection $connection )
 * @method Connection|null getConnection()
 *
 * @package app\common\components\services\items
 *
 * @tag #common #repository #{{snake_case}}
 */
class PascalCaseRepository extends BaseRepository
{
    // {{Boilerplate}}
}