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
    public string $schemeFail = 'Неверный протокол. Допустим только https.';
    public string $pathFail = 'Путь домена должен быть пустым.';
    public string $portFail = 'Порт указывать не нужно.';
    public string $domainFail = 'Указан неверный домен.';
    public string $noValidUrlHost = 'Невалидный URL.';

    public function validatedBy(): string
    {
        return static::class .'Validator';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
