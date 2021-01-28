<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script   language=javascript1.2 src=js/rencana.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX();
echo"<fieldset style='width:500px;'>
     <legend>".$_SESSION['lang']['expansionplan']."</legend>
	 <table cellspacing=1 border=0>
	 <tr><td align=right>
	 ".$_SESSION['lang']['locationname']."
	 </td><td><input type=text id=namalokasi class=myinputtext maxlength=30 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 <td align=right>
	 ".$_SESSION['lang']['startdate']."</td><td><input type=text id=tanggal class=myinputtext maxlength=10 onkeypress=\"return false;\" size=10 onmousemove=setCalendar(this.id) value=".date('d-m-Y')."></td>
	 </tr>	 
	 <tr><td align=right>
	 ".$_SESSION['lang']['villagename']."
	 </td><td><input type=text id=desa class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
    <td align=right>
	 ".$_SESSION['lang']['subdistrict']."</td><td><input type=text id=kecamatan class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 </tr>
	 <tr><td align=right>
	 ".$_SESSION['lang']['regency']."</td><td><input type=text id=kabupaten class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
     <td align=right>
	 ".$_SESSION['lang']['province']."</td><td><input type=text id=provinsi class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 </tr>
	 
	 <tr>
	 	<td align=right>
	 ".$_SESSION['lang']['country']."</td><td><input type=text id=negara class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 	<td align=right>
	 ".$_SESSION['lang']['purposedfor']."</td><td><input type=text id=peruntukan class=myinputtext maxlength=45 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 </tr>	 
	 
	 <tr><td align=right>
	 ".$_SESSION['lang']['pic']."</td><td><input type=text id=kontak class=myinputtext maxlength=25 onkeypress=\"return tanpa_kutip(event);\" size=25></td>
	 <td align=right>Luas Lahan</td><td><input type=text id=luas class=myinputtext maxlength=10 onkeypress=\"return angka_doang(event);\" size=25></td>
	 </tr>
	 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=simpanRencanalahan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=batalRencanalahan()>".$_SESSION['lang']['cancel']."</button>	 
     </fieldset>";
echo "<div style='height:350px;width:100%;overflow:scroll;'>
      <table class=sortable border=0 cellspacing=1>
	  <thead>
	  <tr>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['locationname']."</td>
	  <td>".$_SESSION['lang']['startdate']."</td>
	  <td>".$_SESSION['lang']['purposedfor']."</td>
	  <td>".$_SESSION['lang']['villagename']."</td>
	  <td>".$_SESSION['lang']['subdistrict']."</td>
	  <td>".$_SESSION['lang']['regency']."</td>
	  <td>".$_SESSION['lang']['province']."</td>
	  <td>".$_SESSION['lang']['country']."</td>	
	  <td>".$_SESSION['lang']['pic']."</td>  
	  <td>Luas Lahan</td>  
	  <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	  </thead>
	  <tbody id=container>";
$str1="select * from ".$dbname.".rencana_lahan order by tanggalmulai desc";
if($res1=mysql_query($str1))
{
	$no=0;
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		   <td>".$no."</td>
		    <td>".$bar1->nama."</td>
			<td>".tanggalnormal($bar1->tanggalmulai)."</td>
			<td>".$bar1->peruntukanlahan."</td>
			<td>".$bar1->desa."</td>
			<td>".$bar1->kecamatan."</td>
			<td>".$bar1->kabupaten."</td>
			<td>".$bar1->provinsi."</td>
			<td>".$bar1->negara."</td>
			<td>".$bar1->kontak."</td>
			<td align=right>".$bar1->luas."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->peruntukanlahan."','".$bar1->desa."','".$bar1->kecamatan."','".$bar1->kabupaten."','".$bar1->provinsi."','".$bar1->negara."','".tanggalnormal($bar1->tanggalmulai)."','".$bar1->nama."','".$bar1->kontak."','".$bar1->luas."');\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delRencana('".$bar1->nama."');\"></td></tr>";
	}	 
}	  
echo "</tbody>
	  <tfoot>
	  </tfoot>
	  </table>
	  </div>";	 
CLOSE_BOX();
echo close_body();
?>
