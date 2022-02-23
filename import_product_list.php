<?php
$path = getcwd();
require $path."/../../htdocs/master.inc.php";

$mysql = "/usr/bin/mysql";
$dbuser = "db username";
$dbpass = "db password";
$dbname = "db name";
$dumpdir = $path . "/sqldump";
$dir = scandir($dumpdir);


foreach($dir as $sqlfile) {
	if ($sqlfile == '.' or $sqlfile == '..') {continue;}
	$execmd  = $mysql . " -u ".$dbuser." -p".$dbpass. " ";
	$execmd .= $dbname . " < ";
	$execmd .= $dumpdir . "/" . $sqlfile;
	print($execmd . "\n");
	
	$output = null;
	$retvar = null;
	exec($execmd, $output, $retvar);
	
	if($retvar > 0) {
		print($output[0] . "\n");
		exit(1);
	}
	
}
