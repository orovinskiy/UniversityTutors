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