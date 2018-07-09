<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/20/17
 * Time: 5:18 PM.
 */

namespace sonrac\lumenRest\tests\seeds;

use Faker\Generator;
use Illuminate\Database\Seeder;

abstract class AbstractFakerSeeder extends Seeder
{
    /**
     * @var \Faker\Generator
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = new Generator();

        $this->faker = new Generator();
        $this->faker->addProvider(new \Faker\Provider\Person($this->faker));
        $this->faker->addProvider(new \Faker\Provider\Base($this->faker));
        $this->faker->addProvider(new \Faker\Provider\Uuid($this->faker));
        $this->faker->addProvider(new \Faker\Provider\Internet($this->faker));
        $this->faker->addProvider(new \Faker\Provider\DateTime($this->faker));
    }
}
