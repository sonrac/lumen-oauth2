<?php
/**
 * Created by PhpStorm.
 * User: conci
 * Date: 10/17/17
 * Time: 3:39 PM.
 */

namespace sonrac\lumenRest\tests\app;

use Laravel\Lumen\Application as BaseApplication;

/**
 * Class Application.
 */
class Application extends BaseApplication
{
    /**
     * Register container bindings for the application.
     */
    protected function registerDatabaseBindings()
    {
        $this->singleton('db', function () {
            return $this->loadComponent(
                'database',
                [
                    'Illuminate\Database\DatabaseServiceProvider',
                ],
                'db'
            );
        });
    }
}
