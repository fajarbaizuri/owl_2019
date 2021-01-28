<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$kdOrg=$_POST['kdOrg'];
$noTph=$_POST['noTph'];
$tgl=tanggalsystem($_POST['tgl']);

$idAsisten=$_POST['idAsisten'];
$idMandor=$_POST['idMandor']; 
$idMandor_1=$_POST['idMandor_1'];
$idMandor_2=$_POST['idMandor_2'];
$idMandor_3=$_POST['idMandor_3'];
$idPemeriksa=$_POST['idPemeriksa'];

$thnNo=explode("-",$_POST['tgl']);
$notransaksi=$kdOrg."/".$thnNo[2].$thnNo[1].$thnNo[0]."/PB";
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$noTrans=$_POST['noTrans'];
$periode=$_POST['periode'];
$karyawnId=$_POST['karyawnId'];
$jjgPriksa=$_POST['jjgPriksa'];
$mnth=$_POST['mnth'];
$krgMtng=$_POST['krgMtng'];
$lwtMtng=$_POST['lwtMtng'];
$tngkiPnjng=$_POST['tngkiPnjng'];
$mtng=$_POST['mtng'];


	switch($proses)
	{
            case'getNtph':
                $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $optKary=$optKode;
                $sTph="select distinct kode,keterangan from ".$dbname.".kebun_5tph where kodeorg='".$kdOrg."'";
                $qTph=mysql_query($sTph) or die(mysql_error());
                while($rTph=mysql_fetch_assoc($qTph))
                {
                    if($noTph!='')
                    {
                        $optKode.="<option value='".$rTph['kode']."' ".($rTph['kode']==$noTph?"selected":'').">".$rTph['keterangan']."</option>";
                    }
                    else
                    {
                        $optKode.="<option value='".$rTph['kode']."'>".$rTph['keterangan']."</option>";
                    }
                }
                $sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
                        where (lokasitugas='".$_SESSION['empl']['lokasitugas']."' or subbagian='".$kdOrg."') and tipekaryawan in (3,4)";//kht dan khl
                $qKary=mysql_query($sKary) or die(mysql_error());
                while($rKary=  mysql_fetch_assoc($qKary))
                {
                    if($karyawanId!='')
                    {
                        $optKary.="<option value='".$rKary['karyawanid']."' ".($rKary['karyawanid']==$karyawanId?"selected":"").">".$rKary['namakaryawan']."</option>";
                    }
                    else
                    {
                        $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
                    }
                }
                echo $optKode."###".$optKary;
            
            break;
            case'insert':
            if($kdOrg==''||$idMandor==''||$tgl==''||$idAsisten==''||$idMandor_1==''||$idMandor_2==''||$idMandor_3==''||$idPemeriksa='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
            
            $sCek="select distinct notransaksi from ".$dbname.".kebun_qc_panenht where notransaksi='".$notransaksi."'";
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_panenht (notransaksi, kodeorg, tanggal, asisten,pemeriksa, mandor1, mandorpanen1, 
                          mandorpanen2, mandorpanen3,updateby) 
                          values ('".$notransaksi."','".$kdOrg."','".$tgl."','".$idAsisten."','".$_POST['idPemeriksa']."','".$idMandor."',
                          '".$idMandor_1."','".$idMandor_2."','".$idMandor_3."','".$_SESSION['standard']['userid']."')";
                //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
                else
                {
                    echo $notransaksi;
                }
            }
            else
            {
                exit("Error:Data Sudah Ada");
            }
            break;
            case'update':
            if($idMandor==''||$idAsisten==''||$idMandor_1==''||$idMandor_2==''||$idMandor_3==''||$idPemeriksa='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
             $sInsert="update ".$dbname.".kebun_qc_panenht set asisten='".$idAsisten."', pemeriksa='".$_POST['idPemeriksa']."', mandor1='".$idMandor."', mandorpanen1='".$idMandor_1."', 
                       mandorpanen2='".$idMandor_2."', mandorpanen3='".$idMandor_3."', updateby='".$_SESSION['standard']['userid']."' where notransaksi='".$notransaksi."'";
            if(!mysql_query($sInsert))
            {
                echo "DB Error : ".mysql_error($conn);
            }
            
            break;
            
            case'loadNewData':
            $limit=20;
            $page=0;
            if(isset($_POST['page']))
            {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
            }
            $offset=$page*$limit;

            //$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_panenht a left join ".$dbname.".kebun_qc_panendt b on a.notransaksi=b.notransaksi
                where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_panenht a left join ".$dbname.".kebun_qc_panendt b on a.notransaksi=b.notransaksi
                    where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.",".$limit."";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$optNmKary[$rData['karyawanid']]."</td>";
                    $tab.="<td align=left>".$rData['notph']."</td>";
                    $tab.="<td align=right>".$rData['diperiksa']."</td>";
                    $tab.="<td align=right>".$rData['mentah']."</td>";
                    $tab.="<td align=right>".$rData['kmatang']."</td>";
                    $tab.="<td align=right>".$rData['matang']."</td>";
                    $tab.="<td align=right>".$rData['lmatang']."</td>";
                    $tab.="<td align=right>".$rData['tpanjang']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['pemeriksa']."','".$rData['asisten']."','".$rData['mandor1']."','".$rData['mandorpanen1']."','".$rData['mandorpanen2']."','".$rData['mandorpanen3']."','".$rData['karyawanid']."','".$rData['notph']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."','".$rData['karyawanid']."','".$rData['notph']."');\" >
                            <img src=images/delete1.png class=resicon  title='Delete Semua Data di kodeorganisasi ".$rData['kodeorg'].",tanggal ".tanggalnormal($rData['tanggal'])." ' onclick=\"delDataAll('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."');\" >";
                    
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=16 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=11 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;
            case'cariData':
            $limit=20;
            $page=0;
            if(isset($_POST['page']))
            {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
            }
            $offset=$page*$limit;
            if($kdOrg!='')
            {
                $where.="and kodeorg='".$kdOrg."'";
            }
            if($periode!='')
            {
                $where.="and substr(tanggal,1,7)='".$periode."'";
            }
            //$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_panenht a left join ".$dbname.".kebun_qc_panendt b on a.notransaksi=b.notransaksi
                where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_panenht a left join ".$dbname.".kebun_qc_panendt b on a.notransaksi=b.notransaksi
                    where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by `tanggal` desc limit ".$offset.",".$limit."";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$optNmKary[$rData['karyawanid']]."</td>";
                    $tab.="<td align=left>".$rData['notph']."</td>";
                    $tab.="<td align=right>".$rData['diperiksa']."</td>";
                    $tab.="<td align=right>".$rData['mentah']."</td>";
                    $tab.="<td align=right>".$rData['kmatang']."</td>";
                    $tab.="<td align=right>".$rData['matang']."</td>";
                    $tab.="<td align=right>".$rData['lmatang']."</td>";
                    $tab.="<td align=right>".$rData['tpanjang']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['pemeriksa']."','".$rData['asisten']."','".$rData['mandor1']."','".$rData['mandorpanen1']."','".$rData['mandorpanen2']."','".$rData['mandorpanen3']."','".$rData['karyawanid']."','".$rData['notph']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."','".$rData['karyawanid']."','".$rData['notph']."');\" >
                            <img src=images/delete1.png class=resicon  title='Delete Semua Data di kodeorganisasi ".$rData['kodeorg'].",tanggal ".tanggalnormal($rData['tanggal'])." ' onclick=\"delDataAll('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."');\" >";
                    
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=11 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=11 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;

            case'insert_detail':
               
            if($karyawnId==''||$jjgPriksa==''||$mnth==''||$krgMtng==''||$lwtMtng==''||$tngkiPnjng==''||$mtng==''||$_POST['noTph']=='')
            {
                exit("Error:Field Tidak Boleh Kosong ");
            }
            $totSma=$mnth+$krgMtng+$lwtMtng+$tngkiPnjng+$mtng;
            if($totSma>$jjgPriksa)
            {
                exit("Error:Total Lebih Besar dari Janjang di Periksa");
            }
            $sCek="select distinct notransaksi,karyawanid,notph from ".$dbname.".kebun_qc_panendt 
                   where notransaksi='".$noTrans."' and karyawanid='".$karyawnId."' and notph='".$noTph."'";
            //exit("Error:".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_panendt (notransaksi, notph, karyawanid, diperiksa, mentah, kmatang, matang, lmatang, tpanjang) 
                          values ('".$noTrans."','".$noTph."','".$karyawnId."','".$jjgPriksa."','".$mnth."','".$krgMtng."',
                          '".$mtng."','".$lwtMtng."','".$tngkiPnjng."')";
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            }
            else
            {
                exit("Error:Data Sudah Ada");
            }
            break;
            case'update_detail':
            if($karyawnId==''||$jjgPriksa==''||$mnth==''||$krgMtng==''||$lwtMtng==''||$tngkiPnjng==''||$mtng==''||$_POST['noTph']=='')
            {
                exit("Error:Field Tidak Boleh Kosong jamhari".$sInsert);
            }
            $totSma=$mnth+$krgMtng+$lwtMtng+$tngkiPnjng+$mtng;
            if($totSma>$jjgPriksa)
            {
                exit("Error:Total Lebih Besar dari Janjang di Periksa");
            }
            $sInsert="update ".$dbname.".kebun_qc_panendt set diperiksa='".$jjgPriksa."', mentah='".$mnth."', kmatang='".$krgMtng."', matang='".$mtng."', lmatang='".$lwtMtng."', 
                      tpanjang='".$tngkiPnjng."' 
                      where notransaksi='".$noTrans."' and karyawanid='".$karyawnId."' and notph='".$noTph."'";
            //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'delDetail':
                $sDel="delete from ".$dbname.".kebun_qc_panendt where notransaksi='".$noTrans."' and karyawanid='".$karyawnId."' and notph='".$noTph."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'loadDetail':
            $sDetail="select distinct * from ".$dbname.".kebun_qc_panendt where notransaksi='".$noTrans."'";
            //    exit("Error".$sDetail);
            $qDetail=mysql_query($sDetail) or die(mysql_error());
            while($rData=mysql_fetch_assoc($qDetail))
            {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=left>".$optNmKary[$rData['karyawanid']]."</td>";
                    $tab.="<td align=right>".$rData['notph']."</td>";
                    $tab.="<td align=right>".$rData['diperiksa']."</td>";
                    $tab.="<td align=right>".$rData['mentah']."</td>";
                    $tab.="<td align=right>".$rData['kmatang']."</td>";
                    $tab.="<td align=right>".$rData['matang']."</td>";
                    $tab.="<td align=right>".$rData['lmatang']."</td>";
                    $tab.="<td align=right>".$rData['tpanjang']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' 
                        onclick=\"getData('".$rData['notransaksi']."','".$rData['karyawanid']."','".$rData['notph']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['notransaksi']."','".$rData['karyawanid']."','".$rData['notph']."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
            }
            echo $tab;
            break;
            case'getDetail':
            $sData="select distinct * from ".$dbname.".kebun_qc_panendt where notransaksi='".$noTrans."' and notph='".$noTph."' and karyawanid='".$karyawnId."'";
            $qData=mysql_query($sData) or die(mysql_error());
            $rData=mysql_fetch_assoc($qData);
            echo $rData['karyawanid']."###".$rData['notph']."###".$rData['diperiksa']."###".$rData['mentah']."###".$rData['kmatang']."###".$rData['matang']."###".$rData['lmatang']."###".$rData['tpanjang'];
            break;
            case'delDetailAll':
             $sDel="delete from ".$dbname.".kebun_qc_panenht where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;

		default:
		break;
	}


?>