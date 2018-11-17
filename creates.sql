
CREATE TABLE account
(
    account_id BIGSERIAL,
    session_id CHARACTER(20),
    name CHARACTER VARYING(60) NOT NULL,
    password_hash CHARACTER VARYING(255) NOT NULL,

    time_created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

    PRIMARY KEY (account_id)  
);

CREATE TABLE organization
(
    id BIGINT NOT NULL,
    name CHARACTER VARYING(45) NOT NULL,
    
    PRIMARY KEY (id)
);

CREATE TABLE courses
(
    id BIGSERIAL,
    name CHARACTER VARYING(45),
    organization BIGINT NOT NULL,
    
    PRIMARY KEY (id),
    FOREIGN KEY (organization) REFERENCES organization(id) ON DELETE CASCADE
);

CREATE TABLE student_account
(
    account_id BIGINT NOT NULL,
    gender CHARACTER VARYING(20),
    age DATE,
    course BIGINT NOT NULL,
    phonenumber CHARACTER(9) NOT NULL,
    qualifications BIGINT NOT NULL,
    observations CHARACTER VARYING(45),
    
    PRIMARY KEY (account_id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE,
    FOREIGN KEY (course) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE teacher_account
(
    account_id BIGINT NOT NULL,
    course_id BIGINT NOT NULL,
    category INTEGER NOT NULL,
    
    PRIMARY KEY (account_id, course_id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE green_message
(
    id BIGSERIAL,
    account_id BIGINT NOT NULL,
    course_id BIGINT NOT NULL,
    message TEXT,
    
    PRIMARY KEY (id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE sent_green_message
(
    message_id BIGINT NOT NULL,
    account_id BIGINT NOT NULL,
    already_read BOOLEAN NOT NULL DEFAULT FALSE,
    
    PRIMARY KEY (account_id, message_id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES green_message(id) ON DELETE CASCADE
);

CREATE TABLE alert
(
    id BIGSERIAL,
    account_id BIGINT NOT NULL,
    latitude DOUBLE PRECISION NOT NULL,
    altitude DOUBLE PRECISION NOT NULL,
    alert_type INTEGER NOT NULL,
    time_created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
    
    PRIMARY KEY (id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE
);
