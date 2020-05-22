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

/* Set default year */
CREATE TABLE Info (
    info_id int NOT NULL,
    info_current_year year NOT NULL,

    PRIMARY KEY (info_id)
);

/* TutorYear table for referencing which tutor/year data is being tracked for */
CREATE TABLE TutorYear (
    tutorYear_id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    tutorYear_year year NOT NULL,

    PRIMARY KEY (tutorYear_id),
    FOREIGN KEY (user_id) REFERENCES Tutor (user_id) ON UPDATE CASCADE
);

/* Contain items to be tracked */
CREATE TABLE Item(
    item_id int NOT NULL AUTO_INCREMENT,
    item_name varchar(255) NOT NULL,
    item_type enum("checkbox", "select") NOT NULL,
    item_is_upload bit NOT NULL,
    item_file varchar(255) NULL,

    PRIMARY KEY (item_id)
);

/* Contains possible states for items */
CREATE TABLE State(
    state_id int NOT NULL AUTO_INCREMENT,
    item_id int NOT NULL,
    state_name varchar(255) NULL, /* NULL because it could be text in which case one state entry is required to define if it is for the tutor or the admin*/
    state_set_by enum("default", "tutor", "admin") NOT NULL,
    state_text varchar(5000) NULL,
    state_order int NOT NULL,
    state_is_done int NOT NULL,

    PRIMARY KEY (state_id),
    FOREIGN KEY (item_id) REFERENCES Item (item_id) ON UPDATE CASCADE
);

/* Contains item data for each TutorYear */
CREATE TABLE ItemTutorYear (
    item_id int NOT NULL,
    tutorYear_id int NOT NULL,
    state_id int NOT NULL,
    itemTutorYear_file varchar(255) NULL,

    PRIMARY KEY (item_id, tutorYear_id),
    FOREIGN KEY (item_id) REFERENCES Item (item_id) ON UPDATE CASCADE,
    FOREIGN KEY (tutorYear_id) REFERENCES TutorYear (tutorYear_id) ON UPDATE CASCADE,
    FOREIGN KEY (state_id) REFERENCES State (state_id) ON UPDATE CASCADE
);



INSERT INTO Info Value (1, 2020);

/*
Test Data
@author Dallas Sloan, Oleg
*/

INSERT INTO User Values (default,"bob@mail.com",MD5("1234"),0),(default,"Andy@mail.com",MD5("AndyHot!!"),0),(default,"CoolGuy@mail.com",MD5("notCool!!"),0),
(default,"Jasmine@mail.com",MD5("IgotNada!!"),0),(default,"admin@mail.com",MD5("1234"),1),(default,"carGuy@mail.com",MD5("TrucksLOL!!"),0),
(default,"BenBro@mail.com",MD5("BenRules!!"),0),(default,"meow@mail.com",MD5("CatKing!!"),0),(default,"tiger@mail.com",MD5("tigerRule!!"),0),
(default,"HappyFam@mail.com",MD5("famisCool!!"),0),(default,"flyinPho@mail.com",MD5("iLikePho!!"),0),(default,"whoDis@mail.com",MD5("newPhone!!"),0),
(default,"jdoe@mail.greenriver.edu", MD5("password"), b'0'),
(default, "kflint@mail.greenriver.edu", MD5("password"), b'0'),
(default,"sguy@mail.greenriver.edu", MD5("password"), b'0'),
(default,"tostrich@mail.greenriver.edu", MD5("password"), b'0'),
(default,"test@mail.greenriver.edu", MD5("password"), b'0'),
(default,"jsmith@mail.greenriver.edu", MD5("password"), b'0');

INSERT INTO Tutor VALUES ("1","Bob","Riely","(222) 222-4444","234-43-7853","image.jpg",null),
("2","Andy","Shmuck","(253) 786-5426","xh1AuCZsASCQmYQ=","image.jpg",null),
("3","Richard","Fanny","(222) 222-4444","xh1AuCZsASCQmYQ=","image.jpg",null),
("4","Jasmine","Riely","(206) 037-1056","xh1AuCZsASCQmYQ=","image.jpg",null),
("5","Reddin","Huck","(223) 232-4544","xh1AuCZsASCQmYQ=","image.jpg",null),
("7","Ben","Luss","(242) 222-4444","xh1AuCZsASCQmYQ=","image.jpg",null),
("8","Nick","Goravskiy","(206) 632-0835","xh1AuCZsASCQmYQ=","image.jpg",null),
("9","Joe","Carole","(252) 222-4444","xh1AuCZsASCQmYQ=","image.jpg",null),
("10","Ivan","Hambug","(206) 635-8468","xh1AuCZsASCQmYQ=","image.jpg",null),
("11","Gyser","Huck","(292) 022-4844","xh1AuCZsASCQmYQ=","image.jpg",null),
("12","Chad","Getter","(253) 635-9732","xh1AuCZsASCQmYQ=","image.jpg",null),
(13, "Some", "Guy", "(206) 222-4444",null,null,null),
(14, "Tuna", "Ostrich", "(206) 222-4644",null,null,null),
(15, "Dave", "Test", "(202) 222-4444",null,null,null),
(16, "Joe", "Smith", "(292) 222-4444",null,null,null);



/* Item Test Data */

/* TutorYear */
INSERT INTO `tutors`.`TutorYear` (`tutorYear_id`, `user_id`, `tutorYear_year`) VALUES ('1', '1', 2020);
INSERT INTO `tutors`.`TutorYear` (`tutorYear_id`, `user_id`, `tutorYear_year`) VALUES ('2', '2', 2020);

/* Background */
INSERT INTO `tutors`.`Item` (`item_id`, `item_name`, `item_type`, `item_is_upload`) VALUES ('1', 'Backgroud Check', 'select', b'0');

INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('1', '1', 'Not Done', 'default', '1', '0');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('2', '1', 'In Progress', 'admin', '2', '0');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('3', '1', 'Clear', 'admin', '3', '1');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('4', '1', 'Flag', 'admin', '4', '1');

/* ADP */
INSERT INTO `tutors`.`Item` (`item_id`, `item_name`, `item_type`, `item_is_upload`) VALUES ('2', 'ADP', 'select', b'0');

INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('5', '2', 'Not Done', 'default', '1', '0');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('6', '2', 'Invited', 'admin', '2', '0');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_text`, `state_order`, `state_is_done`) VALUES ('7', '2', 'Registered', 'tutor', 'Once you have registered for ADP, check this box.', '3', '1');

/* A & D */
INSERT INTO `tutors`.`Item` (`item_id`, `item_name`, `item_type`, `item_is_upload`) VALUES ('3', 'A & D', 'checkbox', b'0');

INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_text`, `state_order`, `state_is_done`) VALUES ('8', '3', 'false', 'default', 'Read A& D and check the box.', '1', '0');
INSERT INTO `tutors`.`State` (`state_id`, `item_id`, `state_name`, `state_set_by`, `state_order`, `state_is_done`) VALUES ('9', '3', 'true', 'tutor', '2', '1');

/* ItemTutorYear */
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('1', '1', '1');
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('1', '2', '4');
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('2', '1', '5');
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('2', '2', '7');
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('3', '1', '8');
INSERT INTO `tutors`.`ItemTutorYear` (`item_id`, `tutorYear_id`, `state_id`) VALUES ('3', '2', '9');
