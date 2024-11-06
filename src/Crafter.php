<?php

namespace andy87\yii2\file_crafter;

use andy87\yii2\file_crafter\{components\core\CoreGenerator,
    components\events\CrafterEvent,
    components\events\CrafterEventCommand,
    components\events\CrafterEventGenerate,
    components\events\CrafterEventRender,
    components\models\Dto\Cmd,
    components\models\Schema,
    components\resources\PanelResources,
    components\services\PanelService};
use Yii;
use yii\base\InvalidRouteException;
use yii\gii\CodeFile;

/**
 *  Yii2 Dnk File Crafter - extension for the Gii module in the Yii2 framework that simplifies file generation
 *
 * @property PanelService $panelService
 *
 * @see Crafter::VIEW_WIDGET_GRID_VIEW
 * @see Crafter::VIEW_WIDGET_LIST_VIEW
 *
 * @package andy87\yii2\file_crafter
 */
class Crafter extends CoreGenerator
{
    // Info
    /** @var string ID  */
    public const ID = 'yii2-file-crafter';

    /** @var string Description */
    protected const DESCRIPTION =  'Makes it easier to create a large number of files of the same template.';


    // Directory paths
    /** @var string Path to the root directory */
    public const ROOT = '@vendor/andy87/' . self::ID;

    /** @var string Root directory path */
    public const SRC = self::ROOT . '/src';

    /** @var string View directory  path*/
    public const VIEWS = self::SRC . '/views';


    // Values for the model list display widget
    /** @var string Grid */
    public const VIEW_WIDGET_GRID_VIEW = 'grid';

    /** @var string List */
    public const VIEW_WIDGET_LIST_VIEW = 'list';



    /** @var array Template groups */
    private array $templateGroup = [];


    /** @var PanelService Service handles data processing */
    private PanelService $panelService;

    /** @var PanelResources Resources for the view */
    public PanelResources $panelResources;


    /** @var array Cache settings */
    public array $cache = [
        'dir' => self::DEFAULT_RESOURCES_DIR . '/cache',
        'ext' => '.json'
    ];

    /** @var array Source settings */
    public array $source = [
        'dir' => self::DEFAULT_RESOURCES_DIR . '/templates/source',
        'ext' => '.tpl'
    ];

    /** @var ?string Behavior for event handling */
    public ?string $eventHandler = null;

    /** @var array list custom fields */
    public array $custom_fields = [];


    /** @var array User CLI commands */
    public array $commands = [];

    /** @var array CLI commands result */
    public array $bashResult = [];


    /** @var array Schema list from request for generate files */
    public array $generateList = [];



    /**
     * Init
     *
     * @return void
     *
     * @throws InvalidRouteException
     */
    public function init(): void
    {
        if ( $this->eventHandler ) {
            $this->attachBehavior('eventHandler', $this->eventHandler);
        }

        $this->event(CrafterEvent::BEFORE_INIT );


        $this->setupServices();

        $this->checkDirectories();

        $this->prepareSelectTemplate();

        $this->panelResources = $this->getPanelResources();

        $this->panelService->handlers($this->panelResources);


        $this->event(CrafterEvent::AFTER_INIT );

    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [ ['generateList'], 'safe' ];

        return $rules;
    }

    /**
     *
     * Prepare a group of templates
     * assigns groups to the `templateGroup` property
     *
     * Generate new vision of the `templates` property
     * Copy mapping from the `templates` property to the `templateGroup` property
     *
     * ```
     *  $config['modules']['gii'] = [
     *      'class' => yii\gii\Module::class,
     *      'generators' => [
     *          'fileCrafter' => [
     *              'templates' => [
     *                  'frontend' => [
     *                      'source/file/path' => 'path/for/generate/file'
     *                  ],
     *                  'backend' => [
     *                      'source/file/path.php' => 'path/for/generate/file'
     *                      'source/file/path.tpl' => 'path/for/generate/file'
     *                  ],
     *              ]
     *          ]
     *      ]
     *  ]
     * ```
     *
     * @return void
     */
    protected function prepareSelectTemplate(): void
    {
        foreach ( $this->templates as $key => $template )
        {
            if ( is_array( $template ) )
            {
                $this->templateGroup[$key] = $template;

                $this->templates[$key] = count($template) . " files";
            }
        }
    }

    /**
     * Create directories where templates are stored for generation and cache
     *
     * @return void
     */
    private function checkDirectories(): void
    {
        $directoryList = [
            $this->source['dir'] ?? null,
            $this->cache['dir'] ?? null,
        ];

        foreach ( $directoryList as $dirPath )
        {
            if( $dirPath ) $this->panelService->checkDirectory($dirPath);
        }
    }

    /**
     * Setup services `PanelService`
     *
     * @return void
     */
    public function setupServices(): void
    {
        $this->panelService = new PanelService(
            $this->cache,
            array_keys($this->custom_fields)
        );
    }

    /**
     * Constructor for `PanelResources`
     *
     * @return PanelResources
     */
    private function getPanelResources(): PanelResources
    {
        return new PanelResources(
            $this->panelService->getSchemaDto(),
            $this->panelService->getListSchemaDto()
        );
    }

    /**
     * Event handler
     *
     * @param $name
     * @param array $data
     *
     * @return void
     */
    public function event($name, mixed $data = [] ): void
    {
        if ( $this->eventHandler )
        {
            $event = $this->fabricEvent($name, $data);

            parent::trigger($name, $event);
        }
    }

    /**
     * Fabric event
     *
     * @param string $eventName
     * @param mixed $data
     *
     * @return CrafterEvent
     */
    private function fabricEvent(string $eventName, mixed $data = []): CrafterEvent
    {
        $className = self::EVENT_MAPPING[$eventName];

        switch ($eventName)
        {
            case CrafterEventCommand::class:
            case CrafterEventRender::class:
                /** @var CrafterEventCommand|CrafterEventRender $data */
                $event = $data;
                break;

            default:

                /** @var CrafterEvent|CrafterEventGenerate $event */
                $event = new $className();

                if ($eventName === CrafterEvent::AFTER_GENERATE)
                {
                    $event->files = $data;
                }
        }

        return $event;
    }

    /**
     * Generation Core
     *
     * @return array
     */
    public function generate(): array
    {
        $files = [];

        $this->event(CrafterEventGenerate::BEFORE );

        $listSchemaDto = $this->panelService->getListSchemaDto();

        if (count($listSchemaDto))
        {
            $this->generateList = array_keys($this->generateList);

            foreach ($listSchemaDto as $schema)
            {
                if ( in_array($schema->getTableName(), $this->generateList) )
                {
                    $replaceList = $this->panelService->getReplaceList($schema);

                    $this->bashResult = $this->execCommands($replaceList);

                    $files = array_merge($files, $this->fileGenerating($schema, $replaceList));
                }
            }
        }

        $this->event(CrafterEventGenerate::AFTER, $files );

        return $files;
    }

    /**
     * Execute bash commands
     *
     * @param array $replaceList
     *
     * @return array
     */
    private function execCommands(array $replaceList): array
    {
        $result = [];

        if (count($this->commands))
        {
            foreach ($this->commands as $command)
            {
                $commandCli = new Cmd();
                $commandCli->exec = $this->panelService->replacing($command, $replaceList);
                $commandCli->replaceList = $replaceList;


                $this->event(CrafterEventCommand::BEFORE, $commandCli);


                $output = $this->panelService->runBash($commandCli);

                $commandCli->output = $output;


                $this->event(CrafterEventCommand::AFTER, $commandCli);


                $result[$commandCli->exec] = $commandCli->output;
            }
        }
        return $result;
    }

    /**
     * Generate target files
     *
     * @param Schema $schema
     * @param array $replaceList
     *
     * @return CodeFile[]
     */
    private function fileGenerating(Schema $schema, array $replaceList ): array
    {
        $files = [];

        if ( count($this->templateGroup[$this->template]) )
        {
            $eventRender = new CrafterEventRender();
            $eventRender->schema = $schema;
            $eventRender->replaceList = $replaceList;

            foreach ($this->templateGroup[$this->template] as $sourcePath => $generatePath)
            {
                $eventRender->sourcePath = $this->panelService->constructSourcePath($sourcePath, $this->source['ext'], $replaceList);
                $eventRender->generatePath = $this->panelService->constructGeneratePath($generatePath, $replaceList);

                $this->event(CrafterEventRender::BEFORE, $eventRender );

                $eventRender->content = $this->renderTemplate( $eventRender );

                $this->event(CrafterEventRender::AFTER, $eventRender );

                $files[] = new CodeFile( $eventRender->generatePath, $eventRender->content );
            }
        }

        return $files;
    }

    /**
     * Render template
     *
     * @param CrafterEventRender $eventRender
     *
     * @return string
     */
    private function renderTemplate(CrafterEventRender $eventRender): string
    {
        $eventRender->content = $this->render(
            $eventRender->sourcePath,
            $eventRender->replaceList
        );

        return $this->panelService->replacing(
            $eventRender->content,
            $eventRender->replaceList
        );
    }

    /**
     * Get the root path of the template files that are currently being used.
     *
     * @return string the root path of the template files that are currently being used.
     */
    public function getTemplatePath(): string
    {
        return Yii::getAlias($this->source['dir']);
    }

    /**
     * Add information about the execution of bash commands to the result
     * 
     * {@inheritdoc}
     */
    public function save($files, $answers, &$results): bool
    {
        $saveResult = parent::save($files, $answers, $results);

        $lines = [];

        if (count($this->bashResult))
        {
            $lines[] = "executing bash commands...";
            foreach ($this->bashResult as $command => $output)
            {
                $lines[] = "executing: " . $command;
                $lines = array_merge($lines, explode("\n", $output));
            }
        }

        $results = implode("\n", $lines) . "\n" . $results;

        return $saveResult;
    }
}