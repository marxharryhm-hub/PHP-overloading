Latest version: moina3
This pre-processor is to be linked in php.ini:
auto_prepend_file = "C:\inetpub\wwwroot\moina3.php"

Using this code - you do so with no guarentees, no support. It has no copyright, and no licensing required.
But you use it at your own risk!!!
This is only a technology demonstration version.

This will then allow novel syntax like:
  overload function str( string $s ) { ....snipped
  overload function str( integer $i ) { ... snipped
You can then define code that use this, for ex.:
function disp( ...$x) { foreach( $x as $xx ) echo str( $xx ); }
The correct version of str() will then be selected runtime, based on the type of the value passed in.
You can change the type of $x in disp() and it will still call the correct function now for the new type.

You can also do override and extend - see documentation in the preprocessor.

A more user friendly description is on medium:
https://medium.com/@marxharry.hm/extending-php-ea428fd58bb2
