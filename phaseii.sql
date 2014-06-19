
DROP DATABASE IF EXISTS GALLERYDB;
CREATE DATABASE GALLERYDB;
USE GALLERYDB;

drop table if exists clients;
create table clients
  (fname varchar(30) not null,
    lname varchar(30) not null,
    street varchar(30),
    city varchar(30),
    prov varchar(30),
    country varchar(30),
    pcode varchar(30),
    email varchar(30),
    phone varchar(30)not null,
    PRIMARY KEY (fname, lname, phone))ENGINE=MyISAM;

create table issue_transaction
  (transaction_id int not null PRIMARY KEY,
    fname varchar(30) not null,
    lname varchar(30) not null,
    phone int not null);

create table art
  (serial_number int not null PRIMARY KEY,
    title varchar(38),
    price decimal(65,2) unsigned not null,
    pic_url text,
	sold boolean default 0);

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
    pur_type varchar(30),
    amount int,
    serial_number int not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key(transaction_id) references issue_transaction (transaction_id),
    foreign key(serial_number) references art (serial_number) );

create table purchase_return
  (transaction_id int not null,
    ret_date date,
    pur_type varchar(30),
    id varchar(19),
    amount int,
    serial_number int not null,
    PRIMARY KEY (transaction_id, serial_number),
    foreign key (transaction_id) references issue_transaction (transaction_id) on delete cascade on update cascade,
    foreign key (serial_number) references art (serial_number) on delete cascade on update cascade);

create table artists
  (fname varchar(30) not null,
    lname varchar(30) not null,
    street varchar(30),
    city varchar(30),
    prov varchar(30),
    pcode varchar(30),
    country varchar(30),
    email varchar(30),
    phone varchar(30)not null,
    status varchar(30),
    PRIMARY KEY (fname, lname, phone));

create table supplies
  (fname varchar(30) not null,
   lname varchar(30) not null,
    phone varchar(30) not null,
    commission_rate decimal(65,2) unsigned,
    serial_number int not null,
    PRIMARY KEY (fname, lname, phone, serial_number),
    foreign key (fname, lname, phone) references artists (fname, lname, phone) on delete cascade on update cascade,
    foreign key (serial_number) references art (serial_number) on delete cascade on update cascade);

create table receives_commission
  (transaction_id int not null,
    fname varchar(30) not null,
    lname varchar(30) not null,
    phone varchar(30) not null,
    amount decimal(65,2) unsigned,
    PRIMARY KEY (transaction_id, fname, lname, phone),
    foreign key (transaction_id) references issue_transaction (transaction_id) on delete cascade on update cascade,
    foreign key (fname, lname, phone) references artists (fname, lname, phone) on delete cascade on update cascade);

CREATE TRIGGER insUsr BEFORE INSERT ON clients
    FOR EACH ROW
    INSERT INTO mysql.user (host, user, password)
    VALUES('localhost',lower(concat(New.fname, New.lname)),PASSWORD(new.phone));

CREATE TRIGGER deletePurchase before insert on purchase_return
    FOR EACH ROW
    DELETE FROM purchase WHERE transaction_id = New.transaction_id;

insert into artists values
  ('Pablo','Picasso', '5 Rue De Thorigny', 'Paris', 'Ile-de-France', 'France', '75003', 'guernica37@spain.com', 5556925253, 'inactive');
insert into artists values
  ('Henri', 'Mattisse', 'Place Henri Mattise', 'Paris', 'Ile-de-France', 'France', '75020', 'riteofspring@nice.com', 5552565223, 'inactive');
insert into artists values
  ('Claude', 'Monet', 'Lycee General Claude Monet', 'Paris', 'Ile-de-France', 'France', '75013', 'waterlily@pondlife.com', 5556124553, 'inactive');
insert into artists values
  ('Weiwei', 'Ai', 'Caochangdi', 'Beijing', 'Beijing', '75432', 'China', 'notsorry@caochagdi.com', 5554656253, 'active');
insert into artists values
  ('Damien', 'Hirst', '42 New Compton St', 'London', 'London', 'United Kingdom', 'WC2H 8DA', 'sharklover@yba.com', 5556489895, 'active');
insert into artists values
  ('Leo', 'Davinc', '12 Unkown St.', 'Somewhere', 'Southern', 'France', '94352', 'niceface@yba.com', 5666489895, 'active');
insert into artists values
  ('Dude', 'McGee', '92 New Compton St', 'London', 'London', 'United Kingdom', 'SW1 H3A', 'iloveart@yba.com', 6656489895, 'active');
insert into artists values
  ('Mel', 'Kay', '13 Goodenough St.', 'London', 'London', 'United Kingdom', 'WC2H 8DA', 'ynot@yba.com', 5556489895, 'inactive');
insert into artists values
  ('Donatello', 'Bardi', '1 Florence St', 'Florence', 'Florence', 'Italy', '20091', 'bmoc@yba.com', 9988888889, 'active');
insert into artists values
  ('Sandro', 'Botti', '2 Florence St.', 'Florence', 'Florence', 'Italy', '20090', 'yoyo@italy.com', 1234567800, 'inactive');
insert into artists values
  ('Daily', 'Dally', '888 Nowhere St', 'Oxford', 'Oxfordshire', 'United Kingdom', 'SW8 8DA', 'onthedaily@gmail.com', 5556489896, 'inactive');
insert into artists values
  ('Karl', 'Abt', '1234 Berlin Ave', 'Berlin', 'Berlin', 'Germany', '83723', 'iamgerman@germany.com', 5556489897, 'inactive');
insert into artists values
  ('Shalah', 'Aghapour', '34 Chicken St', 'Istanbul', 'Istanbul', 'Turkey', '33430', 'noidea@gmail.com', 5556489898, 'active');
insert into artists values
  ('Lidia', 'Abdul', '542 Iran Ave', 'Tehran', 'Tehran', 'Iran', '888888', 'labdul@iran.org', 7777777777, 'active');
insert into artists values
  ('Casade', 'Fresh', '4323 West 1003', 'New York', 'New York', 'U.S.A', '58823', 'iloveny@nyny.com', 5556489890, 'active');
insert into artists values
  ('Sigmar', 'Polke', '1232-123 East 42nd St', 'New York', 'New York', 'U.S.A', '33242', 'hokeypokey@gmail.com', 5666489895, 'active');

insert into art values
    (12345, 'Dora Mar', 32000, 'http://upload.wikimedia.org/wikipedia/en/c/c3/Dora_Maar_Au_Chat.jpg', 0);
insert into painting values
    (12345, 'Oil on Canvas', 'Western');
insert into art values
    (12346, 'For The Love of God', 58000, 'http://upload.wikimedia.org/wikipedia/en/6/6d/Hirst-Love-Of-God.jpg', 12346);
insert into sculpture values
    (12346, 'Platinum and Diamonds', 'Modern');
insert into art values
    (12347, 'Han Dynasty Vase', 45000, 'http://www.phaidon.com/resource/ins-absent-2.jpg', 0);
insert into sculpture values
    (12347, 'Han Dynasty Vase, paint', 'Asian');
insert into art values
    (12348, 'Water Lilies', 92000, 'http://upload.wikimedia.org/wikipedia/commons/2/2a/Claude_Monet_-_The_Water_Lilies_-_The_Clouds_-_Google_Art_Project.jpg', 0);
insert into painting values
    (12348, 'Oil on Canvas', 'Western');
insert into art values
    (12349, 'The Dance', 22000, 'http://upload.wikimedia.org/wikipedia/en/2/2e/La_danse_%28I%29_by_Matisse.jpg', 0);
insert into painting values
    (12349, 'Oil on Canvas', 'Western');
insert into art values
    (12350, 'The Eye', 222000, 'http://www.sai.msu.su/wm/paint/auth/klee/klee.1914.jpg', 0);
insert into painting values
    (12350, 'Oil on Canvas', 'Western');
insert into art values
    (12351, 'Never Forget', 2000, 'http://www.dkimages.com/discover/Projects/JH806/previews/30000360.JPG', 0);
insert into painting values
    (12351, 'Pastel', 'Asian');
insert into art values
    (12352, 'Chek Eet', 83900, 'http://britneysbanter.files.wordpress.com/2009/12/african-art-11.jpg', 0);
insert into painting values
    (12352, 'Watercolour', 'East Asian');
insert into art values
    (12353, 'Life', 43200, 'http://img.xcitefun.net/users/2009/09/114728,xcitefun-african-art-mix-6.jpg', 0);
insert into painting values
    (12353, 'Oil on Canvas', 'African');
insert into art values
    (12354, 'Untitled', 3200, 'http://3.bp.blogspot.com/_j0hc4aNzY1M/TNnO5vlFJJI/AAAAAAAAA4A/i4M7uZZ9wC8/s1600/landscape.jpg', 0);
insert into painting values
    (12354, 'Oil on Canvas', 'African');

insert into art values
    (12366, 'Angles', 58000, 'http://4.bp.blogspot.com/_DW4clpN2Zeg/S-EGcBZoN1I/AAAAAAAABXE/VJT-VWYZL7U/s1600/cup.jpg', 0);
insert into sculpture values
    (12366, 'Stone', 'Renaissance');
insert into art values
    (12376, 'God', 3200, 'http://www4.pictures.zimbio.com/gi/Hand+God+Lorenzo+Quinn+installed+Park+Lane+nRqrFgckp5Ml.jpg', 0);
insert into sculpture values
    (12376, 'Metal', 'Modern');
insert into art values
    (12386, 'Horsyy', 58000, 'http://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Lammasu.jpg/800px-Lammasu.jpg', 0);
insert into sculpture values
    (12386, 'Stone', 'Modern');
insert into art values
    (12396, 'Moses', 4320000, 'http://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Moses_San_Pietro_in_Vincoli.jpg/270px-Moses_San_Pietro_in_Vincoli.jpg', 0);
insert into sculpture values
    (12396, 'Stone', 'Renaissance');
insert into art values
    (12367, 'Nitsuke', 565000, 'http://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Miyasaka_Hakuryu_II_-_Tigress_with_Two_Cubs_-_Walters_71909.jpg/800px-Miyasaka_Hakuryu_II_-_Tigress_with_Two_Cubs_-_Walters_71909.jpg', 0);
insert into sculpture values
    (12367, 'Ivory', '19th Century');

insert into clients values
    ('John', 'Doe', '123 Main St.', 'Vancouver', 'BC', 'Canada', 'V17 3X9', 'johndoe@mail.com', '5554356364');
insert into clients values
    ('Jane', 'Doe', '123 Main St.', 'Vancouver', 'BC', 'Canada', 'V17 3X9', 'janedoe@mail.com', '5554357646');
insert into clients values
    ('Fred', 'Smith', '125 Main St.','Vancouver', 'BC', 'Canada', 'V17 3X8', 'fredsmith@mail.com', '5555556364');
insert into clients values
    ('Jim', 'Hughes', '127 Main St.', 'Vancouver', 'BC', 'Canada', 'V17 3X8', 'jimhughes@mail.com', '5556666666');
insert into clients values
    ('Donovan', 'St-Vincent', '130 Main St.', 'Vancouver', 'BC', 'Canada', 'V17 3X6', 'donovansv@mail.com', '5554354666');
insert into clients values
    ('Sam', 'Rothstein', '1242 41st AVE', 'Vancouver', 'BC', 'Canada', 'V5M 5H1', 'imaboss@gmail.com', '1818181818');
insert into clients values
    ('Mary', 'Rothstein', '1242 41st AVE', 'Vancouver', 'BC', 'Canada', 'V5M 5H1', 'imalsoaboss@gmail.com', '1818181818');
insert into clients values
    ('Martyn', 'Kool', '355 Oak St.', 'Vancouver', 'BC', 'Canada', 'V56 5N1', 'kool@gmail.com', '1010101010');
insert into clients values
    ('Ben', 'Williams', '666 Horrid St.', 'Vancouver', 'BC', 'Canada', 'V5H 8H1', 'imnotcool@gmail.com', '1234567890');
insert into clients values
    ('Annie', 'Mac', '1342 11th AVE', 'Surrey', 'BC', 'Canada', 'V5M 9H1', 'whopps@gmail.com', '6667891011');
insert into clients values
    ('Anita', 'Limb', '111 41st AVE', 'Vancouver', 'BC', 'Canada', 'V5M 5H1', 'ineedalimb@gmail.com', '100000000');
insert into clients values
    ('Ruth', 'Florence', '14 Willowdale Rd.', 'North York', 'ON', 'Canada', 'T7Z 5K2', 'rf@yahoo.com', '416788888');
insert into clients values
    ('Adrian', 'Thomas', '14 High St.', 'Calgary', 'AB', 'Canada', 'C6H K3L', 'herro@yahoo.com', '316788888');
insert into clients values
    ('Seb', 'Thrun', '1 Tech Rd.', 'Saskatoon', 'SK', 'Canada', 'L8L 5K2', 'lol@yahoo.com', '88888888888');
insert into clients values
    ('Ken', 'Burns', '888 Montgomery AVE', 'Montreal', 'QC', 'Canada', 'H3A 2L1', 'erickburns@yahoo.com', '51488888888');
insert into clients values
    ('Charline', 'Kahn', '66 Beech AVE', 'Markam', 'ON', 'Canada', 'S8L 5K2', 'maybe@yahoo.com', '6588888888');
insert into clients values
    ('Eli', 'Kowaz', '13 Greer St.', 'Saskatoon', 'SK', 'Canada', 'H8J 5U2', 'elik@yahoo.com', '99988888888');

insert into supplies values
    ('Pablo', 'Picasso', 5556925253, 50, 12345);
insert into supplies values
    ('Henri', 'Mattisse', 5552565223, 55, 12349);
insert into supplies values
    ('Damien', 'Hirst', 5556489895, 60, 12346);
insert into supplies values
    ('Claude', 'Monet', 5556124553, 50, 12348);
insert into supplies values
    ('Weiwei', 'Ai', 5554656253, 50, 12347);

insert into supplies values
    ('Shalah', 'Aghapour', 5556489898, 60, 12350);
insert into supplies values
    ('Lidia', 'Abdul', 7777777777, 50, 12351);
insert into supplies values
    ('Casade', 'Fresh', 5556489890, 55, 12352);
insert into supplies values
    ('Mel', 'Kay', 5556489895, 60, 12353);
insert into supplies values
    ('Dude', 'McGee', 6656489895, 50, 12354);
insert into supplies values
    ('Karl', 'Abt', 5556489897, 50, 12376);

insert into supplies values
    ('Leo', 'Davinc', 5666489895, 5556124553, 12366);
insert into supplies values
    ('Sigmar', 'Polke', 5666489895, 50, 12367);
insert into supplies values
    ('Daily', 'Dally', 5556489896, 55, 12386);
insert into supplies values
    ('Sandro', 'Botti', 1234567800, 60, 12396);
insert into supplies values
    ('Donatello', 'Bardi', 9988888889, 50, 12351);


insert into issue_transaction values
    (54321, 'John', 'Doe', 5554356364);
insert into issue_transaction values
    (54322, 'John', 'Doe', 5554356364);
insert into issue_transaction values
    (54323, 'Fred', 'Smith', 5555556364);
insert into issue_transaction values
    (54324, 'Donovan', 'St-Vincent', 5554354666);
insert into issue_transaction values
    (54325, 'Fred', 'Smith', 5555556364);

insert into purchase values
    (54321, 20140531, 'cash',21000, 12349);
insert into purchase values
    (54323, 20140530, 'mc', 32000, 12345);
insert into purchase values
    (54324, 20140531, 'visa', 80000, 12346);
insert into purchase_return values
    (54321, 20140602, 'cash', null, 21000, 12349);
insert into purchase_return values
    (54323, 20140602, 'mc', '3335 2324 1555 4555', 32000, 12345);

insert into receives_commission values
    (54324, 'Damien', 'Hirst', 5556489895, 29000);

