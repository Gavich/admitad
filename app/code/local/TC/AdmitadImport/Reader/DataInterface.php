<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
interface TC_AdmitadImport_Reader_DataInterface
{
    /**
     * Return categories to import
     *
     * @return array
     */
    public function getCategories();

    /**
     * Returns products to import
     *
     * @return array
     */
    public function getProducts();

    /**
     * Returns array with currencies conversion rate rules
     *
     * @return array(CURRENCY_CODE => RATE)
     */
    public function getCurrencies();
}
