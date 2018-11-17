CREATE EXTENSION citext;

CREATE TABLE account
(
    account_id BIGSERIAL,
    session_id CHARACTER(20) NOT NULL,
    name CITEXT NOT NULL UNIQUE, -- CHARACTER VARYING(20)
    password_hash CHARACTER VARYING(255) NOT NULL,
    mail CITEXT NOT NULL UNIQUE, -- CHARACTER VARYING(80)
    time_created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
    registered_ip CHARACTER VARYING(45) NOT NULL,
    last_time_logged TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
    mail_verified BOOLEAN NOT NULL DEFAULT FALSE,
    ban_time_end TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT '2018-01-31 19:53:25',
    
    PRIMARY KEY (account_id),
    CONSTRAINT check_empty_name CHECK (name != ''),
    CONSTRAINT check_len_name CHECK (LENGTH(name) BETWEEN 5 AND 20)    
);

CREATE TABLE user_account
(
    account_id BIGINT NOT NULL,

    level SMALLINT NOT NULL DEFAULT 1,
    experience BIGINT NOT NULL DEFAULT 0,
    last_ip CHARACTER VARYING(45) NOT NULL,
    
    PRIMARY KEY (account_id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE
);

CREATE TABLE confirm_hashes (
    
	hash_id BIGSERIAL,
    account_id BIGINT NOT NULL,
    hash_type SMALLINT NOT NULL,
    
    hash_code CHARACTER(20) NOT NULL,
    
    time_created TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
    
    PRIMARY KEY (hash_id),
    FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE,
	UNIQUE (account_id, hash_type)
);