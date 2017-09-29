<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use sonrac\lumenRest\models\AccessToken;
use sonrac\lumenRest\models\AuthCode;
use sonrac\lumenRest\models\Client;
use sonrac\lumenRest\models\RefreshToken;
use sonrac\lumenRest\models\repositories\AccessTokenRepository;
use sonrac\lumenRest\models\repositories\AuthCodeRepository;
use sonrac\lumenRest\models\repositories\ClientRepository;
use sonrac\lumenRest\models\repositories\RefreshTokenRepository;
use sonrac\lumenRest\models\repositories\ScopeRepository;
use sonrac\lumenRest\models\repositories\UserRepository;
use sonrac\lumenRest\models\Scope;
use sonrac\lumenRest\models\User;

/**
 * Class Oauth2ServiceProvider
 *
 * @package App\Providers
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class Oauth2ServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        app()->configure('oauth2');
        /**
         * Entities bind
         */
        $this->app->bind('League\\OAuth2\\Server\\Entities\\AccessTokenEntityInterface', AccessToken::class);
        $this->app->bind('League\\OAuth2\\Server\\Entities\\AuthCodeEntityInterface', AuthCode::class);
        $this->app->bind('League\\OAuth2\\Server\\Entities\\ClientEntityInterface', Client::class);
        $this->app->bind('League\\OAuth2\\Server\\Entities\\RefreshTokenEntityInterface', RefreshToken::class);
        $this->app->bind('League\\OAuth2\\Server\\Entities\\ScopeEntityInterface', Scope::class);
        $this->app->bind('League\\OAuth2\\Server\\Entities\\UserEntityInterface', User::class);

        /**
         * bind repositories
         */
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\AccessTokenRepositoryInterface', function () {
            return app()->make(AccessTokenRepository::class);
        });
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\AuthCodeRepositoryInterface', function () {
            return $this->app->make(AuthCodeRepository::class);
        });
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\ClientRepositoryInterface', function () {
            return new ClientRepository();
        });
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\RefreshTokenRepositoryInterface', function () {
            return new RefreshTokenRepository(app(RefreshTokenEntityInterface::class));
        });
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\ScopeRepositoryInterface', ScopeRepository::class);
        $this->app->bind('League\\OAuth2\\Server\\Repositories\\UserRepositoryInterface', function () {
            return new UserRepository();
        });

        $this->bindAuthorizationServer();
    }

    protected function bindAuthorizationServer()
    {
        $this->app->singleton('oauth2.server', function ($app) {
            /**
             * use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
             * use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
             * use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
             * use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
             * use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
             */

            $privateKey = config('oauth2.keyPath') . '/' . config('oauth2.privateKeyName');
            $encryptionKey = config('oauth2.keyPath') . '/' . config('oauth2.publicKeyName');

            /** @var $app \Laravel\Lumen\Application */
            $server = new AuthorizationServer(
                new ClientRepository(),
                app(AccessTokenRepository::class),
                app(ScopeRepositoryInterface::class),
                $privateKey,
                $encryptionKey
            );

            if ($clientConfig = config('oauth2.token_type.client')) {
                $server->enableGrantType(
                    new $clientConfig['class'](),
                    new \DateInterval(config('oauth2.access_token_ttl'))
                );
            }

            if ($passwordConfig = config('oauth2.token_type.password')) {
                $server->enableGrantType(
                    new PasswordGrant(
                        app(UserRepositoryInterface::class),
                        app(RefreshTokenRepositoryInterface::class)),
                    new \DateInterval(config('oauth2.access_token_ttl'))
                );
            }

            if ($refreshConfig = config('oauth2.token_type.refresh_token')) {
                $server->enableGrantType(
                    new $refreshConfig['class'](
                        app(RefreshTokenRepositoryInterface::class)
                    ),
                    new \DateInterval(config('oauth2.access_token_ttl'))
                );
            }

            if ($implicitConfig = config('oauth2.token_type.implicit')) {
                $server->enableGrantType(
                    new $implicitConfig['class'](
                        new \DateInterval(config('oauth2.access_token_ttl'))
                    )
                );
            }

            if ($oauthCodeConfig = config('oauth2.token_type.code')) {
                /** @var \League\OAuth2\Server\Grant\AuthCodeGrant $grant */
                $grant = (new $oauthCodeConfig['class'](
                    app(AuthCodeRepositoryInterface::class),
                    app(RefreshTokenRepositoryInterface::class),
                    new \DateInterval(config('oauth2.token_type.code.code_ttl'))
                ));
                $grant->setRefreshTokenTTL(new \DateInterval(config('oauth2.refresh_token_ttl')));
                $server->enableGrantType(
                    $grant,
                    new \DateInterval(config('oauth2.access_token_ttl'))
                );
            }

            return $server;
        });

        $this->app->alias('oauth2.server', AuthorizationServer::class);
    }
}