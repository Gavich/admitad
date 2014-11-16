<?php

$installer = $this;
$installer->startSetup();

  $installer->getConnection()->addColumn(
        $this->getTable('metadata/metadata'), //table name
        'products',      //column name
        'text'  //datatype definition
        );
  
$installer->endSetup();