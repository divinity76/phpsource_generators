<?php
declare(strict_types = 1);
class PhpClass {
	/** @var string $name */
	public $name;
	/** @var PhpClassMember[] $members */
	public $members = array ();
	public static function createFromArray(array $arr): PhpClass {
		$ret = new PhpClass ();
		foreach ( $arr as $prop => $val ) {
			$ret->{$prop} = $val;
		}
		return $ret;
	}
}
class PhpClassMember {
	/** @var string $name */
	public $name;
	/** @var string|null $type */
	public $type;
	/** @var string|null $description */
	public $description;
	public static function createFromArray(array $arr): PhpClassMember {
		$ret = new PhpClassMember ();
		foreach ( $arr as $prop => $val ) {
			$ret->{$prop} = $val;
		}
		return $ret;
	}
}
function classesToSourceCode(array $classes): string {
	/** @var PhpClass[] $classes */
	// duplicates protection:
	$nodups = array ();
	foreach ( $classes as $key => $class ) {
		if (in_array ( $class->name, $nodups, true )) {
			unset ( $classes [$key] );
		} else {
			$nodups [] = $class->name;
		}
	}
	unset ( $nodups, $key, $class );
	$ret = '';
	foreach ( $classes as $pc ) {
		$ret .= PhpClassToSourceCode ( $pc ) . "\n";
	}
	return $ret;
}
function classToSourceCode(\PhpClass $pc): string {
	$pc = clone $pc;
	$pc->name = ucfirst ( $pc->name );
	$ret = 'class ' . $pc->name . ' {' . "\n";
	foreach ( $pc->members as $member ) {
		if (! empty ( $member->type )) {
			$ret .= '    /** @var ' . $member->type . ' $' . $member->name . ' */' . "\n";
		}
		$ret .= '    public $' . $member->name . ";\n";
	}
	$ret .= '}';
	return $ret;
}
function guessPhpType(string $inputType): string {
	$debug_orig = $inputType;
	$inputType = strtolower ( trim ( $inputType ) );
	$ret = '';
	$types = array (
			'integer' => array (
					'int',
					'integer',
					'smallint',
					'tinyint',
					'mediumint',
					'bigint' 
			),
			'float' => array (
					'float',
					'double',
					'decimal',
					'dec' 
			),
			'bool' => array (
					'bool',
					'boolean' 
			),
			'string' => array (
					'string',
					'char',
					'varchar',
					'tinytext',
					'text',
					'binary',
					'varbinary',
					'tinyblob',
					'blob' 
			) 
	);
	foreach ( $types as $type => $aliasarr ) {
		foreach ( $aliasarr as $alias ) {
			if (false !== stripos ( $inputType, $alias )) {
				return $type; // ... to return the last result instead of the first result, comment out this line
				$ret = $type;
			}
		}
	}
	// hhb_var_dump ( $debug_orig, $ret );
	return $ret;
}
