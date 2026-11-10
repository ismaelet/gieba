<?php

// variables table
if (! $con->query('SELECT 1 FROM variables')) {

    $con->query('
		CREATE TABLE variables (

			id INT(3) AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(30),
			value VARCHAR(300) 

		)
	');

    $con->query('INSERT INTO variables (name, value) VALUES (?, ?)', ['public', 0]);

}

// users table
$con->query('
	CREATE TABLE IF NOT EXISTS users (

		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR(50),
		password VARCHAR(128),
		mail VARCHAR(50) DEFAULT "",
		firstname VARCHAR(50) DEFAULT "",
		lastname VARCHAR(50) DEFAULT "",
		admin TINYINT(1) DEFAULT 0,
		time TIMESTAMP DEFAULT CURRENT_TIMESTAMP

	)
');

// authentication table
$con->query('
	CREATE TABLE IF NOT EXISTS auth_tokens (

		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		selector VARCHAR(12),
		validator VARCHAR(64),
		user INT(11),
		expiration TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP()

	)
');
