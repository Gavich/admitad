<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('categoryreview')};
CREATE TABLE {$this->getTable('categoryreview')} (
  `categoryreview_id` bigint(20) unsigned NOT NULL auto_increment,
  `catid` int(15) NOT NULL,
  `custid` varchar(255) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `status` tinyint(3) unsigned NOT NULL default '1',
  `nickname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `rating_value` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`categoryreview_id`),
  KEY `FK_REVIEW_STATUS` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 