<?php

namespace sonrac\lumenRest\commands;

use Illuminate\Console\Command;

/**
 * Class GenerateKeys
 * Generate oauth2 server keys.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class GenerateKeys extends Command
{
    /**
     * {@inheritdoc}
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $description = 'Generate oauth2 server keys';
    /**
     * {@inheritdoc}
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public $name = 'generate:keys {--force?} {--passphrase=} {--disable-out?}';
    /**
     * {@inheritdoc}
     */
    protected $signature = 'generate:keys 
                                {--force : Force update server keys}
                                {--disable-out : Disable output}
                                {--passphrase= : Enter passphrase (will be replace value in .env). 
                                        If 0 generated without phrase}
                                ';
    /**
     * Key path.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected $keyPath = 'framework/keys';

    /**
     * Oauth2 server config.
     *
     * @var null|array
     */
    protected $oauthConfig = null;

    /**
     * GenerateKeys constructor.
     *
     * @throws \Exception
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function __construct()
    {
        parent::__construct();

        $this->oauthConfig = config('oauth2') ?: [];

        $this->keyPath = isset($this->oauthConfig['keyPath']) ? $this->oauthConfig['keyPath'] :
            storage_path($this->keyPath);

        if (!\mkdir($this->keyPath, 0777, true) && !\is_dir($this->keyPath)) {
            throw new \Exception('Permission denied for server keys path create');
        }
    }

    /**
     * Trigger command.
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function handle()
    {
        $force      = $this->input->getOption('force');
        $phrase     = $this->input->getOption('passphrase');
        $disableOut = $this->input->getOption('disable-out');

        if (!$force) {
            if (!$phrase) {
                $phrase = isset($this->oauthConfig['passPhrase']) ? $this->oauthConfig['passPhrase'] : null;
            }
            $phrase = env('SERVER_PASS_PHRASE', $phrase);
        }

        if ($disableOut) {
            \ob_start();
        }
        if (!\file_exists($this->keyPath.'/'.config('oauth2.privateKeyName')) || $force) {
            if ($phrase) {
                $configPath = storage_path().'/../.env';
                $content    = \file_exists($configPath) ? \file_get_contents($configPath) : 'SERVER_PASS_PHRASE=';

                \file_put_contents(
                    $configPath,
                    \preg_replace('/^SERVER_PASS_PHRASE=(.+)?$/m', "SERVER_PASS_PHRASE={$phrase}", $content)
                );
            }

            $this->generatePrivateKey($phrase);
            $this->generatePublicKey($phrase);
        }

        if ($disableOut) {
            \ob_clean();
        }

        \exec('chmod 660 '.config('oauth2.keyPath').'/*.key');
    }

    /**
     * Generate private outh2 key.
     *
     * @param null|string $phrase Secret phrase
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected function generatePrivateKey($phrase = null)
    {
        $command = 'openssl genrsa ';

        if ($phrase) {
            $command .= " -passout pass:$phrase";
        }

        $command .= " -out {$this->keyPath}/".config('oauth2.privateKeyName');

        \exec($command);
    }

    /**
     * Generate public oauth2 server key.
     *
     * @param string|null $phrase Secret phrase
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    protected function generatePublicKey($phrase = null)
    {
        $command = "openssl rsa -in {$this->keyPath}/".config('oauth2.privateKeyName').' ';

        if ($phrase) {
            $command .= " -passin pass:$phrase";
        }

        $command .= " -pubout -out {$this->keyPath}/".config('oauth2.publicKeyName');

        \exec($command);
    }
}
