<?php

namespace Retailcrm\Validator;


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
    public $schemeFail = 'Неверный протокол. Допустим только https';
    public $pathFail = 'Путь домена должен быть пустым';
    public $portFail = 'Порт указывать не нужно';
    public $domainFail = 'Указан неверный домен';

    public function validatedBy()
    {
        return static::class .'Validator';
    }
}
