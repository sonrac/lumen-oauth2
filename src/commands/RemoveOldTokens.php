<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 8/31/17
 * Time: 6:37 PM
 */

namespace sonrac\lumenRest\commands;


use Illuminate\Console\Command;

/**
 * Class RemoveOldTokens
 * Remove old access & refresh tokens from table
 *
 * @package sonrac\lumenRest\commands
 */
class RemoveOldTokens extends Command
{
    public $description = '';
    public $name = 'clear:tokens';
    /**
     * {@inheritdoc}
     */
    protected $signature = 'clear:tokens';

    public function handle()
    {
        var_dump(123);
    }
}