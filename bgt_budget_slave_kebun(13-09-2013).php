<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
$totRow=isset($_POST['totRow'])?$_POST['totRow']:'';
$keyId=isset($_POST['keyId'])?$_POST['keyId']:'';
$noakunCari=isset($_POST['noakunCari'])?$_POST['noakunCari']:'';

//header

$kdBarangL=isset($_POST['kdBarangL'])?$_POST['kdBarangL']:'';

$rasiohead=isset($_POST['rasiohead'])?$_POST['rasiohead']:'';
$volras=isset($_POST['volras'])?$_POST['volras']:'';
$satKeg=isset($_POST['satKeg'])?$_POST['satKeg']:'';

$kdBlok=isset($_POST['kdBlok'])?$_POST['kdBlok']:'';
$kegId=isset($_POST['kegId'])?$_POST['kegId']:'';
$thnBudget=isset($_POST['thnBudget'])?$_POST['thnBudget']:'';
$noAkun=isset($_POST['noAkun'])?$_POST['noAkun']:'';
$tpBudget=isset($_POST['tpBudget'])?$_POST['tpBudget']:'';
$persen=isset($_POST['persen'])?$_POST['persen']:'';
$rotThn=isset($_POST['rotThn'])?$_POST['rotThn']:'';
$volKeg=isset($_POST['volKeg'])?$_POST['volKeg']:'';
$satuan=isset($_POST['satuan'])?$_POST['satuan']:'';
//$where=" tahunbudget='".$thnBudget."' and kodeorg='".$kdBlok."' and tipebudget='".$tpBudget."' and kegiatan='".$kegId."' and volume='".$volKeg."' and satuanv='".$satuan."' and rotasi='".$rotThn."'";
$where=" tahunbudget='".$thnBudget."' and kodeorg='".$kdBlok."' and tipebudget='".$tpBudget."' and kegiatan='".$kegId."'";
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optNmKeg=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');

$where2=" kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and tipebudget='ESTATE' and tahunbudget='".$thnBudget."'";
//sdm
$jmlhPerson=isset($_POST['jmlhPerson'])?$_POST['jmlhPerson']:'';
$kdGol=isset($_POST['kdGol'])?$_POST['kdGol']:'';
$hkEfektif=isset($_POST['hkEfektif'])?$_POST['hkEfektif']:'';
$tipeBudget=isset($_POST['tipeBudget'])?$_POST['tipeBudget']:'';
$totBiaya=isset($_POST['totBiaya'])?$_POST['totBiaya']:'';

//material
$kdBudget=isset($_POST['kdBudget'])?$_POST['kdBudget']:'';
$kdBrg=isset($_POST['kdBrg'])?$_POST['kdBrg']:'';
$jmlhBrg=isset($_POST['jmlhBrg'])?$_POST['jmlhBrg']:'';
$satuanBrg=isset($_POST['satuanBrg'])?$_POST['satuanBrg']:'';
$totHarga=isset($_POST['totHarga'])?$_POST['totHarga']:'';
$nmBrg=isset($_POST['nmBrg'])?$_POST['nmBrg']:'';
$klmpkBrg=isset($_POST['klmpkBrg'])?$_POST['klmpkBrg']:'';

//tool
$nmBrgL=isset($_POST['nmBrgL'])?$_POST['nmBrgL']:'';
$kdBrgL=isset($_POST['kdBrgL'])?$_POST['kdBrgL']:'';
$jmlhBrgL=isset($_POST['jmlhBrgL'])?$_POST['jmlhBrgL']:'';
$kdBudgetL=isset($_POST['kdBudgetL'])?$_POST['kdBudgetL']:'';
$totHargaL=isset($_POST['totHargaL'])?$_POST['totHargaL']:'';
$satuanBrgL=isset($_POST['satuanBrgL'])?$_POST['satuanBrgL']:'';

//kontrak
$kdBudgetK=isset($_POST['kdBudgetK'])?$_POST['kdBudgetK']:'';
$volKontrak=isset($_POST['volKontrak'])?$_POST['volKontrak']:'';
$satKontrak=isset($_POST['satKontrak'])?$_POST['satKontrak']:'';
$totBiayaK=isset($_POST['totBiayaK'])?$_POST['totBiayaK']:'';

//kendaraan
$rasiodetail=isset($_POST['rasiodetail'])?$_POST['rasiodetail']:'';
$kdVhc=isset($_POST['kdVhc'])?$_POST['kdVhc']:'';
$jmlhJam=isset($_POST['jmlhJam'])?$_POST['jmlhJam']:'';
$totBiayaKend=isset($_POST['totBiayaKend'])?$_POST['totBiayaKend']:'';
$kdBudgetV=isset($_POST['kdBudgetV'])?$_POST['kdBudgetV']:'';
$satVhc=isset($_POST['satVhc'])?$_POST['satVhc']:'';

$thnbudgetHeader=isset($_POST['thnbudgetHeader'])?$_POST['thnbudgetHeader']:'';

switch($proses)
{
		#iindra
	case 'tarobawah':
	//exit("Error:HAHA");
		$haha=substr($kdBudgetL,0,5);
		//exit("Error:$haha");
		echo $haha;
	break;
	
	
	
	case 'getsatuankend':
			$ha="select kelompokvhc from ".$dbname.".vhc_5master WHERE kodevhc='".$kdVhc."' ";	
			$hi=mysql_query($ha) or die(mysql_error());
			$hu=mysql_fetch_assoc($hi);
				$kelompokvhc=$hu['kelompokvhc'];
				
				if($kelompokvhc=='AB')
				{
					echo "HM/".$satKeg."";
				}
				else
					echo "KM/".$satKeg."";
	#iindra
		case 'getrasio':
		$qVol = selectQuery($dbname,'bgt_blok','hathnini',"kodeblok='".$kdBlok."'");
		$resVol = fetchData($qVol);
		$a="select rasio from ".$dbname.".bgt_rasiokegiatan WHERE kodekegiatan='".$kegId."' ";	
		//exit("Error:$sql");
		//echo $sql;
		$b=mysql_query($a) or die(mysql_error());
		$c=mysql_fetch_assoc($b);
		//$ad=$c['rasio'].'##'.$resVol[0]['hathnini'].'##'.$persen;
		//exit("Error:$ad");
		if($c['rasio']!='') {
				echo $c['rasio']*($persen/100)*$resVol[0]['hathnini'];//exit();
		} else {
				echo ($persen/100)*$resVol[0]['hathnini'];//exit();
		}
	break;
	
	
        case'getBlok':
            $optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sVhc="select distinct kodeblok,thntnm,statusblok from ".$dbname.".bgt_blok where tahunbudget='".$thnBudget."' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%' and closed=1";
			
            //exit ("Error".$sVhc);
            
            $qVhc=mysql_query($sVhc) or die(mysql_error($conn));
            $brs=mysql_num_rows($qVhc);
            if($brs>0) {
                while($rVhc=mysql_fetch_assoc($qVhc)) {
                    if($kdBlok!='') {
                        $optBlok.="<option value='".$rVhc['kodeblok']."' ".($kdBlok==$rVhc['kodeblok']?'selected':'').">".$rVhc['kodeblok']." - ".$rVhc['thntnm']."</option>";
                    } else {
                        $optBlok.="<option value='".$rVhc['kodeblok']."'>".$rVhc['kodeblok']." - ".$rVhc['thntnm']."</option>";
                    }
                }
                echo $optBlok;
            } else {
                exit("Error: Blok Anggaran Belum di proses/di input");
            }
            break;
			
			
			
        case'getKegiatan':
        if($kdBlok=='')
        {
            exit("Error:Kodeblok Tidak Boleh Kosong");
        }
        $sStatus="select distinct statusblok from ".$dbname.".bgt_blok where kodeblok='".$kdBlok."'";
        $qStatus=mysql_query($sStatus) or die(mysql_error($conn));
        $rStatus=mysql_fetch_assoc($qStatus);
        
        $optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        //$sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok='".$rStatus['statusblok']."'  order by kodekegiatan asc";
        $sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok in ('PNN','TBM','TM','BBT','TB')  order by kodekegiatan asc";
        $qKeg=mysql_query($sKeg) or die(mysql_error());
        while($rKeg=mysql_fetch_assoc($qKeg))
        {
            if($kegId!='')
            {
                $optKeg.="<option value=".$rKeg['kodekegiatan']." ".($rKeg['kodekegiatan']==$kegId?'selected':'').">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
            }
            else
            {
                $optKeg.="<option value=".$rKeg['kodekegiatan'].">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
            }
        }
        echo $optKeg;
        break;
        case'getSatuan':
           if($kegId=='')
           {
               exit("Error:Kode Kegiatan Tidak Boleh Kosong");
           }
            $sKegiata="select distinct satuan,noakun from ".$dbname.".setup_kegiatan where kodekegiatan='".$kegId."'";
            //exit("Error".$sKegiata);
            $qKegiatan=mysql_query($sKegiata) or die(mysql_error($conn));
            $row=mysql_num_rows($qKegiatan);
            if($row>0)
            {
                $rKegiatan=mysql_fetch_assoc($qKegiatan);
                if($rKegiatan['satuan']=='NULL')
                {
                    $rKegiatan['satuan']='';
                }
                echo $rKegiatan['satuan']."###".$rKegiatan['noakun'];
            }
            else
            {
                exit("Error:Status Blok Tidak Sesuai");
            }
        break;
	case'cekSave':
            if($thnBudget==''||$kegId==''||$kdBlok==''||$tpBudget==''||$noAkun==''||$volKeg==''||$satuan==''||$rotThn=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
            if(strlen($thnBudget)<4)
            {
                exit("Error:Panjang Tahun Kurang");
            }
//            $sCek="select * from ".$dbname.".bgt_budget where ".$where."";
//            $qCek=mysql_query($sCek)or die(mysql_error($conn));
//            $rCek=mysql_num_rows($qCek);
//            if($rCek>0)
//            {
//                exit("Error: Tahun Budget ".$thnBudget." dan Kode kegiatan ".$kegId." sudah terinput");
//            }
            
            $sCek2="select distinct tutup from ".$dbname.".bgt_budget where ".$where2."";
            $qCek2=mysql_query($sCek2)or die(mysql_error($conn));
            $rCek2=mysql_fetch_assoc($qCek2);
            if($rCek2['tutup']>0)
            {
               exit("Error:  Tahun Budget ".$thnBudget." Sudah Di tutup,Tidak dapat menambahkan data");
            }
            if($hkEfektip=='')
            {
                $sHk="select distinct * from ".$dbname.".bgt_hk where tahunbudget='".$thnBudget."'";
                //exit("Error".$sHk);
                $qHk=mysql_query($sHk) or die(mysql_error($conn));
                $rHk=mysql_fetch_assoc($qHk);
                $hkEfektip=intval($rHk['harisetahun'])-intval($rHk['hrminggu'])-intval($rHk['hrlibur'])+intval($rHk['hrliburminggu']);

                $optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                //$sVhc="select distinct kodevhc from ".$dbname.".bgt_vhc_jam where tahunbudget='".$thnBudget."' and unitalokasi='".$_SESSION['empl']['lokasitugas']."'";
                //exit("Error".$sVhc);

                $sVhc="select distinct kodetraksi ,kodevhc from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi where tahunbudget='".$thnBudget."' order by kodevhc asc";
                $qVhc=mysql_query($sVhc) or die(mysql_error($conn));
                while($rVhc=mysql_fetch_assoc($qVhc))
                {
                    $optVhc.="<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc']." [".$rVhc['kodetraksi']."]</option>";
                }
                echo $hkEfektip."###".$optVhc;
            }
	break;
       
        case'getUpah':
            if($kdGol=='')
            {
               exit("Error:Kode Budget Kosong");
            }
          $sUpah="select jumlah from ".$dbname.".bgt_upah where tahunbudget='".$thnBudget."' and kodeorg='".substr($kdBlok,0,4)."' and golongan='".$kdGol."' and closed=1";
          //exit("Error:".$sUpah);
          $qUpah=mysql_query($sUpah) or die(mysql_error($conn));
          $row=mysql_num_rows($qUpah);
          if($row!=0)
          {
              $rUpah=mysql_fetch_assoc($qUpah);
              if($rUpah['jumlah']=='')
              {
                  exit("Error:Data upah belum ada, silahkan cek kembali");
              }
              else
              {
                  $totalUpah=(floatval($rUpah['jumlah'])*floatval($jmlhPerson));//*floatval($hkEfektif);
                  //exit("Error". $rUpah['jumlah']."__".$jmlhPerson."__".$hkEfektif);

                   echo $totalUpah;

              }
          }
          else
          {
              exit("Error:Data belum di tutup, silahkan cek kembali");
          }
            
        break;
        case'saveSdm':
            if($kdGol==''||$totBiaya==0||$jmlhPerson=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
          $sCek="select * from ".$dbname.".bgt_budget where ".$where." and kodebudget='".$kdGol."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, persentase, volume, satuanv, rupiah,rotasi,updateby,jumlah,satuanj) values
                  ('".$thnBudget."','".$kdBlok."','".$tpBudget."','".$kdGol."','".$kegId."','".$noAkun."','".$persen."','".$volKeg."','".$satuan."','".$totBiaya."','".$rotThn."','".$_SESSION['standard']['userid']."','".$jmlhPerson."','HK')";
              //exit("Error:".$sIns);
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data Sudah Ada");
          }
        break;
        case'saveMat':
            if($kdBudget==''||$totHarga==0||$jmlhBrg==''||$kdBrg=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
          $sCek="select * from ".$dbname.".bgt_budget where ".$where." and kodebarang='".$kdBrg."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
                $sRegion="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".substr($kdBlok,0,4)."'";
                //  exit("Error".$sRegion);
                $qRegion=mysql_query($sRegion) or die(mysql_error($conn));
                $rRegion=mysql_fetch_assoc($qRegion);
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, persentase,volume, satuanv,rupiah,rotasi, kodebarang, regional, updateby,jumlah,satuanj) values
                  ('".$thnBudget."','".$kdBlok."','".$tpBudget."','".$kdBudget."','".$kegId."','".$noAkun."','".$persen."','".$volKeg."','".$satuan."','".$totHarga."','".$rotThn."','".$kdBrg."','".$rRegion['regional']."','".$_SESSION['standard']['userid']."','".$jmlhBrg."','".$satuanBrg."')";
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data Sudah Ada");
          }
        break;
        case'saveKontrak':
            if($kdBudgetK==''||$totBiayaK==0||$volKontrak=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
          $sCek="select * from ".$dbname.".bgt_budget where ".$where." and kodebudget like '%KONTRAK%'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, persentase, volume, satuanv,rupiah,rotasi,  updateby,jumlah,satuanj) values
                  ('".$thnBudget."','".$kdBlok."','".$tpBudget."','".$kdBudgetK."','".$kegId."','".$noAkun."','".$persen."','".$volKeg."','".$satuan."','".$totBiayaK."','".$rotThn."','".$_SESSION['standard']['userid']."','".$volKontrak."','".$satKontrak."')";
              if(mysql_query($sIns))
              echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data Sudah Ada");
          }
        break;
        case'saveTool':
             if($kdBudgetL==''||$totHargaL==0||$jmlhBrgL==''||$kdBrgL=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
          $sCek="select * from ".$dbname.".bgt_budget where ".$where." and kodebudget='".$kdBudgetL."' and kodebarang='".$kdBrgL."'";
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, persentase, volume, satuanv,rupiah,rotasi, kodebarang, regional, updateby,jumlah,satuanj) values
                  ('".$thnBudget."','".$kdBlok."','".$tpBudget."','".$kdBudgetL."','".$kegId."','".$noAkun."','".$persen."','".$volKeg."','".$satuan."','".$totHargaL."','".$rotThn."','".$kdBrgL."','".$rRegion['regional']."','".$_SESSION['standard']['userid']."','".$jmlhBrgL."','".$satuanBrgL."')";              
              
              if(mysql_query($sIns))
               echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data Sudah Ada");
          }
        break;
        case'saveKendaran':
            if($kdBudgetV==''||$totBiayaKend==0||$jmlhJam=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
          $sCekJam="select * from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi where tahunbudget='".$thnBudget."' and kodevhc='".$kdVhc."'";
		  //echo $sCekJam.'error';exit;
          $qCekJam=mysql_query($sCekJam) or die(mysql_error($conn));
          $rCekJam=mysql_fetch_assoc($qCekJam);
          $sisa=$rCekJam['jamsetahun']-$rCekJam['teralokasi'];
          if($jmlhJam>$sisa)
          {
              exit("Error: Kendaraan ".$kdVhc." sudah teralokasi sebesar: ".$rCekJam['teralokasi']." dari total jam :".$rCekJam['jamsetahun']." hanya dapat digunakan sebanyak:".$sisa."");
          }
          $sCek="select * from ".$dbname.".bgt_budget where ".$where." and kodebudget like '%VHC%' and kodevhc='".$kdVhc."'";
            //exit("Error".$sCek);
          $qCek=mysql_query($sCek) or die(mysql_error($conn));
          $rCek=mysql_num_rows($qCek);
          if($rCek<1)
          {
             
              $sIns="insert into ".$dbname.".bgt_budget (tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, persentase, volume, satuanv,rupiah,rotasi, kodevhc, updateby,jumlah,satuanj,rasioh,volumerasio,rasiod) values
                  ('".$thnBudget."','".$kdBlok."','".$tpBudget."','".$kdBudgetV."','".$kegId."','".$noAkun."','".$persen."','".$volKeg."','".$satuan."','".$totBiayaKend."','".$rotThn."','".$kdVhc."','".$_SESSION['standard']['userid']."','".$jmlhJam."','".$satVhc."','".$rasiohead."','".$volras."','".$rasiodetail."')";
              if(mysql_query($sIns))
               echo"";
              else
             echo "DB Error : ".$sIns."\n".mysql_error($conn);
          }
          else
          {
              exit("Error:Data Sudah Ada");
          }
        break;
	case'loadDataSdm':
        $sLoad="select tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,updateby,jumlah,satuanj,kunci from ".$dbname.".bgt_budget where 
            ".$where." and kodebudget like '%SDM%'";
           // echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kegiatan']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='center'>".$res['rotasi']."</td>";
            $tab.="<td  align='right'>".$res['volume']."</td>";
            $tab.="<td  align='center'>".$res['satuanv']."</td>";
            //$tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='right'>".number_format($res['rupiah'],0)."</td>";
            $tab.="<td  align='center'>".$res['jumlah']."</td>";  
            $tab.="<td  align='center'>".$res['satuanj']."</td>"; 
            $tab.="<td align=center style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",1)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        echo $tab;
	break;
        case'getBarang'://indra
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";
            if($nmBrg=='')
            {
                $nmBrg=$kdBarang;
            }
            $sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where  kelompokbarang='".substr($klmpkBrg,2,3)."' and (kodebarang like '%".$nmBrg."%'
            or namabarang like '%".$nmBrg."%')";
        //   echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."','".$res['namabarang']."','".$res['satuan']."')\">";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;
            
        break;
        case'getBarangL'://indra
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";
           //$asd=substr($kdBarangL
		   
            /*$sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang like '%".$nmBrgL."%'
            or namabarang like '%".$nmBrgL."%' ";*/
			$sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where  kelompokbarang='".substr($kdBarangL,2,3)."' and (kodebarang like '%".$nmBrgL."%'
            or namabarang like '%".$nmBrgL."%')";
			
			 //
        //echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent onclick=\"setDataL('".$res['kodebarang']."','".$res['namabarang']."','".$res['satuan']."')\">";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;
            
        break;
        case'getHarga':
            if(($jmlhBrg=='')||($jmlhBrg=='0'))
            {
                exit("Jumlah Barang Masih Kosong");
            }
            $sRegion="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".substr($kdBlok,0,4)."' ";
           //exit("Error".$sRegion);
            $qRegion=mysql_query($sRegion) or die(mysql_error($conn));
            $rRegion=mysql_fetch_assoc($qRegion);
            $sHrg="select distinct hargasatuan from ".$dbname.".bgt_masterbarang where regional='".$rRegion['regional']."' and kodebarang='".$kdBrg."' and tahunbudget='".$thnBudget."' and closed=1";
             //exit("Error".$sHrg);
            $qHrg=mysql_query($sHrg) or die(mysql_error($conn));
            $row=mysql_num_rows($qHrg);
            if($row!=0)
            {
                $rHrg=mysql_fetch_assoc($qHrg);
                if(($rHrg['hargasatuan']!='')||($rHrg['hargasatuan']!='0'))
                {
                    $hasil=floatval($rHrg['hargasatuan'])*floatval($jmlhBrg);
                    echo $hasil;
                }
                else
                {
                    exit("Error:Silahkan Hubungi Purchase Dept");
                }
            }
            else
            {
             exit("Error:Silahkan Hubungi Purchase Dept");   
            }
        break;
         case'getHargaL':
            if(($jmlhBrgL=='')||($jmlhBrgL=='0'))
            {
                exit("Jumlah Barang Masih Kosong");
            }
            $sRegion="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".substr($kdBlok,0,4)."' ";
           //exit("Error".$sRegion);
            $qRegion=mysql_query($sRegion) or die(mysql_error($conn));
            $rRegion=mysql_fetch_assoc($qRegion);
            $sHrg="select distinct hargasatuan from ".$dbname.".bgt_masterbarang where regional='".$rRegion['regional']."' and kodebarang='".$kdBrgL."' and tahunbudget='".$thnBudget."' and closed=1";
             //exit("Error".$sHrg);
            $qHrg=mysql_query($sHrg) or die(mysql_error($conn));
            $row=mysql_num_rows($qHrg);
            if($row!=0)
            {
                $rHrg=mysql_fetch_assoc($qHrg);
                if(($rHrg['hargasatuan']!='')||($rHrg['hargasatuan']!='0'))
                {
                    $hasil=floatval($rHrg['hargasatuan'])*floatval($jmlhBrgL);
                    echo $hasil;
                }
                else
                {
                    exit("Error:Silahkan Hubungi Purchase Dept");
                }
            }
            else
            {
             exit("Error:Silahkan Hubungi Purchase Dept");   
            }
        break;
        case'getBiaya':
            
            $sHrg="select distinct rpperjam from ".$dbname.".bgt_biaya_ken_per_jam where tahunbudget='".$thnBudget."' and kodevhc='".$kdVhc."'";
            //exit("Error".$sHrg);
            $qHrg=mysql_query($sHrg) or die(mysql_error($conn));
            $rHrg=mysql_fetch_assoc($qHrg);
            if(($rHrg['rpperjam']!='')||($rHrg['rpperjam']!='0'))
            {
                $hasil=$rHrg['rpperjam']*floatval($jmlhJam);
                //$hasil=number_format($hasil,2);
                //exit("Error".$jmlhJam."__".$rHrg['rpperjam']);
                echo $hasil;
            }
            else
            {
                exit("Error:Biaya kend. Belum ada, hub.Traksi");
            }
        break;
        case'delKodeblok':
            $sDel="delete from ".$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg = '".$kdBlok."' and kegiatan = '".$kegId."' and tipebudget = 'ESTATE' and kodebudget != 'UMUM'";
            if(mysql_query($sDel))
                echo"";
            else
                 echo "DB Error : ".$sDel."\n".mysql_error($conn);
        break;
        case'delData':
            $sDel="delete from ".$dbname.".bgt_budget where kunci='".$idData."'";
            if(mysql_query($sDel))
                echo"";
            else
                 echo "DB Error : ".$sDel."\n".mysql_error($conn);
        break;
        case'loadDataMat':
          $sLoad="select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj,kodebarang from ".$dbname.".bgt_budget where 
           ".$where." and kodebudget in ('M-311','M-312','M-313')";
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kegiatan']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='center'>".$res['rotasi']."</td>";
            $tab.="<td  align='right'>".$res['volume']."</td>";
            $tab.="<td  align='center'>".$res['satuanv']."</td>";
            //$tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='right'>".number_format($res['rupiah'],0)."</td>";
            $tab.="<td align='center'>".$optNmBrg[$res['kodebarang']]."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
           
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",2)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        
//             $tab.="
//		<tr class=rowheader><td colspan=11 align=center>
//		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
//		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//		</td>
//		</tr>";
            echo $tab;
        break;
        case'loadDtLain':
          $sLoad="select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj from ".$dbname.".bgt_budget where 
            ".$where." and kodebudget like '%KONTRAK%'";
           // echo $sLoad; and
           // exit("Error".$sLoad);
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kegiatan']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='center'>".$res['rotasi']."</td>";
            $tab.="<td  align='right'>".$res['volume']."</td>";
            $tab.="<td  align='center'>".$res['satuanv']."</td>";
            //$tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='right'>".number_format($res['rupiah'],0)."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",4)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        
//             $tab.="
//		<tr class=rowheader><td colspan=11 align=center>
//		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
//		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//		</td>
//		</tr>";
            echo $tab;
        break;
      case'loadDataTool':
          $sLoad="select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, volume, satuanv, rupiah,rotasi,jumlah, satuanj,kodebarang from ".$dbname.".bgt_budget where 
           ".$where."  and kodebudget in ('M-361','M-362')";
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kegiatan']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='center'>".$res['rotasi']."</td>";
            $tab.="<td  align='right'>".$res['volume']."</td>";
            $tab.="<td  align='center'>".$res['satuanv']."</td>";
            //$tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='right'>".number_format($res['rupiah'],0)."</td>";
            $tab.="<td align='center'>".$optNmBrg[$res['kodebarang']]."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
           
            $tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",3)\" src='images/application/application_delete.png'/></td>";
            $tab.="</tr>";
        }
        
//             $tab.="
//		<tr class=rowheader><td colspan=11 align=center>
//		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
//		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//		</td>
//		</tr>";
            echo $tab;
        break;
		case'loadDataKend3':
			$x=6;
         case'loadDataKend':
          $sLoad="select kunci,tahunbudget,kodeorg, tipebudget, kodebudget, kegiatan, noakun, kodevhc,  rupiah,rotasi,jumlah, satuanj from ".$dbname.".bgt_budget where 
           ".$where." and kodebudget like '%VHC%'";
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$no."</td>";
            $tab.="<td align='center'>".$res['kunci']."</td>";
            $tab.="<td align='center'>".$res['tahunbudget']."</td>";
            $tab.="<td align='center'>".$res['kodeorg']."</td>";
            $tab.="<td align='center'>".$res['tipebudget']."</td>";
            $tab.="<td align='center'>".$res['kodebudget']."</td>";
            $tab.="<td align='center'>".$res['kegiatan']."</td>";
            $tab.="<td align='right'>".$res['noakun']."</td>";
            $tab.="<td align='center'>".$res['kodevhc']."</td>";
            //$tab.="<td align='right'>".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='right'>".number_format($res['rupiah'],0)."</td>";
            $tab.="<td  align='right'>".$res['jumlah']."</td>";
            $tab.="<td  align='center'>".$res['satuanj']."</td>";
			if(isset($x) and $x==6) {
				$tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",6)\" src='images/application/application_delete.png'/></td>";
			} else {
				$tab.="<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",5)\" src='images/application/application_delete.png'/></td>";
			}
            $tab.="</tr>";
        }
        
//             $tab.="
//		<tr class=rowheader><td colspan=11 align=center>
//		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
//		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//		</td>
//		</tr>";
            echo $tab;
        break;
        case'setKdBrg':
            echo substr($klmpkBrg,2,3);
        break;
        case'loadDetailTotal':
        
            if($thnbudgetHeader!='')
            {
                $whereCari.=" and a.tahunbudget='".$thnbudgetHeader."'";
            }
            if($kdBlok!='')
            {
                $whereCari.=" and a.kodeorg like '".$kdBlok."%'";
            }
            if($noakunCari!='')
            {
                $whereCari.=" and a.noakun='".$noakunCari."'";
            }
            
        $optPage="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sLoad="select * from ".$dbname.".bgt_budget_detail a left join ".$dbname.".setup_kegiatan b ".
            "on a.kegiatan=b.kodekegiatan where b.kelompok in ('TB','BBT','PNN') and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget!='UMUM' and tipebudget='ESTATE' ".$whereCari."";
        // echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        $rAngka=mysql_num_rows($qLoad);
        @$totalPage=$rAngka/50;
        for($starAwal=1;$starAwal<=$totalPage;$starAwal++)
        {
        $optPage.="<option value='".$starAwal."' ".($starAwal==$_POST['page']?'selected':'').">".$starAwal."</option>";
        }
        if($rAngka!=0)
        {
        $limit=50;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
        
        $sLoad="select * from ".$dbname.".bgt_budget_detail a left join ".$dbname.".setup_kegiatan b ".
            "on a.kegiatan=b.kodekegiatan where b.kelompok in ('TB','BBT','PNN') and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kodebudget!='UMUM' and tipebudget='ESTATE' ".$whereCari." limit ".$offset.",".$limit."";
        // echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
           
                $dtClik="onclick=\"getForm('Sebaran','<fieldset style=\'width:520px;height:400px;\'><legend>Sebaran Per Bulan</legend><div id=containerForm style=\'overflow:auto;height:450px;width:480px\'></div><input type=hidden id=keyId value=".$res['kunci']." /></fieldset>',".$res['rupiah'].",".$res['jumlah'].",'".$res['kodebudget']."',event);\"";
           
            $tab.="<tr class=rowcontent style='cursor:pointer;' id=baris".$no.">";
            $tab.="<td><input type=checkbox onclick=sebarkanBoo('".$res['kunci']."',".$no.",this,".$res['rupiah'].",".$res['jumlah']."); title='Sebarkan sesuai proporsi diatas'></td>";
            $tab.="<td >".$no."</td>";
            $tab.="<td align='center' ".$dtClik.">".$res['kodeorg']."</td>";
            $tab.="<td align='center' ".$dtClik.">".$res['kodebudget']."</td>";
            $tab.="<td align='center' ".$dtClik.">".$optNmKeg[$res['kegiatan']]."</td>";
            $tab.="<td align='center' ".$dtClik.">".$optNmBrg[$res['kodebarang']]."</td>";
            $tab.="<td align='right' ".$dtClik.">".$res['kodevhc']."</td>";
            //$tab.="<td align='center' ".$dtClik.">".number_format($res['rupiah'],2)."</td>";
			$tab.="<td align='center' ".$dtClik.">".number_format($res['rupiah'],0)."</td>";
            foreach($arrBln as $brsBln =>$listData)
            {
                if(strlen($brsBln)<2)
                {
                    $b="0".$brsBln;
                }
                else
                {
                    $b=$brsBln;
                }
                //$tab.="<td align='right'>".number_format($res['rp'.$b],2)."</td>";
				$tab.="<td align='right'>".number_format($res['rp'.$b],0)."</td>";
            }
                
            $tab.="<td align=center  style='cursor:pointer;'><img src=\"images/zoom.png\" class=\"resicon\" title='sebarang_".$res['kunci']."' ".$dtClik." /></td>";
            $tab.="</tr>";
        }
        //$optPage="<select id=pageSebaran onchange='loadDetailTotal()'>";
       
        //$optPage.="</select>";
        }
        else
        {
            $tab.="<tr class=rowcontent style='cursor:pointer;' id=baris".$no.">";
            $tab.="<td colspan=21>".$_SESSION['lang']['dataempty']."</td>";
            $tab.="</tr>";
        }
        $page==0?$page=1:$page=$page;
        $totalPage==0?$totalPage=1:$totalPage=$totalPage;
            echo $tab."<input type=hidden id=jmlhRow value=".$no." />"."###".$optPage."###".number_format($totalPage,0)."###".$page;
        break;
        case'getForm':
            $rupiah=$_POST['rupiah'];
            $jumlah=$_POST['jumlah'];
            $kodebudget=$_POST['kodebudget'];
            $sCek="select distinct rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12,fis01, fis02, fis03, fis04, fis05, fis06, fis07, fis08, fis09, fis10, fis11, fis12
                from ".$dbname.".bgt_distribusi where kunci='".$keyId."'";
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rCek=mysql_num_rows($qCek);
            if($rCek<1)
            {
            $sData="select rupiah,jumlah from ".$dbname.".bgt_budget_detail where kunci='".$keyId."'";
            //echo $sData;
            $qData=mysql_query($sData) or die(mysql_error($conn));
            $rData=mysql_fetch_assoc($qData);
            @$totRupiah=$rData['rupiah']/12;
            @$totFisik=$rData['jumlah']/12;

                for($a=1;$a<=12;$a++)
                {
                    $totRupiahArr[$a]=number_format($totRupiah,2,'.','');
                    $totFisikArr[$a]=number_format($totFisik,2,'.','');
                }

            }
            else
            {
                $res=mysql_fetch_assoc($qCek);
                 for($a=1;$a<=12;$a++)
                 {
                     if(strlen($a)<2)
                     {
                         $b="0".$a;
                     }
                     else
                     {
                         $b=$a;
                     }   
                    $totRupiahArr[$a]=$res['rp'.$b];
                    $totFisikArr[$a]=$res['fis'.$b];
                 }
                 $cekFisik=$cekRupiah=0;
                 for($c=1;$c<=12;$c++)
                 {
                    $cekFisik+=$totFisikArr[$c];
                    $cekRupiah+=$totRupiahArr[$c];
                 }
                //exit("Error:".$cekRupiah."__".$cekFisik);
                if($cekFisik==0)
                {
                    $sData="select rupiah,jumlah from ".$dbname.".bgt_budget_detail where kunci='".$keyId."'";
                    //echo $sData;
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    $rData=mysql_fetch_assoc($qData);
                    @$totFisik=$rData['jumlah']/12;
                   
            //$totFisik=floor($totFisik);
                    for($a=1;$a<=12;$a++)
                    {
                       $totFisikArr[$a]=number_format($totFisik,2,'.','');
                    }
                   
                }
                if($cekRupiah==0)
                {
                    $sData="select rupiah,jumlah from ".$dbname.".bgt_budget_detail where kunci='".$keyId."'";
                    $qData=mysql_query($sData) or die(mysql_error($conn));
                    $rData=mysql_fetch_assoc($qData);
                    @$totRupiah=$rData['rupiah']/12;
                     //$totRupiah=floor($totRupiah);
                    for($a=1;$a<=12;$a++)
                    {
                       $totRupiahArr[$a]=number_format($totRupiah,2,'.','');
                    }
                }
            }
            $tot=count($arrBln);
            if($tot==0)
            {
                exit("Error: Data Kosong");
            }
            $tab="<p align=center><table width=100%><tr><td><fieldset><legend>Rupiah:".$kodebudget."</legend>
                   <table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>";
            $tab.="<tr class=rowheader><td>Rp/Thn</td></td><td>%</td>
                   <td>".number_format($rupiah,0)."</td></tr></thead><tbody>";
            
            foreach($arrBln as $rpBln =>$listRpBln)
            {
                @$hasilBlnan=$totRupiahArr[$rpBln]/$rupiah;
                $tab.="<tr class=rowcontent><td>".$listRpBln."</td>
                     <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=rppersen".$rpBln." onblur=ubahNilai(this.value,'".$rupiah."','rupiah_','".$jumlah."') value=".number_format((($hasilBlnan)*100),2,'.','')."></td>";
                $tab.="<td><input type='text' id=rupiah_".$rpBln." class=\"myinputtextnumber\" style=\"width:75px;\" value=".$totRupiahArr[$rpBln]." /></td>
                      </tr>";
            }
            //$tab.="<td colspan=2>Action</td>";
           /*/* foreach($arrBln as $rpBln2 =>$listRpBln2)
            {
                
            }
            * 
            */
            $tab.="<tr class=rowcontent><td  colspan=3 align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveRupiah(".$tot.")\" src='images/save.png'/>&nbsp;&nbsp;<img id='detail_add' title='Clear Form' class=zImgBtn  width='16' height='16'  onclick=\"clearRupiah()\" src='images/clear.png'/></td>";
            $tab.="</tr></tbody></table></fieldset></td><td>";    
            
            $tab.="<fieldset><legend>Fisik:".$kodebudget."</legend>
                <table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>";
            $tab.="<tr class=rowheader><td>Fisik/Thn</td><td>%</td>
                  <td>".number_format($jumlah,0)."</td></tr></thead><tbody>";
            foreach($arrBln as $fisikBln =>$listFisikBln)
            {
                @$hsilFIsik=$totFisikArr[$fisikBln]/$jumlah;
                $tab.="<tr class=rowcontent><td>".$listFisikBln."</td>
                       <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=fispersen".$fisikBln." onblur=ubahNilai(this.value,'".$jumlah."','fisik_') value=".number_format(((@$hsilFIsik)*100),2,'.','')."></td>";
                $tab.="<td><input type='text' id=fisik_".$fisikBln." class=\"myinputtextnumber\" style=\"width:65px;\" value=".$totFisikArr[$fisikBln]." /></td>
                      </tr>";
            }
            
            $tab.="<tr class=rowcontent><td colspan=3 align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveFisik(".$tot.")\" src='images/save.png'/>&nbsp;&nbsp;<img id='detail_add' title='Clear Form' class=zImgBtn  width='16' height='16'  onclick=\"clearFisik()\" src='images/clear.png'/></td>";
            $tab.="</tr></tbody></table></fieldset></td></table></p>"; 
            
            $tab.="<p align=center><button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='closForm()'   >".$_SESSION['lang']['close']."</button></p>";
            echo $tab;
        break;
        case'saveRupiah':
            $sCek="select distinct rupiah,jumlah from ".$dbname.".bgt_budget_detail where kunci='".$keyId."'";
            //exit("Error \n\n".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);
                    for($a=1;$a<=$totRow;$a++)
                    {
                        if($_POST['arrRup'][$a]=='')
                        {
                            $_POST['arrRup'][$a]=0;
                        }
                        $totalSumRup+=$_POST['arrRup'][$a];
                    }
                    if($totalSumRup>$rCek['rupiah'])
                    {
                        exit("Error:Total Rupiah Perbulan Lebih Besar Dari Total Rupiah Setahun");
                    }
                    if(($totalSumRup==0)||($totalSumRup==''))
                    {
                        exit("Error:Total tidak boleh kosong");
                    }

                    $sCek="select distinct * from ".$dbname.".bgt_distribusi where kunci='".$keyId."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_num_rows($qCek);
                    if($rCek<1)
                    {
                        $sInsert="insert into ".$dbname.".bgt_distribusi  (kunci, updateby, rp01, rp02, rp03, rp04, rp05, rp06, rp07, rp08, rp09, rp10, rp11, rp12)";
                        $sInsert.=" values ('".$keyId."','".$_SESSION['standard']['userid']."'";
                        for($a=1;$a<=$totRow;$a++)
                        {
                            $sInsert.=",'".str_replace(',','',$_POST['arrRup'][$a])."'";
                        }
                        $sInsert.=")";
                       //exit("Error \n\n".$sInsert);
                        if(!mysql_query($sInsert))
                        {
                            echo " Gagal,_".$sInsert."__".(mysql_error($conn));
                        }   
                        
                        //$qInsert=mysql_query($sInsert)or die(mysql_error($conn));
                    }
                    else
                    {
                        $sUpdate="update ".$dbname.".bgt_distribusi set updateby='".$_SESSION['standard']['userid']."' ";
                        // exit("Error".$sUpdate);
                                        for($a=1;$a<=$totRow;$a++)
                                        {
                                            if(strlen($a)=='1')
                                            {
                                                $c="0".$a;
                                            }
                                            else
                                            {
                                                $c=$a;
                                            }
                                            $sUpdate.=" ,rp".$c."='".$_POST['arrRup'][$a]."'";
                                      
                                        }
                         $sUpdate.=" where kunci='".$keyId."'";
                        if(!mysql_query($sUpdate))
                        {
                        echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                        }   
                    }
            break;
            case'saveFisik':
            $sCek="select distinct rupiah,jumlah from ".$dbname.".bgt_budget_detail where kunci='".$keyId."'";
            //exit("Error \n\n".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error($conn));
            $rCek=mysql_fetch_assoc($qCek);
                    for($a=1;$a<=$totRow;$a++)
                    {
                        if($_POST['arrFisik'][$a]=='')
                        {
                            $_POST['arrFisik'][$a]=0;
                        }
                        $totalSumFisik+=$_POST['arrFisik'][$a];
                    }
//                    if($totalSumFisik>$rCek['jumlah'])
//                    {
//                        exit("Error:Total Fisik Perbulan Lebih Besar Dari Total Rupiah Setahun");
//                    }
                    if(($totalSumFisik==0)||($totalSumFisik==''))
                    {
                        exit("Error: Total tidak boleh kosong");
                    }
                    

                    $sCek="select distinct * from ".$dbname.".bgt_distribusi where kunci='".$keyId."'";
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rCek=mysql_num_rows($qCek);
                    if($rCek<1)
                    {
                        $sInsert="insert into ".$dbname.".bgt_distribusi  (kunci, updateby, fis01, fis02, fis03, fis04, fis05, fis06, fis07, fis08, fis09, fis10, fis11, fis12)";
                        $sInsert.=" values ('".$keyId."','".$_SESSION['standard']['userid']."'";
                        for($a=1;$a<=$totRow;$a++)
                        {
                            $sInsert.=",'".str_replace(',','',$_POST['arrFisik'][$a])."'";
                        }
                        $sInsert.=")";
                       //exit("Error \n\n".$sInsert);
                        if(!mysql_query($sInsert))
                        {
                            echo " Gagal,_".$sInsert."__".(mysql_error($conn));
                        }   
                        
                        //$qInsert=mysql_query($sInsert)or die(mysql_error($conn));
                    }
                    else
                    {
                        $sUpdate="update ".$dbname.".bgt_distribusi set updateby='".$_SESSION['standard']['userid']."' ";
                        // exit("Error".$sUpdate);
                                        for($a=1;$a<=$totRow;$a++)
                                        {
                                            if(strlen($a)=='1')
                                            {
                                                $c="0".$a;
                                            }
                                            else
                                            {
                                                $c=$a;
                                            }
                                            $sUpdate.=" ,fis".$c."='".$_POST['arrFisik'][$a]."'";
                                      
                                        }
                         $sUpdate.=" where kunci='".$keyId."'";
                        if(!mysql_query($sUpdate))
                        {
                        echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                        }   
                    }
            break;
			case 'getDetailData3':
				$x='3';
            case'getDetailData':
                $tmbh='';
                if($thnbudgetHeader=='')
                {
					$thnbudgetHeader=date('Y');
				}
                $tmbh=" and tahunbudget='".$thnbudgetHeader."'";
                if($noakunCari!='')
                {
                    $tmbh.=" and kegiatan='".$noakunCari."'";
                }
                if($kdBlok!='')
                {
                    $blok="kodeorg='".$kdBlok."'";
                    $blok1="a.kodeorg='".$kdBlok."'";
                }
                else
                {
                    $blok="kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
                    $blok1="a.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
                }
                $tab="<table cellspacing=1 cellpadding=1 class=sortable border=0><thead>";
                $tab.="<tr class=rowheader>";
                $tab.="<td>No.</td>";
                $tab.="<td>".$_SESSION['lang']['budgetyear']."</td>";
                $tab.="<td>".$_SESSION['lang']['tipe']."</td>";
                $tab.="<td>".$_SESSION['lang']['kodeblok']."</td>";
                $tab.="<td>".$_SESSION['lang']['noakun']."</td>";
                $tab.="<td>".$_SESSION['lang']['kegiatan']."</td>";
                
                $tab.="<td>".$_SESSION['lang']['volume']."</td>";
                $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
                $tab.="<td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>";
                $tab.="<td colspan=2>Action</td>";
                $tab.="</tr></thead>";
                $tab.="<tbody>";
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                
                $sql2="select * from ".$dbname.".bgt_budget a left join ".$dbname.".setup_kegiatan b ".
                    "on a.kegiatan=b.kodekegiatan where b.kelompok in ('TB','BBT','LC') and ".
                    $blok1." and kodebudget!='UMUM' ".$tmbh."  group by a.tahunbudget,a.kodeorg,a.tipebudget,a.kegiatan,a.noakun order by tahunbudget desc ";
                $query2=mysql_query($sql2) or die(mysql_error());
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
                $sData="select a.tahunbudget,a.kodeorg, a.tipebudget, a.kodebudget, a.kegiatan, ".
                    "a.noakun, a.volume, a.satuanv,a.rotasi,a.tutup,a.rasioh,a.volumerasio,a.rasiod from ".$dbname.".bgt_budget a left join ".
                    $dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".$blok1.
                    " and tipebudget='ESTATE'  and kodebudget!='UMUM' ".$tmbh.
                    " and b.kelompok in ('TB','BBT','LC') group by a.tahunbudget,a.kodeorg,a.tipebudget, a.kegiatan, a.noakun order by a.tahunbudget desc limit ".$offset.",".$limit." ";
                //exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                $no=0;
                while($rData=mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['tahunbudget']."</td>";
                    $tab.="<td>".$rData['tipebudget']."</td>";
                    $tab.="<td>".$rData['kodeorg']."</td>";
                    $tab.="<td>".$rData['noakun']."</td>";
                    $tab.="<td>".$optNmKeg[$rData['kegiatan']]."</td>";
                    
                    $tab.="<td align=right>".$rData['volume']."</td>";
                    $tab.="<td>".$rData['satuanv']."</td>";
                    $tab.="<td align=right>".$rData['rotasi']."</td>";
                    if($rData['tutup']==0)
                    {
						////perubahan indra
					if(isset($x) and $x==3) {
						$tab.="<td  align=center style='cursor:pointer;'><img id='detail_edit' title='Edit' class=zImgBtn onclick=\"filFieldHead3('".$rData['tahunbudget']."','".$rData['kodeorg']."','".$rData['tipebudget']."','".$rData['noakun']."','".$rData['kegiatan']."','".$rData['volume']."','".$rData['satuanv']."','".$rData['rotasi']."','".$rData['rasioh']."','".$rData['volumerasio']."','".$rData['rasiod']."')\" src='images/application/application_edit.png'/></td>";
					} else {
						$tab.="<td  align=center style='cursor:pointer;'><img id='detail_edit' title='Edit' class=zImgBtn onclick=\"filFieldHead('".$rData['tahunbudget']."','".$rData['kodeorg']."','".$rData['tipebudget']."','".$rData['noakun']."','".$rData['kegiatan']."','".$rData['volume']."','".$rData['satuanv']."','".$rData['rotasi']."','".$rData['rasioh']."','".$rData['volumerasio']."','".$rData['rasiod']."')\" src='images/application/application_edit.png'/></td>";
					}
					///
					
                    if($rData['tutup']!=1)$tab.="<td  align=center style='cursor:pointer;'><img id='detail_del' title='Delete' class=zImgBtn onclick=\"delFieldHead('".$rData['tahunbudget']."','".$rData['kodeorg']."','".$rData['kegiatan']."')\" src='images/application/application_delete.png'/></td>";
                    else $tab.="<td  align=center style='cursor:pointer;'>".$_SESSION['lang']['tutup']."</td>";
                    }
                    else
                    {
                        $tab.="<td>".$_SESSION['lang']['tutup']."</td>";
                    }
                    $tab.="</tr>";
                }
                  $tab.="
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariTrans(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariTrans(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    $tab.="<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
                $tab.="</tbody></table>";
                echo $tab;
            break;
		
		
            case'closeBudget':
                if($thnBudget=='')
                {
                    exit("Error: Tahun Budget Tidak Boleh Kosong");
                }
                $sQl="select distinct tutup from ".$dbname.".bgt_budget where ".$where2." and tutup=1";
               // exit("error".$sQl);
                $qQl=mysql_query($sQl) or die(mysql_error($conn));
                $row=mysql_num_rows($qQl);
                if($row!=1)
                {
                    $sUpdate="update ".$dbname.".bgt_budget set tutup=1 where ".$where2."";
                    //exit("error".$sUpdate);
                    if(mysql_query($sUpdate))
                        echo"";
                    else
                         echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                }
                else
                {
                    exit("Error:Sudah di Tutup");
                }
            break;
            case'getThnBudget':
            $optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
            $sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='ESTATE' and tutup=0 order by tahunbudget desc";
            $qThn=mysql_query($sThn) or die(mysql_error($conn));
            while($rThn=mysql_fetch_assoc($qThn))
            {
             $optThnTtp.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
            }
            echo $optThnTtp;
            break;
case 'sebarDoong':
        $var1=$_POST['var1'];
        $var2=$_POST['var2'];
        $var3=$_POST['var3'];
        $var4=$_POST['var4'];
        $var5=$_POST['var5'];
        $var6=$_POST['var6'];
        $var7=$_POST['var7'];
        $var8=$_POST['var8'];
        $var9=$_POST['var9'];
        $var10=$_POST['var10'];
        $var11=$_POST['var11'];
        $var12=$_POST['var12'];
        $rupiah=$_POST['rupe'];
        $fis=$_POST['fis'];
        $kunci=$_POST['kunci'];
        $str="delete from ".$dbname.".bgt_distribusi where kunci=".$kunci;
        mysql_query($str);
        $str="insert into ".$dbname.".bgt_distribusi (kunci, rp01, fis01, rp02, fis02, rp03, fis03, rp04, fis04, rp05, fis05, rp06, fis06, rp07, fis07, rp08, fis08, rp09, fis09, rp10, fis10, rp11, fis11, rp12, fis12, updateby)
              values(".$kunci.",  
                       ".$var1*$rupiah.",
                       ".$var1*$fis.",
                       ".$var2*$rupiah.",
                       ".$var2*$fis.",
                       ".$var3*$rupiah.",
                       ".$var3*$fis.",
                       ".$var4*$rupiah.",
                       ".$var4*$fis.",
                       ".$var5*$rupiah.",
                       ".$var5*$fis.",
                       ".$var6*$rupiah.",
                       ".$var6*$fis.",
                       ".$var7*$rupiah.",
                       ".$var7*$fis.",
                       ".$var8*$rupiah.",
                       ".$var8*$fis.",
                       ".$var9*$rupiah.",
                       ".$var9*$fis.",
                       ".$var10*$rupiah.",
                       ".$var10*$fis.",
                       ".$var11*$rupiah.",
                       ".$var11*$fis.",
                       ".$var12*$rupiah.",
                       ".$var12*$fis.",
                       ".$_SESSION['standard']['userid'].");";
        if(mysql_query($str))
        {}
        else
        {
            echo "Error;".mysql_error($conn);
        }   
        break;
 case 'getLuas':
        $blok=$_POST['blok'];
        $str="select hathnini from ".$dbname.".bgt_blok where kodeblok='".$blok."'";
        $res=mysql_query($str);
        $luas=0;
        while($bar=mysql_fetch_object($res))
        {
         $luas=$bar->hathnini;   
        }
        echo $luas;
        break;
	case 'getSebaranPemeliharaan':
		
		//exit("Error:MASUK");
	
		$res="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>
						<thead>
						<tr class=rowheader>
						<td></td>               
						<td>No</td>
						<td>Tahun Budget</td>
						<td>".$_SESSION['lang']['kodeblok']."</td>
						<td>".$_SESSION['lang']['kodeanggaran']."</td>
						<td>".$_SESSION['lang']['namakegiatan']."</td>
						<td>".$_SESSION['lang']['namabarang']."</td>
						<td>".$_SESSION['lang']['kodevhc']."</td>
						<td>".$_SESSION['lang']['total']."</td>";
		foreach($arrBln as $brsBulan =>$listBln) {
			$res.="<td>".$listBln." Rp</td>";
		}
		
		$sLoad="select * from ".$dbname.".bgt_budget_detail a left join ".$dbname.".setup_kegiatan b ".
			"on a.kegiatan=b.kodekegiatan where b.kelompok in ('TB','LC','PNN','TBM','TM') and a.kodeorg like '".
				$_SESSION['empl']['lokasitugas']."%' and kodebudget!='UMUM' and tipebudget='ESTATE' and tutup=0 and tahunbudget=".$thnbudgetHeader;
				
		//echo $sLoad;		
		$resSebaran = fetchData($sLoad);
		
		## Option
		$keg = '';$brg='';
		foreach($resSebaran as $row) {
				if($keg!='') {
						$keg.=',';
				}
				if($brg!='') {
						$brg.=',';
				}
				$keg.="'".$row['kegiatan']."'";
				$brg.="'".$row['kodebarang']."'";
		}
		if($keg=='') {
				$optNmKeg = array();
		} else {
				$optNmKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
						"kodekegiatan in (".$keg.")");
		}
		if($brg=='') {
				$optNmBrg = array();
		} else {
				$optNmBrg = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
						"kodebarang in (".$brg.")",'0',true);
		}
		
		$res.="<td>Action</td></tr>
				</thead><tbody id=containDataTotal>";
		foreach($resSebaran as $key=>$row) {
				$res.="<tr class=rowcontent style='cursor:pointer;' id=barisSebaran".$key.">";
				
				//update ind
				/*$dtClik="onclick=\"getForm('Sebaran','<fieldset style=\'width:520px;height:400px;\'>".
						"<legend>Sebaran Per Bulan</legend><div id=containerForm style=\'overflow:auto;height:450px;".
						"width:480px\'></div><input type=hidden id=keyId2 value=".$row['kunci']." /></fieldset>',".
						$row['rupiah'].",".$row['jumlah'].",'".$row['kodebudget']."',event,true);\"";*/
		
				$res.="<td><input type=checkbox onclick=sebarkanRawat('".$row['kunci']."',".
						$key.",this,".$row['rupiah'].",".$row['jumlah']."); title='Sebarkan sesuai proporsi diatas'></td>";
				$res.="<td >".($key+1)."</td>";
				$res.="<td align='center' ".$dtClik.">".$row['tahunbudget']."</td>";
				$res.="<td align='center' ".$dtClik.">".$row['kodeorg']."</td>";
				$res.="<td align='center' ".$dtClik.">".$row['kodebudget']."</td>";
				$res.="<td align='center' ".$dtClik.">".$optNmKeg[$row['kegiatan']]."</td>";
				$res.="<td align='center' ".$dtClik.">".$optNmBrg[$row['kodebarang']]."</td>";
				$res.="<td align='right' ".$dtClik.">".$row['kodevhc']."</td>";
				//$res.="<td align='center' ".$dtClik.">".number_format($row['rupiah'],2)."</td>";
				$res.="<td align='center' ".$dtClik.">".number_format($row['rupiah'],0)."</td>";
				foreach($arrBln as $brsBln =>$listData) {
						if(strlen($brsBln)<2) {
								$b="0".$brsBln;
						} else {
								$b=$brsBln;
						}
						//$res.="<td align='right'>".number_format($row['rp'.$b],2)."</td>";
						$res.="<td align='right'>".number_format($row['rp'.$b],0)."</td>";
				}
		
				$res.="<td align=center  style='cursor:pointer;'><img src=\"images/zoom.png\"".
						" class=\"resicon\" title='sebarang_".$row['kunci']."' ".$dtClik." /></td>";
				$res.="</tr>";
		}
		$res.="</tbody></table>";
		echo $res;
		break;
	default:
	break;
}
?>