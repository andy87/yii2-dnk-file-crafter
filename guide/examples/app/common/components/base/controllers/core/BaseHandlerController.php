<?php declare(strict_types=1);

namespace app\common\components\base\controllers\core;

use yii\web\Controller;
use app\common\components\traits\ApplyHandlerTrait;
use app\common\components\base\handlers\items\core\BaseHandler;
use app\common\components\interfaces\controllers\items\ControllerWithHandlerInterface;

/**
 * < Common > Родительский класс для всех контроллеров с сервисом
 *
 * @property BaseHandler $handler
 * @property array $configHandler
 *
 * @package app\common\components\base\controllers
 *
 * @tag: #base #controller #web
 */
abstract class BaseHandlerController extends Controller implements ControllerWithHandlerInterface
{
    /**
     * Трейт для применения сервиса
     */
    use ApplyHandlerTrait;



    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $this->setupHandler();
    }
}