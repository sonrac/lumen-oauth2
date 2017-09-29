<?php
/**
 * Created by PhpStorm.
 * User: Donii Sergii <doniysa@gmail.com>
 * Date: 9/21/17
 * Time: 11:53 AM
 */

namespace tests\units;


use Illuminate\Support\Str;
use sonrac\lumenRest\models\AccessToken;
use sonrac\lumenRest\tests\TestCase;

class AccessTokenTest extends TestCase
{
    protected $_seeds = ['users', 'clients'];

    /**
     * Scopes test
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testScopesWithDateTimeAttributes()
    {
        /** @var AccessToken $token */
        $token = new AccessToken();

        $token->access_token = $key = Str::random(32);
        $token->client_id = 1;
        $token->grant_type = AccessToken::TYPE_CLIENT_CREDENTIALS;
        $token->expires_at = time();
        $token->created_at = $date = new \DateTime();
        $this->assertTrue($token->save());

        $token = \DB::table('access_tokens')->get()->first();

        $this->assertEquals($key, $token->access_token);
        $this->assertEquals($date->format("Y-m-d H:i:"), date("Y-m-d H:i:", $token->created_at));

        $token = AccessToken::query()
            ->where('access_token', '=', $key)
            ->first();

        $this->assertEquals($date->format('Y-m-d H:i:s'), $token->created_at->format('Y-m-d H:i:s'));
    }
}