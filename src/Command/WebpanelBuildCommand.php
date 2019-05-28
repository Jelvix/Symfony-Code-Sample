<?php

namespace App\Command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;


/**
 * Class WebpanelBuildCommand
 * Run build process of webpanel (Angular based)
 * @package App\Command
 */
class WebpanelBuildCommand extends Command
{
    protected static $defaultName = 'app:webpanel:build';
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        parent::__construct(self::$defaultName);
        $this->params = $params;
    }

    protected function configure()
    {
        $this
            ->setDescription('Build angular webpanel project')
            ->addOption('prod', null, InputArgument::OPTIONAL, 'Prod mode enabled');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $prod = $input->getOption('prod');
        $argsStr = '';
        if ($prod) {
            $argsStr = '--prod';
            $io->note('Build in prod mode');
        }
        $io->text('Start building... webpanel root path: ' . $this->params->get('webpanel_root_path'));
        $process = new Process('cd webpanel && node ./node_modules/@angular/cli/bin/ng build --deploy-url=' . $this->params->get('webpanel_deploy_url') . ' ' . $argsStr);
        $process->setTimeout(360);
        $process->mustRun(function ($err, $messages) use ($io) {
            if ($err === 'err') {
                $io->warning($messages);
            }
        });
        $output = $process->getOutput();
        $configFilename = $this->parseAndSaveToConfig($output);
        $io->text($output);
        $io->text('Config file is generated: ' . $configFilename);
        $io->success('webpanel is built successfully!');
    }

    private function parseAndSaveToConfig($output)
    {
        $rootPath = $this->params->get('kernel.project_dir') . '/config/packages/';
        $fileSystem = new Filesystem(new Local($rootPath));
        $fileName = 'webpanel.yaml';

        $lines = explode("\n", $output);

        $scripts = [];
        $styles = [];
        usort($lines, function ($a, $b) {
            if (preg_match("/\[entry\]/", $a)) {
                return -1;
            }
            if (preg_match("/\[entry\]/", $b)) {
                return 1;
            }
            if (preg_match("/polyfills/", $a)) {
                return -1;
            }
            if (preg_match("/polyfills/", $b)) {
                return 1;
            }
            if (preg_match("/\[initial\]/", $a)) {
                return -1;
            }
            if (preg_match("/\[initial\]/", $b)) {
                return 1;
            }
            return 0;
        });
        foreach ($lines as $line) {
            if (preg_match("/^chunk\s+\{\w+\}\s+([a-zA-Z]+\.([\w]+\.)?(js|css))/", $line, $matches)) {
                if (preg_match("/\.js$/", $matches[1])) {
                    $scripts[] = $matches[1];
                } else {
                    $styles[] = $matches[1];
                }
            }
        }

        $content = sprintf("parameters:\n  webpanel:\n    scripts: ['%s']\n    styles: ['%s']",
            implode("','", $scripts), implode("','", $styles));
        $fileSystem->put($fileName, $content);
        return $rootPath . $fileName;
    }
}
