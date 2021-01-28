<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script   language=javascript1.2 src=js/rencana.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX();
$str="select nama,desa,tanggalmulai from ".$dbname.".rencana_lahan order by tanggalmulai";
$res=mysql_query($str);

$opt="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->nama."'>".$bar->nama."-".$bar->desa."-".tanggalnormal($bar->tanggalmulai)."</option>";
}

$str1="select * from ".$dbname.".rencana_lahan order by tanggalmulai";
$res1=mysql_query($str1);

echo"<fieldset style='width:500px;'>
     <legend>Status ".$_SESSION['lang']['lahan']."</legend>
	 <table>
	 <tr><td>".$_SESSION['lang']['locationname']."</td><td colspan=2><select id=lokasi onchange=loadRencanaUbahStatus(this.options[this.selectedIndex].value);>".$opt."</select></td>
	 <td>Status</td><td><select id=status>
	     <option value=0>".$_SESSION['lang']['proses']."</option>
		 <option value=2>".$_SESSION['lang']['fail']."</option>
		 <option value=1>".$_SESSION['lang']['redyforoperation']."</option>
		 </status></td>
	 </tr>
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=simpanStatusTerakhir()>".$_SESSION['lang']['save']."</button>
	 ";	 
echo"</fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo"<table class=sortable width=100% cellspacing=1 border=0>
     <thead>
	 <tr class=rowheader>
		   <td>No</td>
		    <td>".$_SESSION['lang']['locationname']."</td>
			<td>".$_SESSION['lang']['tanggal']."</td>
			<td>".$_SESSION['lang']['peruntukanlahan']."</td>
			<td>".$_SESSION['lang']['villagename']."</td>
			<td>".$_SESSION['lang']['subdistrict']."</td>
			<td>".$_SESSION['lang']['regency']."</td>
			<td>".$_SESSION['lang']['province']."</td>
			<td>".$_SESSION['lang']['country']."</td>
			<td>".$_SESSION['lang']['pic']."</td>
			<td>".$_SESSION['lang']['status']."</td>
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