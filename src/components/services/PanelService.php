<?php

namespace andy87\yii2\dnk_file_crafter\components\services;

use andy87\yii2\dnk_file_crafter\components\models\TableInfoDto;
use andy87\yii2\dnk_file_crafter\components\services\producers\TableInfoProducer;
use Yii;

/**
 *
 */
class PanelService
{
    /**
     * @var array
     */
    private array $params;



    /**
     * @var CacheService
     */
    private CacheService $cacheService;

    /**
     * @var TableInfoProducer
     */
    private TableInfoProducer $tableInfoProducer;



    /**
     * @param array $params
     */
    public function __construct( array $params )
    {
        $this->params = $params;

        $this->cacheService = new CacheService($this->params['cache']);

        $this->tableInfoProducer = new TableInfoProducer($this->cacheService);
    }

    /**
     * @return TableInfoDto
     */
    public function getTableInfoDto(): TableInfoDto
    {
        $tableInfoDto = $this->tableInfoProducer->create($this->params[TableInfoDto::ATTR_CUSTOM_FIELDS]);

        $customFields = [];

        foreach ($this->params[TableInfoDto::ATTR_CUSTOM_FIELDS] as $key => $label )
        {
            $customFields[$key] = '';
        }

        $this->params[TableInfoDto::ATTR_CUSTOM_FIELDS] = $customFields;
        $tableInfoDto->load($this->params);

        if ( $tableName = Yii::$app->request->get(TableInfoDto::SCENARIO_UPDATE) )
        {
            $params = $this->cacheService->getContentCacheFile($tableName);

            $tableInfoDto->table_name = strtolower($tableName);

            if (count($params))
            {
                $tableInfoDto->scenario = TableInfoDto::SCENARIO_UPDATE;

                $tableInfoDto->load($params, '');
            }

        }

        if ( Yii::$app->request->isPost )
        {
            $tableInfoDto = $this->tableInfoProducer->create(Yii::$app->request->post());

            $this->cacheService->removeItem($tableInfoDto->{TableInfoDto::ATTR_TABLE_NAME});

            if ( $tableInfoDto->save() )
            {
                $tableInfoDto = new TableInfoDto($this->params['cache']);
            }
        }

        return $tableInfoDto;
    }

    /**
     * @return TableInfoDto[]
     */
    public function getListTableInfoDto(): array
    {
        $list = $this->cacheService->getCacheFileList();

        $tableInfoDtoList = [];

        foreach ($list as $cacheFile)
        {
            $pathInfo = pathinfo($cacheFile);

            $name = $pathInfo['filename'];

            $params = $this->cacheService->getContentCacheFile($name);

            $tableInfoDtoList[] = $this->tableInfoProducer->create($params);
        }

        return $tableInfoDtoList;
    }

    /**
     * @param string $remove
     *
     * @return void
     */
    public function removeModel(string $remove): void
    {
        $this->cacheService->removeItem($remove);
    }

}