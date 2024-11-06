<?php

namespace andy87\yii2\file_crafter\components\services;

use Yii;
use yii\helpers\Inflector;
use yii\base\InvalidRouteException;
use andy87\yii2\file_crafter\Crafter;
use andy87\yii2\file_crafter\components\models\{ Field, Schema, Dto\Cmd };
use andy87\yii2\file_crafter\components\{ resources\PanelResources, services\producers\SchemaProducer };

/**
 * Service for Panel
 *
 * @package andy87\yii2\file_crafter\components\services
 *
 * @tag: #service #panel
 */
class PanelService
{
    /** @var CacheService */
    private CacheService $cacheService;

    /** @var SchemaProducer */
    private SchemaProducer $schemaProducer;



    /**
     * PanelService constructor.
     *
     * @param Crafter $crafter
     *
     * @tag #constructor
     */
    public function __construct( Crafter $crafter )
    {
        $this->cacheService = new CacheService($crafter->cache);

        $this->schemaProducer = new SchemaProducer($crafter->custom_fields);
    }

    /**
     * Get SchemaDto
     *
     * @return Schema
     */
    public function getSchemaDto(): Schema
    {
        return $this->schemaProducer->create();
    }

    /**
     * Handler Create/Update
     *
     * @param Schema $schemaDto
     *
     * @return Schema
     *
     * @throws InvalidRouteException
     */
    public function handlerSchema(Schema $schemaDto): Schema
    {
        if ( $tableName = Yii::$app->request->get(Schema::SCENARIO_UPDATE) )
        {
            $params = $this->cacheService->getContentCacheFile($tableName);

            $schemaDto->table_name = strtolower($tableName);

            if (count($params))
            {
                $schemaDto->scenario = Schema::SCENARIO_UPDATE;

                $schemaDto->load($params, '');
            }
        }

        $isCreate = isset($_POST[Schema::SCENARIO_CREATE]);
        $isUpdate = Yii::$app->request->get(Schema::SCENARIO_UPDATE, false);

        if ( Yii::$app->request->isPost && ( $isCreate || $isUpdate ) )
        {
            $params = Yii::$app->request->post();

            $schemaDto = $this->schemaProducer->create($params);

            $this->cacheService->removeItem($schemaDto->{Schema::TABLE_NAME});

            if ( $this->save($schemaDto) )
            {
                $this->goHome();
            }
        }

        return $schemaDto;
    }

    /**
     * Save schema to cache file
     *
     * @param Schema $schemaDto
     *
     * @return false|int
     */
    private function save(Schema $schemaDto): bool|int
    {
        $schemaDto->{Schema::TABLE_NAME} = strtolower(str_replace([' ','-'], '_', $schemaDto->{Schema::TABLE_NAME}));

        $fileName =  $this->cacheService->constructPath($schemaDto->{Schema::TABLE_NAME});

        $params = $schemaDto->attributes;

        foreach ($schemaDto->{Schema::DB_FIELDS} as $index => $dbField)
        {
            if ($dbField[Field::FOREIGN_KEYS] ?? false) {
                $params[Schema::DB_FIELDS][$index][Field::FOREIGN_KEYS] = 'checked';
            }
            if ($dbField[Field::UNIQUE] ?? false) {
                $params[Schema::DB_FIELDS][$index][Field::UNIQUE] = 'checked';
            }
            if ($dbField[Field::NOT_NULL] ?? false) {
                $params[Schema::DB_FIELDS][$index][Field::NOT_NULL] = 'checked';
            }
        }

        unset($params['scenario']);

        $content = json_encode( $params, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );

        $update = Yii::$app->request->get(Schema::SCENARIO_UPDATE);

        if ( $update && $update !== $params[Schema::TABLE_NAME] ) {
            $this->removeItem($update);
        }

        return file_put_contents( Yii::getAlias($fileName), $content );
    }

    /**
     * Redirect to main page of panel
     *
     * @return void
     *
     * @throws InvalidRouteException
     */
    public function goHome(): void
    {
        $url = Yii::$app->request->pathInfo;

        Yii::$app->response->redirect("/$url");
    }

    /**
     * Collect list of SchemaDto
     *  from cache files
     *
     * @return Schema[]
     */
    public function getListSchemaDto(): array
    {
        $list = $this->cacheService->getCacheFileList();

        $listSchemaDto = [];

        foreach ($list as $cacheFile)
        {
            $fileName = pathinfo($cacheFile, PATHINFO_FILENAME);

            $params = $this->cacheService->getContentCacheFile($fileName);

            $schemaDto = $this->schemaProducer->create($params);

            $listSchemaDto[] = $schemaDto;
        }

        return $listSchemaDto;
    }

    /**
     * Remove cache data
     *
     * @param string $remove
     *
     * @return void
     */
    public function removeModel(string $remove): void
    {
        $this->cacheService->removeItem($remove);
    }

    /**
     * Get path for target generate file
     *
     * @param string $generatePath
     * @param array $replaceList
     *
     * @return string
     */
    public function constructGeneratePath(string $generatePath, array $replaceList = []): string
    {
        $generatePath = Yii::getAlias("@root/$generatePath");

        return $this->replacing($generatePath, $replaceList);
    }

    /**
     * Get path for source template file
     *
     * @param string $sourcePath
     * @param string $ext
     * @param array $replaceList
     *
     * @return string
     */
    public function constructSourcePath(string $sourcePath, string $ext, array $replaceList = []): string
    {
        if ( !pathinfo($sourcePath, PATHINFO_EXTENSION) ) {
            $sourcePath .= $ext;
        }

        $sourcePath = Yii::getAlias($sourcePath);

        return $this->replacing($sourcePath, $replaceList);
    }

    /**
     * Execute bash
     *
     * @param Cmd $commandCli
     *
     * @return ?string
     */
    public function runBash(Cmd $commandCli): ?string
    {
        return shell_exec($commandCli->exec) ?? null;
    }

    /**
     * @param string $cacheFileName
     *
     * @return void
     */
    public function removeItem(string $cacheFileName): void
    {
        $itemPath =  $this->cacheService->constructPath($cacheFileName);

        $itemPath = Yii::getAlias($itemPath);

        if ( file_exists($itemPath)) unlink($itemPath);
    }

    /**
     * Replace the template with the specified parameters
     *  {{PascalCase}} - PascalCase
     *  {{camelCase}} - camelCase
     *  {{snake_case}} - snake_case
     *  {{kebab-case}} - kebab-case
     *
     * @param string $content
     * @param array $replaceParams
     *
     * @return string
     */
    public function replacing(string $content, array $replaceParams ): string
    {
        return str_replace(array_keys($replaceParams), array_values($replaceParams), $content);
    }

    /**
     * Generate the list of parameters for replacing
     *
     * @param Schema $schemaDto
     *
     * @return array
     */
    public function getReplaceList(Schema $schemaDto): array
    {
        $tableName = $schemaDto->{Schema::TABLE_NAME};
        $tableName = str_replace([' ','-'], '_', $tableName);

        $pascalCase = Inflector::id2camel($tableName,'_');

        $params = [
            '{{PascalCase}}' => $pascalCase,
            '{{camelCase}}' => lcfirst($pascalCase),
            '{{snake_case}}' => $schemaDto->{Schema::TABLE_NAME},
            '{{kebab-case}}' => str_replace('_', '-', $schemaDto->{Schema::TABLE_NAME}),
        ];

        $customFields = $schemaDto->getCustomFields();

        if (count($customFields)) {
            foreach ($customFields as $key => $title ) {
                $params["{{{$key}}}"] = $title;
            }
        }

        return $params;
    }


    /**
     * Common Handler for
     *
     * @throws InvalidRouteException
     */
    public function handlers(PanelResources$panelResources): void
    {
        $this->removeHandler();

        $panelResources->schemaDto = $this->handlerSchema($panelResources->schemaDto);
    }

    /**
     * Remove handler
     *
     * @return void
     *
     * @throws InvalidRouteException
     */
    private function removeHandler(): void
    {
        if ( $remove = Yii::$app->request->get(Schema::SCENARIO_REMOVE) )
        {
            $this->removeModel( $remove );

            $this->goHome();
        }
    }


    /**
     * Check directory and create if not exists
     *
     * @param string $dirPath
     *
     * @return void
     */
    public function checkDirectory( string $dirPath ): void
    {
        $dirPath = Yii::getAlias($dirPath);

        if ( !is_dir($dirPath) ) mkdir($dirPath, 0777, true);
    }
}