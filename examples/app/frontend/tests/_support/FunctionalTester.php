<?php declare(strict_types=1);

namespace app\frontend\tests;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * @param $message
     *
     * @return void
     */
    public function seeValidationError($message): void
    {
        $this->see($message, '.invalid-feedback');
    }

    /**
     * @param $message
     *
     * @return void
     */
    public function dontSeeValidationError($message): void
    {
        $this->dontSee($message, '.invalid-feedback');
    }
}
