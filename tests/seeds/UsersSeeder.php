<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest\tests\seeds;

use Illuminate\Support\Facades\Hash;

/**
 * Class UsersSeeder
 *
 * @package sonrac\lumenRest\tests\seeds
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class UsersSeeder extends AbstractFakerSeeder
{
    /**
     * Run seeder
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function run()
    {
        \DB::table('users')->delete();

        $data = [];
        for ($i = 1; $i < 20; $i++) {
            $data[] = [
                'id'          => $i,
                'username'    => 'test_user_' . $i,
                'password'    => Hash::make('test_user_' . $i),
                'email'       => $this->faker->email . $i,
                'register_at' => $this->faker->unixTime,
                'first_name'  => $this->faker->firstName . $i,
                'last_name'   => $this->faker->lastName . $i,
            ];
        }

        \DB::table('users')
            ->insert($data);
    }
}