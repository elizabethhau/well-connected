-- DDL for table creation

use hhau_db;

drop table if exists employment;
drop table if exists education;
drop table if exists user;
drop table if exists gradschool;
drop table if exists company;
drop table if exists job;

-- creating the tables
create table user(
       uid integer auto_increment primary key,
       email varchar(100) default null,
       password varchar(100)not null,
       name varchar(50) not null,
       classyear char(4) default null,
       major varchar(50) default null,
       minor varchar(50) default null
       )
       ENGINE = InnoDB;

create table gradschool(
       gid int auto_increment primary key,
       name varchar(50) not null,
       location varchar(20) default null
       )
        ENGINE = InnoDB;

create table company (
       cid int auto_increment primary key,
       name varchar(50) not null
       )
        ENGINE = InnoDB;

create table job (
       jid int auto_increment primary key,
       title varchar(100) not null,
       field varchar(100)default null,
       location varchar (50) default null
       )
        ENGINE = InnoDB;

-- The employment table links the user with the company and/ or the job
create table employment (
       uid int not null,
       cid int,
       jid int,
       startDate char(7), -- in the format of mm/yyyy
       endDate char(7), -- in the format of mm/yyyy or 'present'
       INDEX(uid), 
       INDEX(cid),
       INDEX(jid),
       foreign key (uid) references user(uid) on delete cascade,
       foreign key (cid) references company(cid) on delete cascade,
       foreign key (jid) references job(jid) on delete cascade
       )
       ENGINE = InnoDB;

-- The education table links the user with the graudate school(s)
create table education (
       uid int not null,
       gid int not null,
       gradYear char (4),
       degree varchar (50),
       program varchar(50), -- could change this to enum too
       INDEX (uid),
       INDEX (gid),
       foreign key (uid) references user(uid) on delete cascade,
       foreign key (gid) references gradschool(gid) on delete cascade
       )
       ENGINE = InnoDB;

-- sample data

insert into user (password,name, email, classyear, major, minor) values
        ('asldkfjs','Elizabeth Hau', 'hhau@wellesley.edu','2016','Computer Science','Mathematics');

insert into user (password,name,email, classyear, major,minor) values
       ('asl;dkjasldkfj','Emily Cetlin','ecetlin@wellesley.edu','2015','Mathematics','Computer Science');

insert into user (password, name, email, classyear, major) values
       ('asldkfa;','Emma Kaufman','ekaufma2@wellesley.edu','2015','Political Science');

insert into user (password, name, email, classyear, major,minor) values
       ('oranges!','Emily Ahn','eahn@wellesley.edu','2016','Cognitive and Linguistics Science','Computer Science');

insert into user (password, name, email, classyear, major) values
       ('halloha','Amanda Hui', 'ahui@wellesley.edu','2015','Economics');

insert into user (password, name, email, classyear, major, minor) values
       ('infinity','Alexi Block Gorman','ablockgo@wellesley.edu','2016','Mathematics','Computer Science');

insert into user (password, name, email, classyear, major, minor) values
       ('thebest','Mashiwat Mahbub','smahbub@wellesley.edu','2015','Mathematics','Psychology');

insert into user (password, name, email, classyear, major, minor) values
       ('talimarcus','Tali Marcus','tmarcus@wellesley.edu','2015','Psychology','Computer Science');

insert into user (password, name, email, classyear, major, minor) values
       ('password','Celia Honigberg','chonigbe@wellesley.edu','2015','Computer Science','Geoscience');

insert into user (password, name, email, classyear, major, minor) values
      ('1234', 'Sample User', 'username@wellesley.edu', '2016', 'Computer Science', 'Mathematics');

insert into gradschool(name, location) values ('Harvard University','Boston,MA');
insert into gradschool(name, location) values ('Columbia University','New York,NY');
insert into gradschool(name, location) values ('Massachusetts Institute of Technology','Cambridge,MA');
insert into gradschool(name, location) values ('University of California, Berkeley','Berkeley,CA');
insert into gradschool(name, location) values ('University of Washington','Seattle,WA');
insert into gradschool(name, location) values ('University of Southern California','Los Angeles,CA');
insert into gradschool(name, location) values ('University of Pennsylvania','Philadelphia,PA');


insert into company (name) values ('Eaton Vance');
insert into company (name) values ('Google');
insert into company (name) values ('Holland & Knight');
insert into company (name) values ('Amazon');
insert into company (name) values ('JP Morgan');
insert into company (name) values ('General Electric (GE)');
insert into company (name) values ('Fidelity');

insert into job (title,field,location) values ('Research Analyst','Investment Management','Boston,MA');
insert into job (title, field, location) values ('Software Engineer','Technology','Mountain View, CA');
insert into job (title,field,location) values ('Consultant','Investment Banking','New York,NY');
insert into job (title,field,location) values ('Software Development Leadership Program (SDLP)','Software','San Ramon,CA');
insert into job (title, field,location) values ('Data Engineer','Tech','North Caroline');
insert into job (title, field, location) values ('Software Engineer', 'Technology', 'Seattle, WA');

insert into employment (uid,cid, jid) values ('2','1','1'); -- specified both cid and jid
insert into employment (uid,cid) values ('3','3'); -- only specifies cid
insert into employment (uid,jid) values ('1','2'); -- only jid is specified
insert into employment (uid, cid, jid) values ('5','5','3');
insert into employment (uid, cid, jid) values ('4','2','2');
insert into employment (uid, cid, jid, startDate) values ('8','6','4','08/2015');
insert into employment (uid, cid, jid, startDate) values ('9','7','5','08/2015');
insert into employment (uid, cid, jid, startDate) values ('10', '4', '6', '08/2015');

insert into education (uid, gid, gradyear, degree, program) values ('1','2','2019','Masters','Computer Science');
insert into education (uid, gid, degree, program) values ('2', '3','PhD','Law');
insert into education (uid, gid) values ('3', '1');
insert into education (uid, gid, program) values ('4','6', 'Linguistics');
insert into education (uid, gid, gradyear, degree, program) values ('6','4','2020','PhD','Mathematics');