<?php
	$prePHP = obj( version: '3.5', update: 'built: 21 May 2026 - implements OOX' );

	$prePHP->notes = " This preload implements keywords: override, oveload and extend. It also implements 
	a number of general purpose functions.
	";
	
	$prePHP->FUNCTIONS = [];	

	//these are functions not affected by OOX, and do not use OOX
	function obj( ...$p ){ return (object)$p; }
	function struct( ...$T ) { 
		$t = array_key_first($T); $T[$t]->_type = $t ?: null; return $T[$t]; 
	 }	
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
		if ( $start < 0 ) $start += strlen( $hey ) + 1;
		return strrpos( substr( $hey, 0, $start), $needle ); 
	 }
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

			if ( strlen( $bra ) > 1 || strlen( $ket ) > 1 ) { 	
				$j = --$i; 				
				$i = strpos( $s, $bra, $i+1 ); 
				$j = strpos( $s, $ket, $j+1 );  
				$n = 1;
				redo:
					if ( $i !== false && $i < $j) {
						$n++;
						$i = strpos( $s, $bra, $i+1 );
						goto redo;
					}
					if ( $j !== false && ( $j < $i || $i === false) ) {
						$n--; if ( $n <= 1 ) return $j;
						$j = strpos( $s, $ket, $j+1 );
						goto redo;
					}
				return $n <= 1 ? $j : false;
			}

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
	function array_map_keys( $fn, $A ) {
		$R = [];
		foreach( $A as $k => $v ) $R[] = $fn( $k, $v );
		return $R;
	 }
	function split( $d, $s, $n=0 ) { 
		$r = $n ? explode( $d, $s, $n ) : explode( $d, $s );		
		for( $i = 0; $i < $n; $i++ ) $r[$i] ??= null;
		return $r;
	 }	 
	function splitt( $d, $s, $n=0, $default=null ) { //trimmed and no empty elements	
		if ($d[0] === '/' && $d[-1] === '/' && strlen($d) > 2 ) {
			echo $d;
			$r = $n ? preg_split( $d, $s, $n ) : preg_split( $d, $s );			
		}
		else $r = $n ? explode( $d, $s, $n ) : explode( $d, $s );
		$r = array_filter( array_map( 'trim', $r ) );
		for( $i = 0; $i < $n; $i++ ) $r[$i] ??= $default;		
		return $r;
	 }
	function hide( $s ) {
		$j = strlen( $s );		
		for ( $i = 0; $i < $j; $i++ ) $s[$i] = $s[$i] | "\x80";	
		return $s;
	 }
	function unhide( $s ) {
		$j = strlen( $s );		
		for ( $i = 0; $i < $j; $i++ ) $s[$i] = $s[$i] & "\x7F";
		return $s;
	 }
	function valtype( $v ) { 	
		if ($v === null) return 'null';
		if ( is_array( $v ) ) {
			if ( array_is_list( $v ) ) return 'list';			
			return 'array';
		}
		if ( gettype( $v ) == 'object') {		
			if ( isset( $v->_type ) && $v->_type ) return $v->_type;
			if ( count( get_class_methods($v) ) == 0 ) return 'record'; //objects with no methods
			return strtolower( get_class($v) );
		}
		return gettype( $v );
	 }
	function overload_type_expression( $s ) {
		if ( $k = strpos( $s, ',' ) ) return overload_type_expression( substr( $s, 0, $k ) ) . ',' .  
			overload_type_expression( substr( $s, $k+1 ) );
		if ( $k = strpos( $s, '>' ) ) return overload_type_expression( substr( $s, $k+1 ) ) . ',' . 
			overload_type_expression( substr( $s, 0, $k ) );		
		if ( $k = strpos( $s, '|' ) ) {
			$i = strposrev( $s, '_', $k ) ?: 0;
			$j = strpos( $s, '_', $k ) ?: strlen( $s );			
			$LS = substring( $s, 0, $k-1) . substring( $s, $j );
			$RS = ($i > 0 ? substring( $s, 0, $i) : '') . substring( $s, $k+1 );
			return overload_type_expression($LS) . ',' . overload_type_expression( $RS ); 
		}				
		return $s;
	 }
	function str_struct( $x ) {
		$T = $t = '';
		foreach ( $x as $k => $v ) 
			if ( $k == '_type' ) $T = $v;
			else $t .= $k . ':\'' . $v .'\',';
		return $T . ':{' . $t . '}';
	 }
	function str( $x ) { 
		if ($x === null) return 'null';
		if ($x === true) return 'true';
		if ($x === false) return 'false';
		return print_r( $x, true ); 
	 }
	function hideNCS( $s ) {
		$L = strlen( $s ); 
		$q = null;
		$i = $c = $C = 0;
		for( $j = 0; $j < $L ; $j++ ) {			
			switch( $s[ $j ] ) {	
				case "\\": $j++; break; //escape - ignore next char
				case "'": case '"': case '`': if ( $C || $c ) break;					
					if ( $q == $s[ $j ] ) { 						
						$len = $j - $i;
						$s = substr_replace( $s, str_repeat( "\x80", $len ) | substr($s, $i, $len), $i, $len );
						$q = null; 
					} 
					elseif ( ! $q ) { $q = $s[$j]; $i = $j + 1; }					
					break;					
				case '/': if ( $q ) break;
					if ( $s[$j+1] === '*' ) { $C++; if ($C === 1) $i = $j; }
					elseif ($C) {
						if ( $s[$j-1] === '*' ) $C--; 
						if ($C === 0) for (; $i <= $j; $i++ ) $s[$i] = $s[$i] > ' ' ? ' ' : $s[$i];		
					}
					elseif ( $s[$j+1] === '/' && !$c ) { $c++; $i = $j; }					
					break;
				case '#': if ( !$q && !$C && !$c ) { $c++; $i = $j; } break;
				case "\n": case "\r": if ( $c ) {
						for(; $i < $j; $i++ ) if ($s[$i] > ' ') $s[$i] = ' ';
						$c = 0; 
					}
					break;
			}
		}	
		return $s;
	 };	
	function split_param_types( $ps ) {
		$ps = splitt(',', $ps );
		$types = [];
		$vars = [];
		foreach ($ps as $p) { 
			$i = strpos( $p, '$'); if ($i === false) throw new exception( "overload signature invalid:" . $p );
			if ( $i===0 ) {// untyped ...?
				if ( count($types) > 0 ) throw new exception( "untyped parameter must be last in overload signature:" . $p );			
				$vars[] = $p;
			}
			elseif ($p[$i-1] === '.') { //variadic
				[$types[], $v ] = splitt( '.', $p, 2 );
				$vars[] = '.' . $v;
			} 
			else {
				[$types[], $v ] = splitt( '$', $p, 2 );
				$vars[] = '$' . $v;
			}
		}	
		return [ join( '_', $types ), join(', ', $vars ) ] ;
 	 } 
	function disp( ...$x ) { 		
		foreach ( $x as $i => $xx ) {
			if ( $i ) echo ' ';
			echo __call( 'str',  $xx ); //to use OOX versions of a function
		}
		echo '<br>';
	 }
	function formstr( $template, $params, $trunc = true ) {
		/*	
			{name} {name%9} {name%9.9} {name%x9.9} where x can be: n/d/s/f
			%n	number %n99.9 never truncated
			%n,	number with thousands separator
			%d	date 'yyyy-mm-dd HH`hmi:ss dow Month Mon fff yy hh m h w' never truncated
			%s	string
			%f	sprintf()	
		*/			

		if ( is_array( $params ) ) $params = (object) $params;
		//does the template provide defaults?
		if ( preg_match_all( '/\{(\w*)=([^}]*)/', $template, $M, PREG_SET_ORDER + PREG_OFFSET_CAPTURE   ) ) {
			foreach ( $M as $m ) $params->{$m[1][0]} ??= $m[2][0]; //keep first value			
			foreach ( array_reverse( $M ) as $m ) {
				$template = substr_replace( $template, '', $m[2][1]-1, strlen($m[2][0] )+1 );
			}
		}
		foreach ( $params as $k => $v ) {
			$v = trim( __call( 'str', $v ) );  //to use OOX versions of a function
			if ( is_numeric($k) ) $k++;
			$template = str_replace( '{' . $k . '}', $v, $template ); // {n}			
			$i = 0;
			while ( ( $i = strpos( $template, '{'.$k.'%', $i ) ) !== false ) { 				
				$j = strpos( $template, '}', $i );
				$fs = substring( $template, $i + strlen($k) + 2 , $j-1);				
				[$w,$d] = split( '.', $fs, 2, true );	
				$trunc_ = $trunc;
				if ( $w[0] === 'n' ) { //never truncated: %n99.9 %n99.9
					$trunc_ = false;
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
				
				if ( isset( $W ) && $trunc_ && strlen($v) > $W ) $v = substr( $v, 0, $W ); 
				$template = substring_replace( $template, $v, $i, $j );
			}
		}
		return $template;
	 } 
	
	function scan( ...$P ) {  //scan( in:'hay', from: 0, 'x', 'y', ['a','b'] )
		$from = $P['from'] ?? 0; unset( $P['from'] );
		if ( isset( $P['in'] ) ) { $hay = $P['in']; unset( $P['in'] ); }	
		else { $hay = $P[0]; unset( $P[0] ); }	
		if ( isset( $P['for'] ) ) { $P = $P['for']; if ( gettype( $P ) !== 'array' )  $P = [$P]; }
		$r = [];
		foreach ( $P as $p ) {
			if ( gettype( $p ) == 'array' ) {
				if ( $rr = scan( in: $hay, from: $from, for:$p ) ) $r[] = $rr;
			}
			else {				
				$i = $from - 1;
				while ( ( $i = strpos( $hay, $p, $i+1 ) ) !== false ) $r[] = $i; 
			}
		}
		sort($r);
		return $r;
	 }
		//disp( scan( 'abc', 'x' ), scan( 'abc', 'x' ) ? true : false );
		//disp( scan( 'abc', 'a' ), scan( 'abc', 'a' ) ? true : false );
		//disp( scan( 'abc', 'c', 'a' ), scan( 'abc', 'c', 'a' ) ? true : false );
		//disp( scan( 'xxxabc', 'b', 'a' ), scan( 'xxxabc', 'b', 'a' ) ? true : false );
		//disp( scan( 'xxxabc', ['a', 'b'] ), scan( 'xxxabc', ['a', 'b'] ) ? true : false );
		//foreach ( scan( for:'x', in:'xxxabc', from:1 )  as $i ) disp('i:', $i );	
		
	function __call( $fn, ...$X ) {
			global $prePHP;
			if ( $fn === 'valtype' || str_starts_with( $fn, '-valtype' ) ) $sigx = '';	//can only be extended, never overloaded
			else $sigx = join( '_', array_map( fn($x) => __call( 'valtype', $x ), $X ) );		
			$fnx = false;
			if ( $fn[0] === '-' ) { $fnx = substr( $fn,1 ); $fn = substr( $fn,1, -3 ); }

			foreach( explode( ',', overload_type_expression( $sigx ) ) as $sig ) {
				for(;;) {
					if ( isset ( $prePHP->FUNCTIONS[$fn][$sig] ) ) {
						if ( $fnx ) { 
							if ( ( $n = array_search( $fnx, $prePHP->FUNCTIONS[$fn][$sig] ) ) === false ) break;
							if ( --$n < 0 ) break;
						} else {						
							$n = count( $prePHP->FUNCTIONS[$fn][$sig] ) - 1;							
						}
						return $prePHP->FUNCTIONS[$fn][$sig][$n](...$X);
					}
					if ( !$sig ) break;
					$sig = substr( $sig, 0, strrpos( $sig, '_' ) );
				}
			}
			if ( function_exists ( $fn) ) return $fn( ...$X );
			throw new exception( 'overload signature not found:' . $fn . '(' . 
				join( '_', array_map( fn($x) => valtype($x), $X ) )	.')' );		
		 }

	function prePHP( $file=null, $src=null ) { global $prePHP;
		if ( $file ) {
			if ( isset( $prePHP->script[ $file ] ) ) return;
			$src = file_get_contents( $file );		
		}
		
		$i = -1; //for each php block in the file
		
		while ( ( $i = strpos( $src, '<?php', $i+1 ) ) !== false ) {
			$i += 5;
			$j = strpos( $src, '?>', $i ) ?: strlen( $src ) + 1;
			$j--;
			$php = substr( $src, $i, $j - $i + 1 );
			$php = hideNCS( $php );

								
			//for each library to be imported:
				if ( preg_match_all( '/[\n\r]\s*require/', $php, $M, PREG_OFFSET_CAPTURE + PREG_SET_ORDER ) ) {	
					foreach ( $M as $m ) { 	
						for ( $p = $m[0][1]; $php[$p] <= ' '; $p++ ); //put p on 'require					
						for ( $pp = $p; $php[$pp] > ' '; $pp++ );	  //put pp on filename	
						$q = strpos( $php, ';', $pp );				  //put q on end of filename
						$f = trim( substr( $php, $pp, $q - $pp), " \t'" ); //get filename
						//echo $f . "<br>";
						prePHP( file: unhide( $f ) );
						$php[$p] = '#';
					}	
				}

			

			// ==>
			$d = 0;
			$D = [];
			foreach ( scan($php, '==>') as $q ) {				
				$q += $d;				
				for( $p = $q-1; $php[$p] <= ' '; $p-- );	

				if ( $php[$p] !== ')' ) { 
					//declaration/no parameters ........
					for( $k = $p; $php[$k] > ' '; $k-- );
					$D[] = substring($php, $k+1, $p );
					$php[$p] = $php[$p] | "\xF0";					
					$php = substr_replace( $php, '()', $p+1, 0 );
					$p+=2;	$q+=2;	$d+=2;
					goto func;
				}
				
				if ( $php[$p] == ')' ) { 
					for( $p = $q-1; $php[$p] != '('; $p-- );
					if ( preg_match( '/\W/', $php[$p-1] ) ) { //lambda:	_($x)==>  :  fn($x)=>
						$php = substring_replace( $php, 'fn'.substring( $php, $p, $q-1), $p, $q);
						$d++;
					} else { func:
						for( ; $php[$p] > ' '; $p-- );
						for( $k = $q+3; $php[$k] <= ' '; $k++ );
						if ( $php[$k] == '{' ) { //long function:	func($x) ==> {
							$s = 'Function '. substring( $php, $p+1, $q-1 );						
							$php = substring_replace( $php, $s, $p+1, $k-1 );
							$d += strlen($s) - ($k-$p-1);
						} else { //long function:	inc($x) ==> $x+1
							for( $m = $k; $php[$m] != ';'; $m++ );

							$s = 'function '. substring( $php, $p+1, $q-1 ) . '{ return ' . 
								substring( $php, $k, $m ) . '}';
							$php = substring_replace( $php, $s, $p+1, $m );
							$d += strlen($s) - ($m-$p);							
						}
					}
				}								
			}
			foreach( $D as $d ) {
				$php = preg_replace( '/\b'.$d.'\b/', $d.'()', $php );
			}

			//short function syntax: f()==>
				//	add( $a, $b ) ==> $a + $b; 
				//  add( $a, $b ) ==> { return $a + $b; } 					  
				//  the left bracket for the parameters must touch the function name
/*
				$q = -1;
				while ( ( $q = strpos( $php, '==>', $q+1 ) ) !== false ) {
					$p = strposrev( $php, '(', $q );
					if ( $php[$p-1] > ' ' ) {
						while ( $php[$p] > ' ' ) $p--;
						for ( $m = $q+3; $php[$m] <= ' '; $m++ );
						if ( $php[$m] !== '{' ) {
							$m = strpos( $php, ';', $m );
							$php = substr_replace( $php, '}', $m+1, 1 );
							$php = substr_replace( $php, '{ return ', $q, 4 );
						} else
							$php = substr_replace( $php, '', $q-1, 4 );					
						$php = substr_replace( $php, 'function ', $p+1, 0 );
					}
				}	
*/

			//lambda functions:	()==> 
				//   ($a,$b) ==> $a <=> $b
				// here the left bracket must be preceded with a non-word
//				$php = preg_replace( '/\W(\([^()]*\)\s*)==>/', ' fn$1=>', $php );



			//for $i=1..10 { : _ii_ is reserved
				$php = preg_replace_callback( '/\bfor\s+\$([^=]+)=([^.]+)\.\.([^{]+)/', 
					function ($m) {
						$i = trim($m[1]);
						$i0 = trim($m[2]);
						$i9 = trim($m[3]);
						return '$_'.$i.$i.'_ = '.$i9.'; for( $'.$i.'='.$i0.'; $'.$i.' <= $_'.$i.$i.'_; $'.$i.'++ )' ;
					},
					$php );
			

			//OOX: overload/override/extend function str():
				if ( preg_match_all( '/[\n\r]\s*(overload|override|extend)\s+(function)\s+(\w+)/', 
					$php, $M, PREG_OFFSET_CAPTURE + PREG_SET_ORDER ) ) {	
					$d = 0;
					foreach ( $M as $m ) {			
						$p = $m[1][1] + $d;				//   over_l function xxx( type $x ) 			
						$k = strpos( $php, '(', $p );	//   p                  k         q 
						$q = strpos( $php, ')', $k );	//                    fn     ps							
						$fn = $m[3][0];			
						$ps = trim( substring( $php, $k+1, $q-1 ) );
						$ts = '';
						if ( $m[1][0] === 'overload' ) {				
							[$ts, $ps] = split_param_types( $ps );
							$ts = overload_type_expression( $ts );
						}
						$n = $prePHP->FUNCTIONS[ $fn ][ 0 ] ?? 1; 		
						$prePHP->FUNCTIONS[ $fn ][ 0 ] = $n + 1;
						foreach( explode(',', $ts) as $t ) $prePHP->FUNCTIONS[ $fn ][ $t ][] = "{$fn}_{$n}_";
						$fx = "function {$fn}_{$n}_({$ps})";
						$php = substring_replace( $php, $fx, $p, $q );						
						$d += strlen( $fx ) - ($q - $p + 1);						
					}
				}
		
			// new inline object syntax: {x:'ex'}			
				$p = 0;
				while ( ( $p = strpos( $php, '{' , $p+1 ) ) !== false ) { // ,:=( { name:'harry' }
					//must be '{name:'
					if ( $php[ $p+1 ] !== '$' ) { // not {$...}
						for ( $q = $p + 1;  $php[$q] <= 32; $q++);
						for (; $php[$q] > 32; $q++);
						if ( $php[$q-1] == ':' ) {
							for ( $k = $p - 1;  $k > 0 && $php[$k] <= ' '; $k--);
							switch ( $php[$k] ) {
							case ':': case '=': case '(': case ',': 
								$q = str_paired( $php, $p );							
								$php[$q] = ')';
								$php = substr_replace( $php, 'obj(', $p, 1 );
							}
						}
					}
				}

			// new OO dot operator, only $object.member, not  $o[$i].mem
				$php = preg_replace( '/(\$[\w._]+)\.(\w)/', '$1->$2', $php );			

			//enum {}
				if ( preg_match_all( '/[\n\r]\s*(enum\s*\{)/', $php, $M, PREG_OFFSET_CAPTURE ) ) {
					$d = 0;
					$n = 1;
					echo '<pre>';
					foreach( $M[1] as $m ) {
						$p = $m[1] + $d;
						$o = strpos( $php, '{', $p );
						$q = strpos( $php, '}', $o );
						$e = '';
						$E = substring( $php, $o+1, $q-1 );
						foreach( splitt( ',', $E ) as $v ) {
							$e .= "define('$v',$n);";
							$n <<= 1;
						}
						$e .= str_repeat( "\n", substr_count( $E, "\n" ) );										
						$php = substring_replace( $php, $e, $p, $q );
						$d += strlen($e) - ($q - $p + 1);
					}
				}				

			//publish $X;  ...make it global everywhere
				// $X ===> $GLOBALS['X']
				if ( preg_match( '/[\n\r]\s*publish\s/', $php, $M, PREG_OFFSET_CAPTURE ) ) {
					$p = strpos( $php, 'p', $M[0][1] );				
					$q = strpos( $php, ';', $p );
					$php[$p] = '#';
					foreach( splitt(',', substr( $php, $p + 7, $q - $p - 7 ) ) as $pv ) {
						$x = '/\\'.$pv.'\b/';
						$php = preg_replace( $x, '$GLOBALS[\'' . substr($pv,1). '\']', $php );
					}
				}
				if ( preg_match_all( '/[\n\r]\s*published\s/', $php, $M, PREG_OFFSET_CAPTURE ) ) {
					foreach ( array_reverse( $M[0] ) as $m ) {
						$p = strpos( $php, 'p', $m[1] );				
						for ($k = 0; $k < 10; $k++) { $php[$p+$k] = ' '; }
						$q = strpos( $php, '=', $p );						
						$pv = trim( substr( $php, $p+10, $q - $p - 10 ) ); 						
						$x = '/\\'.$pv.'\b/';
						$php = preg_replace( $x, '$GLOBALS[\'' . substr($pv,1). '\']', $php );						
					}
				}

			$src = substr_replace( $src, $php, $i, $j - $i + 1 );
		} //while
		

		$prePHP->script[ $file ] = $src;
	 } //function prePHP
	
	function deref() { global $prePHP;
		foreach ( $prePHP->script as $f => $src ) {
			if ( $f === 0 ) $src = "<?php\n" . $src. "\n?>";
			$i = -1; //for each php block in the file
			while ( ( $i = strpos( $src, '<?php', $i+1 ) ) !== false ) {
				$i += 5;
				$j = strpos( $src, '?>', $i ) ?: strlen( $src ) + 1;
				$j--;
				$php = substr( $src, $i, $j - $i + 1 );
				foreach( $prePHP->FUNCTIONS as $fn => $fx ) {
					$FN = '__call( \'' . $fn . '\',';
					$php = preg_replace_callback( '/function\s+'.$fn.'\(|\b('.$fn.')\(/', 
						function ( $m ) use( $FN ) { return isset( $m[1] ) ? $FN : $m[0]; }, 
						$php 
					); 
					$php = preg_replace( '/\b'.$fn.'_\(/', '__call( \'-\'.__function__ ,', $php  );
				}
				$php = unhide( $php );
				$src = substr_replace( $src, $php, $i, $j - $i + 1 );
			}
			$prePHP->script[ $f ] = $src;
		} //next script
	 }

$prePHP->script_name = $_SERVER['SCRIPT_FILENAME']; // get the script being called for
prePHP( file: $prePHP->script_name );	//get the script to be executed
deref();

//echo '<pre>'; print_r( $prePHP->FUNCTIONS ); die;
//echo '<pre>'; print_r( $prePHP->script ); die;
//print_r( split( "\n", $prePHP->script[ $prePHP->script_name ] ) ); die;

	foreach ( $prePHP->script as $_filename_ => $_script_ ) {
		try { 
			eval( '?>' . $_script_ ); 		
		} catch ( throwable $ex ) { 
			echo '<pre>'; print_r((array)$ex); 
			echo '<pre>';
			print_r( error_get_last() );
			print_r( split( "\n", $prePHP->script[ $_filename_ ] ) ); 
			die;
		}
	}

die;
?>
