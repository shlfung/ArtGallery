DROP DATABASE ARTGALLERYDB;
CREATE DATABASE ARTGALLERYDB;
USE ARTGALLERYDB;


create table clients
  (fname varchar(30),
    lname varchar(30) not null,
    street varchar(30),
    city varchar(30),
    prov varchar(30),
    pcode varchar(30),
    email varchar(30),
    phone int not null,
    PRIMARY KEY (lname, phone));

create table issue_transaction
  (transaction_id int not null PRIMARY KEY,
    lname varchar(30) not null,
    phone int not null);

create table art
  (serial_number int not null PRIMARY KEY,
    title varchar(38),
    price decimal(65,2) unsigned not null);

create table sculpture
  (serial_number int not null PRIMARY KEY,
    material varchar(30),
    sculpture_style varchar(30),
    foreign key(serial_number) references art (serial_number) on delete cascade on update cascade);

create table painting
  (serial_number int not null PRIMARY KEY,
    medium varchar(30),
    painting_style varchar(30),
    foreign key(serial_number) references art (serial_number) on delete cascade on update cascade);

create table purchase
  (transaction_id int not null,
    purchase_date date,
    receipt_id int,
    pur_type varchar(30),
    id int,
    amount int,
    serial_number int not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key(transaction_id) references issue_transaction (transaction_id) on delete cascade on update cascade,
    foreign key(serial_number) references art (serial_number) on delete cascade on update cascade);

create table purchase_return
  (transaction_id int not null,
    ret_date date,
    pur_type varchar(30),
    id int,
    amount int,
    serial_number int not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key (transaction_id) references issue_transaction (transaction_id) on delete cascade on update cascade,
    foreign key (serial_number) references art (serial_number) on delete cascade on update cascade);

create table artists
  (fname varchar(30),
    lname varchar(30) not null,
    street varchar(30),
    city varchar(30),
    prov varchar(30),
    pcode varchar(30),
    email varchar(30),
    phone int not null,
    status varchar(30),
    PRIMARY KEY (lname, phone));

create table supplies
  (lname varchar(30) not null,
    phone int not null,
    commission_rate decimal(65,2) unsigned,
    serial_number int not null,
    PRIMARY KEY (lname, phone, serial_number),
    foreign key (lname, phone) references artists (lname, phone) on delete cascade on update cascade,
    foreign key (serial_number) references art (serial_number) on delete cascade on update cascade);

create table receives_commission
  (transaction_id int not null,
    lname varchar(30) not null,
    phone int not null,
    amount decimal(65,2) unsigned,
    PRIMARY KEY (transaction_id, lname, phone),
    foreign key (transaction_id) references issue_transaction (transaction_id) on delete cascade on update cascade,
    foreign key (lname, phone) references artists (lname, phone) on delete cascade on update cascade);

insert into artists values
  ('Pablo Picasso', 'Paris, France', 'guernica37@spain.com', 5556925253, 'inactive');
insert into artists values
  ('Henri Mattise', 'Paris, France', 'riteofspring@nice.com', 5552565223, 'inactive');
insert into artists values
  ('Claude Monet', 'Paris, France', 'waterlily@pondlife.com', 5556124553, 'inactive');
insert into artists values
  ('Ai Weiwei', 'Beijing, China', 'notsorry@caochagdi.com', 5554656253, 'active');
insert into artists values
  ('Damien Hirst', 'London, England', 'sharklover@yba.com', 5556489895, 'active');

insert into art values
    (12345, 'Dora Mar', 32000);
insert into painting values
    (12345, 'Oil on Canvas', 'Western');
insert into art values
    (12346, 'For The Love of God', 58000);
insert into sculpture values
    (12346, 'Platinum and Diamonds', 'Modern');
insert into art values
    (12347, 'Han Dynasty Vase', 45000);
insert into sculpture values
    (12347, 'Han Dynasty Vase, paint', 'Asian');
insert into art values
    (12348, 'Water Lilies', 92000);
insert into painting values
    (12348, 'Oil on Canvas', 'Western');
insert into art values
    (12349, 'The Dance', 22000);
insert into painting values
    (12349, 'Oil on Canvas', 'Western');

insert into clients values
    ('John Doe', '123 Main St.', 'johndoe@mail.com', 5554356364);
insert into clients values
    ('Jane Doe', '123 Main St.', 'janedoe@mail.com', 5554357646);
insert into clients values
    ('Fred Smith', '125 Main St.', 'fredsmith@mail.com', 5555556364);
insert into clients values
    ('Jim Hughes', '127 Main St.', 'jimhughes@mail.com', 5556666666);
insert into clients values
    ('Donovan St-Vincent', '130 Main St.', 'donovansv@mail.com', 5554354666);

insert into supplies values
    ('Pablo Picasso', 5556925253, 50, 12345);
insert into supplies values
    ('Henri Matisse', 5552565223, 55, 12349);
insert into supplies values
    ('Damien Hirst', 5556489895, 60, 12346);
insert into supplies values
    ('Claude Monet', 5556124553, 50, 12348);
insert into supplies values
    ('Ai Weiwei', 5554656253, 50, 12347);

insert into issue_transaction values
    (54321, 'John Doe', 5554356364);
insert into issue_transaction values
    (54322, 'John Doe', 5554356364);
insert into issue_transaction values
    (54323, 'Fred Smith', 5555556364);
insert into issue_transaction values
    (54324, 'Donovan St-Vincent', 5554354666);
insert into issue_transaction values
    (54325, 'Fred Smith', 5555556364);

insert into purchase values
    (54321, 20140531, 123, 'cash', null, 21000, 12349);
insert into purchase values
    (54323, 20140530, 124, 'mc', 3335 2324 1555 4555, 32000, 12345);
insert into purchase values
    (54324, 20140531, 125, 'visa', 5665 5468 5648 6548, 80000, 12346);
insert into return values
    (54322, 20140602, 126, 'cash', null, 21000, 12349);
insert into return values
    (54325, 20140602, 127, 'mc', 3335 2324 1555 4555, 32000, 123445);

insert into receives_commission values
    (54324, 'Damien Hirst', 5556489895, 29000);
