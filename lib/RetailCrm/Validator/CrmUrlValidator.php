<?php


namespace RetailCrm\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CrmUrlValidator
 *
 * @package Retailcrm\Validator
 */
class CrmUrlValidator extends ConstraintValidator
{
    /**
     * Функция проверки валидности значения
     *
     * @param mixed      $value Проверяемое значение
     * @param Constraint $constraint Ограничение для валидации
     */
    public function validate($value, Constraint $constraint)
    {
        $crmUrl = parse_url($value);
        $this->checkUrlFormat($crmUrl, $constraint);

        $validDomains = $this->getValidDomains($crmUrl['host']);

        if (false === array_search($crmUrl['host'], $validDomains)) {
            $this->context->buildViolation($constraint->domainFail)->addViolation();
        }
    }

    private function checkUrlFormat(array $crmUrl, Constraint $constraint)
    {
        if (isset($crmUrl['scheme']) && $crmUrl['scheme'] !== 'https') {
            $this->context->buildViolation($constraint->schemeFail)->addViolation();
        }

        if (isset($crmUrl['path']) && $crmUrl['path'] !== '/' && $crmUrl['path'] !== '') {
            $this->context->buildViolation($constraint->pathFail)->addViolation();
        }

        if (isset($crmUrl['port']) && !empty($crmUrl['port'])) {
            $this->context->buildViolation($constraint->portFail)
                ->addViolation();
        }
    }

    private function getValidDomains($host)
    {
        $subdomain = explode('.', $host)[0];

        $boxDomainsContent = json_decode(file_get_contents("https://infra-data.retailcrm.tech/box-domains.json"), true);
        $crmDomainsContent = json_decode(file_get_contents("https://infra-data.retailcrm.tech/crm-domains.json"), true );

        $boxDomains = array_column($boxDomainsContent['domains'], 'domain');
        $crmDomains = array_column($crmDomainsContent['domains'], 'domain');

        foreach ($crmDomains as $key => $domain){
            $crmDomains[$key] = sprintf("%s.%s", $subdomain, $domain);
        }

        return array_merge($boxDomains, $crmDomains);
    }
}
