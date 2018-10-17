create table users
(
	id int unsigned auto_increment
		primary key,
	username varchar(64) not null,
	password varchar(128) not null,
	`_token` varchar(128) null,
	photo varchar(255) default 'default.jpg' null,
	blocked tinyint(1) unsigned zerofill default '0' null,
	attempts tinyint(1) unsigned zerofill default '0' null,
	birthday date not null,
	sex char not null,
	constraint users__token_uindex
	unique (`_token`)
);