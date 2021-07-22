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
    public $schemeFail = 'Incorrect protocol. Only https is allowed.';
    public $pathFail = 'The domain path must be empty.';
    public $portFail = 'The port does not need to be specified.';
    public $domainFail = 'An invalid domain is specified.';
    public $noValidUrlHost = 'Incorrect Host URL.';
    public $noValidUrl = 'Incorrect URL.';
    public $queryFail = 'The query must be blank.';
    public $fragmentFail = 'The fragment should be blank.';
    public $authFail = 'No need to provide authorization data.';
    public $getFileError = 'Unable to obtain reference values.';

    public function validatedBy(): string
    {
        return static::class .'Validator';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
