<?php

namespace RetailCrm\Tests\Validator;

use PHPUnit\Framework\TestCase;
use RetailCrm\Validator\CrmUrl;

/**
 * Class CrmUrlTest
 *
 * @package RetailCrm\Tests\Validator
 */
class CrmUrlTest extends TestCase
{
    public function testValidatedBy()
    {
        $crmUrl = new CrmUrl();

        self::assertEquals('Указан неверный домен.', $crmUrl->domainFail);
        self::assertEquals('Невалидный URL.', $crmUrl->noValidUrlHost);
        self::assertEquals('Порт указывать не нужно.', $crmUrl->portFail);
        self::assertEquals('Неверный протокол. Допустим только https.', $crmUrl->schemeFail);
        self::assertEquals('Путь домена должен быть пустым.', $crmUrl->pathFail);
        self::assertEquals(CrmUrl::class .'Validator', $crmUrl->validatedBy());
    }

    public function testGetTargets()
    {
        $crmUrl = new CrmUrl();
        self::assertEquals('property', $crmUrl->getTargets());
    }
}
