<?php

$installer = $this;
$installer->startSetup();

 
    $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'categories',      //column name
        'text'  //datatype definition
        );
      $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'category_ids',      //column name
        'text'  //datatype definition
        );
  
$installer->endSetup();