<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/20/17
 * Time: 5:17 PM
 */

namespace sonrac\lumenRest\tests\seeds;


use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    const SECRET_KEYS = [
        'wyM2dmxwgleR7EeFFh6KoTYUr6akFwy9',
        'GH16IFXYRDV8md4oQzgQiNp9AHxs0O8s',
        '2F9YszziqRXTboOp9DjbA63ZVKAQzjAq',
        '5WtQ60wggTdVFdql4AtSbglPsrVb92Rk',
        'G1oReDT4T9evms2fZrUpTMHhYvQWaMaU',
        'XK0ENsPMrg3ckcorBvGKmZlTPCA950AW',
        'oyocNHVSS6wlyeBEt6rmTebGyEq0B2Gv',
        'dmXWO0bMxSaUCewB0ZHFjuJSoruX0Vch',
        'Xt2t49oZRpPVn1eGUfrDkPG6gBf0FGuK',
        'i7qRaRgWbng73DRyXSI1OKFCSGDjjE6h',
    ];

    const REDIRECT_URI_TEST_CLIENT = 'http://localhost/token-redirect-url';

    public function run()
    {
        $data = [];

        \DB::table('clients')->delete();

        foreach (self::SECRET_KEYS as $index => $SECRET_KEY) {
            $data[] = [
                'id'           => $index + 1,
                'user_id'      => $index ? $index : null,
                'secret_key'   => $SECRET_KEY,
                'name'         => 'Test Client ' . $index,
                'is_active'    => 1,
                'redirect_url' => self::REDIRECT_URI_TEST_CLIENT,
                'created_at'   => time(),
                'updated_at'   => time(),
                'last_login'   => time(),
            ];
        }

        \DB::table('clients')
            ->insert($data);

    }
}