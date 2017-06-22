<?php
declare(strict_types = 1);
require_once ('common.inc.php');
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