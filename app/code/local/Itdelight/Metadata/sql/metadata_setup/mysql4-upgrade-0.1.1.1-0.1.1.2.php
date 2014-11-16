<?php

$installer = $this;
$installer->startSetup();

  $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'cat',      //column name
        'int'  //datatype definition
        );
   $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'cat_child',      //column name
        'int'  //datatype definition
        );
    $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'prod_cat',      //column name
        'int'  //datatype definition
        );
     $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'prod_childcat',      //column name
        'int'  //datatype definition
        );
      $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'prod_form',      //column name
        'int'  //datatype definition
        );
       $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'cat_form',      //column name
        'int'  //datatype definition
        );
  
$installer->endSetup();