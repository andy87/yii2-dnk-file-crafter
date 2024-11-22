<?php declare(strict_types=1);

namespace app\frontend\tests\unit\services\items;

use app\frontend\services\items\PascalCaseService;
use app\common\components\base\{ tests\unit\services\BaseServiceTest, services\items\ItemService };

/**
 * < Frontend > PascalCaseServiceTest
 *
 * @property ItemService $service
 *
 * @package app\frontend\tests\unit\services\items
 *
 * @tag #frontend #test #service
 */
class PascalCaseServiceTest extends BaseServiceTest
{
    /** @var ItemService|string класс сервиса */
    public ItemService|string $classnameService = PascalCaseService::class;

    // {{Boilerplate}}
}