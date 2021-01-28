<?//@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zMaster.js></script> 
<script language=javascript src=js/zSearch.js></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<?
include('master_mainMenu.php');
$arr = "##periode";
OPEN_BOX();

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_spkht
      order by tanggal desc";
$res=mysql_query($str); 
$optperiode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optperiode.="<option value='".$bar->periode."'>".$bar->periode."</option>";       
}

?>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['summaryprogress']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td>
    <td><select id=periode style='width:200px;'><?php echo $optperiode; ?></select></td>
</tr>
<tr>
    <td colspan="3">
      <?php echo " <button onclick=\"zPreview('summary_slave_progress_spk','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'summary_slave_progress_spk.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
    <button onclick=\"zPdf('summary_slave_progress_spk','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>"; ?></td>
</tr>
</table>
</fieldset>
<?

CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend>
                 <div id='reportcontainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
                 </fieldset>"; 
CLOSE_BOX();
close_body();
exit;
?>
