<?php

namespace RetailCrm\Validator;

use JsonException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CrmUrlValidator
 *
 * @package Retailcrm\Validator
 */
class CrmUrlValidator extends ConstraintValidator
{
    public const boxDomainsUrl = "https://infra-data.retailcrm.tech/box-domains.json";
    public const crmDomainsUrl = "https://infra-data.retailcrm.tech/crm-domains.json";
    
    /**
     * @var \Symfony\Component\Validator\Constraint
     */
    private $constraint;
    
    /**
     * Validate CRM URL
     *
     * @param mixed      $value URL from form
     * @param Constraint $constraint Restriction for validation
     */
    public function validate($value, Constraint $constraint): void
    {
        $this->constraint = $constraint;
        $filtredUrl = filter_var($value, FILTER_VALIDATE_URL);
    
        if (false === $filtredUrl) {
            $this->context->buildViolation($constraint->noValidUrl)->addViolation();
        } else {
            $urlArray = parse_url($filtredUrl);
        
            if ($this->checkUrlFormat($urlArray)) {
                $mainDomain = $this->getMainDomain($urlArray['host']);
                $existInCrm = $this->checkDomains(self::crmDomainsUrl, $mainDomain);
                $existInBox = $this->checkDomains(self::boxDomainsUrl, $urlArray['host']);

                if (false === $existInCrm && false === $existInBox) {
                    $this->context->buildViolation($constraint->domainFail)->addViolation();
                }
            }
        }
    }
    
    /**
     * @param array $crmUrl
     *
     * @return bool
     */
    private function checkUrlFormat(array $crmUrl): bool
    {
        $checkResult = true;
        
        if (!isset($crmUrl['host'])) {
            $this->context->buildViolation($this->constraint->noValidUrlHost)->addViolation();
    
            $checkResult = false;
        }

        if (isset($crmUrl['query']) && !empty($crmUrl['query'])) {
            $this->context->buildViolation($this->constraint->queryFail)->addViolation();
    
            $checkResult = false;
        }
    
        if ((isset($crmUrl['pass']) && !empty($crmUrl['pass']))
            || (isset($crmUrl['user']) && !empty($crmUrl['user']))
        ) {
            $this->context->buildViolation($this->constraint->authFail)->addViolation();
        
            $checkResult = false;
        }

        if (isset($crmUrl['fragment']) && !empty($crmUrl['fragment'])) {
            $this->context->buildViolation($this->constraint->fragmentFail)->addViolation();
    
            $checkResult = false;
        }

        if (isset($crmUrl['scheme']) && $crmUrl['scheme'] !== 'https') {
            $this->context->buildViolation($this->constraint->schemeFail)->addViolation();
            
            $checkResult = false;
        }

        if (isset($crmUrl['path']) && $crmUrl['path'] !== '/' && $crmUrl['path'] !== '') {
            $this->context->buildViolation($this->constraint->pathFail)->addViolation();
            
            $checkResult = false;
        }

        if (isset($crmUrl['port']) && !empty($crmUrl['port'])) {
            $this->context->buildViolation($this->constraint->portFail)->addViolation();
    
            $checkResult = false;
        }

        return $checkResult;
    }
    
    /**
     * @param string $domainUrl
     *
     * @return array
     */
    private function getValidDomains(string $domainUrl): array
    {
        try {
            $content = json_decode(file_get_contents($domainUrl), true, 512, JSON_THROW_ON_ERROR);
            
            return array_column($content['domains'], 'domain');
        } catch (JsonException $exception) {
            $this->context->buildViolation($this->constraint->getFileError)->addViolation();
            
            return [];
        }
    }
    
    /**
     * @param $host
     *
     * @return string
     */
    private function getMainDomain($host): string
    {
        $hostArray = explode('.', $host);
        unset($hostArray[0]);
        
        return implode('.', $hostArray);
    }
    
    /**
     * @param string $crmDomainsUrl
     * @param string $domainHost
     *
     * @return bool
     */
    private function checkDomains(string $crmDomainsUrl, string $domainHost): bool
    {
        $domains = $this->getValidDomains($crmDomainsUrl);
    
        if (in_array($domainHost, $domains, true)) {
            return true;
        }
        
        return false;
    }
}
