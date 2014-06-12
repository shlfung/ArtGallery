drop table clients;
drop table issue_transaction;
drop table receives_commission;
drop table purchase;
drop table return;
drop table artists;
drop table supplies;
drop table sculpture;
drop table painting;
drop table art;

create table clients
  (name varchar(30) not null,
    address varchar(30),
    email varchar(30),
    phone number(38) not null,
    PRIMARY KEY (name, phone));

create table issue_transaction
  (transaction_id number(38) not null,
    name varchar(30) not null,
    phone number(38) not null);

create table art
  (serial_number number(38) not null PRIMARY KEY,
    title varchar(38),
    price number(38));

create table sculpture
  (serial_number number(38) not null PRIMARY KEY,
    material varchar(30),
    sculpture_style varchar(30),
    foreign key (serial_number) references art);

create table painting
  (serial_number number(38) not null PRIMARY KEY,
    medium varchar(30),
    painting_style varchar(30),
    foreign key (serial_number) references art);

create table purchase
  (transaction_id number(38) not null,
    purchase_date date,
    receipt_id number(38),
    pur_type varchar(30),
    id number(38),
    amount number(38),
    serial_number number(38) not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key (transaction_id) references issue_transaction,
    foreign key (serial_number) references art);

create table return
  (transaction_id number(38) not null,
    return_date date,
    pur_type varchar(30),
    id number(38),
    amount number(38),
    serial_number number(38) not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key (transaction_id) references issue_transaction,
    foreign key (serial_number) references art);

create table artists
  (name varchar(30) not null,
    studio_address varchar(30),
    email varchar(30),
    phone number(38) not null,
    status varchar(30),
    PRIMARY KEY (name, phone));

create table supplies
  (name varchar(30) not null,
    phone number(38) not null,
    commission_rate number(38),
    serial_number number(38) not null,
    PRIMARY KEY (name, phone, serial_number),
    foreign key (name, phone) references artists,
    foreign key (serial_number) references art);

create table receives_commission
  (transaction_id number(38) not null,
    name varchar(30) not null,
    phone number(38) not null,
    amount number(38),
    PRIMARY KEY (transaction_id, name, phone),
    foreign key (transaction_id) references transaction,
    foreign key (name, phone) references artists);

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
insert into art values
    (12346, 'For The Love of God', 58000);
insert into art values
    (12347, 'Han Dynasty Vase', 45000);
insert into art values
    (12348, 'Water Lilies', 92000);
insert into art values
    (12349, 'The Dance', 22000);

insert into sculpture values
    (12346, 'Platinum and Diamonds', 'Modern');
insert into sculpture values
    (12347, 'Han Dynasty Vase, paint', 'Asian');
insert into painting values
    (12345, 'Oil on Canvas', 'Western');
insert into painting values
    (12348, 'Oil on Canvas', 'Western');
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
    

    




