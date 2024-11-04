<?php

namespace andy87\yii2\file_crafter\components\core;

use yii\gii\Generator;

/**
 *
 */
abstract class CoreGenerator extends Generator
{

    /** @var string ID on module list */
    public const ID = null;


    /** @var string Path on root directory */
    public const SRC = null;

    /** @var string Path with view directory */
    public const VIEWS = null;



    /**
     * Используется в абстракции
     *
     * @return string
     *
     * \vendor\yiisoft\yii2-gii\src\views\default\view.php
     *
     * ```
     *  <?= $this->renderFile( $generator->formView(), [
     *      'generator' => $generator,
     *      'form' => $form,
     *  ]) ?>
     * ```
     * @see self::formView()
     */
    public function formView(): string
    {
        return static::VIEWS . '/panel.php';
    }

    /**
     * Return extension `name`
     *
     * @return string
     */
    public function getName(): string
    {
        return 'File Crafter';
    }

    /**
     * Return ext `description`
     *
     * @return string
     */
    public function getDescription(): string
    {
        return static::DESCRIPTION;
    }
}