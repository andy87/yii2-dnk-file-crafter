<?php declare(strict_types=1);

namespace app\common\components\core\tests\base\unit\services;

use app\common\components\core\services\items\CoreItemService;
use app\common\components\core\tests\base\unit\source\BaseUnitTest;
use app\common\components\traits\ApplyServiceTrait;

/**
 * < Common > Base Service Test
 *
 * @property CoreItemService $service
 * @property CoreItemService|string $classnameService
 *
 * @package app\common\components\core\tests\unit
 *
 * @cli ./vendor/bin/codecept run app/common/components/base/tests/unit/service/BaseServiceTest
 *
 * @tag: #base #abstract #test #service
 */
abstract class BaseServiceTest extends BaseUnitTest
{
    use ApplyServiceTrait;

    // {{Parent}}
}