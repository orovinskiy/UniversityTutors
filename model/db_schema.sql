/*
Schema for application's database.

@author Keller Flint
 */

/* User table containing basic user data. */
CREATE TABLE User (
    user_id int NOT NULL AUTO_INCREMENT,
    user_email varchar(255) NOT NULL,
    user_password varchar(255) NOT NULL,
    user_is_admin bit NOT NULL,

    PRIMARY KEY (user_id)
);

/* Tutor table containing basic tutor data. Child of User table. */
CREATE TABLE Tutor (
    user_id int,
    tutor_first varchar(255) NOT NULL,
    tutor_last varchar(255) NOT NULL,
    tutor_phone varchar(255) NULL,
    tutor_ssn varchar(255) NULL,
    tutor_image varchar(255) NULL,

    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES User (user_id) ON UPDATE CASCADE
);

/* Year table containing the onboarding data for tutors each year. */
CREATE TABLE Year (
    year_id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    year_start year NOT NULL,
    year_packet_sent bit NOT NULL,
    year_background enum('none', 'sent', 'clear', 'flag') NOT NULL,
    year_reference enum('none', 'incomplete', 'clear', 'flag') NOT NULL,
    year_offer_letter bit NOT NULL,
    year_affirmation_disclosures bit NOT NULL,
    year_sexual_misconduct bit NOT NULL,
    year_w4 bit NOT NULL,
    year_handbook_verification bit NOT NULL,
    year_ADP enum('none', 'invited', 'registered') NOT NULL,
    year_i9 enum('none', 'tutor', 'admin') NOT NULL,
    year_orientation bit NOT NULL,
    year_placement varchar(255) NULL,

    PRIMARY KEY (year_id),
    FOREIGN KEY (user_id) REFERENCES Tutor (user_id) ON UPDATE CASCADE
);

/*Test Data*/

INSERT INTO User Values (default,"Bobby@mail.com","bobTroll!!",0),(default,"Andy@mail.com","AndyHot!!",0),(default,"CoolGuy@mail.com","notCool!!",0),
(default,"Jasmine@mail.com","IgotNada!!",0),(default,"Admin@mail.com","Admin101!!",1),(default,"carGuy@mail.com","TrucksLOL!!",0),
(default,"BenBro@mail.com","BenRules!!",0),(default,"meow@mail.com","CatKing!!",0),(default,"tiger@mail.com","tigerRule!!",0),
(default,"HappyFam@mail.com","famisCool!!",0),(default,"flyinPho@mail.com","iLikePho!!",0),(default,"whoDis@mail.com","newPhone!!",0);

INSERT INTO Tutor VALUES ("13","Bob","Riely","206-345-7843","234-43-7853","image.jpg"),("14","Andy","Shmuck","253-786-5426","937-20-0573","image.jpg"),
("15","Richard","Fanny","253-285-7210","073-96-1074","image.jpg"),("16","Jasmine","Riely","206-037-1056","658-43-0123","image.jpg"),
("18","Reddin","Huck","206-856-0264","106-54-8528","image.jpg"),
("19","Ben","Luss","253-883-2222","973-91-7893","image.jpg"),("20","Nick","Goravskiy","206-632-0835","123-45-6789","image.jpg"),
("21","Joe","Carole","206-764-2945","964-53-0274","image.jpg"),("22","Ivan","Hambug","206-635-8468","936-02-6482","image.jpg"),
("23","Gyser","Huck","206-345-7843","294-03-7851","image.jpg"),("24","Chad","Getter","253-635-9732","087-66-9882","image.jpg");

INSERT INTO Year VALUES (default,"1","2020",1,"sent","clear",0,1,1,0,1,"invited","tutor",1,"Fredwork"),
(default,"2","2020",1,"flag","none",1,1,0,0,0,"registered","none",1,"Green Elementry"),
(default,"3","2020",0,"clear","incomplete",0,0,1,1,1,"none","admin",1,"Somplace Elementry"),
(default,"4","2020",1,"none","flag",1,1,0,0,0,"registered","none",0,"AwsomeVill Elementry"),
(default,"5","2020",1,"clear","clear",0,1,0,1,1,"none","tutor",1,"Fredwork High"),
(default,"7","2020",0,"none","incomplete",1,1,1,1,1,"invited","none",1,"WASU Elementry"),
(default,"8","2020",0,"flag","clear",0,0,0,0,0,"none","none",0,"NoWhere Elementry"),
(default,"9","2020",1,"clear","none",1,0,0,1,0,"invited","admin",0,"Grassvill Elementry"),
(default,"10","2020",1,"sent","none",0,1,0,0,1,"invited","none",1,"Gamevill Elementry"),
(default,"11","2020",0,"flag","incomplete",1,1,1,0,1,"registered","tutor",1,"NoneExistence Elementry"),
(default,"12","2020",1,"none","clear",0,0,1,1,1,"none","tutor",0,"Ghetttto Elementry");

/*
Test Data for application database

@author Dallas Sloan
 */

-- Insert statements for user table --
insert into User values(default,"jdoe@mail.greenriver.edu", "password", b'1');
insert into User values(default, "kflint@mail.greenriver.edu", "password", b'1');
insert into User values(default,"sguy@mail.greenriver.edu", "password", b'0');
insert into User values(default,"tostrich@mail.greenriver.edu", "password", b'0');
insert into User values(default,"test@mail.greenriver.edu", "password", b'0');
insert into User values(default,"jsmith@mail.greenriver.edu", "password", b'0');

-- Insert statements for Tutor table
insert into Tutor (user_id, tutor_first, tutor_last, tutor_phone) values(2, "Some", "Guy", 1231231234);
insert into Tutor (user_id, tutor_first, tutor_last, tutor_phone) values(3, "Tuna", "Ostrich", 1112223333);
insert into Tutor (user_id, tutor_first, tutor_last, tutor_phone) values(4, "Dave", "Test", 8585858585);
insert into Tutor (user_id, tutor_first, tutor_last, tutor_phone) values(5, "Joe", "Smith", 2222333545);

-- Insert statements for Year table
insert into Year values(default,2,2020,b'1',"sent","flag",b'1', b'0',b'0',b'1',b'0',"invited", "none", b'0', "not placed");
insert into Year values(default,2,2019,b'1',"clear","flag",b'1', b'1',b'0',b'0',b'0',"invited", "none", b'0', "not placed");
insert into Year values(default,3,2020,b'1',"none","incomplete",b'1', b'1',b'0',b'1',b'0',"invited", "none", b'1', "Seattle High");
insert into Year values(default,3,2017,b'0',"none","incomplete",b'0', b'0',b'0',b'0',b'0',"registered", "tutor", b'0', "Not Placed");
insert into Year values(default,4,2020,b'1',"flag","none",b'0', b'1',b'1',b'1',b'1',"none", "none", b'1', "Test Middle School");
insert into Year values(default,5,2018,b'1',"clear","clear",b'1', b'0',b'0',b'0',b'1',"registered", "admin", b'0', "Test High School");
insert into Year values(default,5,2019,b'1',"clear","flag",b'0', b'0',b'1',b'1',b'1',"invited", "tutor", b'1', "Ranier");
insert into Year values(default,5,2020,b'0',"none","incomplete",b'1', b'1',b'1',b'1',b'1',"none", "none", b'1', "Test High School");





>>>>>>> master
