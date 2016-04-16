<?php 
function dellinks($content) {
	    //$content = preg_replace('|<a[^>]+>([^<]+)</a>|is', '$1', $content);
		$content = preg_replace('|<a[^>]+>([^<]+)</a>|is', '', $content);
	    return $content;
	}
	

$dbname = "working_mama";

$username = "root";
$password = "Get#1forLM";
$hostname = "localhost"; 

//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password) 
 or die("Unable to connect to MySQL");
echo "Connected to MySQL<br>";

//select a database to work with
$selected = mysql_select_db("$dbname",$dbhandle) 
  or die("Could not select $dbname");

//execute the SQL query and return records
$result = mysql_query("SELECT comment_ID, comment_content FROM wp_comments;");

//fetch tha data from the database 
while ($row = mysql_fetch_array($result)) {
   //echo "comment_ID:".$row{'comment_ID'}." comment_content:".$row{'comment_content'};
   $new_comment=dellinks($row{'comment_content'});
 //echo "comment_ID:".$row{'comment_ID'}." comment_content:".$new_comment;
 $update_query="Update wp_comments set comment_content='".$new_comment."' where comment_ID=".$row{"comment_ID"}.";";
 echo $update_query.'<BR>';
 $updt = mysql_query($update_query);
}
//close the connection
mysql_close($dbhandle);		
		
?>