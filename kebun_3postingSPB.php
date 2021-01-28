<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="application/javascript" src="js/kebun_3postingSPB.js"></script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['nospb'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariSpb()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	 </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="list_ganti">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['listSpb']?></legend>
<table cellspacing="1" border="0">
<thead>
<?php echo"
<tr class=\"rowheader\">
<td>No.</td>
		<td>".$_SESSION['lang']['nospb']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
<td>Action</td>
</tr>"
?>
</thead>
<tbody id="contain">
<?php
$lokasi=$_SESSION['empl']['lokasitugas'];
$limit=10;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where kodeorg='".$lokasi."' order by nospb desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	
	$sql="select * from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc  limit ".$offset.",".$limit."";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$res['nospb']."</td>
		<td align=center>".tanggalnormal($res['tanggal'])."</td>";
		if($res['updateby']!=$_SESSION['standard']['userid'])
		{
		echo"
		<td>
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$res['nospb']."','','kebun_spbPdf',event);\">";
		if($res['posting']<1)
		{
			echo"&nbsp;<a href=# onClick=\"postingData('".$res['nospb']."');\">".$_SESSION['lang']['belumposting']."</a>";
		}
		else
		{
			echo "&nbsp;".$_SESSION['lang']['posting'];
		}
		echo"</td>";}
		else
		{
			echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$res['nospb']."','','kebun_spbPdf',event);\"></td>";
		}
	
	}
	echo" </tr><tr class=rowheader><td colspan=11 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
				?>

</tbody>
</table>
</fieldset>
<?php CLOSE_BOX()?>
</div>
<?php 
echo close_body();
?>