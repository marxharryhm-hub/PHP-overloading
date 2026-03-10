<pre>
<?php
$prePHP_version = '2.1; built: 10 March 2026 - implements OOX';
/*
	Developer: Harry Marx marxh2@unisa.ac.za  
	 OOX - Overload, Override and eXtend

	 overload:: the creation of alternative forms of the same function, 
		servicing a different set of types of values passed as arguments.
	 override:: to create a function that replaces previous definitions of it.
	 extend:: to extend the functionality of an existing function, providing 
	  new code, and allowing to call the previous version of this function 
	  as part of the new code.

	 To "override" a function, you simply define it again. This new defintion 
	 becomes the "extended" version of it. It means redeclarations of function
	 are now allowed.
	 To access the overridden version, extend the function name in the 
	 extended function, with a single underscode. 
	 You can only access an overridden function, from its own extended version.

	 overload function name( typed paramters, untyped parameters ) {} )

	  Warning:
	   Overloading / overriding native PHP functions is not advisable. 
	   Doing so will most likely break existing code.

	  signature:: the combination of the name and types of parameters, 
		of an overloaded function

	  When overloading a variadic, the required types must be defined on inidividual
	  parameters, and the variadic parameter will be typeless. As the types must match
	  the signature, you cannot use variadic parameters in the signature.
	  
	  Types used in signatures correspond to the values returned by vartype(). 
	  'int' will not work, as vartype(1) will return 'integer'.

	  All the types for the arguments passed to an overloaded function, 
	  will be included in the initial required signature.
	  If not found, the types will be dropped one by one to search with 
	  the least degenerate signature.
	  All types provided in the signature must match passed types.
	  You cannot use optional parameters in the signature.
	  
	  You can only use untyped parameters for an overloaded function as the 
	  last parameters.
	  They will not be included in the overload's signature.
	  Untypes parameters may be optional, and have default values.    

	  You can override/extend an overloaded function - you have to precede 
	  it again with 'overload'. 

	  If a non-overloaded function with the same name exists, it will be called 
	  if no overloaded signature matches. 
	  This will then not produce the "signature not found" error.

	  See vartype() - it can be extended/overridden to alter its return values.
	  
	  ERROR: in overload function: 
	   if the overload function throws an error, it will report as such.  
	   
	  ERROR: overload signature not found:
	   If the overloaded function is called with arguments that does not 
	  match any defined signature, an error will be thrown.
	   The reported unmatched signature can then be used to create a new 
	  overload that does service it.

	 override/extending functions
		
	  Warning:
	   Extending / overriding native PHP functions is not advisable. 
	   Doing so will most likely break existing code.

	  You can override any function by merely redeclaring it.
	  You can override a function multiple times. 
	  By default only the last version will be called.

	  You can make recursive calls to and in OOX functions. 

	  You can, only in the extended function, call the immediate previous 
	  version of it. The previous version is called by appending an 
	  underscore to the function name.

	 Technical issues:
	  Overriding/overloading/extending (OOX) should not change behaviour of 
	  functions used by the OOX process, and the router functions 
	  (except vartype() ). 
	  These are unaffected by OOX, except for vartype() and str(). 
	  die() does not abort OOX declarations - they will still apply even is 
	  after the die() instruction.
	  Commenting it out will remove OOX instructions.

	 vartype( $variable )
	  This is a slightly more generic function returning the class names for 
	  objects, and returning the 'vartype' property on objects with this property.  
	  You may override/extend it with new types, which will affect router 
	  functions.
	  You cannot overload vartype(). You can override/extend it.

	Other functions included in this package:

	 disp( ...$variables )
	  It allows the display of multiple arguments, of various types.
	  It uses an overloaded function str(), to serve the various types.
	  It has a base function that will display all types using print_r() 
	  that is not overloaded.

	 array_map_keys( $array, $function )
	  Same as array_map, but with keys. $function takes 2 arguments, 
	  key and value.
	  A new value is calculated for each element, indexed as a list.

	 substring( $string, $from, $to=-1) and 
	 substring_replace( $string, $insert, $from, $to, $inclusive) 
	   Returns part of the sting including the $from and $to indexes.
	   If $from or $to is negative, they are relative to the end of the string, 
		  starting with -1.
	   if $from is a string, $from will be past the end of the first occurance 
		  of $from.
	   if $to is a string, $to will be the up to before the first occurance 
		  of $to after $from.
	   0 indicates the left most char, and -1 the right most char.
	   The following all returns '34567'
		substring( '01234567890',  3,    7) //including the index chars
		substring( '01234567890',  3,   -4)
		substring( '01234567890', -8,    7)
		substring( '01234567890', -8,   -4)
		substring( '01234567890','12','89') //excluding the delimiters
		substring( 'zzz{xxx}xxx', '{', '}') //returns 'xxx'
	  substring_replace()
	   If $from and $to are integers, replaces the chars in $string with $insert, 
	   starting at $from and ending with $to, both also replaced.
	   If $from and $to are strings, they are replaced.   

	   substring_replace( 'zzz{xxx}zzz','a', 3, 7)     //returns 'zzzazzz'
	   substring_replace( 'zzz{xxx}zzz','a', '{', '}' ) //returns 'zzzazzz'
	  
	 strposrev( $hay, $needle, $from )
	  strrpos() searches from the index to the end of the string, 
	  while strposrev searches from the index to the beginning of the string.
	  Returnd false of not found.
	   strrpos( '.A..4.A....A', 'A',  4 ) returns 11  
	   strposrev( '.A..4.A....A', 'A',  4 ) returns  1 

	 str_paired( $string, $from=0, $bra=null, $ket=null)
	  Searches for the end of the first bracket pair starting at $from.
	  If $bra is not provided, $bra will uptake the first 
	  of (, {, [ or <.  
	  If $ket is not provided, it will complement $bra.     

	formstr( $string, $parameters_object )  
	  String interpolation with formatting.  
	  $parameters_object is an object with properties coinsiding with 
	  symbols in $string to replace.
	  To interpolate {x: 123} in string, use the property name in curly
		brackets:  '...{x}...'
		formatting follows: '...{x%10}...'
	  no rounding, tuncating:      
		no %      ---x---    
		%10       ---x         ---    
		%-10      ---         x---    
		%10       ---123.888888---    
		%-10      ---123.888888---    
		%-10.3    ---   123.889---    
		%-10.3    ---     123.9---    
		%-10.3    ---       123---   
	  rounding, no truncating:   
		%n-10.3   ---   123.000---    
		%n-10.3   ---12345678901234.000---    
		%n-10.3   ---   123.889---    
		%n-10.3   ---1234567.889---    
		%n-10.3   ---123456789123.889---    
		%n-10.3   ---12345678.889---    
		%n-10.3   ---1234567898765.889---    
		%n,-10.2  ---123,456,789,876.67---   
	  dates:     
		%d        ---2026-03-03 10h54:18 Tue---    
		%d10      ---2026-03-03---    
		%dd.dd Mon \'yy     
				  ---03 Mar '26---   
	  misc:   %s  ---Hello worl---   
	  using sprintf formats:   
		%f       

	record(...$named_parameters)  
	  Wraps the named input parameters as an object. 

	split( $delimiter, $string, $count=0, $trimmed=false )  
	  Wraps explode(), and return at least $count elements.  
	  If trimmed = true, it will trim all elements, and filter 
	  out empty elements.  It will then append null to return $count elements.   

	str()  
	  This is a "volitile" function, and it will affect OOX functionality if
	  extended.
	  Used to convert any passed value into a string, used by disp() 
	  and formstr().  
	  You can overload this function for any vartype().   

	inline object syntax:  
	  Brings PHP in line with javascript syntax for defining objects inline.  
	  This is used often to pass named, optional parameters, perhaps with 
	  default values.    
	  ex.:   
		echo formstr( "My name is {name} and I am {age} years old.", 
		  {name:'Harry'} )   

		function formstr( $stringtoformat, $params )    
		  //add the default values on $params
		  $params->age ??= '21 with 40y experience'; 
		  
		  ...

*/
/*
2.1	fixed importing of files
*/
$_FUNCTIONS = [];
$_IMPORTED = [];
$_script = [];

//no OOX allowed in boot
//volitile functions - changes to it in user code will affect prePHP
//also functions that use volitile functions are volitile
$volitile = ['str','vartype'];
$boot = <<<'boot'
	//cannot redeclare native functions here
	function import($f) {
		eval( '?>'. $GLOBALS['_script'][$f] );
	 }
	function str( $x ) { 
		if ($x === null) return '<i>null</i>';
		if ($x === true) return '<i>true</i>';
		if ($x === false) return '<i>false</i>';
		return print_r( $x, true ); 
	 }		 
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
	function record(...$x) { return (object) $x; }
	function substring( $s, $from, $to=-1 ) {
		if ( is_string( $from ) ) { 
			$k = strpos( $s, $from ); if ($k === false) return ''; $from = $k + strlen($from); }
		if ($from < 0 ) $from += strlen( $s );   
		if ( is_string( $to ) ) { 
			$to = strpos( $s, $to, $from+1 ); if ($to === false) return ''; $to--; }		
		if ($to < 0 ) $to += strlen( $s ); 
		if ( $from <= $to ) return substr( $s, $from, $to - $from + 1);
		return '';
	 };	
	function strposrev( $hey, $needle, $start=-1 ) {
			return strrpos( $hey, $needle, $start >=0 ? $start - strlen($hey) : $start); 
		 };
	function str_paired( $s, $i=0, $bra=null, $ket=null ) {
			if ( gettype( $i ) == 'string' ) $i = strpos( $s, $i ); 
			if ( $i === false ) return false;
			if ( $i < 0 ) $i += strlen( $s );
			if ( $bra == null ) { // grap first bracketable char
				$q = null;
				for(; $i < strlen($s); $i++) {
					switch ( $s[ $i ] ) {
						case '\\': $i++; break;
						case '"': if ($q == '"') $q = null; else $q ??= '"'; break;
						case "'": if ($q == "'") $q = null; else $q ??= "'"; break;
						case '(': if ( !$q ) $bra = '('; break 2;
						case '[': if ( !$q ) $bra = '['; break 2;
						case '{': if ( !$q ) $bra = '{'; break 2;
						case '<': if ( !$q ) $bra = '<'; break 2;
					}
				}
			}
			if ( $bra == null ) return false;

			if ( $ket == null ) {
				switch ( $bra ) {			
					case '(': $ket = ')'; break;
					case '[': $ket = ']'; break;
					case '{': $ket = '}'; break;
					case '<': $ket = '>'; break;
				}
			}
			if ( $ket == null ) return false;

			$n = 0;
			$q = null;
			for(; $i < strlen($s); $i++) {
				switch ( $s[ $i ] ) {
					case '\\': $i++; break;
					case '"': if ($q == '"') $q = null; else $q ??= '"'; break;
					case "'": if ($q == "'") $q = null; else $q ??= "'"; break;
					case $bra: if ( !$q ) $n++; break;
					case $ket: if ( !$q ) $n--; if ( $n <= 0 ) return $i;			
				}
			}
			return false;
		 };
	function substring_replace( $s, $ss, $from, $to=-1 ) { //on i, replacing i..j
			if ( is_string( $from ) ) { 
				$i = strpos( $s, $from ); if ( $i === false ) return ''; 								
			}
			else $i = $from;
			if ( $i < 0 ) $i += strlen( $s );   

			if ( is_string( $to ) ) { 
				$j = strpos( $s, $to, $i+1 ); if ( $j === false ) return ''; 		
				$j += strlen($to);
			}	
			else $j = $to;
			if ( $j < 0 ) $j += strlen( $s ); 

			return substr_replace( $s, $ss, $i, $j - $i + 1);
		 };
	function disp( ...$x ) { 
		foreach ( $x as $i => $xx ) {
			if ( $i ) echo ' ';
			echo str( $xx );
		}
		echo '<br>';
	 }	
	function array_map_keys( $fn, $A ) {
		$R = [];
		foreach( $A as $k => $v ) $R[] = $fn( $k, $v );
		return $R;
	 }
	function formstr( $template, $params, $trunc = true ) {
		/*	- numbers are never truncated if too big for width
			{name} {name%9} {name%9.9} {name%x9.9}
			%n	number %n99.9
			%n,	number with thousands separator
			%d	date 'yyyy-mm-dd HH`hmi:ss dow Month Mon fff yy hh m h w'
			%s	string
			%f	sprintf()	
		*/
		foreach ( $params as $k => $v ) {
			$v = trim( str($v) );
			$template = str_replace( '{' . $k . '}', $v, $template ); // {n}			
			$i = 0;
			while ( ( $i = strpos( $template, '{'.$k.'%', $i ) ) !== false ) { 				
				$j = strpos( $template, '}', $i );
				$fs = substring( $template, $i + strlen($k) + 2 , $j-1);				
				[$w,$d] = split( '.', $fs, 2, true );	
				if ( $w[0] === 'n' ) { //never truncated: %n99.9 %n99.9
					$trunc = false;
					$w = substr( $w, 1 );
					if ($w[0] === ',') { 
						$w = substr( $w, 1);						
						$W = abs($w);
						if (!$d) $d = 0;						
						$D = strpos( $v, '.'); if ( $D < 0 ) $D = strlen($v);  								
						$v = number_format( $params[$k], $d, '.', ',' );
						if ( $d && strlen($v) < $W && strpos( $v, '.' ) < 0 ) $v .= '.';
						while ( strlen($v) < $W && strlen($v) - strpos( $v, '.' ) <= $d ) $v .= '0';
						if ( $w < 0 ) while ( strlen($v) < $W ) $v = ' ' + $v;	

					} else {
						$W = abs($w);
						if (!$d) $d = 0;						
						$v = number_format( $params[$k], 0, '.', '' );
						$D = strpos( $v, '.' ); if ( $D < 0 ) $D = strlen($D);  
						$v = number_format( $params[$k], $d, '.', '' );
						if ( $d && strlen($v) < $W && strpos( $v, '.' ) < 0 ) $v .= '.';
						while ( strlen($v) < $W && strlen($v) - strpos( $v, '.' ) <= $d ) $v .= '0';
						if ( $w < 0 ) while ( strlen($v) < $W ) $v = ' ' . $v;
					}

				}
				elseif ( $w[0] === 'd' ) { // never truncated: %d10.yyyy-mm-dd hh24:mi:si}	
					$w = substr( $w, 1 );
					if ($w) $W = abs($w);
					$date = $params[$k];
					$v = '';					
					if (!$d) $d = 'yyyy-mm-dd HH`hmi:ss dow';
					while ($d) {
						if ( str_starts_with( $d, 'Month' ) )   { $v .= date( 'F', $date ); $d = substr( $d, 5 ); }
						elseif ( str_starts_with( $d, 'yyyy') ) { $v .= date( 'Y', $date ); $d = substr( $d, 4 ); }
						elseif ( str_starts_with( $d, 'fff' ) ) { $v .= date( 'u', $date ); $d = substr( $d, 3 ); }
						elseif ( str_starts_with( $d, 'Mon' ) ) { $v .= date( 'M', $date ); $d = substr( $d, 3 ); }
						elseif ( str_starts_with( $d, 'dow' ) ) { $v .= date( 'D', $date ); $d = substr( $d, 3 ); }
						elseif ( str_starts_with( $d, 'yy' ) )  { $v .= date( 'y', $date ); $d = substr( $d, 2 ); }
						elseif ( str_starts_with( $d, 'mm' ) )  { $v .= date( 'm', $date ); $d = substr( $d, 2 ); }						
						elseif ( str_starts_with( $d, 'dd' ) )  { $v .= date( 'd', $date ); $d = substr( $d, 2 ); }						
						elseif ( str_starts_with( $d, 'HH' ) )  { $v .= date( 'H', $date ); $d = substr( $d, 2 ); }
						elseif ( str_starts_with( $d, 'hh' ) )  { $v .= date( 'h', $date ); $d = substr( $d, 2 ); }						
						elseif ( str_starts_with( $d, 'mi' ) )  { $v .= date( 'i', $date ); $d = substr( $d, 2 ); }
						elseif ( str_starts_with( $d, 'ss' ) )  { $v .= date( 's', $date ); $d = substr( $d, 2 ); }						
						elseif ( str_starts_with( $d, 'm' ) )   { $v .= date( 'n', $date ); $d = substr( $d, 1 ); }						
						elseif ( str_starts_with( $d, 'h' ) )   { $v .= date( 'g', $date ); $d = substr( $d, 1 ); }						
						elseif ( str_starts_with( $d, 'w' ) )   { $v .= date( 'w', $date ); $d = substr( $d, 1 ); }						
						elseif ( str_starts_with( $d, '`' ) )   { $v .= $d[1]; $d = substr( $d, 2 ); }
						else { $v .= $d[0]; $d = substr( $d, 1 ); }
					}

				}
				elseif ( $w[0] === 's' ) { // %10
					$w = substr( $w, 1 ); 
					$W = abs($w);
					if ( $w < 0 ) while ( strlen($v) < $W ) $v = ' ' + $v;
					else while ( strlen($v) < $W ) $v .= ' ';
				}
				elseif ( $w[0] === 'f' ) { //%f.2f
					$w = substr( $w, 1 ); 
					$v = sprintf( $w, $params[$k] );
				}
				else { // %10 // %10.1	
					$W = abs( $w );
					if ( is_numeric( $d ) && is_numeric( $v ) ) $v = '' . round( $v, $d );									
					if ( $w < 0 ) while ( strlen($v) < $W ) $v = ' ' . $v;
					else while ( strlen($v) < $W ) $v .= ' ';					
				}
				
				if ( isset( $W ) && $trunc && strlen($v) > $W ) $v = substr( $v, 0, $W ); //trunc
				$template = substring_replace( $template, $v, $i, $j );
			}
		}
		return $template;
	 } 
	function split( $d, $s, $n=0, $trimmed=false ) { //trimmed and no empty elements		
		$r = $n ? explode( $d, $s, $n ) : explode( $d, $s );
		if ($trimmed) $r = array_filter( array_map( 'trim', $r ) );
		while ( count($r) < $n ) array_push( $r, null);
		return $r;
	 }	 
	function is_native_function( $fn ) {
	    if ( ! function_exists( $fn ) ) return false;    
		$f = new ReflectionFunction( $fn );		
	    $filename = $f->getFileName();    		
		return strpos($filename, "eval()'d code") === false;	
	 }
boot;

//OOX allowed here
$redeclared = <<<'redeclare'
	//redeclare native functions here

redeclare;

function _call_( $fn, $n, ...$p ) {
	global $_FUNCTIONS;
	$n ??= count( $_FUNCTIONS[$fn] ) - 1;	
	$f = $_FUNCTIONS[ $fn ][ $n ];	
	if ( isset ( $f->f ) ) return ($f->f)( ...$p );	
	$f = $_FUNCTIONS[ $fn ][ $n ]->f = eval( "return function ({$f->ps}) {$f->fx};" );		
	return ($f)( ...$p );
}

	$unhide = function( $s ) {
		$j = strlen( $s );		
		for ( $i = 0; $i < $j; $i++ ) if ( ( $o = ord( $s[$i] ) ) > 127 ) 
			$s[$i] = chr( $o & 127 );	
		return $s;
	};

	$parse = function ( $s ) { global $unhide;
		global $_FUNCTIONS;
		$s = "\n".$s;
		if ( preg_match_all( '/[\n\r]\s*(function)\s+/', $s, $M, PREG_OFFSET_CAPTURE + PREG_SET_ORDER ) ) {
			foreach ($M as $f) {
				$i = $f[1][1];
				$k = strpos( $s, '(', $i );
				$p = strpos( $s, '{', $i );
				$q = str_paired( $s, $p );
				$fn = trim( substring( $s, $i+8, $k-1 ) ); 
				if ( ! preg_match( '/^[a-z]\w*$/', $fn ) ) throw new exception ('invalid function name:'.$fn);
				$ps = trim( substring( $s, $k, $p-1 ), "() \t\r\n" );
				$fx = trim( substring( $s, $p, $q ) );					
				$n = count($_FUNCTIONS[$fn] ?? [] );			
				if ($n) $fx = preg_replace( '/\b'.$fn.'_\(/', "_call_( '$fn', ".($n-1).", ", $fx ); //extended				
							
				$_FUNCTIONS[$fn][] = record( 				
					ps:			$unhide( $ps ),
					fx:			$unhide( $fx ),
				);
				for(; $i <= $q; $i++) if ( $s[$i] > ' ' ) $s[$i] = ' ';
			}		
		}

		if ( preg_match_all( '/[\n\r]\s*(overload)\s+function\s+/', $s, $M, PREG_OFFSET_CAPTURE + PREG_SET_ORDER ) ) {

			foreach ($M as $f) {
				$i = $f[1][1];
				$k = strpos( $s, '(', $i );
				$p = strpos( $s, '{', $i );
				$q = str_paired( $s, $p );
				$fn = trim( substring( $s, strpos( $s, 'ion', $i) + 4, $k-1 ) ); 
				if ( ! preg_match( '/^[a-z]\w*$/', $fn ) ) throw new exception ('invalid function name:'.$fn);
				$ps = trim( substring( $s, $k, $p-1 ), "() \t\r\n" );
				$fx = trim( substring( $s, $p, $q ) );	
				$ts = '_'.trim( join('_', array_map( fn($x) => trim( substr($x,0,strpos($x,'$')-1) ), 
					explode( ',', $ps ) ) ) , '_.');
				$ps =  join(', ', array_map( fn($x) => trim( substr($x,strpos($x,'$') ) ), 
					explode( ',', $ps ) ) );			
				$sig = $fn . $ts;
							
				$n = count($_FUNCTIONS[$sig] ?? [] );			
				if ($n) $fx = preg_replace( '/\b'.$fn.'_\(/', 
					'_call_( \''.$sig.'\', '.($n-1).', ', $fx ); //extended									
				$_FUNCTIONS[$sig][] = record( 				
					ps:			$unhide( $ps ),
					fx:			$unhide( $fx ),
				);
				for(; $i <= $q; $i++) if ( $s[$i] > ' ' ) $s[$i] = ' ';

				if ( ! isset( $_FUNCTIONS[ '_o_'.$fn ] ) ) {
					$router = <<<'router'
						{ global $_FUNCTIONS;
							$f = $f0 = '{fn}_' . join( '_', array_map( fn($x) => vartype($x), $x ) );									
							while ( $f ) {
								if ( isset( $_FUNCTIONS[$f] ) ) try {
										return _call_( $f, null, ...$x ); 
									} catch ( Exception $ex ) {
										throw new Exception( 'in overload function:' . $f . '(): ' . $ex->getMessage() );
									}
								$f = substr( $f, 0, strrpos( $f, '_' ) );
							}
							throw new exception ( 'overload signature not found: '.$f0 );
						}
					router;
					$_FUNCTIONS[ '_o_'.$fn ][] = record(
						ps:			'...$x',
						fx:			trim( str_replace( '{fn}', $fn, $router) ),
					);
				}			
			}
		}

		return $s;
	};

	$deref = function ( $s ) {	
		global $_FUNCTIONS;
		foreach( $_FUNCTIONS as $fn => $fx ) {
			if ( isset( $fx[0]->nored ) ) {
				$f = substr( $fn, 3 );
				if ( str_starts_with( $fn, '_o_' ) ) {
					$s = preg_replace( '/\b'.$f.'\(/', "_o_{$f}( ", $s );
					$s = preg_replace( '/_call_\(\''.$f.'\',null,/', "_call_('_o_{$f}',null,", $s );
				}
			}
			elseif ( str_starts_with( $fn, '_o_' ) ) {
				$f = substr( $fn, 3 );
				//overloaded
				$s = preg_replace( '/\b'.$f.'\(/', "_call_('_o_{$f}',null, ", $s );
				//it was extended too
				$s = preg_replace( '/_call_\(\''.$f.'\',null,/', "_call_('_o_{$f}',null,", $s );
			}
			else {
				$s = preg_replace( '/\b'.$fn.'\(/', "_call_('{$fn}',null,", $s );
			}
		}

		return $s;
	};

	$derefFuncs = function () {
		global $_FUNCTIONS, $deref;
		foreach( $_FUNCTIONS as $fn => $f ) {
			foreach ( $f as $n => $ff ) {
				$_FUNCTIONS[$fn][$n]->fx = $deref( $ff->fx );
			}
		}
	};

	$hideNCS = function( $s ) {
		$L = strlen( $s ); 
		$q = null;
		$i = $c = $C = 0;
		for( $j = 0; $j < $L ; $j++ ) {			
			switch( $s[ $j ] ) {				
				case "'": case '"':
					if ( !$C && !$c) {
						if ( $q == $s[ $j ] ) { 
							while ( ++$i < $j ) $s[$i] = chr( ord( $s[$i] ) | 128 );
							$q = null; 
						} 
						elseif ( ! $q ) { $q = $s[$j]; $i = $j; }
					}
					break;					
				case '/': 
					if ( ! $q ) {
						if ( $s[$j+1] == '*' ) { $C++; if ($C == 1) $i = $j-1; }
						elseif ($C) {
							if ( $s[$j-1] == '*' ) $C--; 
							if ($C == 0) while ( $i++ < $j ) if ($s[$i] > ' ') $s[$i] = ' ';												
						}
						elseif ( $s[$j+1] == '/' && !$c ) { $c++; $i = $j-1; }
					}
					break;
				case '#': if ( !$q && !$C && !$c ) { $c++; $i = $j-1; } break;
				case "\n": case "\r": 
					if ($c) {
						while ( $i++ < $j ) if ($s[$i] > ' ') $s[$i] = ' ';
						$c = 0; 
					}
					break;
			}		
		}
		return $s;
	};	

	$prePHP = function ( $file ) { global $parse, $hideNCS, $unhide, $prePHP, $_script;
		$p = 0;
		$src = trim( file_get_contents( $file ) ); 

		while ( ( $p = strpos( $src, '<?php', $p ) ) !== false ) {
			$q = strpos( $src, '?>', $p ); if ( $q === false ) $q = strlen( $src );
			$s = substring( $src, $p+5, $q-1);
			if ( stripos( $s, '#[noprephp]' ) === false ) {

				$s = $hideNCS( $s );	

				//require_once './gap_core.php';
				//require
				//include								
					if ( preg_match_all( '/[\n\r]\s*(require|require_once|include|include_once)\s+/', $s, $M, 
						PREG_SET_ORDER + PREG_OFFSET_CAPTURE ) ) {		
						foreach( $M as $m ) {
							$i = ( $m[0][1] );
							$i += strlen( $m[0][0] );
							$j = strpos( $s, ';', $i );
							$f = substring( $s, $i, $j-1 );
							$f = trim( $f,  ' \'');
							if ( ! isset( $_IMPORTED[ $f ] ) ) { 
								$_IMPORTED[ $f ] = true;
								$f = $prePHP( $f );										
							} else $f = '';	
							$s = substr_replace( $s, $f ? " import('$f'); #" : '#', $m[1][1], 0 );
						}
					}

				// lambda functions:	()=>  become fn()=>  
					$s = preg_replace( '/\W(\([^()]*\)\s*=>)/', ' fn$1', $s );

				// new inline object syntax:
					$i = 0;
					while ( ( $i = strpos( $s, '{' , $i+1 ) ) !== false ) { // [=(,] { name:'harry' }
						if ( $s[$i+1] !== '$' ) { // not {$...}
							$k = $i-1;
							while ($k && ord( $s[$k] ) <= 32 ) $k--;
							switch ( $s[$k] ) {
							case '=': case '(': case ',': 
								$j = str_paired( $s, $i );						
								$s[$j] = ')';
								$s = substring_replace( $s, 'record(', $i, $i );
							}
						}			
					}	

				$ss = $parse( $s );
				if ( strlen( trim($ss) ) == 0 ) {
					$src = substring_replace( $src, '', $p, $q+1 );
					$p--;
				}
				elseif ( $ss !== $s ) 
					$src = substring_replace( $src, $ss, $p+5, $q-1 );			

			}
			$p++;
		}
		if (strlen( $src) > 0) { $_script[$file] = $src; return $file; }
		return false;
	 };


eval( $boot ); //no OOX allowed
$parse( $hideNCS( $boot ) );

$redeclared = $parse( $hideNCS( $redeclared ) );
$derefFuncs();
$redeclared = $deref( $redeclared );
eval( $redeclared );

$_script_name = $_SERVER['SCRIPT_FILENAME'];
$prePHP( $_script_name );

//reduce functions that was not OOX'ed to direct calls - no need to do _call_()
foreach ( $_FUNCTIONS as $fn => $fx ) {	
	if ( count( $fx ) == 1 && ! is_native_function( $fn ) && ! in_array( $fn, $volitile) ) {			
		foreach ( $volitile as $vf ) { //funcs that contain volitile funcs is also volitile
			if ( str_contains( $fx[0]->fx, $vf.'(' ) ) goto next_fn;
			if ( str_contains( $fx[0]->fx, "_call_('$vf'" ) ) goto next_fn;
		}
		if ( ! is_callable( $fn ) ) {
			eval ( "function {$fn} ( {$fx[0]->ps} ) {$fx[0]->fx}" ); 
		}		
		$_FUNCTIONS[$fn][0]->nored = true;				
	}
	next_fn:
}


$derefFuncs();

foreach( $_script as $i => $s ) $_script[$i] = $unhide( $deref ( $s ) );

//echo '<pre>'.print_r( $_FUNCTIONS, true ); //die;
//foreach( $_script as $f => $s ) echo '<pre>'. $f . ':<br>'. str_replace('<','&lt;', trim($s)); die;
//echo '<pre>'. $_script_name . ':<br>'. str_replace('<','&lt;', trim( $_script[$_script_name] )); //die;

$cleanup = function() {
	foreach( ['cleanup','boot','redeclare','unhide','parse','deref','derefFuncs','hideNCS','prePHP'] as $x ) 
		unset($GLOBALS[$x]);
};
$cleanup();


eval( '?>' . $_script[ $_script_name ] );
die;
?>

