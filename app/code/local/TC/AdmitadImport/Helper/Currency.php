<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_Currency extends Mage_Core_Helper_Abstract
{
    /** @var array */
    private $_currencyData;

    /**
     * Initialize helper with data
     *
     * @param TC_AdmitadImport_Reader_DataInterface $data
     */
    public function init(TC_AdmitadImport_Reader_DataInterface $data)
    {
        $this->_currencyData = $data->getCurrencies();
    }

    /**
     * Convert given currency to base currency (RUR is base by default, configuration for this not supported)
     *
     * @param float  $value
     * @param string $currencyCode
     *
     * @return float
     * @throws Exception
     */
    public function getConvertedValue($value, $currencyCode = null)
    {
        if (empty($currencyCode)) {
            $currencyCode = 'RUB';
        }

        if (!isset($this->_currencyData[$currencyCode])) {
            throw new Exception(sprintf('Could not convert currency "" to base', $currencyCode));
        }

        return (float)$this->_currencyData[$currencyCode] * $value;
    }
}
