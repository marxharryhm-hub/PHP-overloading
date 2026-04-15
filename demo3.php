<pre>
Demo for prePHP3
------
Introduction
------------

Encapsulation, inheritance and polymorhism was the main drive force for building OOP.
Yet OOP is not the only paradigm with dominion over these concepts.

I would like to explore how some of these technical concepts in OO, can be realized 
in non-OO domains. The reason being the overhead in many OO languages, and 
syntactical verbosity.

Encaplulation is the collecting of variables (properties) into a stucture (a class).
Polymorhism is mapping functions (methods) to these structures. And then inheritance 
is the mechanism to progress from abstract classes to specialized implimentations thereof.

In contrast, Functional Programming (FP), focus on the following princples:
- Immutable data - no re-assignment
- Pure functions: functions are not allowed to have side effects
- First-class functions: Functions can be passed as arguments, and returned as results 
- Higher order functions: Functions that uses First-Class functions

Combining FP with OO could be advantagous.
I will discuss PHP without referring to its OO implimentation.

Encapsulation
-------------

prePHP propmotes the easy grouping of fields into structures, with the minimum of syntax, 
using a variadic function as intermediate. 
Consider the syntax for associative arrays:		
	$A = ['name' => 'harry', 'job' => 'developer' ];
Accessing it:
	echo $A['name'] . ' is a ' . $A['job'];

PHP does allow the creation of an onject, based on associative arrays:
	$O = (object) ['name' => 'harry', 'job' => 'developer' ];
	echo $O->name . ' is a ' . $O->job;
Such an object instantiates a stdClass, and as such does not have methods.
For our purposes we can call this a Structure, borrowing from C/C++.

But PHP also allows named parameters when calling functions, and variadic functions, 
accepting any number of arguments. Combining these allows prePHP to present:
	function obj( ...$x ) { return (object) $x; }

This simplifies structures:
	$O = obj( name:'harry', job:'developer' );
	echo $O->name . ' is a ' . $O->job;

And prePHP parsing allows for:
	$O = { name:'harry', job:'developer' }; //javascript style syntax
	echo $O.name . ' is a ' . $O.job;

It is clear how this syntactical simplification contributes to readibility, 
and less cross language barriers.


Polymorhism
-----------

This is allowing multiple defintions on a single function name, and then mapping particular definitions
to particular structures. In OO this usually is done through abstract classes and extended implementations.
But another concept is fairly well defined - function overloading. This is where the mapping is done based
on the type of the variables the function is called with, and the types of variables the functions accepts.
In OO the namespace problem is approached from the variable's persepctive:
	circle.draw();
where circle is some object, an instantiation of a Cirlce class, which is derived from perhaps a Shape class.
Typically the Shape class presents an abstract draw() method.

In a non-OO domain, a draw() function would be defined, which accepts as argument, a particular shape:
	draw( circle );
The instruction flow more naturally with the verb before the noun.
prePHP allows for the redeclaration of the function draw() (using the 'overload' keyword), 
where each version would accept a different type of value.
	overload function draw( circle $S ) { }
	overload function draw( square $S ) { }
	...etc.
Note important differences here. First, the selection of the function to be executed, 
the overload resolution, happens NOT on the type of the variable, but on the type of the data in the variable.
This takes advantage of the "untyped" nature of PHP. All values in PHP have a type, but the type is associated 
with the value, and not the variable - the container, in which the value is stored.
And then the overload resolution happens at runtime, not compile time.

I could define structures now as follows:
	$circle = {_type:'circle', x:100, y:100, radius:30};
or
	$circle = struct( circle: { x:100, y:100, radius:30 } );
Then to define a function to draw it:
	function draw( circle $c ) { }



To do:
extending/overriding an overloaded function
move router to end of script to preserve line numbers

<?php

	
	

	//just an example of a simplified for-loop syntax
		for $i = 1..10 {echo $i;} 
		echo '<br>';
		disp( { one:1, two:2 } ); //is obj if precede with (,=

	//simplified function definitions: 'is given by'
	//also works for lamdas:  array_map( ($x) ==> ...
		add($a, $b) ==> $a + $b;  // fat-arrow functions 
		add2($a, $b) ==> { return $a + $b; } // fat-arrow function with {body}
		disp( add(1,2), add2(1,2) ); //disp() is a polymorphic echo

	//extend and override
		//str() is an existing core function
		//this is an example of a sequence of extends, and calling previous versions
		xstr( $x ) ==> print_r($x, true );
		extend xstr( $x1 ) ==> 'v1 ' . xstr_($x1) ; //note the trailing underscore,
		extend xstr( $x2 ) ==> 'v2 ' . xstr_($x2) ; //to chain to previous versions.	
		extend xstr( $x3 ) ==> 'v3 ' . xstr_($x3) ;
		echo xstr( 1 ) . '<br>';
		//If you uncomment the next instruction, it nullify the above extensions,
		//and the output of the above echo changes accordingly
			//override xstr( $x ) ==> $x;
		//You cannot use the fn_() form to access an extended version in an overridden version.
		//You cannot use a particular version, override it, and then use the overridden version.
		//Overriding or extending a function does so globally !!!

	//Now we demo overload, where the exact version of the namesake function is based on the type of arguments
	//This form is polymorphism is runtime, and based on the type of the value passed, 
	//not the type associated with the variable!
	//The function valtype() is used by this mechanism to route the call to the correct overload.
	
	disp('<hr>');
	//More complex demo:
	//Define a function display(), that uses an overloaded function val() to format the output.
	//The overload resolution uses valtype(), which can also be extended.
	//This will call the relevant polymorhic function val()
	function display( ...$x ) {
		foreach ( $x as $i => $xx ) {
			if ( $i ) echo ' ';
			echo val( $xx );
		}
		echo '<br>';
	}
	//Here we defined independant overloaded versions of val() that will be called based
	//on the type returned from valtype(), for the actual value passed.
	val( $i ) ==> print_r( $i, true );				// this is a root, non-overloaded default
	overload val( integer $i ) ==> 'ival=' . $i;	// should be called for integers
	overload val( string $s ) ==> 'sval=' . $s;		// should be called for strings	
	
	//extend valtype() to return some custom type based on some arbitrary condition
	extend valtype( $v ) ==> $v === 1 ? 'number' : valtype_($v);
	// returns "number integer string"
	display( valtype(1), valtype(2), valtype( 'xxx' ) ); 
	//define an overload formatter now for exactly this new custom type
	overload val( number $i ) ==> '#' . $i;			
	
	display( 1, 2, 'x' );
	//disp( 1, 2, 'x' );
	// The output: '#1 ival=2 sval=x'
	// valtype() returned 'number' for '1', and this caused the overload resolution to call 
	// val( number $i ), when display() tried to display the 1.

	//Note - valtype() will, if the object has a property 'valtype', return its value
	//This allows for "typed objects"
	$to = {_type:'developer', name:'harry' };

	//The dot-operator has now two functions - getting an object's member, or concatenating strings.
	//If any of the two chars next to the dot is a not alphabetic, it is seen as concatenation.
	//If both are alphabetic, it is used as a member selection.
	disp( $to.name . ' is a ' . valtype( $to ) );

	display( [1,2,3] );
	//If the below definition is not added, and we did not define a "root" function with no overloading,
	//you will get: Exception: overload signature not found: val(list)
	//This indicates that display() could not find an appropiate val() with which to display it.
	//You can resolve this by either defining the root function, which will handle all uncatered types,
	//or by defining an overload:
	overload val( list $x ) ==> 'list=['.join(', ', $x) . ']';	

	//complex signatures - multiple parameters
	//the pattern of arguments forms the "signature" of the overload to call.
	//if not found, it will drop the last type, and search again, until a root overload.
	//if none found, it will fail and throw an exception.
	//This we call degenerative overloading.
	overload function do_x( integer $i ) { disp( "do1int: $i" ); }
	overload function do_x( integer $i, integer $j ) { disp( "do2int: $i $j" ); }
	overload function do_x( integer $i, integer $j, integer $k ) { disp( "do3int: $i $j $k" ); }
	overload function do_x( string ...$s ) { disp( "do strings:", ...$s ); } 
	do_x(2);
	do_x(2,3);
	do_x(2,3,4);
	do_x(2,3,4,5); //signature will degerate until the first type match is found
	do_x( 'a','b','c' ); //this signature will match degenaratively	
	//Only the last parameter of overload functions may be untyped
	//You cannot use 'mixed' to match any type
	//Parametes may have default values - useful if matched degeneratively.
	//Optional parameters not applicable to overloaded functions

//overriding an existing overload, will override ALL overloads.
//override function do_x( string ...$s ) { disp( "this replaces all overloads:", ...$s ); } 

//extending an existing overload, will override ALL overloads......... :-(
//I would like this not to ... but don't have an elegant solution yet..............

	//Degeneracy approaches inheritance. If a the arguments' signature does not match, 
	//overloading tries a generative signature - take note of the type expressions and 
	//operators (in priority sequence). 
	overload area(    shape $s) ==> 'abstract';
	overload area(   circle $s) ==> 'circle area is:' . pi() * $s.radius ** 2;
	overload area(rectangle $s) ==> 'rectangle area is:' . $s.width * $s.height;
	overload area(   square $s) ==> 'square area is:'. $s.length ** 2;

	disp( 'circle:',	area( struct( circle:	{ radius:10 } ) ) );
	disp( 'rectangle:', area( struct( rectangle:{ width:10, height:10 } ) ) );
	disp( 'square:',	area( struct( square:	{ length:10 } ) ) );	
	//the following call will use a 'shape_square' overload, else degenrate to a 'shape' overload
	disp( 'shape_square:', area( struct( shape_square: { length:10 } ) ) );	
	//the following call will use square or shape, to find an overload
	disp( 'square|shape:', area( { _type:'square|shape', length:10 } ) ); //here the _type syntax must be used
	//the following call will use a 'square' overload, then 'shape'
	disp( 'shape>square:', area( { _type:'shape>square', length:10 } ) );
	//the following call will use a 'square' overload, then 'shape'
	disp( 'square,shape:', area( { _type:'square,shape', length:10 } ) );	

	//You can also use type expressions when overloading a function:	
	overload do_x( staff|client ...$s ) ==> "do staff/client: " . str_struct( ...$s ); 
	disp( do_x( struct(  staff: {name:'harry'} ) ) );
	disp( do_x( struct( client: {name:'harry'} ) ) );

	disp( splitt( ',', overload_type_expression( 'a|b_1|2' ) ) );


	//There are alternative mechanisms in native PHP, which allows polymorhism inside a function.
	//The first uses getType() on an untyped parameter, which will return the type of the value passed  
	//( which is the basis for valtype() ).
	function do_show1( $x ) {
		if ( getType($x) === 'integer' ) echo 'interger:' . $x . '<br>';
		if ( getType($x) === 'string' ) echo 'string:' . $x . '<br>';
	}
	do_show1( 1 );
	do_show1( 'x' );
	//This approach limits the polymorhism to native PHP types, and does not allow new types to be processed, 
	//without modifying the existing function. In contrast, OOX does allow new forms of the function, without
	//modifying existing code.
	
	//The second alternative is to use named parameters with a variadic functions.
	//The passed values are packaged in an associative array.
	function do_show2( ...$x ) {
		if ( isset( $x['int'] ) ) echo 'int:' . $x['int'] . '<br>';
		if ( isset( $x['str'] ) ) echo 'str:' . $x['str'] . '<br>';
	}
	do_show2( int:1 );
	do_show2( str:'x' );
	//This approach is not limited to native or even define types, but totally dynamic.
	//It does require the arguments be named correctly though.
	//It also does not isolate previously written code like an overload approach does.
//--------


	//You can change the type of a struct by simply calling struct() again, or by setting _type.	
	$s = struct( { name: 'harry' } ); //untyped - disp() as stdClass
	disp($s);
	$s = struct( boss: $s ); //can be re-typed
	$s._type = 'staff>boss'; //can be re-typed with expression
	disp( $s );
	overload str( boss $s ) ==> 'His Great ' . strtoupper($s.name);
	overload str( staff $s ) ==> 'Hardworking Staff ' . strtoupper($s.name);


?>
