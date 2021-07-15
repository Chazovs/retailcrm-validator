<?php

namespace Retailcrm\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class CrmUrlConstraint
 *
 * @package Retailcrm\Validator
 */
class CrmUrlConstraint extends Constraint
{
    public $schemeFail = 'Неверный протокол. Допустим только https';
    public $pathFail = 'Путь домена должен быть пустым';
    public $portFail = 'Порт указывать не нужно';
    public $domainFail = 'Указан неверный домен';
}
