<?php
declare(strict_types = 1);
require_once ('common.inc.php');
function DBToClasses(\PDO $db_handle, string $dbname): array {
	/** @var PhpClass[] $classes */
	$classes = array ();
	$db = $db_handle;
	unset ( $db_handle );
	$tables = $db->query ( 'SHOW TABLES IN `' . $dbname . '`', PDO::FETCH_NUM );
	// hhb_var_dump($tables->fetchAll(PDO::FETCH_ASSOC)) & die();
	foreach ( $tables as $table ) {
		$table = $table [0];
		$class = new PhpClass ();
		$classes [] = $class;
		$class->name = ucfirst ( $table );
		$cols = $db->query ( 'SHOW COLUMNS FROM `' . $table . '`' )->fetchAll ( PDO::FETCH_ASSOC );
		foreach ( $cols as $col ) {
			// var_dump ( $col ) & die ();
			$member = new PhpClassMember ();
			$class->members [] = $member;
			$member->name = $col ['Field'];
			$member->type = guessPhpType ( $col ['Type'] );
			if (! empty ( $member->type )) {
				$tmp = filter_var ( $col ['Null'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
				if ($tmp === true) {
					$member->type .= '|null';
				}
				unset ( $tmp );
			}
		}
	}
	return $classes;
}
function objectToClasses($obj): array {
	$arr = json_decode ( json_encode ( $obj ), true );
	return arrayToClasses ( $arr );
}
function arrayToClasses(array $arr, string $basename = '?'): array {
	/** @var PhpClass[] $classes */
	$classes = array ();
	$class = new PhpClass ();
	$classes [] = $class;
	$class->name = $basename;
	foreach ( $arr as $key => $val ) {
		$mem = new PhpClassMember ();
		$class->members [] = $mem;
		$mem->name = $key;
		if ($val !== NULL) {
			if (is_array ( $val )) {
				$mem->type = ucfirst ( $key );
				$classes = array_merge ( $classes, arrayToClasses_ ( $val, $key ) );
			} else {
				$mem->type = gettype ( $val );
			}
		}
	}
	return $classes;
}