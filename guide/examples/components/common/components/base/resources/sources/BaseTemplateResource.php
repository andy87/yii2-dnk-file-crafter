<?php declare(strict_types=1);

namespace app\components\common\components\base\resources\sources;

use Yii;
use app\components\common\components\base\resources\BaseResource;

/**
 * Base class for all resources with template
 *
 * @package common\components\base\resources
 *
 * @tag: #base #resource #template
 */
abstract class BaseTemplateResource extends BaseResource
{
    /** @var string Template name for rendering */
    public string $template;



    /**
     * @return string
     */
    public function render(): string
    {
        return Yii::$app->view->render($this->template, $this->release());
    }
}