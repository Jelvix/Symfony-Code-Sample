<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 0:39
 */

namespace App\ServiceFactory;


use Danhunsaker\Flysystem\Redis\RedisAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Predis\Client;

class RedisFileSystemFactory
{

    public static function createFileSystem(): FilesystemInterface
    {
        $redis = new Client();
        $adapter = new RedisAdapter($redis);
        $filesystem = new Filesystem($adapter);
        return $filesystem;
    }


}