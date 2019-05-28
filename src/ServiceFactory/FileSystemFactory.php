<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 0:39
 */

namespace App\ServiceFactory;


use Danhunsaker\Flysystem\Redis\RedisAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Predis\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileSystemFactory
{

    public static function createFileSystem(ParameterBagInterface $params): FilesystemInterface
    {
        $adapter = new Local($params->get('app_services')['generated_classes_path']);
        $filesystem = new Filesystem($adapter);
        return $filesystem;
    }


}