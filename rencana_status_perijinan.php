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
     <legend>".$_SESSION['lang']['newlandmatters']."</legend>
	 <table>
	 <tr><td>".$_SESSION['lang']['locationname']."</td><td colspan=2><select id=lokasi onchange=loadRencanaStatus(this.options[this.selectedIndex].value);>".$opt."</select></td><td>".$_SESSION['lang']['keterangan']."</td><td colspan=2 rowspan=4><textarea class=mytextarea id=keterangan cols=25 rows=5 onkeypress=\"return tanpa_kutip(event);\"></textarea></td></tr>
	 <tr><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext onmouseover=setCalendar(this.id) id=tanggal size=10 onkeypress=\"return false;\"></td></tr>
	 <tr><td>".$_SESSION['lang']['pic']."</td><td><input type=text class=myinputtext id=pic size=15 onkeypress=\"return tanpa_kutip(event);\"></td></tr>	 
	 <tr><td>".$_SESSION['lang']['status']."</td><td><select id=status><option value='PROSES'>PROSES</option><option value='TUNDA'>TUNDA</option><option value='G A G A L'>G A G A L</option><option value='SELESAI'>SELESAI</option></select></td>
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveRencanaStatus()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=batalRencanaStatus()>".$_SESSION['lang']['cancel']."</button>
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
			<td>".$_SESSION['lang']['status']."</td>
			<td>".$_SESSION['lang']['pic']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
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