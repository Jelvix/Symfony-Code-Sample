<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 23.01.2019
 * Time: 15:04
 */

namespace App\EventSubscriber;


use App\Security\PermissionsProviderInterface;
use App\Utils\ReloadCacheInterface;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppCacheClearListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $permissionsProvider;

    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    public function __construct(
        LoggerInterface $logger,
        PermissionsProviderInterface $permissionsProvider,
        FilesystemInterface $fileSystem
    ) {
        $this->logger = $logger;
        $this->permissionsProvider = $permissionsProvider;
        $this->fileSystem = $fileSystem;
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onCacheCleared'
        ];
    }


    public function onCacheCleared(ConsoleEvent $event)
    {
        if ($event->getCommand()->getName() === 'cache:clear') {
            if ($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->logger->warning('Reload ACL cache');
                $this->permissionsProvider->reloadCache();
            }

            $list = $this->fileSystem->listContents();

            foreach ($list as $fileInfo) {
                if ($fileInfo['type'] === 'file' && preg_match("/Service.*\.php/", $fileInfo['filename'])) {
                    $this->fileSystem->delete($fileInfo['filename']);
                }
            }
        }
    }
}