<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>Hutang TBS</b>'); //1 O
?>
<script type="text/javascript" src="js/keu_2hutangTBS.js" /></script>
<div id="action_list">
<?php

	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
for($x=0;$x<=15;$x++)
{
  $t=mktime(0,0,0,intval(date('m')-$x),15,date('Y')); 
   $optper.="<option value='".date('Y-m',$t)."'>".date('m-Y',$t)."</option>";
}
echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
			echo $_SESSION['lang']['kebun'].":<select id=kodeorg name=kodeorg style=width:200px>".$optOrg."</select>&nbsp;"; 
			echo $_SESSION['lang']['periode'].":<input type=\"text\" class=\"myinputtext\" id=\"tgl_1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\"> s.d. 
			     <input type=\"text\" class=\"myinputtext\" id=\"tgl_2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\">";
			echo"<button class=mybutton onclick=save_pil('preview')>".$_SESSION['lang']['save']."</button>";
			echo"<button class=mybutton onclick=save_pil('excel')>".$_SESSION['lang']['excel']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php 
CLOSE_BOX();
OPEN_BOX();

?>
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
   <div id="container">
   </div>
    </fieldset>
	<iframe id=excl name=excl frameborder=0 width=0 height=0 src=''></iframe>
<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>