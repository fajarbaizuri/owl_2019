<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script   language=javascript1.2 src=js/rencana.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX();
$str="select nama,desa from ".$dbname.".rencana_lahan order by tanggalmulai";
$res=mysql_query($str);

$opt="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->nama."'>".$bar->nama."-".$bar->desa."</option>";
}
echo"<fieldset style='width:500px;'>
     <legend>".$_SESSION['lang']['koordlahanbaru']."</legend>
	 <table>
	 <tr><td>".$_SESSION['lang']['locationname']."</td><td colspan=2><select id=lokasi onchange=loadKoordinat(this.options[this.selectedIndex].value);>".$opt."</select></td><td>".$_SESSION['lang']['asl']."</td><td colspan=2><input type=text class=myinputtext id=dpl size=3 onkeypress=\"return angka_doang(event);\">dpl</td></tr>
	 <tr><td>".$_SESSION['lang']['latituded']."</td><td><input type=text class=myinputtext id=jls size=3 onkeypress=\"return angka_doang(event);\"></td><td>".$_SESSION['lang']['latitudem']."</td><td><input type=text class=myinputtext id=mls size=3 onkeypress=\"return angka_doang(event);\"></td><td>".$_SESSION['lang']['latitudes']."</td><td><input type=text class=myinputtext id=dls size=3 onkeypress=\"return angka_doang(event);\"></td></tr>
	 <tr><td>".$_SESSION['lang']['longituded']."</td><td><input type=text class=myinputtext id=jbt size=3 onkeypress=\"return angka_doang(event);\"></td><td>".$_SESSION['lang']['longitudem']."</td><td><input type=text class=myinputtext id=mbt size=3 onkeypress=\"return angka_doang(event);\"></td><td>".$_SESSION['lang']['longitudes']."</td><td><input type=text class=myinputtext id=dbt size=3 onkeypress=\"return angka_doang(event);\"></td></tr>	 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveRencanaKoordinat()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=batalRencanaKoordinat()>".$_SESSION['lang']['cancel']."</button>
	 ";	 
echo"</fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo"<table class=sortable width=100% cellspacing=1 border=0>
     <thead>
	 <tr class=rowheader>
		   <td>No</td>
		    <td>".$_SESSION['lang']['locationname']."</td>
			<td>".$_SESSION['lang']['latituded']."</td>
			<td>".$_SESSION['lang']['latitudem']."</td>
			<td>".$_SESSION['lang']['latitudes']."</td>
			<td>".$_SESSION['lang']['longituded']."</td>
			<td>".$_SESSION['lang']['longitudem']."</td>
			<td>".$_SESSION['lang']['longitudes']."</td>
			<td>".$_SESSION['lang']['asl']."</td>
			<td></td>
	 </tr>		
	 </thead>
	 <tbody id=container>
	 </tbody>
	 <tfoot>
	 </tfoot>
     </table>";
CLOSE_BOX();
echo close_body();
?>