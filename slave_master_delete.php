<?php
require_once('master_validation.php');
require_once('config/connection.php');

# Get Parameter
$tableName = $_POST['tableName'];
$data = $_POST;
unset($data['tableName']);

# Generate Condition
$where = "";
$i=0;
foreach($data as $key=>$row) {
	if($i==0){
		if(is_string($row)) {
			$where .= $key." = '".$row."'";
		} else {
			$where .= $key." = ".$row;
		}
	} else {
		if(is_string($row)) {
			$where .= " and ".$key." = '".$row."'";
		} else {
			$where .= " and ".$key." = ".$row;
		}
	}
	$i++;
}

# Generate Query

if ($tableName=='sdm_5gajipokok'){
//UPDATE  `fajar`.`sdm_5gajipokok` SET  `key` =  '0002' WHERE  `sdm_5gajipokok`.`tahun` =1993 AND  `sdm_5gajipokok`.`karyawanid` =0000000715 AND  `sdm_5gajipokok`.`idkomponen` =1;
$ha = "update ".$dbname.".".$tableName." set `key`='".$_SESSION['standard']['userid']."' where ".$where;
if(!mysql_query($ha)) {
	echo "DB Error : ".mysql_error($conn);
	exit;
}
}

$query = "delete from ".$dbname.".".$tableName." where ".$where;
if(!mysql_query($query)) {
	echo "DB Error : ".mysql_error($conn);
	exit;
}
?>