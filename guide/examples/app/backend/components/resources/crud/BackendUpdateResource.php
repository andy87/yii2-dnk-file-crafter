<?php declare(strict_types=1);

namespace app\backend\components\resources\crud;

use app\common\components\base\moels\items\core\BaseModel;

/**
 * < Backend > Родительский класс для ресурса создания модели в окружении `backend`
 *
 * @package app\backend\components\resources\crud
 *
 * @property ?BaseModel $form
 *
 * @tag: #backend #source #resource #update
 */
abstract class BackendUpdateResource extends BackendFormResource
{
    // {{Boilerplate}}
}