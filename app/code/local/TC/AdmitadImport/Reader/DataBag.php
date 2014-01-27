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

    /**
     * Constructor
     *
     * @param array $categories
     * @param array $products
     */
    public function __construct(array $categories, array $products)
    {
        $this->_categories = $categories;
        $this->_products   = $products;
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
}
