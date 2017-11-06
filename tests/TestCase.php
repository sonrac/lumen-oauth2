<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;

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

    protected $_seeds = [];

    /**
     * Boots the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $this->app = require __DIR__ . '/app/app.php';
        $this->artisan('generate:keys');

        $this->artisan('migrate', [
            '--path' => __DIR__ . '/../migrations',
        ]);

        return $this->app;
    }

    public function setUp()
    {
        $this->init();

        parent::setUp();

        if (is_array($this->_seeds) && count($this->_seeds)) {
            foreach ($this->_seeds as $seed) {
                $this->artisan('db:seed', [
                    '--class' => '\\sonrac\\lumenRest\\tests\\seeds\\' . ucfirst($seed) . 'Seeder',
                ]);
            }
        }
    }

    protected function init()
    {
        if (!is_dir($dir = __DIR__ . '/app/database/migrations')) {
            symlink(__DIR__ . '/../migrations', $dir);
        }

        if (!is_file($file = __DIR__ . '/out/database.sqlite')) {
            file_put_contents($file, '');
        }
    }
}
