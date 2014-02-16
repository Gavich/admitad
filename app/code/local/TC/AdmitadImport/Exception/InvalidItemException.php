<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Exception_InvalidItemException extends Exception
{
    /** @var array */
    private $_product;

    /**
     * Constructor
     *
     * @param array $product
     * @param array $errors
     */
    public function __construct($product, $errors = array())
    {
        $this->_product = $product;
        parent::__construct(sprintf('Invalid product data, errors: %s', implode(', ', $errors)));
    }

    /**
     * Getter for invalid product data
     *
     * @return array
     */
    public function getProduct()
    {
        return $this->_product;
    }
}
