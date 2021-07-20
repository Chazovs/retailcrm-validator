<?php

namespace RetailCrm\Tests\Validator;


use PHPUnit\Framework\TestCase;
use RetailCrm\Validator\CrmUrl;
use RetailCrm\Validator\CrmUrlValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

class CrmUrlValidatorTest extends TestCase
{
    public function testValidateSuccess(): void
    {
        $validCrms = [
            'https://asd.retailcrm.ru',
            'https://test.retailcrm.pro',
            'https://raisa.retailcrm.es',
            'https://blabla.simla.com',
            'https://blabla.simlachat.com',
            'https://blabla.simlachat.ru',
            'https://blabla.ecomlogic.com',
            'https://retailcrm.inventive.ru',
            'https://crm.baucenter.ru',
            'https://crm.holodilnik.ru',
            'https://crm.eco.lanit.ru',
            'https://ecom.inventive.ru',
            'https://retailcrm.tvoydom.ru',
        ];
    
        $translator = new class() implements TranslatorInterface, LocaleAwareInterface {
            use TranslatorTrait;
        };
    
        $metadata = new LazyLoadingMetadataFactory();
        $factory = new ExecutionContextFactory($translator);
        $validator = new CrmUrlValidator();

        foreach ($validCrms as $validCrm) {
            $context = new ExecutionContext(
                new RecursiveValidator($factory, $metadata, new ConstraintValidatorFactory()),
                CrmUrl::class,
                $translator
            );
            $context->setConstraint(new CrmUrl());
            $validator->initialize($context);
            $validator->validate($validCrm, new CrmUrl());

            self::assertEmpty($context->getViolations());
        }
    }

    public function testValidateFailed(): void
    {
        $failedUrls = [
            [
                'url' => 'http://asd.retailcrm.ru',
                'errors' => ['Invalid protocol. The only https is allowed.'],
            ],
            [
                'url' => 'https://test.retailcrm.pro:8080',
                'errors' => ['Do not need to specify the port.'],
            ],
            [
                'url' => 'https://raisa.retailcrm.ess',
                'errors' => ['Invalid domain specified.'],
            ],
            [
                'url' => 'https://blabla.simlla.com',
                'errors' => ['Invalid domain specified.'],
            ],
            [
                'url' => 'https:/blabla.simlachat.ru',
                'errors' => [
                    'Invalid URL.',
                ],
            ],
            [
                'url' => 'htttps://blabla.ecomlogic.com',
                'errors' => ['Invalid protocol. The only https is allowed.'],
            ],
            [
                'url' => 'https://blabla.ecomlogic.com/test',
                'errors' => ['The domain path must be empty.'],
            ],
            [
                'url' => 'htttps://blabla.eecomlogic.com/test',
                'errors' => [
                    'Invalid protocol. The only https is allowed.',
                    'The domain path must be empty.',
                ],
            ],
            [
                'url' => 'https://test:test@blabla.eecomlogic.com/test?test=test#fragment',
                'errors' => [
                    'The query must be empty.',
                    'No need to provide authorization data',
                    'The fragment must be empty.',
                    'The domain path must be empty.',
                ],
            ],
        ];

        $translator = new class() implements TranslatorInterface, LocaleAwareInterface {
            use TranslatorTrait;
        };

        $metadata = new LazyLoadingMetadataFactory();
        $factory = new ExecutionContextFactory($translator);
        $validator = new CrmUrlValidator();

        foreach ($failedUrls as $failedUrl) {
            $context = new ExecutionContext(
                new RecursiveValidator($factory, $metadata, new ConstraintValidatorFactory()),
                CrmUrl::class,
                $translator
            );
            $context->setConstraint(new CrmUrl());
            $validator->initialize($context);
            $validator->validate($failedUrl['url'], new CrmUrl());

            foreach ($failedUrl['errors'] as $key=>$error){
                self::assertEquals($context->getViolations()->get($key)->getMessage(), $failedUrl['errors'][$key]);
            }

            self::assertCount($context->getViolations()->count(), $failedUrl['errors']);
        }
    }
}
