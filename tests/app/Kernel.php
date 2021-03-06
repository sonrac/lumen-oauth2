<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\tests\app;

use sonrac\lumenRest\commands\GenerateKeys;

/**
 * Class Kernel.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Kernel extends \Laravel\Lumen\Console\Kernel
{
    protected $commands = [
        GenerateKeys::class,
    ];
}
