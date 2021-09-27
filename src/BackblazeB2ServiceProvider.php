<?php

namespace ToiLaDev\Flysystem;

use BackblazeB2\Client as BackblazeClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class BackblazeB2ServiceProvider extends ServiceProvider
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
                throw new BackblazeB2Exception('Please set all configuration keys. (accountId, applicationKey, [bucketId OR bucketName])');
            }
            $client = new BackblazeClient($config['accountId'], $config['applicationKey']);
            $adapter = new BackblazeAdapter($client, $config['bucketName'] ?? null, $config['bucketId'] ?? null, $config['url'] ?? null);

            return new Filesystem($adapter);
        });
    }

    public function register()
    {
    }
}
