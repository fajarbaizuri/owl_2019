<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
switch ($proses){
        case 'preview':
                $param=$_POST; 
            break;
         case 'excel':
                $param=$_GET;    
             break;
}
		  
		//ambil kebun per afdeling
		 $str="SELECT namaorganisasi,kodeorganisasi,induk FROM ".$dbname.".`organisasi` WHERE `tipe` =  'AFDELING' and  induk like '".$param['idKebun']."' and  namaorganisasi like '%AFD%'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
			$AFDNM[$bar->kodeorganisasi]=$bar->induk;
        }
		//ambil jumlah PEMANEN KHT sesuai organisasi
		
		$str="select count(distinct namakaryawan) as jumlah,sum(hk) as hk,left(tanggal,7) as bulan,left(afd,6) as afd,sum(hasilkerja) as janjang,sum(hasilkerjakg) as kg  from ".$dbname.".kebun_evaluasi_panen where tipe in ('KHT','KBL')and left(tanggal,7) ='".$param['periode']."'  GROUP BY left(tanggal,7),left(afd,6)"; 
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
			
			$PEMANENKHT[$bar->afd]=$bar->jumlah;
			$HKKHT[$bar->afd]=$bar->hk;
			$HASIL_JJG_KHT[$bar->afd]=$bar->janjang;
			$HASIL_KG_KHT[$bar->afd]=$bar->kg;
        }
		//ambil jumlah PEMANEN BHL sesuai organisasi
		$str="select count(distinct namakaryawan) as jumlah,sum(hk) as hk,left(tanggal,7) as bulan,left(afd,6) as afd,sum(hasilkerja) as janjang,sum(hasilkerjakg) as kg  from ".$dbname.".kebun_evaluasi_panen where tipe in ('KHL','Kontrak') and left(tanggal,7) ='".$param['periode']."'  GROUP BY left(tanggal,7),left(afd,6)";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
			$PEMANENBHL[$bar->afd]=$bar->jumlah;
			$HKBHL[$bar->afd]=$bar->hk;
			$HASIL_JJG_BHL[$bar->afd]=$bar->janjang;
			$HASIL_KG_BHL[$bar->afd]=$bar->kg;
        } 
		
       
 $stream.="Evaluasi Kinerja ".$param['idKebun']." Periode:".$param['periode']."
         <table class=sortable border=0 cellspacing=1>
          <thead class=rowheader>
          <tr>
			 <td rowspan=\"4\" align=\"center\" bgcolor=\"#CCCCCC\">Kebun</td>
             <td rowspan=\"4\" align=\"center\" bgcolor=\"#CCCCCC\">Afdeling</td>
             <td colspan=\"18\" align=\"center\" bgcolor=\"#CCCCCC\">Realisasi Kinerja</td>   
          </tr>
		  <tr>
			 <td colspan=\"6\" align=\"center\" bgcolor=\"#CCCCCC\">Tenaga Kerja(Orang)</td>
             <td colspan=\"3\" align=\"center\" bgcolor=\"#CCCCCC\">Rata-Rata Kehadiran Periode:".$param['periode']." (Hari) </td>
             <td colspan=\"6\" align=\"center\" bgcolor=\"#CCCCCC\">Rata-Rata Output PerHK</td>   
			 <td colspan=\"3\" align=\"center\" bgcolor=\"#CCCCCC\">BJR</td>   
          </tr>
		  <tr>
			 <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHT</td>
             <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHL</td>
             <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">Jumlah</td>   
			 
			 <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHT</td>
             <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHL</td>
             <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">Jumlah</td>   
			 
			 <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHT</td>
             <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHL</td>
             <td colspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">Jumlah</td>   
			 
			 <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHT</td>
             <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">KHL</td>
             <td rowspan=\"2\" align=\"center\" bgcolor=\"#CCCCCC\">Jumlah</td>   
          </tr>
		  <tr>
			 <td align=\"center\" bgcolor=\"#CCCCCC\">KHT</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Persen</td>
			 <td align=\"center\" bgcolor=\"#CCCCCC\">KHL</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Persen</td>
			 <td align=\"center\" bgcolor=\"#CCCCCC\">Jumlah</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Persen</td>
			 
			 
			 
			 <td align=\"center\" bgcolor=\"#CCCCCC\">Jjg</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Kg</td>
			 
			 <td align=\"center\" bgcolor=\"#CCCCCC\">Jjg</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Kg</td>
			 
			 <td align=\"center\" bgcolor=\"#CCCCCC\">Jjg</td>
             <td align=\"center\" bgcolor=\"#CCCCCC\">Kg</td>
			 
			 
             
          </tr>
		  </thead><tbody>
          ";
      //jumlah hari
      $mk=mktime(0,0,0,substr($param['periode'],5,2),15,substr($param['periode'],0,4));
      $jhari=date('j',$mk);
      $a=0;
      
          foreach($AFDNM as $Key=>$Value){
			  $PKHT=!empty($PEMANENKHT[$Key])?$PKHT=$PEMANENKHT[$Key]:$PKHT=0;
			  $PBHL=!empty($PEMANENBHL[$Key])?$PBHL=$PEMANENBHL[$Key]:$PBHL=0;
			  $TotP=$PKHT+$PBHL;
			  $SenKHT=($PKHT/$TotP)*100;
			  $SenBHL=($PBHL/$TotP)*100;
			  $senTot=$SenKHT+$SenBHL;
			  
			  $PHK_KHT=!empty($HKKHT[$Key])?$PHK_KHT=$HKKHT[$Key]:$PHK_KHT=0;
			  $PHK_BHL=!empty($HKBHL[$Key])?$PHK_BHL=$HKBHL[$Key]:$PHK_BHL=0;
			  $THK_= $PHK_KHT+$PHK_BHL;
			  
			  //rata-rata hk
			  $RKHT=$PHK_KHT/$PKHT;
			  $RBHL=$PHK_BHL/$PBHL;
			  $TRHK=$THK_/$TotP;
			  
			  
			
			  //janjang dan kg
			  $JJG_KHT=!empty($HASIL_JJG_KHT[$Key])?$JJG_KHT=$HASIL_JJG_KHT[$Key]:$JJG_KHT=0;
			  $KG_KHT=!empty($HASIL_KG_KHT[$Key])?$KG_KHT=$HASIL_KG_KHT[$Key]:$KG_KHT=0;
			  
			  $JJG_BHL=!empty($HASIL_JJG_BHL[$Key])?$JJG_BHL=$HASIL_JJG_BHL[$Key]:$JJG_BHL=0;
			  $KG_BHL=!empty($HASIL_KG_BHL[$Key])?$KG_BHL=$HASIL_KG_BHL[$Key]:$KG_BHL=0;
			  
			  $STRJJG=$JJG_KHT+$JJG_BHL;
			  $STRKG=$KG_KHT+$KG_BHL;
			  
			  //-----
			  $TRJJG_KHT=$JJG_KHT/$PHK_KHT;
			  $TRKG_KHT=$KG_KHT/$PHK_KHT;
			  
			  $TRJJG_BHL=$JJG_BHL/$PHK_BHL;
			  $TRKG_BHL=$KG_BHL/$PHK_BHL;
			  
			  $SSTRJJG=$STRJJG/$THK_;
			  $SSTRKG=$STRKG/$THK_;
			  
			  //bjr
			  $UBJR_KHT=$TRKG_KHT/$TRJJG_KHT;
			  $UBJR_BHL=$TRKG_BHL/$TRJJG_BHL;
			  $TBJR_R=$SSTRKG/$SSTRJJG;
			  
			  
			  
			  
			  
			  
			  $stream.="<tr class=rowcontent>
                           <td width=\"70px\" align=center>".$Value."</td>
                           <td width=\"70px\" align=center>".$Key."</td>
						   <td width=\"50px\" align=right>".$PKHT."</td>
						   <td width=\"50px\" align=right>".round($SenKHT)."%</td>
						   <td width=\"50px\" align=right>".$PBHL."</td>
						   <td width=\"50px\" align=right>".round($SenBHL)."%</td>
						   <td width=\"50px\" align=right>".$TotP."</td>
						   <td width=\"50px\" align=right>".round($senTot)."%</td>
						   
						   <td width=\"50px\" align=right>".round($RKHT)."</td>
						   <td width=\"50px\" align=right>".round($RBHL)."</td>
						   <td width=\"50px\" align=right>".round($TRHK)."</td>
						   
						   <td width=\"60px\" align=right>".number_format(round($TRJJG_KHT))."</td>
						   <td width=\"60px\" align=right>".number_format(round($TRKG_KHT))."</td>
						   <td width=\"60px\" align=right>".number_format(round($TRJJG_BHL))."</td>
						   <td width=\"60px\" align=right>".number_format(round($TRKG_BHL))."</td>
						   <td width=\"60px\" align=right>".number_format(round($SSTRJJG))."</td>
						   <td width=\"60px\" align=right>".number_format(round($SSTRKG))."</td>
						   
						   <td width=\"60px\" align=right>".number_format(($UBJR_KHT),2)."</td>
						   <td width=\"60px\" align=right>".number_format(($UBJR_BHL),2)."</td>
						   <td width=\"60px\" align=right>".number_format(($TBJR_R),2)."</td>
						   
                     </tr>";
			$TPKHT+=$PKHT;
			$TPBHL+=$PBHL;
			$TTotP+=$TotP;
			
			$TRPKHT+=$PHK_KHT;
			$TRPBHL+=$PHK_BHL;
			$TRTotP+=$THK_;
			
			$AJJG_KHT+=$JJG_KHT;
			$AKG_KHT+=$KG_KHT;
			  
			$AJJG_BHL+=$JJG_BHL;
			$AKG_BHL+=$KG_BHL;
			  
			$ASTRJJG+=$STRJJG;
			$ASTRKG+=$STRKG;
			
			
			
            //$tttt=str_pad($x, 2, "0", STR_PAD_LEFT);
            
            //if($jjgpanen[$param['periode']."-".$tttt][$bl]>0 or $jjgangkut[$param['periode']."-".$tttt][$bl]>0 or $brdkbn[$param['periode']."-".$tttt][$bl]>0 or $brd[$param['periode']."-".$tttt][$bl]>0)
            //{
				/*
                $a++;
                $stream.="<tr class=rowcontent>
                           <td>".$a."</td>
                           <td>".$param['periode']."-".$tttt."</td>
                           <td>".$bl."</td>
                           <td>".$tt[$bl]."</td>
                            <td align=right>".number_format($jjgpanen[$param['periode']."-".$tttt][$bl])."</td>
                            <td align=right>".number_format($jjgangkut[$param['periode']."-".$tttt][$bl])."</td>    
                           <td align=right>".number_format($brdkbn[$param['periode']."-".$tttt][$bl],2)."</td>
                           <td align=right>".number_format($brd[$param['periode']."-".$tttt][$bl],2)."</td>    
                     </tr>";
                $tjp+=$jjgpanen[$param['periode']."-".$tttt][$bl];
                $tja+=$jjgangkut[$param['periode']."-".$tttt][$bl];
                $tbk+=$brdkbn[$param['periode']."-".$tttt][$bl];
                $tb+=$brd[$param['periode']."-".$tttt][$bl];
				*/
            //}
          }
			  $SenTKHT=($TPKHT/$TTotP)*100;
			  $SenTBHL=($TPBHL/$TTotP)*100;
			  $senTTot=$SenTKHT+$SenTBHL;
			  
			  $KRKHT=$TRPKHT/$TPKHT;
			  $KRBHL=$TRPBHL/$TPBHL;
			  $TKRHK=$TRTotP/$TTotP;
			  
			$XJJG_KHT=$AJJG_KHT/$TRPKHT;
			$XKG_KHT=$AKG_KHT/$TRPKHT;
			  
			$XJJG_BHL=$AJJG_BHL/$TRPBHL;
			$XKG_BHL=$AKG_BHL/$TRPBHL;
			  
			$XSTRJJG=$ASTRJJG/$TRTotP;
			$XSTRKG=$ASTRKG/$TRTotP;
			
			 //summary bjr
			  $SUBJR_KHT=$XKG_KHT/$XJJG_KHT;
			  $SUBJR_BHL=$XKG_BHL/$XJJG_BHL;
			  $STBJR_R=$XSTRKG/$XSTRJJG;
			
      $stream.="</tbody><tfoot>
                    <tr class=rowcontent>
                       <td colspan=2>TOTAL</td>
                       <td align=right>".number_format($TPKHT)."</td>
                       <td align=right>".number_format($SenTKHT)."%</td>
                       <td align=right>".number_format($TPBHL)."</td>
                       <td align=right>".number_format($SenTBHL)."%</td>   
					   <td align=right>".number_format($TTotP)."</td>   
					   <td align=right>".number_format($senTTot)."%</td>   
					   <td align=right>".round($KRKHT)."</td>   
					   <td align=right>".round($KRBHL)."</td>   
					   <td align=right>".round($TKRHK)."</td>   
					   <td align=right>".number_format(round($XJJG_KHT))."</td>   
					   <td align=right>".number_format(round($XKG_KHT))."</td>   
					   <td align=right>".number_format(round($XJJG_BHL))."</td>   
					   <td align=right>".number_format(round($XKG_BHL))."</td>   
					   <td align=right>".number_format(round($XSTRJJG))."</td>   
					   <td align=right>".number_format(round($XSTRKG))."</td>   
					   <td align=right>".number_format($SUBJR_KHT,2)."</td>   
					   <td align=right>".number_format($SUBJR_BHL,2)."</td>   
					   <td align=right>".number_format($STBJR_R,2)."</td>   
                       </tr align=right>
                 </tfoot></table>";
        //========================================
switch ($proses){
        case 'preview':
                echo $stream;
            break;
         case 'excel':
            $nop_="EVALUASI_KINERJA_PANEN_".$param['unit']."_".$param['periode'];
            if(strlen($stream)>0)
            {
                 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                 gzwrite($gztralala, $stream);
                 gzclose($gztralala);
                 echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls.gz';
                    </script>";
            }
             break;
}

?>
