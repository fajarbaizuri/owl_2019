<?php
//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$_POST['periode']!=''?$periode=$_POST['periode']:$periode=$_GET['periode'];
$_POST['tampil']!=''?$tampil=$_POST['tampil']:$tampil=$_GET['tampil'];
$_POST['pabrik']!=''?$pabrik=$_POST['pabrik']:$pabrik=$_GET['pabrik'];
//$tampil=$_POST['tampil'];
//$pabrik=$_POST['pabrik'];
//$method=;
//bulanan
//exit("Error".$method);
$str="select * from ".$dbname.".pabrik_produksi where tanggal like '".$periode."%'
	      and kodeorg='".$pabrik."'
		  order by tanggal asc";
    $res2=mysql_query($str);
    $res=mysql_query($str);
	//ubdah di sini ind
	//$no=0;
	
    while($datArr=  mysql_fetch_assoc($res2))
    {
		
        $tbs[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['tbsdiolah'];
        $jmOer[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['oer'];
        $jmOerPk[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['oerpk'];
    }
    
   
    if($_GET['method']!='excel')
    {
        $bg="";
        $brdr="0";
         echo "<fieldset><legend>".$_SESSION['lang']['list']."
	     <img src='images/icons/Basic_set_Png/statistics_16.png' class=resicon title='Grafics'  onclick=grafikProduksi('".$periode."','".$tampil."','".$pabrik."',event)>
		 <img src='images/skyblue/pdf.jpg' class=resicon title='PDF' onclick=laporanPDF('".$periode."','".$tampil."','".$pabrik."',event)>
	    <img src='images/skyblue/excel.jpg' class=resicon title='Spreadsheet' onclick=laporanEXCEL('".$periode."','".$tampil."','".$pabrik."',event)>      
            </legend>";
    }
    else
    {
        $bg=" bgcolor=#DEDEDE";
        $brdr="1";
    }
    $tab.="
      <table class=sortable cellspacing=1 border=".$brdr." width=100%>
	    <thead>
		  <tr class=rowheader>
		   <td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['kodeorganisasi']."</td>
		   <td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['tanggal']."</td>
		   <td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['tersedia']." (Kg.)</td>
		   <td align=center colspan=2  ".$bg.">".$_SESSION['lang']['tbsdiolah']." (Kg.)
                   </td>
                   <td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['kapasitasOlah']." (Ton/Jam)</td>
		   <td colspan=6 align=center  ".$bg.">".$_SESSION['lang']['cpo']."
		   </td>
		   <td colspan=6 align=center  ".$bg.">".$_SESSION['lang']['kernel']."
		   </td>
                    <td rowspan=2 align=center  ".$bg.">".$_SESSION['lang']['jampengolahan']."
		   </td>
                    <td rowspan=2 align=center  ".$bg.">".$_SESSION['lang']['jamstagnasi']."
		   </td>
                    <td rowspan=2 align=center  ".$bg.">".$_SESSION['lang']['sisa']." (Kg.)</td>
					 <td rowspan=2 align=center  ".$bg.">OER SDI</td>
		  </tr>  
		  
		  <tr class=rowheader> 
                    <td align=center  ".$bg.">HI</td><td align=center  ".$bg.">S/D</td> 

		   <td align=center  ".$bg.">".$_SESSION['lang']['cpo']." (Kg) HI</td>
                   <td align=center  ".$bg.">".$_SESSION['lang']['cpo']." (Kg) S/D</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center  ".$bg.">(FFa)(%)</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['kadarair']." (%)</td>
		   
		   <td align=center  ".$bg.">".$_SESSION['lang']['kernel']." (Kg) HI</td>
                   <td align=center  ".$bg.">".$_SESSION['lang']['kernel']." (Kg) S/D</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center  ".$bg.">(FFa) (%)</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center  ".$bg.">".$_SESSION['lang']['kadarair']." (%)</td>
		  </tr>
		</thead>
		<tbody>";
    $tgl=1;
	
       while($bar=mysql_fetch_object($res))
        {
			
		//ubah di sini	ind
		//$no+=1;	
           $sPengolahan="select sum(jamdinasbruto) as jampengolahan, sum(jamstagnasi) as jamstagnasi from ".$dbname.".pabrik_pengolahan 
               where kodeorg='".$bar->kodeorg."' and tanggal='".$bar->tanggal."'";
           //echo $sPengolahan."__\n";
           $qPengolahan=mysql_query($sPengolahan) or die(mysql_error($conn));
           $rPengolahan=mysql_fetch_assoc($qPengolahan);
           if(strlen($tgl)==1)
           {
               $agl="0".$tgl;
           }
           $tglServ=substr($bar->tanggal,0,8);
           $tab.="<tr class=rowcontent>";
           $tab.="<td>".$bar->kodeorg."</td>
                  <td>".tanggalnormal($bar->tanggal)."</td>
                  <td align=right>".number_format($bar->tbsmasuk+$bar->sisatbskemarin,0,'.',',')."</td>";
				  
				  
            $tbsSd=$tbs[$bar->kodeorg][$tglServ.$agl+1];	
            $tbsSd2=$tbs[$bar->kodeorg][$bar->tanggal];
            $tbsTot=$tbsSd2+$tbsSd;
            $des+=$tbsTot;
			
			
			//echo $tbsTot;
			
            //get cpo 
            $oerSd=$jmOer[$bar->kodeorg][$tglServ.$agl+1];
            $oerSd2=$jmOer[$bar->kodeorg][$bar->tanggal];
            $oerTot=$oerSd2+$oerSd;
            $oerTotal+=$oerTot;
			//echo $oerTotal;
            
			//get oer sdi
			$oersdi=$bar->tbsdiolah/$bar->oer;
			$oersdi2=$des/$oerTotal;
			/*echo $bar->tbsdiolah;
			echo "_________";
			echo $oerTotal;*/
            //get pk
            $oerpkSd=$jmOerPk[$bar->kodeorg][$tglServ.$agl+1];
            $oerpkSd2=$jmOerPk[$bar->kodeorg][$bar->tanggal];
            $oerpkTot=$oerpkSd+$oerpkSd2;
            $oerpkTotal+=$oerpkTot;
			//echo $oerpkTotal;
            //@$kpsitas=($bar->tbsdiolah/($rPengolahan['jampengolahan']-$rPengolahan['jamstagnasi']))/1000;   
			$hasilA1=number_format($rPengolahan['jampengolahan'],2,'.',',');
			$hasilB1=number_format($rPengolahan['jamstagnasi'],2,'.',',');
			@$kpsitas=($bar->tbsdiolah/(selisih_jam($hasilA1,$hasilB1)))/1000;   
			         
			$tab.="<td align=right>".number_format($bar->tbsdiolah,0,'.',',')."</td>
					<td align=right>".number_format($des,0,'.',',')."</td>
					<td align=right>".number_format($kpsitas,2,'.',',')."</td>";
            $tab.="<td align=right>".number_format($bar->oer,0,'.',',')."</td>
					<td align=right>".number_format($oerTotal,0,'.',',')."</td>";
            $tab.="<td align=right>".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffa,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoran,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarair,2,'.',',')."</td>";
             $tab.="<td align=right>".number_format($bar->oerpk,0,'.',',')."</td><td align=right>".number_format($oerpkTotal,0,'.',',')."</td>";
			 
            // $tab.="<td align=right>".(@number_format($oerTotal/$des*100,2,'.',','))."</td>
		   $tab.="<td align=right>".(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".number_format($bar->ffapk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarkotoranpk,2,'.',',')."</td>
		   <td align=right>".number_format($bar->kadarairpk,2,'.',',')."</td>";
		   
		
		
		 $hasilA=number_format($rPengolahan['jampengolahan'],2,'.',',');
			$hasilB=number_format($rPengolahan['jamstagnasi'],2,'.',',');
         $tab.="<td align=right>".number_format(selisih_jam($hasilA,$hasilB),2,'.',',')."</td>";
		 //$tab.="<td align=right>".selisih_jam($hasilA,$hasilB)."</td>";
		 // $tab.="<td align=right>".$rPengolahan['jampengolahan']."</td>";
           $tab.="<td align=right>".number_format($rPengolahan['jamstagnasi'],2,'.',',')."</td>
		   		  <td align=right>".number_format($bar->sisahariini,0,'.',',')."</td>";
			$tab.="<td align=right>".number_format($oersdi2,2,'.',',')."</td>";
		   $tab.="</tr>";
           $tgl++;
         }
		 
		 
		 $ha="select sum(tbsdiolah) as tbsdiolah,sum(oer) as oer,sum(oerpk) as oerpk from ".$dbname.".pabrik_produksi where tanggal like '".$periode."%' and kodeorg='".$pabrik."' ";
		// echo $ha;
		 $hi=mysql_query($ha);
		 $hu=mysql_fetch_assoc($hi);
		 	$tbsolahrt=$hu['tbsdiolah'];
			$oercpoolahrt=$hu['oer'];
			$oerkernelolahrt=$hu['oerpk'];
			//echo $tbsolahrt.__.$oerolahrt;exit();
				$oercpopersenrt=($oercpoolahrt/$tbsolahrt)*100;
				$oerkernelpersenrt=($oerkernelolahrt/$tbsolahrt)*100;
		 
		 
		 
		 $tab.="<thead><tr>
					<td colspan=8 align=center ".$bg.">Rata-Rata</td>
					<td ".$bg." align=right>".number_format($oercpopersenrt,2)."</td>	
					<td colspan=5 ".$bg."></td>
					<td ".$bg." align=right>".number_format($oerkernelpersenrt,2)."</td>	 
					<td colspan=7 ".$bg."></td>
		 		</tr>";	  
		
       $tab.="	</tbody>
		<tfoot>
		</tfoot>
	  </table>
	  </fieldset>";
      if($_GET['method']=='excel')
      {
                      //exit("Error:masuk".$method);
          $dte=date("YmdHis");
                      $nop_="laporan_produksi_".$dte;
            if(strlen($tab)>0)
            {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                    }
                }	
               closedir($handle);
            }
             $handle=fopen("tempExcel/".$nop_.".xls",'w');
             if(!fwrite($handle,$tab))
             {
              echo "<script language=javascript1.2>
                    parent.window.alert('Can't convert to excel format');
                    </script>";
               exit;
             }
             else
             {
              echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls';
                    </script>";
             }
            closedir($handle);
            }

      }
      else
      {
          echo $tab;
      }
	  /*
	   19.00 A
	   01.30 B
	   
	   17.70
	   */

function selisih_jam($jam1,$jam2){
	
	$jamA = explode(".", $jam1);
	$jamB = explode(".", $jam2);
	
	if(intval($jamA[1]) < intval($jamB[1])){
		$hasilM=60-intval($jamB[1]);	
		$hasilJ=(intval($jamA[0])-intval($jamB[0]))-1;
	}else{
		$hasilM=intval($jamA[1])-intval($jamB[1]);	
		$hasilJ=intval($jamA[0])-intval($jamB[0]);
	}
	
	return $hasilJ.".".$hasilM;
}
	  

?>