<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/20/17
 * Time: 6:19 PM
 */

namespace sonrac\lumenRest\tests\seeds;


use Illuminate\Database\Seeder;

class ScopesSeeder extends Seeder
{
    public function run() {

        \DB::table('scopes')->delete();

        $data = [];
        foreach ([
            'default' => 'Default',
            'basic' => 'Basic',
            'email' => 'Email'
                 ] as $scope => $description) {

            $data[] = [
                'name' => $scope,
                'description' => $description,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        \DB::table('scopes')
            ->insert($data);
    }
}