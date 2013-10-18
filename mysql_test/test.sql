delimiter @
create procedure insert_innodb(in item integer)
BEGIN
declare counter int;
set counter = item;
while counter >=1 do 
insert into innodb1 values(counter,concat ('mysqlsystems.com',counter),repeat('bla',10));
insert into innodb2 values(counter,concat ('mysqlsystems.com',counter),repeat('bla',10));
insert into innodb3 values(counter,concat ('mysqlsystems.com',counter),repeat('bla',10));
insert into innodb4 values(counter,concat ('mysqlsystems.com',counter),repeat('bla',10));
set counter = counter - 1;
end while;
end
@

create table innodb1(
id int(11) not null auto_increment,
name varchar(50) default null,
post text,
primary key(id)
) engine=innodb default charset=utf8;

create table innodb2(
id int(11) not null auto_increment,
name varchar(50) default null,
post text,
primary key(id)
) engine=innodb default charset=utf8;

create table innodb3(
id int(11) not null auto_increment,
name varchar(50) default null,
post text,
primary key(id)
) engine=innodb default charset=utf8;

create table innodb4(
id int(11) not null auto_increment,
name varchar(50) default null,
post text,
primary key(id)
) engine=innodb default charset=utf8;