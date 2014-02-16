<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Reader_DataBag implements TC_AdmitadImport_Reader_DataInterface
{
    /** @var array */
    private $_categories;

    /** @var array */
    private $_products;

    /** @var array */
    private $_currencies;

    /**
     * Constructor
     *
     * @param array $categories
     * @param array $products
     * @param array $currencies
     */
    public function __construct(array $categories, array $products, array $currencies)
    {
        $this->_categories = $categories;
        $this->_products   = $products;
        $this->_currencies = $currencies;
    }

    /**
     * Return categories to import
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * Returns products to import
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Returns array with currencies conversion rate rules
     *
     * @return array(CURRENCY_CODE => RATE)
     */
    public function getCurrencies()
    {
        return $this->_currencies;
    }
}
