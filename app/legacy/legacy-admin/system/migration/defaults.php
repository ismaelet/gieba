<?php

use Legacy\System\Classes\DB;

global $con;

// config table
DB::execute('
	CREATE TABLE IF NOT EXISTS config (
		id INT(3) AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(30),
		value VARCHAR(300) 
	)
');

if (! DB::exists('config WHERE name="public"')) {
    DB::add('config (name, value) VALUES (?, ?)', ['public', 0]);
}

// users table
DB::execute('
	CREATE TABLE IF NOT EXISTS users (
		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR(50) DEFAULT "",
		password VARCHAR(128) DEFAULT "",
		email VARCHAR(50) DEFAULT "",
		firstname VARCHAR(50) DEFAULT "",
		lastname VARCHAR(50) DEFAULT "",
		groupId INT(11) DEFAULT 0,
		admin TINYINT(1) DEFAULT 0,
		time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)
');

// password resets
DB::execute('
	CREATE TABLE IF NOT EXISTS user_resets (
		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		userID INT(11),
		security VARCHAR(10),
		time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)
');

// authentication table
DB::execute('
	CREATE TABLE IF NOT EXISTS auth_tokens (
		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		selector VARCHAR(12),
		validator VARCHAR(64),
		userID INT(11),
		expiration TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP()
	)
');
