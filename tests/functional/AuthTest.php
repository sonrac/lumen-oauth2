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
 * Test AuthTest.
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class AuthTest extends TestCase
{
    protected $seeds = ['users', 'clients'];

    /**
     * Test client credentials.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testsClientCredential()
    {
        $reqData = [
            'grant_type'    => AccessToken::TYPE_CLIENT_CREDENTIALS,
            'client_id'     => 1,
            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
            'scope'         => 'default',
            'redirect_uri'  => ClientsSeeder::REDIRECT_URI_TEST_CLIENT,
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->post('/oauth/access_token', $reqData);

        $resp = \json_decode($data->response->getContent(), true);

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);

        return [$resp['access_token'], AccessToken::query()->get()->first()];
    }

    /**
     * Password client grant auth test.
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

        $resp = \json_decode($data->response->getContent(), true);

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);
        $this->assertArrayHasKey('refresh_token', $resp);

        return $resp['refresh_token'];
    }

    /**
     * Password client grant auth test.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testImplicitClient()
    {
        $reqData = [
            'response_type' => AccessToken::RESPONSE_IMPLICIT,
            'client_id'     => 1,
            'scope'         => 'default',
            'redirect_uri'  => ClientsSeeder::REDIRECT_URI_TEST_CLIENT,
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->get('/authorize?'.\http_build_query($reqData));

        $data->seeHeader('location');
        $url = \parse_url($data->response->headers->get('location'));

        $this->assertContains('access_token', $url['fragment'], true);
        $this->assertContains('bearer', \mb_strtolower($url['fragment']), true);
        $this->assertContains('token_type', $url['fragment'], true);
        $this->assertContains('expires_in', $url['fragment'], true);
    }

    /**
     * Password client grant auth test.
     *
     * @depends testPasswordClient
     *
     * @author  Donii Sergii <doniysa@gmail.com>
     *
     * @param mixed $token
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

        $resp = \json_decode($data->response->getContent(), true);

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);
        $this->assertArrayHasKey('refresh_token', $resp);
    }

    /**
     * Test auth code.
     *
     * @return string
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testGetAuthCode()
    {
        $reqData = [
            'response_type' => AccessToken::RESPONSE_AUTHORIZATION_CODE,
            'client_id'     => 1,
            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
            'scope'         => 'default',
            'state'         => 213,
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->get('/authorize?'.\http_build_query($reqData));

        $data->seeHeader('location');
        $data->seeStatusCode(302);
        $url = \parse_url($data->response->headers->get('location'));

        $this->assertContains('code', $url['query'], true);
        $this->assertContains('state', $url['query'], true);

        \parse_str($url['query'], $parts);

        $this->assertArrayHasKey('code', $parts);
        $this->assertArrayHasKey('state', $parts);

        return $parts['code'];
    }

    /**
     * Test authenticate with code.
     *
     * @param string $code
     *
     * @depends testGetAuthCode
     *
     * @author  Donii Sergii <doniysa@gmail.com>
     */
    public function testAuthWithCode($code)
    {
        $reqData = [
            'grant_type'    => AccessToken::TYPE_AUTHORIZATION_CODE,
            'code'          => $code,
            'client_id'     => 1,
            'client_secret' => ClientsSeeder::SECRET_KEYS[0],
            'scope'         => 'default',
        ];
        /** @var \Tests\Functional\AuthTest $data */
        $data = $this->post('/oauth/access_token', $reqData);

        $resp = \json_decode($data->response->getContent(), true);

        $this->assertArrayHasKey('access_token', $resp);
        $this->assertEquals('Bearer', $resp['token_type']);
        $this->assertArrayHasKey('refresh_token', $resp);
    }

    /**
     * Test successfully get protected oauth method.
     *
     * @param string $token
     *
     * @depends testsClientCredential
     *
     * @author  Donii Sergii <doniysa@gmail.com>
     */
    public function testSuccessSecurityMiddleware($token)
    {
        $token[1]->save();
        $this->post('/user-info', [], [
            'authorization' => $token[0],
        ])->seeJsonStructure(['user', 'client'])
            ->seeStatusCode(200);
    }

    /**
     * Test denied protected method access denied.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testDeniedProtectedMethod()
    {
        $this->post('/user-info', [])
            ->seeJsonStructure(['error', 'message', 'hint'])
            ->seeStatusCode(401);
    }

    /**
     * Test denied protected method with invalid access token.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testDeniedProtectedMethodWithInvalidAccessToken()
    {
        $this->post('/user-info', [], [
            'authorization' => 'asdeasdasdasdasdasd',
        ])
            ->seeJsonStructure(['error', 'message', 'hint'])
            ->seeStatusCode(401);
    }
}
