<?php

namespace ToiLaDev\Flysystem\BackBlade;

use ToiLaDev\Flysystem\BackBlade\Exceptions\B2Exception;
use ToiLaDev\Flysystem\BackBlade\Exceptions\BadJsonException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\BadValueException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\BucketAlreadyExistsException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\BucketNotEmptyException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\FileNotPresentException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\NotFoundException;
use ToiLaDev\Flysystem\BackBlade\Exceptions\UnauthorizedAccessException;
use GuzzleHttp\Psr7\Response;

class ErrorHandler
{
    protected static $mappings = [
        'bad_json'                       => BadJsonException::class,
        'bad_value'                      => BadValueException::class,
        'duplicate_bucket_name'          => BucketAlreadyExistsException::class,
        'not_found'                      => NotFoundException::class,
        'file_not_present'               => FileNotPresentException::class,
        'cannot_delete_non_empty_bucket' => BucketNotEmptyException::class,
        'unauthorized'                   => UnauthorizedAccessException::class,
    ];

    /**
     * @param Response $response
     *
     * @throws B2Exception
     */
    public static function handleErrorResponse(Response $response)
    {
        $responseJson = json_decode($response->getBody(), true);

        if (isset(self::$mappings[$responseJson['code']])) {
            $exceptionClass = self::$mappings[$responseJson['code']];
        } else {
            // We don't have an exception mapped to this response error, throw generic exception
            $exceptionClass = B2Exception::class;
        }

        throw new $exceptionClass('Received error from B2: '.$responseJson['message']);
    }
}
