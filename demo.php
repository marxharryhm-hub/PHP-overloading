<pre>
<?php	
	
	
	//create a few custom display formats:
		overload function str( integer $i ) { return '##' . $i; }	
		overload function str( number $i ) { return '#' . $i; }	
		overload function str( record $r ) { 
			$s = 'record[';
			foreach ( $r as $p => $v ) $s .= '<br>   '.$p.': '.$v.'<br>';
			return $s.']'; 
		}

	disp( 1 );
	disp( 'alpha' );
	disp( '123' );

	function test( ...$x ) {
		echo 'base:';
		foreach( $x as $xx ) disp( $xx );
	}

	overload function test( integer $i, ...$x=[] ) {
		echo 'test1:';
		disp( $i );
		foreach( $x as $xx ) disp( $xx );
	}

	overload function test( integer $i, ...$x=[] ) {
		echo 'test2:';
		disp( $i );
		disp( $x );
	}

	test( 999, { name: 'harry' } );

	disp( vartype( {name:'harry'} ) );
	disp( {name:'harry'} );

	function vartype( $x ) { 
		if ( gettype( $x ) == 'string' && is_numeric( $x ) ) return 'number';
		return vartype_( $x ); 
	}

	disp(
	substring( '01234567890',  3,    7), //including the index chars
    substring( '01234567890',  3,   -4),
    substring( '01234567890', -8,    7),
    substring( '01234567890', -8,   -4),
    substring( '01234567890','12','89'), //excluding the delimiters
    substring( 'zzz{xxx}xxx', '{', '}'), //returns '{xxx}'
	);

	disp( substring_replace( '012{345}67890',  'abc', '{', '}') );
	disp( substring_replace( '012{345}67890',  'abc', '{3', '5}') );

?>
