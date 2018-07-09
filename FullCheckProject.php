<?php
/**
 * @author Donii Sergii <s.donii@infomir.com>
 */

namespace Smac\Check;

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Process\Process;

/**
 * Class FullCheckProject
 * Run all scripts from composer.json.
 */
class FullCheckProject
{
    /**
     * Scripts list from composer.json.
     *
     * @var array
     */
    private $scripts = [];

    private $ignore = ['php FullCheckProject.php'];

    /**
     * Path to composer.json.
     *
     * @var string
     */
    private $file;

    /**
     * FullCheckProject constructor.
     *
     *
     * @param string $file
     *
     * @throws \Exception
     */
    public function __construct($file = null)
    {
        $file   = $file ?: __DIR__.'/composer.json';
        $config = \json_decode(
            \file_get_contents($file),
            true
        );
        $this->file = $file;

        if (!isset($config['scripts'])) {
            throw new \Exception('Composer scripts is empty');
        }
        $this->scripts = $config['scripts'];
    }

    /**
     * Run check.
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Exception
     */
    public function run()
    {
        $path = \dirname($this->file);

        foreach ($this->scripts as $script) {
            if (\in_array($script, $this->ignore ?? [], false)) {
                continue;
            }
            $command = "cd {$path}; {$script}";
            /** @var Process $process */
            $process = new Process($command);
            $process->run();
            if (0 !== (int) $process->wait()) {
                echo $process->getOutput();

                throw new \Exception("Script $command run with error. Check it first");
            }
            echo $process->getOutput();
        }
    }
}

(new FullCheckProject())->run();
