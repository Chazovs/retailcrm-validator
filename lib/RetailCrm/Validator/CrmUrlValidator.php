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
     * Validate CRM URL
     *
     * @param mixed      $value URL from form
     * @param Constraint $constraint Ограничение для валидации
     */
    public function validate($value, Constraint $constraint)
    {
        $urlArray = parse_url($value);

        if ($this->checkUrlFormat($urlArray, $constraint)) {
            $validDomains = $this->getValidDomains($urlArray['host']);

            if (false === array_search($urlArray['host'], $validDomains)) {
                $this->context->buildViolation($constraint->domainFail)->addViolation();
            }
        }
    }

    /**
     * @param array                                   $crmUrl
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @return bool
     */
    private function checkUrlFormat(array $crmUrl, Constraint $constraint): bool
    {
        if (!isset($crmUrl['scheme']) || !isset($crmUrl['host'])) {
            $this->context->buildViolation($constraint->noValidUrlHost)->addViolation();

            return false;
        }

        if (isset($crmUrl['query']) && !empty($crmUrl['query'])) {
            $this->context->buildViolation($constraint->queryFail)->addViolation();

            return false;
        }

        if (isset($crmUrl['pass']) && !empty($crmUrl['pass'])) {
            $this->context->buildViolation($constraint->authFail)->addViolation();

            return false;
        }

        if (isset($crmUrl['user']) && !empty($crmUrl['user'])) {
            $this->context->buildViolation($constraint->authFail)->addViolation();

            return false;
        }

        if (isset($crmUrl['fragment']) && !empty($crmUrl['fragment'])) {
            $this->context->buildViolation($constraint->fragmentFail)->addViolation();

            return false;
        }

        if (isset($crmUrl['scheme']) && $crmUrl['scheme'] !== 'https') {
            $this->context->buildViolation($constraint->schemeFail)->addViolation();
        }

        if (isset($crmUrl['path']) && $crmUrl['path'] !== '/' && $crmUrl['path'] !== '') {
            $this->context->buildViolation($constraint->pathFail)->addViolation();
        }

        if (isset($crmUrl['port']) && !empty($crmUrl['port'])) {
            $this->context->buildViolation($constraint->portFail)->addViolation();
        }

        return true;
    }

    /**
     * @param string $host
     *
     * @return array
     */
    private function getValidDomains(string $host): array
    {
        $subdomain = explode('.', $host)[0];
        $boxDomainsContent = json_decode(file_get_contents("https://infra-data.retailcrm.tech/box-domains.json"), true);
        $crmDomainsContent = json_decode(file_get_contents("https://infra-data.retailcrm.tech/crm-domains.json"), true);
        $boxDomains = array_column($boxDomainsContent['domains'], 'domain');
        $crmDomains = array_column($crmDomainsContent['domains'], 'domain');

        foreach ($crmDomains as $key => $domain) {
            $crmDomains[$key] = sprintf("%s.%s", $subdomain, $domain);
        }

        return array_merge($boxDomains, $crmDomains);
    }
}
