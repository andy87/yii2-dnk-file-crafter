<?php declare(strict_types=1);

namespace andy87\yii2\file_crafter\tests\models\dto;

use andy87\yii2\file_crafter\components\models\dto\Cmd;
use andy87\yii2\file_crafter\tests\core\UnitTestCore;

/**
 * @cli vendor/bin/phpunit tests/models/dto/CmdTest.php --testdox
 *
 * @package andy87\yii2\file_crafter\tests\models\dto
 *
 * @tag: #test #model #dto #cmd
 *
 */
class CmdTest extends UnitTestCore
{
    /**
     * @cli vendor/bin/phpunit tests/models/dto/CmdTest.php --testdox --filter testCmd
     *
     * @return void
     */
    public function testCmd(): void
    {
        $cmd = new Cmd();

        $dataCmd = [
            'exec' => 'ls -la',
            'output' => 'test',
            'replaceList' => [
                'test' => 'test',
            ],
        ];

        $cmd->exec = $dataCmd['exec'];
        $cmd->output = $dataCmd['output'];
        $cmd->replaceList = $dataCmd['replaceList'];

        $this->assertInstanceof(Cmd::class, $cmd);

        $this->assertEquals($cmd->exec, $dataCmd['exec']);
        $this->assertEquals($cmd->output, $dataCmd['output']);
        $this->assertEquals($cmd->replaceList, $dataCmd['replaceList']);
    }
}