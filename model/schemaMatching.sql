/*
@author Oleg
 */

 CREATE TABLE Region(
    region_id int NOT NULL AUTO_INCREMENT,
    region_name VARCHAR(255) NOT NULL,

    PRIMARY KEY (region_id)
 );

 CREATE TABLE School(
    school_id int NOT NULL AUTO_INCREMENT,
    school_name VARCHAR(255) NOT NULL,

    PRIMARY KEY (school_id)
 );

 CREATE TABLE Role(
    role_id int NOT NULL AUTO_INCREMENT,
    school_id int NOT NULL,
    role_name VARCHAR(255) NOT NULL,
    role_notes TEXT,

    PRIMARY KEY (role_id),
    FOREIGN  KEY (school_id) REFERENCES School(school_id) ON DELETE CASCADE
 );

 CREATE TABLE Tag(
    tag_id int NOT NULL AUTO_INCREMENT,
    tag_name VARCHAR(255) NOT NULL,

    PRIMARY KEY (tag_id)
 );

 CREATE TABLE TutorPlace(
    tutor_id int NOT NULL AUTO_INCREMENT,
    tutor_name VARCHAR(255) NOT NULL,
    tutor_notes TEXT,

    PRIMARY KEY (tutor_id)
 );

 CREATE TABLE TagGroup(
    tagGroup_id int NOT NULL AUTO_INCREMENT,
    role_id int NOT NULL,
    tagGroup_name VARCHAR(255) NOT NULL,
    tagGroup_weight int NOT NULL,

    PRIMARY KEY (tagGroup_id),
    FOREIGN KEY (role_id) REFERENCES Role(role_id) ON DELETE CASCADE
 );

 /* Many To Many Relationships */

 CREATE TABLE Tag_TagGroup(
    tagGroup_id int NOT NULL,
    tag_id int NOT NULL,

    FOREIGN KEY (tagGroup_id) REFERENCES TagGroup(tagGroup_id) ON DELETE  CASCADE,
    FOREIGN KEY (tag_id) REFERENCES Tag(tag_id) ON DELETE  CASCADE
 );

 CREATE TABLE Tutor_Tag(
    tutor_id int NOT NULL,
    tag_id int NOT NULL,

    FOREIGN KEY (tutor_id) REFERENCES TutorPlace(tutor_id) ON DELETE  CASCADE,
    FOREIGN KEY (tag_id) REFERENCES Tag(tag_id) ON DELETE  CASCADE
 );

 CREATE TABLE Tutor_Region(
    tutor_id int NOT NULL,
    region_id int NOT NULL,

    FOREIGN KEY (tutor_id) REFERENCES TutorPlace(tutor_id) ON DELETE  CASCADE,
    FOREIGN KEY (region_id) REFERENCES Region(region_id) ON DELETE  CASCADE
 );