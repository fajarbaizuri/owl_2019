<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_payrollBPJS.js'></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
include('master_mainMenu.php');

$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str);
$kar=1;
$prsh=4;
$pph=0;        
while($bar=mysql_fetch_object($res))
   {
    if($bar->id=='karyawanbpjs')
        $kar=$bar->value;
    if($bar->id=='perusahaanbpjs')
        $prsh=$bar->value;
   }
        
//+++++++++++++++++++++++++++++++++++++++++++++
	OPEN_BOX('','<b>BPJS SETUP:</b>');
	    echo"<div id=period>";
 			 $optc="<option value='".date('Y-m')."'>".date('m-Y')."</option>";
			  for($v=-2;$v<3;$v++)
			  {
			  	 $per=mktime(0,0,0,date('m')-$v,15,date('Y'));
			  	$optc.="<option value=".date('Y-m',$per).">".date('m-Y',$per)."</option>";
			  }
			echo"<fieldset style='width:300px;text-align:center;'>
 				 <legend><b>Porsi BPJS:</b>
				 </legend>
				 Karyawan &nbsp &nbsp :<input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 id=karyawan size=3 value=".$kar.">%<br>
				 Perusahaan:<input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 id=perusahaan size=3 value=".$prsh.">%<br>                                 
				 <button class=mybutton onclick=setJmsPorsi()>Save</button>
				 </fieldset>";			
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>