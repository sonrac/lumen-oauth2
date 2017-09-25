<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\tests;

use Illuminate\Filesystem\Filesystem;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;
use sonrac\lumenRest\Oauth2ServiceProvider;
use sonrac\lumenRest\tests\app\Kernel;


/**
 * Class TestCase
 *
 * @package sonrac\lumenRest
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class TestCase extends LumenTestCase
{

    use DatabaseTransactions,
        DatabaseMigrations;

    /**
     * Boots the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $this->app = require __DIR__ . '/app/app.php';

        $this->artisan('migrate', [
            '--path' => __DIR__ . '/../migrations'
        ]);

        return $this->app;
    }

    /**
     * run package database migrations
     *
     * @return void
     */
    public function migrate()
    {
        $fileSystem = new Filesystem();

        foreach($fileSystem->files(__DIR__ . "/../src/migrations") as $file)
        {

        }
    }
}