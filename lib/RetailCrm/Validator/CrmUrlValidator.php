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
            $this->context->buildViolation($constraint->domainFail)
                ->addViolation();
        }
    }

    private function checkUrlFormat(array $crmUrl, Constraint $constraint)
    {
        if (isset($crmUrl['scheme']) && $crmUrl['scheme'] !== 'https') {
            $this->context->buildViolation($constraint->schemeFail)
                ->addViolation();
        }

        if (isset($crmUrl['path']) && $crmUrl['path'] !== '/' && $crmUrl['path'] !== '') {
            $this->context->buildViolation($constraint->pathFail)
                ->addViolation();
        }

        if (isset($crmUrl['port']) && !empty($crmUrl['port'])) {
            $this->context->buildViolation($constraint->portFail)
                ->addViolation();
        }
    }

    private function getValidDomains($host)
    {
        $subdomain = explode($host, '.')[0];

        $boxDomains = json_decode(file_get_contents("https://infra-data.retailcrm.tech/box-domains.json"));
        $crmDomains = json_decode(file_get_contents("https://infra-data.retailcrm.tech/crm-domains.json"));

        foreach ($crmDomains->domains as $key=>$domain){
            $crmDomains->domains[$key]->domain = sprintf("%s.%s", $subdomain, $domain->domain);
        }

        return array_merge($crmDomains->domains, $boxDomains->domains);
    }
}
