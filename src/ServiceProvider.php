<?php

namespace ToiLaDev\Flysystem\Backblaze;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        Storage::extend('b2', function ($app, $config) {
            $bucketIsConfigured = isset($config['bucketId']) || isset($config['bucketName']);
            if (!(
                isset($config['accountId']) &&
                isset($config['applicationKey']) &&
                $bucketIsConfigured
            )) {
                throw new B2Exception('Please set all configuration keys. (accountId, applicationKey, [bucketId OR bucketName])');
            }
            $client = new Client($config['accountId'], $config['applicationKey']);
            $adapter = new Adapter($client, $config['bucketName'] ?? null, $config['bucketId'] ?? null, $config['url'] ?? null);

            return new Filesystem($adapter);
        });
    }

    public function register()
    {
    }
}
