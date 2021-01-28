<?php
if(!isset($_SESSION)) {
	session_start();
}
include_once('lib/zLib.php');

$param = $_POST;
$proses = $_GET['proses'];
switch($proses) {
	case 'filterData':
		$qBjr = selectQuery($dbname,'kebun_5bjrharian','*',"tanggal='".tanggalsystem($param['tanggal']).
			"' and left(afdeling,4)='".$_SESSION['empl']['lokasitugas']."'");
		$resBjr = fetchData($qBjr);
		
		$res = '';
		foreach($resBjr as $key=>$row) {
			$res .= "<tr id='tr_".$key."' class='rowcontent'>";
			$res .= "<td id='tahuntanam_".$key."' value='".$row['tahuntanam']."'>".$row['tahuntanam']."</td>";
			$res .= "<td id='afdeling_".$key."' value='".$row['afdeling']."'>".$row['afdeling']."</td>";
			$res .= "<td id='tanggal_".$key."' value='".tanggalnormal($row['tanggal'])."'>".tanggalnormal($row['tanggal'])."</td>";
			$res .= "<td id='bjr_".$key."' value='".$row['bjr']."'>".$row['bjr']."</td>";
			
			// Edit
			$res .= "<td><img id='editRow".$key."' title='Edit' class=\"zImgBtn\" src=\"images/001_45.png\"";
			$res .= "onclick=\"editRow(".$key.",'##tahuntanam##afdeling##tanggal##bjr','##".$row['tahuntanam'].
				"##".$row['afdeling']."##".tanggalnormal($row['tanggal'])."',null,'kebun_5bjrharian')\"";
			$res .= "></td>";
			
			// Delete
			$res .= "<td><img id='delRow".$key."' title='Hapus' class=\"zImgBtn\" src=\"images/delete_32.png\"";
			$res .= "onclick=\"delRow(".$key.",'##tahuntanam##afdeling##tanggal','##".$row['tahuntanam'].
				"##".$row['afdeling']."##".tanggalnormal($row['tanggal'])."##".$row['bjr']."')\"";
			$res .= "></td>";
			
			$res .= "</tr>";
		}
		if(empty($res)) {
			$res .= "<tr><td colspan=6>Data Kosong</td></tr>";
		}
		echo $res;
		break;
}

?>