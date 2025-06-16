<?php
	// check this file's MD5 to make sure it wasn't called before
	$tenantId = Authentication::tenantIdPadded();
	$setupHash = __DIR__ . "/setup{$tenantId}.md5";

	$prevMD5 = @file_get_contents($setupHash);
	$thisMD5 = md5_file(__FILE__);

	// check if this setup file already run
	if($thisMD5 != $prevMD5) {
		// set up tables
		setupTable(
			'enrollment', " 
			CREATE TABLE IF NOT EXISTS `enrollment` ( 
				`date` DATE NULL,
				`stid` INT(35) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`stid`),
				`full_name` INT(35) UNSIGNED NOT NULL,
				`class` INT(4) UNSIGNED NOT NULL,
				`term` VARCHAR(40) NOT NULL,
				`year` INT(4) UNSIGNED NULL,
				`fees_code` INT(13) UNSIGNED NULL,
				`amount_received` INT(11) NULL,
				`balance` INT(11) NULL,
				`cleared` VARCHAR(10) NULL,
				`structure` INT(9) NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('enrollment', ['full_name','class','fees_code',]);

		setupTable(
			'classes', " 
			CREATE TABLE IF NOT EXISTS `classes` ( 
				`id` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`class` VARCHAR(3) NOT NULL,
				`year` INT(11) NULL
			) CHARSET utf8mb4"
		);

		setupTable(
			'registration', " 
			CREATE TABLE IF NOT EXISTS `registration` ( 
				`id` INT(35) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`date_of_joining` DATE NULL,
				`photo` VARCHAR(40) NOT NULL,
				`full_name` VARCHAR(40) NOT NULL,
				`date_of_birth` DATE NOT NULL,
				`age` INT(11) NULL,
				`gender` VARCHAR(40) NOT NULL,
				`parent_gurdian` VARCHAR(40) NOT NULL,
				`contact` INT(11) NOT NULL,
				`address` VARCHAR(40) NOT NULL
			) CHARSET utf8mb4"
		);

		setupTable(
			'fees_structure', " 
			CREATE TABLE IF NOT EXISTS `fees_structure` ( 
				`id` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`code` VARCHAR(3) NOT NULL,
				UNIQUE `code_unique` (`code`),
				`fees` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
				`description` TEXT NULL
			) CHARSET utf8mb4"
		);

		setupTable(
			'fees_payments', " 
			CREATE TABLE IF NOT EXISTS `fees_payments` ( 
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`date` DATE NULL,
				`student` INT(10) UNSIGNED NOT NULL,
				`class` INT(10) UNSIGNED NULL,
				`term` INT(10) UNSIGNED NULL,
				`amount_received` INT(7) NOT NULL,
				`received_from` VARCHAR(25) NOT NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('fees_payments', ['student',]);

		setupTable(
			'clearance_tickets', " 
			CREATE TABLE IF NOT EXISTS `clearance_tickets` ( 
				`c_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`c_id`),
				`date` DATE NULL,
				`full_name` VARCHAR(40) NULL,
				`class` VARCHAR(40) NULL,
				`term` VARCHAR(4) NULL
			) CHARSET utf8mb4"
		);

		setupTable(
			'subjects', " 
			CREATE TABLE IF NOT EXISTS `subjects` ( 
				`sub_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`sub_id`),
				`subject` VARCHAR(40) NOT NULL
			) CHARSET utf8mb4"
		);

		setupTable(
			'staff', " 
			CREATE TABLE IF NOT EXISTS `staff` ( 
				`id` INT(35) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`staff_id` VARCHAR(7) NOT NULL DEFAULT 'Em_',
				UNIQUE `staff_id_unique` (`staff_id`),
				`full_name` VARCHAR(40) NOT NULL,
				`contact` VARCHAR(40) NOT NULL,
				`subject` INT UNSIGNED NOT NULL,
				`date_of_joining` DATE NOT NULL,
				`contract_ends` DATE NOT NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('staff', ['subject',]);

		setupTable(
			'class_notes', " 
			CREATE TABLE IF NOT EXISTS `class_notes` ( 
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`subject` INT UNSIGNED NOT NULL,
				`class` INT(4) UNSIGNED NOT NULL,
				`term` VARCHAR(3) NOT NULL,
				`topics` TEXT NOT NULL,
				`resource` VARCHAR(40) NOT NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('class_notes', ['subject','class',]);

		setupTable(
			'assessments', " 
			CREATE TABLE IF NOT EXISTS `assessments` ( 
				`id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`class` INT(35) UNSIGNED NULL,
				`total_marks` INT NULL,
				`teacher` INT(35) UNSIGNED NULL,
				`subject` INT UNSIGNED NULL,
				`avg_performance` VARCHAR(40) NULL,
				`highest_mark` VARCHAR(40) NULL,
				`lowest_mark` VARCHAR(40) NULL,
				`date` DATE NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('assessments', ['class','teacher','subject',]);

		setupTable(
			'results', " 
			CREATE TABLE IF NOT EXISTS `results` ( 
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`assess` INT UNSIGNED NULL,
				`student_name` INT(35) UNSIGNED NULL,
				`total_marks` INT UNSIGNED NULL,
				`result` INT NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('results', ['assess','student_name',]);

		setupTable(
			'exams', " 
			CREATE TABLE IF NOT EXISTS `exams` ( 
				`exm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`exm_id`),
				`class` INT(35) UNSIGNED NULL,
				`total_marks` INT NULL,
				`teacher` VARCHAR(40) NULL,
				`subject` INT UNSIGNED NULL,
				`avg_performance` INT(3) NULL,
				`highest_mark` INT(3) NULL,
				`lowest_mark` INT(3) NULL,
				`date` DATE NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('exams', ['class','subject',]);

		setupTable(
			'rizalts', " 
			CREATE TABLE IF NOT EXISTS `rizalts` ( 
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				`exm_id` INT UNSIGNED NULL,
				`student_name` INT(35) UNSIGNED NULL,
				`total_marks` INT UNSIGNED NULL,
				`result` INT NULL
			) CHARSET utf8mb4"
		);
		setupIndexes('rizalts', ['exm_id','student_name',]);



		// save MD5
		@file_put_contents($setupHash, $thisMD5);
	}


	function setupIndexes($tableName, $arrFields) {
		if(!is_array($arrFields) || !count($arrFields)) return false;

		foreach($arrFields as $fieldName) {
			if(!$res = @db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")) continue;
			if(!$row = @db_fetch_assoc($res)) continue;
			if($row['Key']) continue;

			@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
		}
	}


	function setupTable($tableName, $createSQL = '', $arrAlter = '') {
		global $Translation;
		$oldTableName = '';
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)) {
			$matches = [];
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/i", $arrAlter[0], $matches)) {
				$oldTableName = $matches[1];
			}
		}

		if($res = @db_query("SELECT COUNT(1) FROM `$tableName`")) { // table already exists
			if($row = @db_fetch_array($res)) {
				echo str_replace(['<TableName>', '<NumRecords>'], [$tableName, $row[0]], $Translation['table exists']);
				if(is_array($arrAlter)) {
					echo '<br>';
					foreach($arrAlter as $alter) {
						if($alter != '') {
							echo "$alter ... ";
							if(!@db_query($alter)) {
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
							} else {
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				} else {
					echo $Translation['table uptodate'];
				}
			} else {
				echo str_replace('<TableName>', $tableName, $Translation['couldnt count']);
			}
		} else { // given tableName doesn't exist

			if($oldTableName != '') { // if we have a table rename query
				if($ro = @db_query("SELECT COUNT(1) FROM `$oldTableName`")) { // if old table exists, rename it.
					$renameQuery = array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)) {
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
					} else {
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				} else { // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			} else { // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@db_query($createSQL)) {
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . db_error(db_link()) . '</div>';

					// create table with a dummy field
					@db_query("CREATE TABLE IF NOT EXISTS `$tableName` (`_dummy_deletable_field` TINYINT)");
				} else {
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}

			// set Admin group permissions for newly created table if membership_grouppermissions exists
			if($ro = @db_query("SELECT COUNT(1) FROM `membership_grouppermissions`")) {
				// get Admins group id
				$ro = @db_query("SELECT `groupID` FROM `membership_groups` WHERE `name`='Admins'");
				if($ro) {
					$adminGroupID = intval(db_fetch_row($ro)[0]);
					if($adminGroupID) @db_query("INSERT IGNORE INTO `membership_grouppermissions` SET
						`groupID`='$adminGroupID',
						`tableName`='$tableName',
						`allowInsert`=1, `allowView`=1, `allowEdit`=1, `allowDelete`=1
					");
				}
			}
		}

		echo '</div>';

		$out = ob_get_clean();
		if(defined('APPGINI_SETUP') && APPGINI_SETUP) echo $out;
	}
