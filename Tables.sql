-- Ashley Thomas and Sasha Levy
-- Tables.sql
-- Helikon

use athomas2_db;
-- first, drop any existing tables

drop table if exists ratings;
drop table if exists reviews;
drop table if exists contribution;
drop table if exists likes;
drop table if exists friends;
drop table if exists `user`;
drop table if exists person;
drop table if exists media;

create table user (
       uid int not null primary key auto_increment,
       name varchar(50),
       username varchar(50),
       password varchar(50),
       email varchar(50),
       picture enum('y','n')
)
	ENGINE = InnoDB;

create table friends (
       uid int not null,
       friendid int not null,
       state enum('0','1'),
       primary key (uid,friendid),
       INDEX (uid),
       INDEX (friendid),
       foreign key (uid) references user(uid) on delete restrict,
       foreign key (friendid) references user(uid) on delete restrict
)
	ENGINE = InnoDB;

create table person (
       pid int not null primary key auto_increment,
       name varchar(50),
       description varchar(400),
       picture enum('y','n') -- picture will be a string that is a link to this person's picture
)
	ENGINE = InnoDB;

create table media (
       mid int not null primary key auto_increment,
       rating tinyint(1),
       title varchar(50),
       dateadded datetime,
       description varchar(400),
       genre enum('action','comedy','adventure','documentary','drama','mystery','reality','sitcom','anime','children','classic','faith','foreign','horror','independent','musical','romance','scifi','fantasy','romance','thriller','medical','procedural','hiphop','pop','classical','jazz','rap','country','alternative','faith','rock','blues','children','dance','electronic','easy listening','r&b','soul','reggae','metal','soundtrack','foreign','indie','kpop','dubstep'),
       length varchar(20),
       preview varchar(200), -- will have a link to a video or stream of the media
       `type` enum('tv','movie','song','album'),
       picture enum('y','n'), -- will have link to picture
       albumid int,
       INDEX (albumid),
       foreign key (albumid) references media(mid) on delete restrict
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

create table likes (
       uid int not null,
       mid int not null,
       primary key (uid,mid),
       INDEX (uid),
       INDEX (mid),
       dateadded datetime, -- when user added this to their liked table 
       foreign key (mid) references media(mid) on delete restrict,
       foreign key (uid) references user(uid) on delete restrict
)
	ENGINE = InnoDB;

create table reviews (
       rid int not null primary key auto_increment,
       uid int not null,
       mid int not null,
       dateadded datetime,
       initial int,
       comment varchar(1000),
       INDEX (uid),
       INDEX (mid),
       INDEX (initial),
       foreign key (initial) references reviews(rid) on delete set null,
       foreign key (uid) references user(uid) on delete restrict,
       foreign key (mid) references media(mid) on delete restrict
)
	ENGINE = InnoDB;

create table ratings (
       uid int not null,
       mid int not null,
       rating int,
       primary key (uid,mid),
       INDEX (uid),
       INDEX (mid),
       foreign key (uid) references user(uid) on delete restrict,
       foreign key (mid) references media(mid) on delete restrict
)
       ENGINE = InnoDB;

       
insert into user values (1,'Ashley','athomas2','woohoo','athomas2@wellesley.edu',NULL);
insert into user values (2,'Sasha','alevy2','green','alevy2@wellesley.edu',NULL);
insert into person values (1,'Hugh Laurie','link');
insert into media values (1,5,'Tomorrowland','1999-09-09 04:04:12','it is great','adventure',2,'video','movie','link',NULL);
insert into likes values (1,1,'0000-00-00');
insert into contribution values (1,1);
insert into media values (2,5,'Someone Like You','1999-09-09 04:04:12','adele!','alternative',3,'clip','song','link',NULL);
insert into media values (3,5,'21','1999-09-09 04:04:12','adele!','alternative',5,'clip','album','link',NULL);
update media set albumid = 3 where mid = 2;
insert into reviews (uid,mid,dateadded,initial,comment) values (1,1,'2014-04-14',1,'hello');
insert into reviews (uid,mid,dateadded,initial,comment) values (1,1,'2014-04-15',1,'hello yourself!');
insert into reviews (uid,mid,dateadded,initial,comment) values (2,1,'2014-04-16',1,'hello yourself!');
insert into reviews (uid,mid,dateadded,initial,comment) values (1,1,'2014-04-17',4,'hello yourself!');
insert into reviews (uid,mid,dateadded,initial,comment) values (1,1,'2014-04-18',1,'hello yourself!');
insert into reviews (uid,mid,dateadded,initial,comment) values (1,1,'2014-04-19',4,'hello yourself!');