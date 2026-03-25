CREATE TABLE tx_aisteablog_domain_model_post (
    title         varchar(255)  NOT NULL DEFAULT '',
    slug          varchar(2048) NOT NULL DEFAULT '',
    teaser        text,
    bodytext      mediumtext,
    cover_image   int(11) UNSIGNED NOT NULL DEFAULT '0',
    author        varchar(255)  NOT NULL DEFAULT '',
    publish_date  int(11)       NOT NULL DEFAULT '0',
    categories    int(11) UNSIGNED NOT NULL DEFAULT '0',
    tags          int(11) UNSIGNED NOT NULL DEFAULT '0',
    view_count    int(11) UNSIGNED NOT NULL DEFAULT '0'
);

CREATE TABLE tx_aisteablog_domain_model_category (
    title       varchar(255)  NOT NULL DEFAULT '',
    slug        varchar(2048) NOT NULL DEFAULT '',
    description text
);

CREATE TABLE tx_aisteablog_domain_model_tag (
    title varchar(255) NOT NULL DEFAULT ''
);

CREATE TABLE tx_aisteablog_post_category_mm (
    uid_local       int(11) UNSIGNED DEFAULT '0' NOT NULL,
    uid_foreign     int(11) UNSIGNED DEFAULT '0' NOT NULL,
    sorting         int(11) UNSIGNED DEFAULT '0' NOT NULL,
    sorting_foreign int(11) UNSIGNED DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_aisteablog_domain_model_comment (
    uid           int(11) NOT NULL auto_increment,
    pid           int(11) NOT NULL DEFAULT '0',
    tstamp        int(11) unsigned NOT NULL DEFAULT '0',
    crdate        int(11) unsigned NOT NULL DEFAULT '0',
    deleted       tinyint(4) unsigned NOT NULL DEFAULT '0',
    hidden        tinyint(4) unsigned NOT NULL DEFAULT '0',
    post          int(11) unsigned NOT NULL DEFAULT '0',
    author_name   varchar(255) NOT NULL DEFAULT '',
    author_email  varchar(255) NOT NULL DEFAULT '',
    content       text,
    approved      tinyint(1) unsigned NOT NULL DEFAULT '0',

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_aisteablog_post_tag_mm (
    uid_local       int(11) UNSIGNED DEFAULT '0' NOT NULL,
    uid_foreign     int(11) UNSIGNED DEFAULT '0' NOT NULL,
    sorting         int(11) UNSIGNED DEFAULT '0' NOT NULL,
    sorting_foreign int(11) UNSIGNED DEFAULT '0' NOT NULL,
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
