
#
# Table structure for table 'tx_savlibraryexample1_members'
#
CREATE TABLE tx_savlibraryexample1_members (
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) unsigned DEFAULT '0' NOT NULL,
    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
    firstname tinytext,
    lastname tinytext,
    street text,
    zipcode tinytext,
    city tinytext,
    image int(11) unsigned DEFAULT '0',

    PRIMARY KEY (uid),
    KEY parent (pid)
);


