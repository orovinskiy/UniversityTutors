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





