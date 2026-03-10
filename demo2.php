<pre>
<?php

disp( "Here we do a number of disp() again, and it uses str(), being overloaded elsewhere.");
disp( "hello world" );
disp( 123.456 );
disp( 123 );
disp( ['a' => 1,2,3] );
disp( [1,2,3] );
disp( (object)['x'=>'z'] );
disp( true );
disp( formstr( '012{x}3456', {x:'a'} ) );

//elsewhere: here we overload str() for various signatures
//note this is simpler scheme than done in pro.php
//overload{string}	function str($a) { return "$a"; }
overload function str(string $a) { return "$a"; } 
overload function str(integer $i) { return '#' .$i; }
overload function str(double $n) { return '#'.$n; } 
overload function str(array $n) { return print_r($n, true); } 
overload function str(stdclass $n) { return print_r($n, true); }
overload function str(list $n) { return 'list:[' . join(', ', $n) . ']'; } 
//This is a base function to service unmatched calls. 
//disp() has a default behaviour which can be replaced with this.
//Note it being used to display 'true' as we did not overload str() to do so.
//You can override the base function of an overloaded function.
function str($x) { return '<i>'.vartype($x).':</i>' . print_r($x,true); } 

//It is not a good idea to override any native functions... But you can...
//This version does exaclty what strpos did, just 3x slower. But you can...
//I disabled it, because it breaks PHP...
/*
disp( strpos( '0123456789', '456' ) );
override function strpos( $s, $ss, $i=0 ) { 
	$j = 0; 	
	while ( isset( $ss[ $j ] ) && isset( $s[ $i + $j ] ) ) {		
		if ( $s[$i + $j ] === $ss[$j] ) $j++;
		else { $j = 0; $i++; }
	}
	return isset($ss[$j]) ? false : $i + 1; //now strpos() is 1-based ... oooo naughty.
	//see how this changes substring() based on string arguments if you rename this function
}
*/
disp( 'test new substring() function:' );
disp( substring( '01234567890',3,7) );
disp( substring( '01234567890','12','89') );

disp( substring( 'zzz{xxx}xxx', 3, 7) );
disp( substring( 'zzz{xxx}xxx', '{', '}') );
disp( substring_replace( 'zzz{xxx}zzz','a', 3, 7) );
disp( substring_replace( 'zzz{xxx}zzz','a', '{x', 'x}') );
disp( substring_replace( 'zzz{xxx}zzz','a', '{x', 'x}', true ) );

disp('<hr>');

disp('Standard way of creating object inline, and displaying it:');
	$t = (object)[ 'vartype' => 'myobj', 'values' => [1,2,3] ];
	disp( 'vartype:', vartype($t), '<br>', print_r($t, true), '<hr>');

disp('A "better" way of creating inline objects, using: function record(...$p) {return (object)$p;}' );
disp("Also note the property <i>vartype</i>, and the overload on str\x28) a bit further on." );
	$t = record( vartype:'mystuff', name:'harry', car:'ford' ); //record() is variadic in prePHP
	disp( 'vartype:', vartype($t), '<br>', $t, '<hr>');

disp("Here is an even more cleaner syntax:" );
	$t = { vartype:'mystuff', name:'Batman', car:'Batmobile' };
	disp($t);

//here is the overload for the objects with a property vartype=mystuff
overload function str(mystuff $x) {
	$s = '';
	foreach( $x as $k => $v ) if ($k != 'vartype' ) $s .= "   ->$k = $v <br>";		
	return "mystuff: {<br>{$s} }";
}
//in order for overloading to work on objects with vartype properties:
function vartype( $v ) {	
	if ($v === null) return 'null';
	if ( is_array( $v ) && array_is_list( $v ) ) return 'list';
	if ( gettype( $v ) == 'object') {			
		if ( isset( $v->vartype ) ) return $v->vartype;
		if ( count(get_class_methods($v)) == 0 ) return 'record'; //objects with no methods
		return strtolower( get_class($v) );
	}
	return gettype( $v );
}

disp('<hr>');

$r = { name: 'harry' };
disp($r);

overload function str(record $r) {
	$s = '';
	foreach( $r as $k => $v ) $s .= "   ->$k = $v <br>";		
	return "record: {<br>{$s} }";
}


disp( '<hr>' );
disp( 'Here we demo the ability to <i>extend</i> the functionality on a native function:');
disp( '0123456789',  4,  3 );
disp( substr( '0123456789',  4,  3 ) );
disp( substr( '0123456789', -6,  3 ) );
disp( substr( '0123456789',  6, -3 ) );
disp( substr( '0123456789', -4, -3 ) );
disp( substr( '0123six789', 'x', -3 ) ); //see how my 1-based strpos now fails me...

overload function str( number $n ) { return '#'.$n; }

function substr( $x, $i, $l ) { //my implementation of substr - just for demo :)
	$s = '';	
	for (; $l > 0; $l--) {
		$s .= $x[$i];
		$i++;
	}
	return $s;
}

function substr( $s, $i, $j ) {  //#1. we change substr so neg j returns chars BEFORE $i.
	if ( $j < 0 ) { $j = -$j; $i -= $j - 1; }
	return substr_( $s, $i, $j ); //this will call the native substr, because it is the first extention
}

//and you can extend the same function multiple times
function substr( $s, $i, $j ) {  //#2. now we allow for string delimiters, and call the prev extended version
	if ( vartype( $i ) == 'string' ) $i = strpos( $s, $i );	
	if ( vartype( $j ) == 'string' ) $j = strpos( $s, $j, $i );
	if ( $j < 0 ) { $j = -$j; $i -= $j - 1; }
	return substr_( $s, $i, $j ); //this will call the prev extended version just above this
}

disp( '<hr>' );

	disp( vartype("123") );
	disp( vartype("abc") );
	disp( vartype( { vartype:'mystring', value:'abc' } ) );

	//you can extend an overridden function
	function vartype($x) {
		if ( gettype( $x ) == 'string' && is_numeric($x) ) return 'number';
		return vartype_($x);
	}	

	//you can override an extended function
	function foo($x) { return $x; }
	function foo($x){ return 2*$x; }	
	disp( foo(10) );

	function str($x) { return 'base:' . str_($x); }

?>
