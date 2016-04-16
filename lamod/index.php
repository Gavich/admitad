<?php
 
$id  	= isset( $_GET['id'] ) ? rtrim( trim( $_GET['id'] ), '/' ) : 'default';
$f	= fopen( 'redirects.txt', 'r' );
$urls	= array();
 
// The file didn't open correctly.
if ( !$f ) {
	echo 'Make sure you create your redirects.txt file and that it\'s readable by the redirect script.';
	die;
}
 
// Read the input file and parse it into an array
while( $data = fgetcsv( $f ) ) {
	if ( !isset( $data[0] ) || !isset( $data[1] ) )
		continue;
	
	$key = trim( $data[0] );
	$val = trim( $data[1] );
	$urls[ $key ] = $val;
}
 
// Check if the given ID is set, if it is, set the URL to that, if not, default
$url = ( isset( $urls[ $id ] ) ) ? $urls[ $id ] : ( isset( $urls[ 'default' ] ) ? $urls[ 'default' ] : false );

if ( $url ) {
	header( "X-Robots-Tag: noindex, nofollow", true );
	header( "Location: " .  $url, 302 );
	die;	
} else {
	echo '<p>Make sure yor redirects.txt file contains a default value, syntax:</p>
	<pre>default,http://example.com</pre>
	<p>Where you should replace example.com with your domain.</p>';
}