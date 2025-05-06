<?php
	define('PREPEND_PATH', '');
	include_once(__DIR__ . '/lib.php');

	// accept a record as an assoc array, return transformed row ready to insert to table
	$transformFunctions = [
		'enrollment' => function($data, $options = []) {
			if(isset($data['full_name'])) $data['full_name'] = pkGivenLookupText($data['full_name'], 'enrollment', 'full_name');
			if(isset($data['class'])) $data['class'] = pkGivenLookupText($data['class'], 'enrollment', 'class');
			if(isset($data['year'])) $data['year'] = thisOr($data['class'], pkGivenLookupText($data['year'], 'enrollment', 'year'));

			return $data;
		},
		'classes' => function($data, $options = []) {

			return $data;
		},
		'registration' => function($data, $options = []) {
			if(isset($data['date_of_joining'])) $data['date_of_joining'] = guessMySQLDateTime($data['date_of_joining']);
			if(isset($data['date_of_birth'])) $data['date_of_birth'] = guessMySQLDateTime($data['date_of_birth']);
			if(isset($data['contact'])) $data['contact'] = str_replace('-', '', $data['contact']);

			return $data;
		},
	];

	// accept a record as an assoc array, return a boolean indicating whether to import or skip record
	$filterFunctions = [
		'enrollment' => function($data, $options = []) { return true; },
		'classes' => function($data, $options = []) { return true; },
		'registration' => function($data, $options = []) { return true; },
	];

	/*
	Hook file for overwriting/amending $transformFunctions and $filterFunctions:
	hooks/import-csv.php
	If found, it's included below

	The way this works is by either completely overwriting any of the above 2 arrays,
	or, more commonly, overwriting a single function, for example:
		$transformFunctions['tablename'] = function($data, $options = []) {
			// new definition here
			// then you must return transformed data
			return $data;
		};

	Another scenario is transforming a specific field and leaving other fields to the default
	transformation. One possible way of doing this is to store the original transformation function
	in GLOBALS array, calling it inside the custom transformation function, then modifying the
	specific field:
		$GLOBALS['originalTransformationFunction'] = $transformFunctions['tablename'];
		$transformFunctions['tablename'] = function($data, $options = []) {
			$data = call_user_func_array($GLOBALS['originalTransformationFunction'], [$data, $options]);
			$data['fieldname'] = 'transformed value';
			return $data;
		};
	*/

	@include(__DIR__ . '/hooks/import-csv.php');

	$ui = new CSVImportUI($transformFunctions, $filterFunctions);
