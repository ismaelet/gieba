<?php

use Legacy\System\Classes\DB;

// menu categories table
if (! DB::table('admin_categories')) {
    DB::execute('
		CREATE TABLE admin_categories (
			id INT(3) AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(30),
			slug VARCHAR(15),
			manager TINYINT(1) DEFAULT 0,
			sort INT(3) DEFAULT 0
		)
	');

    DB::execute("INSERT INTO admin_categories (name, slug, manager) VALUES ('Impostazioni', 'settings', 1)");
}

// menu sections table
if (! DB::table('admin_sections')) {
    DB::execute("
		CREATE TABLE admin_sections (
			id INT(4) AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(50),
			slug VARCHAR(30),
			category VARCHAR(15),
			template VARCHAR(10) DEFAULT 'admin',
			hidden TINYINT(1) DEFAULT 0,
			url VARCHAR(30) DEFAULT '',
			sort INT(3) DEFAULT 0,
			manager TINYINT(1) DEFAULT 0
		)
	");

    DB::execute("INSERT INTO admin_sections (name, slug, category, manager) VALUES ('Generali', 'general', 'settings', 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, manager) VALUES ('Menu - Categorie', 'categories/list', 'settings', 'list', 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, hidden, manager) VALUES ('Aggiungi a Menu - Categorie', 'categories/add', 'settings', 'form', 1, 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, manager) VALUES ('Menu - Sezioni', 'sections/list', 'settings', 'list', 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, hidden, manager) VALUES ('Aggiungi a Menu - Sezioni', 'sections/add', 'settings', 'form', 1, 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, manager) VALUES ('Variabili di sistema', 'variables/list', 'settings', 'list', 1)");
    DB::execute("INSERT INTO admin_sections (name, slug, category, template, hidden, manager) VALUES ('Aggiungi una variabile', 'variables/add', 'settings', 'form', 1, 1)");
}

// authentication table
DB::execute('
	CREATE TABLE IF NOT EXISTS auth_tokens_admin (
		id INT(11) AUTO_INCREMENT PRIMARY KEY,
		selector VARCHAR(12),
		validator VARCHAR(64),
		userID INT(11),
		expiration TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP()
	)
');
