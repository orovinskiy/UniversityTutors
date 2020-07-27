/*
Schema for application's database.

@author Keller Flint
 */
  DROP TABLE if exists User, Tutor, Info, TutorYear, Item, State, ItemTutorYear;

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
    tutor_bio varchar(5000) NULL,
    tutor_bio_done tinyint DEFAULT 0

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



INSERT INTO Info VALUES (1, 2020);



INSERT INTO User VALUES (default, "admin@mail.com", "81dc9bdb52d04dc20036dbd8313ed055", 1);