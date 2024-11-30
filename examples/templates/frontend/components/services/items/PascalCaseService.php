<?php declare(strict_types=1);

namespace app\frontend\components\services\items;

use yii\data\ActiveDataProvider;
use app\frontend\models\items\PascalCase;
use app\common\components\interfaces\CatcherInterface;
use app\frontend\models\search\items\PascalCaseSearch;
use app\common\components\base\moels\items\source\SourceModel;
use app\common\components\interfaces\models\SearchModelInterface;
use app\common\components\base\producers\items\source\SourceProducer;
use app\frontend\components\dataProviders\items\PascalCaseDataProvider;
use app\common\components\base\repository\items\source\SourceRepository;

/**
 * < Frontend > Сервис для работы с сущностью `PascalCase`
 *
 * @property array $configProducer
 * @property array $configRepository
 * @property ActiveDataProvider|string $dataProviderClass
 * @property array|string $configLogger;
 * @property CatcherInterface $logger;
 * @property SourceProducer $provider
 * @property SourceRepository $repository
 *
 * @method PascalCaseSearch getSearchModel(array $params = [], string $formName = '')
 * @method PascalCaseDataProvider getDataProviderBySearchModel(PascalCaseSearch $searchModel, array $params = [])
 * @method PascalCase getItemById(int $id, bool $runValidation = false)
 * @method PascalCase modelCreate(mixed $params)
 * @method PascalCase updateModel(?PascalCase $model, mixed $params)
 * @method int deleteItemByCriteria(array $criteria)
 *
 * @package app\frontend\components\services\items
 *
 * @tag: #boilerplate #frontend #service #{{snake_case}}
 */
class PascalCaseService extends \app\common\components\services\items\PascalCaseService
{
    /** @var SourceModel|string $modelClass класс модели */
    protected SourceModel|string $modelClass = PascalCase::class;

    /** @var SearchModelInterface|string */
    protected SearchModelInterface|string $searchModelClass = PascalCaseSearch::class;

    // {{Boilerplate}}
}