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
    user_id int NOT NULL,
    tutor_first varchar(255) NULL,
    tutor_last varchar(255) NULL,
    tutor_phone varchar(255) NULL,
    tutor_ssn varchar(255) NULL,
    tutor_image varchar(255) NULL,
    tutor_bio varchar(1000) NULL,

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
    year_SPS enum('none', 'tutor', 'admin') NOT NULL,
    year_ADP enum('none', 'invited', 'registered') NOT NULL,
    year_i9 enum('none', 'tutor', 'admin') NOT NULL,
    year_orientation bit NOT NULL,
    year_placement varchar(255) NULL,

    PRIMARY KEY (year_id),
    FOREIGN KEY (user_id) REFERENCES Tutor (user_id) ON UPDATE CASCADE
);

CREATE TABLE Info (
    info_id int NOT NULL,
    info_current_year year NOT NULL,

    PRIMARY KEY (info_id)
);

/*
Set default year
@author Keller Flint
 */

INSERT INTO Info Value (1, "2020");

/*
Test Data
@author Dallas Sloan, Oleg
*/

INSERT INTO User Values (default,"Bobby@mail.com","bobTroll!!",0),(default,"Andy@mail.com","AndyHot!!",0),(default,"CoolGuy@mail.com","notCool!!",0),
(default,"Jasmine@mail.com","IgotNada!!",0),(default,"Admin@mail.com","Admin101!!",1),(default,"carGuy@mail.com","TrucksLOL!!",0),
(default,"BenBro@mail.com","BenRules!!",0),(default,"meow@mail.com","CatKing!!",0),(default,"tiger@mail.com","tigerRule!!",0),
(default,"HappyFam@mail.com","famisCool!!",0),(default,"flyinPho@mail.com","iLikePho!!",0),(default,"whoDis@mail.com","newPhone!!",0),
(default,"jdoe@mail.greenriver.edu", "password", b'1'),
(default, "kflint@mail.greenriver.edu", "password", b'1'),
(default,"sguy@mail.greenriver.edu", "password", b'0'),
(default,"tostrich@mail.greenriver.edu", "password", b'0'),
(default,"test@mail.greenriver.edu", "password", b'0'),
(default,"jsmith@mail.greenriver.edu", "password", b'0');

INSERT INTO Tutor VALUES ("1","Bob","Riely","(222) 222-4444","234-43-7853","image.jpg",null),("2","Andy","Shmuck","(253) 786-5426","937-20-0573","image.jpg",null),
("3","Richard","Fanny","(222) 222-4444","073-96-1074","image.jpg",null),("4","Jasmine","Riely","(206) 037-1056","658-43-0123","image.jpg",null),
("5","Reddin","Huck","(223) 232-4544","106-54-8528","image.jpg",null),
("7","Ben","Luss","(242) 222-4444","973-91-7893","image.jpg",null),("8","Nick","Goravskiy","(206) 632-0835","123-45-6789","image.jpg",null),
("9","Joe","Carole","(252) 222-4444","964-53-0274","image.jpg",null),("10","Ivan","Hambug","(206) 635-8468","936-02-6482","image.jpg",null),
("11","Gyser","Huck","(292) 022-4844","294-03-7851","image.jpg",null),("12","Chad","Getter","(253) 635-9732","087-66-9882","image.jpg",null),
(13, "Some", "Guy", "(206) 222-4444",null,null,null),
(14, "Tuna", "Ostrich", "(206) 222-4644",null,null,null),
(15, "Dave", "Test", "(202) 222-4444",null,null,null),
(16, "Joe", "Smith", "(292) 222-4444",null,null,null);

INSERT INTO Year VALUES (default,"1","2020",1,"sent","clear",0,1,1,0,1,"none","invited","tutor",1,"Fredwork"),
(default,"2","2020",1,"flag","none",1,1,0,0,0,"none","registered","none",1,"Green Elementry"),
(default,"3","2020",0,"clear","incomplete",0,0,1,1,1,"none","none","admin",1,"Somplace Elementry"),
(default,"4","2020",1,"none","flag",1,1,0,0,0,"tutor","registered","none",0,"AwsomeVill Elementry"),
(default,"5","2020",1,"clear","clear",0,1,0,1,1,"tutor","none","tutor",1,"Fredwork High"),
(default,"7","2020",0,"none","incomplete",1,1,1,1,1,"none","invited","none",1,"WASU Elementry"),
(default,"8","2020",0,"flag","clear",0,0,0,0,0,"none","none","none",0,"NoWhere Elementry"),
(default,"9","2020",1,"clear","none",1,0,0,1,0,"admin","invited","admin",0,"Grassvill Elementry"),
(default,"10","2020",1,"sent","none",0,1,0,0,1,"admin","invited","none",1,"Gamevill Elementry"),
(default,"11","2020",0,"flag","incomplete",1,1,1,0,1,"none","registered","tutor",1,"NoneExistence Elementry"),
(default,"12","2020",1,"none","clear",0,0,1,1,1,"tutor","none","tutor",0,"Ghetttto Elementry"),
(default,13,2020,b'1',"sent","flag",b'1', b'0',b'0',b'1',b'0',"none","invited", "none", b'0', "not placed"),
(default,13,2019,b'1',"clear","flag",b'1', b'1',b'0',b'0',b'0',"admin","invited", "none", b'0', "not placed"),
(default,14,2020,b'1',"none","incomplete",b'1', b'1',b'0',b'1',b'0',"tutor","invited", "none", b'1', "Seattle High"),
(default,14,2017,b'0',"none","incomplete",b'0', b'0',b'0',b'0',b'0',"none","registered", "tutor", b'0', "Not Placed"),
(default,15,2020,b'1',"flag","none",b'0', b'1',b'1',b'1',b'1',"none","none", "none", b'1', "Test Middle School"),
(default,16,2018,b'1',"clear","clear",b'1', b'0',b'0',b'0',b'1',"admin","registered", "admin", b'0', "Test High School"),
(default,16,2019,b'1',"clear","flag",b'0', b'0',b'1',b'1',b'1',"none","invited", "tutor", b'1', "Ranier"),
(default,16,2020,b'0',"none","incomplete",b'1', b'1',b'1',b'1',b'1',"none","none", "none", b'1', "Test High School");




