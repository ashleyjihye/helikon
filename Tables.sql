-- Ashley Thomas and Sasha Levy
-- Tables.sql
-- Helikon

use athomas2_db;
-- first, drop any existing tables

drop table if exists reviews;
drop table if exists contribution;
drop table if exists tvlikes;
drop table if exists movielikes;
drop table if exists musiclikes;
drop table if exists `user`;
drop table if exists person;
drop table if exists movie;
drop table if exists tv;
drop table if exists song;
drop table if exists album;
drop table if exists music;
drop table if exists media;

create table user (
       uid int not null primary key auto_increment,
       name varchar(50),
       username varchar(50),
       password varchar(50),
       friends varchar(10000)
)
	ENGINE = InnoDB;

create table person (
       pid int not null primary key auto_increment,
       name varchar(50),
       picture varchar(50) -- picture will be a string that is a link to this person's picture
)
	ENGINE = InnoDB;

create table media (
       mid int not null primary key auto_increment,
       rating tinyint(1),
       title varchar(50),
       dateadded datetime,
       characteristics varchar(200),
       preview varchar(200), -- will have a link to a video or stream of the media
       `type` enum('tv','movie','song','album'),
       picture varchar(200) -- will have link to picture
)
	ENGINE = InnoDB;

create table contribution (
       pid int not null,
       mid int not null,
       primary key (pid,mid),
       INDEX (mid),
       INDEX (pid),
       foreign key (mid) references media(mid) on delete restrict,
       foreign key (pid) references person(pid) on delete restrict
)
	ENGINE = InnoDB;

create table tv (
       mid int not null primary key,
       genre enum('action','comedy','adventure','documentary','drama','mystery','reality','sitcom','anime','children','classic','faith','foreign','horror','independent','musical','romance','scifi','fantasy','romance','thriller'),
       length tinyint(1), -- number of seasons or episodes
       INDEX (mid),
       foreign key (mid) references media(mid) on delete restrict
)
	ENGINE = InnoDB;

create table movie (
       mid int not null primary key,
       genre enum('action','comedy','adventure','documentary','drama','mystery','reality','sitcom','anime','children','classic','faith','foreign','horror','independent','musical','romance','scifi','fantasy','romance','thriller'),
       INDEX (mid),
       foreign key (mid) references media(mid) on delete restrict
)
	ENGINE = InnoDB;

create table music (
       mid int not null primary key,
       genre enum('hiphop','pop','classical','jazz','rap','country','alternative','faith','rock','blues','children','dance','electronic','easy listening','r&b','reggae','metal','soundtrack','foreign','indie','kpop','dubstep'),
       INDEX (mid),
       foreign key (mid) references media(mid) on delete restrict
)
	ENGINE = InnoDB;

create table album (
       mid int not null primary key,
       INDEX (mid),
       foreign key (mid) references music(mid) on delete restrict
)
	ENGINE = InnoDB;

create table song (
       mid int not null primary key,
       albumid int,
       INDEX (mid),
       INDEX (albumid),
       foreign key (albumid) references album(mid) on delete restrict,
       foreign key (mid) references music(mid) on delete restrict
 
)
	ENGINE = InnoDB;

create table tvlikes (
       uid int not null,
       mid int not null,
       primary key (uid,mid),
       INDEX (uid),
       INDEX (mid),
       foreign key (mid) references tv(mid) on delete restrict,
       foreign key (uid) references user(uid) on delete restrict
)
	ENGINE = InnoDB;

create table movielikes (
       uid int not null,
       mid int not null,
       primary key (uid,mid),
       INDEX (uid),
       INDEX (mid),
       foreign key (mid) references movie(mid) on delete restrict,
       foreign key (uid) references user(uid) on delete restrict
)
	ENGINE = InnoDB;

create table musiclikes (
       uid int not null,
       mid int not null,
       primary key (uid,mid),
       INDEX (uid),
       INDEX (mid),
       foreign key (mid) references music(mid) on delete restrict,
       foreign key (uid) references user(uid) on delete restrict
)
	ENGINE = InnoDB;

create table reviews (
       rid int not null primary key,
       uid int not null, -- user that wrote it
       mid int not null, -- what the user is commenting on
       dateadded datetime, -- when the user wrote it
       initial int, -- what the original review the user is commenting on is (null if it is the original)
       comment varchar(1000), -- the actual review or comment
       INDEX (uid),
       INDEX (mid),
       INDEX (initial),
       foreign key (initial) references reviews(rid) on delete restrict,
       foreign key (uid) references user(uid) on delete restrict,
       foreign key (mid) references media(mid) on delete restrict

)
	ENGINE = InnoDB;
       
insert into user values (1,'Ashley','athomas2','woohoo','2,3,4,5');
insert into person values (1,'Hugh Laurie','link');
insert into media values (1,5,'Tomorrowland','1999-09-09 04:04:12','it is great','video','movie','link');
insert into movie values (1,'action');
insert into movielikes values (1,1);
insert into contribution values (1,1);
insert into media values (2,5,'Someone Like You','12','adele!','clip','song','link');
insert into music values (2,'alternative');
insert into media values (3,5,'21','12','adele!','clip','album','link');
insert into music values (3, 'alternative');
insert into album values (3);
insert into song values (2,NULL);
update song set albumid = 3 where mid = 2;
insert into reviews values (1,1,1,'123',NULL,'hello');
insert into reviews values (2,1,1,'123',1,'hello yourself!');