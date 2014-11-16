<?php

$installer = $this;
$installer->startSetup();
$installer->run("
  CREATE TABLE `{$installer->getTable('metadata/metadata')}` (
     `metadata_id` int(11) NOT NULL auto_increment,
     `category_id` int,
     `title` text,
     `description` text,
     `keywords` text,
     PRIMARY KEY  (`metadata_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;      
");
$installer->endSetup();