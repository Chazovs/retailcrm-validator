<?php

namespace RetailCrm\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class CrmUrl
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @package Retailcrm\Validator
 */
class CrmUrl extends Constraint
{
    public string $schemeFail = 'Invalid protocol. The only https is allowed.';
    public string $pathFail = 'The domain path must be empty.';
    public string $portFail = 'Do not need to specify the port.';
    public string $domainFail = 'Invalid domain specified.';
    public string $noValidUrlHost = 'Invalid URL Host.';
    public string $noValidUrl = 'Invalid URL.';
    public string $queryFail = 'The query must be empty.';
    public string $fragmentFail = 'The fragment must be empty.';
    public string $authFail = 'No need to provide authorization data';
    public string $getFileError = 'It is impossible to get reference values';

    public function validatedBy(): string
    {
        return static::class .'Validator';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
