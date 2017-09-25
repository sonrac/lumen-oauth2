<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace Tests\Functional;

use sonrac\lumenRest\models\AccessToken;
use sonrac\lumenRest\tests\seeds\ClientsSeeder;
use sonrac\lumenRest\tests\TestCase;

/**
 * Class AuthTest
 * Test AuthTest
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class AuthTest extends TestCase
{
    protected $_seeds = ['scopes', 'users', 'clients'];

//    /**
//     * Test client credentials
//     *
//     * @author Donii Sergii <doniysa@gmail.com>
//     */
//    public function testsClientCredential()
//    {
//        $reqData = [
//            'grant_type'    => AccessToken::TYPE_CLIENT_CREDENTIALS,
//            'client_id'     => 1,
//            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
//            'scope'         => 'default',
//            'redirect_uri'  => ClientsSeeder::REDIRECT_URI_TEST_CLIENT,
//        ];
//        /** @var \Tests\Functional\AuthTest $data */
//        $data = $this->post('/oauth/access_token', $reqData);
//
//        $resp = json_decode($data->response->getContent(), true);
//
//        $this->assertArrayHasKey('access_token', $resp);
//        $this->assertEquals('Bearer', $resp['token_type']);
//    }

    /**
     * Password client grant auth test
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testPasswordClient()
    {
        $reqData = [
            'grant_type'    => AccessToken::TYPE_PASSWORD,
            'client_id'     => 1,
            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
            'scope'         => 'default',
            'redirect_uri'  => ClientsSeeder::REDIRECT_URI_TEST_CLIENT,
            'username'      => 'test_user_1',
            'password'      => 'test_user_1',
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->post('/oauth/access_token', $reqData);

        $resp = json_decode($data->response->getContent(), true);

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);
        $this->assertArrayHasKey('refresh_token', $resp);

        return $resp['refresh_token'];
    }
//
//    /**
//     * Password client grant auth test
//     *
//     * @author Donii Sergii <doniysa@gmail.com>
//     */
//    public function testImplicitClient()
//    {
//        $reqData = [
//            'grant_type'    => AccessToken::TYPE_IMPLICIT,
//            'client_id'     => 1,
//            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
//            'scope'         => 'default',
//            'redirect_uri'  => ClientsSeeder::REDIRECT_URI_TEST_CLIENT,
//            'username'      => 'test_user_1',
//            'password'      => 'test_user_1',
//        ];
//        /** @var \Tests\Functional\AuthTest $data */
//        $data = $this->post('/oauth/access_token', $reqData);
//
//        $resp = json_decode($data->response->getContent(), true);
//
//        $this->assertArrayHasKey('access_token', $resp);
//        $this->assertEquals('Bearer', $resp['token_type']);
//        $this->assertArrayHasKey('refresh_token', $resp);
//    }

    /**
     * Password client grant auth test
     *
     * @depends testPasswordClient
     *
     * @author  Donii Sergii <doniysa@gmail.com>
     */
    public function testRefreshToken($token)
    {
        $reqData = [
            'grant_type'    => AccessToken::TYPE_REFRESH_TOKEN,
            'refresh_token' => $token,
            'client_id'     => 1,
            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
            'scope'         => 'default',
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->post('/oauth/access_token', $reqData);

        $resp = json_decode($data->response->getContent(), true);

        if (!isset($resp['access_token'])) {
            var_dump($reqData, $resp);
            exit;
        }

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);
        $this->assertArrayHasKey('refresh_token', $resp);
    }
}
