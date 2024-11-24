<?php declare(strict_types=1);

namespace app\console\tests\unit\models\items;

use app\console\models\items\PascalCase;
use app\common\components\base\{ tests\unit\models\BaseModelTest, moels\items\core\BaseModel };

/**
 * < Console > PascalCaseServiceTest
 *
 * @cli ./vendor/bin/codecept run app/console/tests/unit/models/items/PascalCaseTest
 *
 * @cli ./vendor/bin/codecept run app/console/tests/unit/models/items/PascalCaseTest:testInspectAttributes
 * @method PascalCase testInspectAttributes()
 *
 * @package app\console\tests\unit\models\items
 *
 * @tag #console #test #model
 */
class PascalCaseTest extends BaseModelTest
{
    /** @var BaseModel|string $modelClass */
    protected BaseModel|string $modelClass = PascalCase::class;

    // {{Boilerplate}}
}