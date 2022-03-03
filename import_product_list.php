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

//set stock to zero in the products table!!!
//remove all stocks
print("removing stocks");
"mysql -u ".$dbuser." -p ".$dbpass." < update llx_product set stock=0;"
"mysql -u ".$dbuser." -p ".$dbpass." < update llx_product_stock set reel=0;"
