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


	//an example on how to extend formstr() to accept in-sito defaults
	function formstr( $s, $v ) {
		if ( preg_match_all( '/\{(\w*)=([^}]*)/', $s, $M, PREG_SET_ORDER + PREG_OFFSET_CAPTURE   ) ) {
			foreach ( array_reverse( $M ) as $m ) 
				$s = substr_replace( $s, '', $m[2][1]-1, strlen($m[2][0] )+1 );			
			$v->{$m[1][0]} ??= $m[2][0];
		}
		return formstr_( $s, $v ); //call previous version
	}

	disp( formstr( 'Interpolation with defaults: {name} {surname=marx}' , {name:'harry'} ) );

?>
