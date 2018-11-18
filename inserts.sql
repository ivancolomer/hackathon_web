INSERT INTO account (session_id, name, password_hash) VALUES('', 'Pepito', '$2y$11$Ku.9FwdvILlkhQbMgflYF.uPKw5G26XCt8lt2mhC9vNXp5q9b4R.W');
INSERT INTO organization (name) VALUES('Episcopal');
INSERT INTO courses (name, organization) VALUES('1rESO', 1);
INSERT INTO student_account (account_id, gender, age, course, phonenumber, qualifications, observations) VALUES(100000, 'hombre', '31/01/1998', 1, '123456789', 5, 'Ninguna');


SELECT a.password_hash, s.gender, t.mail FROM account a LEFT JOIN student_account s ON(a.account_id = s.account_id AND a.account_id = 100000) LEFT JOIN teacher_account t ON(a.account_id = t.account_id AND a.account_id = 100000);


SELECT a.account_id, t.mail, t.organization_id FROM account a INNER JOIN teacher_account t ON (a.account_id = 100002 AND a.session_id = '9XHxptaE5IckEDYxwmw9' AND a.account_id = t.account_id);