<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>




<?php
OPEN_BOX();

echo "<thead><table class=sortable cellspacing=1 border=0><tr class=rowheader><td>No</td><td>nama depan</td><td>nama belakang</td><td>nama belakang</td><td>NIK</td>
<td>lokasitugas</td>
<td>sub bagian</td></tr>";


$a="select * from ".$dbname.".datakaryawan order by namakaryawan ";
//echo $a;
$b=mysql_query($a);
while($c=mysql_fetch_assoc($b))
{	
	$no+=1;
	$x=$c['namakaryawan'];
	$ind=explode(" ",$x);
	//print_r($ind);

	echo"  
		<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$ind[0]."</td>
			<td>".$ind[1]."</td>
			<td>".$ind[2]."</td>
			<td>".$c['nik']."</td>
			<td>".$c['lokasitugas']."</td>
			<td>".$c['subbagian']."</td>
		</tr>
";
}
echo "</table>";
CLOSE_BOX();




echo close_body();					
?>