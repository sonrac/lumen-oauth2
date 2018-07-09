<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

namespace sonrac\lumenRest;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use sonrac\lumenRest\contracts\RefreshTokenEntityInterface;
use sonrac\lumenRest\contracts\repositories\AccessTokenRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\AuthCodeRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\ClientRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\RefreshTokenRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\ScopeRepositoryInterface;
use sonrac\lumenRest\contracts\repositories\UserRepositoryInterface;
use sonrac\lumenRest\guard\JWT;
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
 * Oauth2 service provider.
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
        /*
         * Entities bind
         */
        $this->app->bind('sonrac\\lumenRest\\contracts\\AccessTokenEntityInterface', AccessToken::class);
        $this->app->bind('sonrac\\lumenRest\\contracts\\AuthCodeEntityInterface', AuthCode::class);
        $this->app->bind('sonrac\\lumenRest\\contracts\\ClientEntityInterface', Client::class);
        $this->app->bind('sonrac\\lumenRest\\contracts\\RefreshTokenEntityInterface', RefreshToken::class);
        $this->app->bind('sonrac\\lumenRest\\contracts\\ScopeEntityInterface', Scope::class);
        $this->app->bind('sonrac\\lumenRest\\contracts\\UserEntityInterface', User::class);

        /*
         * bind repositories
         */
        $this->app->bind('sonrac\\lumenRest\\contracts\\repositories\\AccessTokenRepositoryInterface', function () {
            return app()->make(AccessTokenRepository::class);
        });
        $this->app->bind('sonrac\\lumenRest\\contracts\\repositories\\AuthCodeRepositoryInterface', function () {
            return $this->app->make(AuthCodeRepository::class);
        });
        $this->app->bind('sonrac\\lumenRest\\contracts\\repositories\\ClientRepositoryInterface', function () {
            return new ClientRepository();
        });
        $this->app->bind(
            'sonrac\\lumenRest\\contracts\\repositories\\RefreshTokenRepositoryInterface',
            function () {
                return new RefreshTokenRepository(app(RefreshTokenEntityInterface::class));
            }
        );
        $this->app->bind(
            'sonrac\\lumenRest\\contracts\\repositories\\ScopeRepositoryInterface',
            ScopeRepository::class
        );
        $this->app->bind(
            'sonrac\\lumenRest\\contracts\\repositories\\UserRepositoryInterface',
            function () {
                return new UserRepository();
            }
        );

        $this->bindAuthorizationServer();
    }

    protected function bindAuthorizationServer()
    {
        $this->app->singleton('oauth2.server', function ($app) {
            $privateKey = config('oauth2.keyPath').'/'.config('oauth2.privateKeyName');
            $encryptionKey = config('oauth2.keyPath').'/'.config('oauth2.publicKeyName');

            /** @var $app \Laravel\Lumen\Application */
            $server = new AuthorizationServer(
                app(ClientRepositoryInterface::class),
                app(AccessTokenRepositoryInterface::class),
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
                        app(RefreshTokenRepositoryInterface::class)
                    ),
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

        $this->bindResourceServer();
    }

    protected function bindResourceServer()
    {
        $this->app->singleton(ResourceServer::class, function () {
            return new ResourceServer(
                $this->app->make(AccessTokenRepositoryInterface::class),
                config('oauth2.keyPath').'/'.config('oauth2.publicKeyName')
            );
        });

        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            $resourceServer = new ResourceServer(
                $this->app->make(AccessTokenRepositoryInterface::class),
                config('oauth2.keyPath').'/'.config('oauth2.publicKeyName')
            );

            $guard = new JWT(
                $name,
                $app['auth']->createUserProvider($config['provider']),
                $resourceServer,
                $app->make(ServerRequestInterface::class)
            );
            $app->refresh('request', $guard, 'setRequest');

            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return $guard;
        });
    }
}
