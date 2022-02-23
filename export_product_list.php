<?php
$path = getcwd();
require $path."/../../htdocs/master.inc.php";

$sql = "show tables";

$resql = $db->query($sql);

$dbuser = "database user name";
$dbpass = "database password";
$dbname = "database name";
$mysqldump = "/usr/bin/mysqldump";
$outdir = $path."/sqldump/";

if (!is_dir($outdir)){
	mkdir($outdir);
}

if ($resql) {
	while($res_obj = $db->fetch_array($resql)) {
		
		$tablename = $res_obj[0];
		$outfile = $outdir . substr($tablename, 4);
		$cmdstr  = $mysqldump . " -u ".$dbuser." -p".$dbpass." ".$dbname." ";
		$cmdstr .= $tablename." > ";
		$cmdstr .= $outfile.".sql";
		$must_dump = false;
		
		//categories
		if(strstr($tablename, "llx_categorie")) {
			$must_dump = true;				
		}
		
		//warehouses
		if(strstr($tablename, "llx_entrepot")) {
			$must_dump = true;				
		}
		
		//products
		if(strstr($res_obj[0], "llx_product")) {
			if($res_obj[0] == "llx_product_stock") {continue;}
			$must_dump = true;
		}
		
		// config
		if(strstr($res_obj[0], "llx_const")) {
			$must_dump = true;
		}
		
		if ($must_dump) {
			$output = null;
			$retval = null;
			print($cmdstr);
			exec($cmdstr, $output, $retval);
			if($retval > 0) {
				print($output[0] . "\n");
				exit(1);
			}
			print("\n");
		}
		
	};
} else {
		print("sql statement error");
		exit(0);
}


//print("---- \n\n\n");

//var_dump($conf);
