<?php
	define('PREPEND_PATH', '');
	include_once(__DIR__ . '/lib.php');

	// accept a record as an assoc array, return transformed row ready to insert to table
	$transformFunctions = [
		'enrollment' => function($data, $options = []) {
			if(isset($data['date'])) $data['date'] = guessMySQLDateTime($data['date']);
			if(isset($data['full_name'])) $data['full_name'] = pkGivenLookupText($data['full_name'], 'enrollment', 'full_name');
			if(isset($data['class'])) $data['class'] = pkGivenLookupText($data['class'], 'enrollment', 'class');
			if(isset($data['fees_code'])) $data['fees_code'] = pkGivenLookupText($data['fees_code'], 'enrollment', 'fees_code');
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
		'fees_structure' => function($data, $options = []) {

			return $data;
		},
		'fees_payments' => function($data, $options = []) {
			if(isset($data['date'])) $data['date'] = guessMySQLDateTime($data['date']);
			if(isset($data['student'])) $data['student'] = pkGivenLookupText($data['student'], 'fees_payments', 'student');
			if(isset($data['class'])) $data['class'] = thisOr($data['student'], pkGivenLookupText($data['class'], 'fees_payments', 'class'));
			if(isset($data['term'])) $data['term'] = thisOr($data['student'], pkGivenLookupText($data['term'], 'fees_payments', 'term'));

			return $data;
		},
		'clearance_tickets' => function($data, $options = []) {
			if(isset($data['date'])) $data['date'] = guessMySQLDateTime($data['date']);

			return $data;
		},
		'subjects' => function($data, $options = []) {

			return $data;
		},
		'staff' => function($data, $options = []) {
			if(isset($data['subject'])) $data['subject'] = pkGivenLookupText($data['subject'], 'staff', 'subject');
			if(isset($data['date_of_joining'])) $data['date_of_joining'] = guessMySQLDateTime($data['date_of_joining']);
			if(isset($data['contract_ends'])) $data['contract_ends'] = guessMySQLDateTime($data['contract_ends']);

			return $data;
		},
		'class_notes' => function($data, $options = []) {
			if(isset($data['subject'])) $data['subject'] = pkGivenLookupText($data['subject'], 'class_notes', 'subject');
			if(isset($data['class'])) $data['class'] = pkGivenLookupText($data['class'], 'class_notes', 'class');

			return $data;
		},
		'assessments' => function($data, $options = []) {
			if(isset($data['class'])) $data['class'] = pkGivenLookupText($data['class'], 'assessments', 'class');
			if(isset($data['teacher'])) $data['teacher'] = pkGivenLookupText($data['teacher'], 'assessments', 'teacher');
			if(isset($data['subject'])) $data['subject'] = pkGivenLookupText($data['subject'], 'assessments', 'subject');
			if(isset($data['date'])) $data['date'] = guessMySQLDateTime($data['date']);

			return $data;
		},
		'results' => function($data, $options = []) {
			if(isset($data['assess'])) $data['assess'] = pkGivenLookupText($data['assess'], 'results', 'assess');
			if(isset($data['student_name'])) $data['student_name'] = pkGivenLookupText($data['student_name'], 'results', 'student_name');
			if(isset($data['total_marks'])) $data['total_marks'] = thisOr($data['assess'], pkGivenLookupText($data['total_marks'], 'results', 'total_marks'));

			return $data;
		},
		'exams' => function($data, $options = []) {
			if(isset($data['class'])) $data['class'] = pkGivenLookupText($data['class'], 'exams', 'class');
			if(isset($data['subject'])) $data['subject'] = pkGivenLookupText($data['subject'], 'exams', 'subject');
			if(isset($data['date'])) $data['date'] = guessMySQLDateTime($data['date']);

			return $data;
		},
		'rizalts' => function($data, $options = []) {
			if(isset($data['exm_id'])) $data['exm_id'] = pkGivenLookupText($data['exm_id'], 'rizalts', 'exm_id');
			if(isset($data['student_name'])) $data['student_name'] = pkGivenLookupText($data['student_name'], 'rizalts', 'student_name');
			if(isset($data['total_marks'])) $data['total_marks'] = thisOr($data['exm_id'], pkGivenLookupText($data['total_marks'], 'rizalts', 'total_marks'));

			return $data;
		},
	];

	// accept a record as an assoc array, return a boolean indicating whether to import or skip record
	$filterFunctions = [
		'enrollment' => function($data, $options = []) { return true; },
		'classes' => function($data, $options = []) { return true; },
		'registration' => function($data, $options = []) { return true; },
		'fees_structure' => function($data, $options = []) { return true; },
		'fees_payments' => function($data, $options = []) { return true; },
		'clearance_tickets' => function($data, $options = []) { return true; },
		'subjects' => function($data, $options = []) { return true; },
		'staff' => function($data, $options = []) { return true; },
		'class_notes' => function($data, $options = []) { return true; },
		'assessments' => function($data, $options = []) { return true; },
		'results' => function($data, $options = []) { return true; },
		'exams' => function($data, $options = []) { return true; },
		'rizalts' => function($data, $options = []) { return true; },
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
