<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/keu_laporan.js"></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>ARUS KAS LANGSUNG</b>');


//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".keu_jurnaldt
      order by periode desc";
	  
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	

if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL')
{   
        //=================ambil PT;  
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
              where tipe='PT'
                  order by namaorganisasi";
        $res=mysql_query($str);
        $optpt="";
        while($bar=mysql_fetch_object($res))
        {
                $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }

        //=================ambil gudang;  
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
                        or tipe='HOLDING' or tipe='TRAKSI')  and induk!=''
                        ";
        $res=mysql_query($str);
        $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
        while($bar=mysql_fetch_object($res))
        {
                $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }
		
		
		//get existing kasbank
		
		$str="select noakun,namaakun  from ".$dbname.".keu_5akun where (noakun like '11101%' or noakun like '11102%') order by namaakun desc";
		
		$res=mysql_query($str);
		#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
		$optkasbank="<option value=''>".$_SESSION['lang']['all']."</option>";
		while($bar=mysql_fetch_object($res))
		{
		$optkasbank.="<option value='".$bar->noakun."'>".$bar->namaakun."</option>";
		}	
}
else
{
        $optpt="";
        $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
         $optgudang.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";
		 //get existing kasbank
		$str="select noakun,namaakun  from ".$dbname.".keu_5akun where (noakun like '11101%' or noakun like '11102%') and pemilik='".$_SESSION['empl']['lokasitugas']."' order by namaakun desc";
		
		$res=mysql_query($str);
		#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
		$optkasbank="<option value=''>".$_SESSION['lang']['all']."</option>";
		while($bar=mysql_fetch_object($res))
		{
		$optkasbank.="<option value='".$bar->noakun."'>".$bar->namaakun."</option>";
		}	
   
}

echo"<fieldset>
     <legend>Arus Kas Langsung</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value),document.getElementById('kasbank').selectedIndex=0; >".$optpt."</select>
	 
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=ambilKasFlow(document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value,this.options[this.selectedIndex].value)>".$optgudang."</select>
	  Kas / Bank : "."<select id=kasbank onchange=hideById('printPanel')>".$optkasbank."</select>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	
       
        
	 <button class=mybutton onclick=getLaporanArusKasLangsung()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'keu_lap_arus_kas_langsung_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div id=container style='width:100%;height:359px;overflow:scroll;'>
      
     </div>";
CLOSE_BOX();
close_body();
?>