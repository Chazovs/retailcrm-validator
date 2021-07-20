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

        self::assertEquals('Invalid domain specified.', $crmUrl->domainFail);
        self::assertEquals('Invalid URL.', $crmUrl->noValidUrlHost);
        self::assertEquals('Do not need to specify the port.', $crmUrl->portFail);
        self::assertEquals('Invalid protocol. The only https is allowed.', $crmUrl->schemeFail);
        self::assertEquals('The domain path must be empty.', $crmUrl->pathFail);
        self::assertEquals(CrmUrl::class .'Validator', $crmUrl->validatedBy());
    }

    public function testGetTargets()
    {
        $crmUrl = new CrmUrl();
        self::assertEquals('property', $crmUrl->getTargets());
    }
}
