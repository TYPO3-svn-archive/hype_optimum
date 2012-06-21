#
# Table structure for table 'tx_hypeoptimum_cache'
#
CREATE TABLE tx_hypeoptimum_cache (
	id int(11) NOT NULL auto_increment,
	identifier varchar(255) DEFAULT '' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	content mediumblob,
	lifetime int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier)
) ENGINE=InnoDB;

#
# Table structure for table 'tx_hypeoptimum_cache_tag'
#
CREATE TABLE tx_hypeoptimum_cache_tag (
	id int(11) NOT NULL auto_increment,
	identifier varchar(255) DEFAULT '' NOT NULL,
	tag varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	KEY cache_id (identifier),
	KEY cache_tag (tag)
) ENGINE=InnoDB;