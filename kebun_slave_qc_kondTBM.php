<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$afdelingId=$_POST['afdelingId'];
$tgl=tanggalsystem($_POST['tgl']);
$idAsisten=$_POST['idAsisten'];
$idPetugas=$_POST['idPetugas']; 


$thnNo=explode("-",$_POST['tgl']);
$notransaksi=$afdelingId."/".$thnNo[2].$thnNo[1].$thnNo[0]."/KLTBM";
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$noTrans=$_POST['noTrans'];
$periode=$_POST['periode'];
$kdBlok=$_POST['kdBlok'];
$piringan=$_POST['piringan'];
$gwngan=$_POST['gwngan'];
$pmupukn=$_POST['pmupukn'];
$hpt=$_POST['hpt'];



	switch($proses)
	{
            case'getBlok':
                $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
               
                $sTph="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$afdelingId."'";
                //exit("Error:".$sTph);
                $qTph=mysql_query($sTph) or die(mysql_error());
                while($rTph=mysql_fetch_assoc($qTph))
                {
                    if($noTph!='')
                    {
                        $optKode.="<option value='".$rTph['kodeorganisasi']."' ".($rTph['kodeorganisasi']==$kdBlok?"selected":'').">".$rTph['namaorganisasi']."</option>";
                    }
                    else
                    {
                        $optKode.="<option value='".$rTph['kodeorganisasi']."'>".$rTph['namaorganisasi']."</option>";
                    }
                }
               
                echo $optKode;
            
            break;
            case'insert':
            if($afdelingId==''||$idPetugas==''||$tgl==''||$idAsisten=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
            
            $sCek="select distinct notransaksi from ".$dbname.".kebun_qc_kondisitbmht where notransaksi='".$notransaksi."'";
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_kondisitbmht (notransaksi, afdeling, tanggal, asisten,petugas,updateby) 
                          values ('".$notransaksi."','".$afdelingId."','".$tgl."','".$idAsisten."','".$idPetugas."','".$_SESSION['standard']['userid']."')";
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
            if($afdelingId==''||$idPetugas==''||$tgl==''||$idAsisten=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
             $sInsert="update ".$dbname.".kebun_qc_kondisitbmht set asisten='".$idAsisten."', petugas='".$idPetugas."', updateby='".$_SESSION['standard']['userid']."' where notransaksi='".$notransaksi."'";
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
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_kondisitbmht a left join ".$dbname.".kebun_qc_kondisitbmdt b on a.notransaksi=b.notransaksi
                where substr(`afdeling`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_kondisitbmht a left join ".$dbname.".kebun_qc_kondisitbmdt b on a.notransaksi=b.notransaksi
                    where substr(`afdeling`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.",".$limit."";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$rData['afdeling']."</td>";
                    $tab.="<td align=left>".$rData['piringan']."</td>";
                    $tab.="<td align=right>".$rData['gawangan']."</td>";
                    $tab.="<td align=right>".$rData['pemupukan']."</td>";
                    $tab.="<td align=right>".$rData['hpt']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['afdeling']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['asisten']."','".$rData['petugas']."','".$rData['kodeorg']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."','".$rData['kodeorg']."');\" >
                            <img src=images/delete1.png class=resicon  title='Delete Semua Data di kodeorganisasi ".$rData['afdeling'].",tanggal ".tanggalnormal($rData['tanggal'])." ' onclick=\"delDataAll('".$rData['notransaksi']."','".$rData['afdeling']."','".tanggalnormal($rData['tanggal'])."');\" >";
                    
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=9 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=9 align=center>
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
            if($afdelingId!='')
            {
                $where.="and afdeling='".$afdelingId."'";
            }
            if($periode!='')
            {
                $where.="and substr(tanggal,1,7)='".$periode."'";
            }
            //$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_kondisitbmht a left join ".$dbname.".kebun_qc_kondisitbmdt b on a.notransaksi=b.notransaksi
                where substr(`afdeling`,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_kondisitbmht a left join ".$dbname.".kebun_qc_kondisitbmdt b on a.notransaksi=b.notransaksi
                    where substr(`afdeling`,1,4)='".$_SESSION['empl']['lokasitugas']."' ".$where." order by `tanggal` desc limit ".$offset.",".$limit."";
                //echo $sData;
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$rData['afdeling']."</td>";
                    $tab.="<td align=left>".$rData['piringan']."</td>";
                    $tab.="<td align=right>".$rData['gawangan']."</td>";
                    $tab.="<td align=right>".$rData['pemupukan']."</td>";
                    $tab.="<td align=right>".$rData['hpt']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['afdeling']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['asisten']."','".$rData['petugas']."','".$rData['kodeorg']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."','".$rData['kodeorg']."');\" >
                            <img src=images/delete1.png class=resicon  title='Delete Semua Data di kodeorganisasi ".$rData['afdeling'].",tanggal ".tanggalnormal($rData['tanggal'])." ' onclick=\"delDataAll('".$rData['notransaksi']."','".$rData['afdeling']."','".tanggalnormal($rData['tanggal'])."');\" >";
                    
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=9 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=9 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;

            case'insert_detail':
               
            if($kdBlok==''||$piringan==''||$gwngan==''||$pmupukn==''||$hpt=='')
            {
                exit("Error:Field Tidak Boleh Kosong ");
            }
  
            $sCek="select distinct notransaksi,kodeorg from ".$dbname.".kebun_qc_kondisitbmdt 
                   where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
            //exit("Error:".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_kondisitbmdt (notransaksi, kodeorg, piringan, gawangan, pemupukan, hpt) 
                          values ('".$noTrans."','".$kdBlok."','".$piringan."','".$gwngan."','".$pmupukn."','".$hpt."')";
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
             if($kdBlok==''||$piringan==''||$gwngan==''||$pmupukn==''||$hpt=='')
            {
                exit("Error:Field Tidak Boleh Kosong ");
            }
            $sInsert="update ".$dbname.".kebun_qc_kondisitbmdt set piringan='".$piringan."', gawangan='".$gwngan."', pemupukan='".$pmupukn."', hpt='".$hpt."'
                      where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
            //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'delDetail':
                $sDel="delete from ".$dbname.".kebun_qc_kondisitbmdt where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'loadDetail':
            $sDetail="select distinct * from ".$dbname.".kebun_qc_kondisitbmdt where notransaksi='".$noTrans."'";
            //    exit("Error".$sDetail);
            $qDetail=mysql_query($sDetail) or die(mysql_error());
            while($rData=mysql_fetch_assoc($qDetail))
            {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=left>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".$rData['piringan']."</td>";
                    $tab.="<td align=right>".$rData['gawangan']."</td>";
                    $tab.="<td align=right>".$rData['pemupukan']."</td>";
                    $tab.="<td align=right>".$rData['hpt']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' 
                        onclick=\"getData('".$rData['notransaksi']."','".$rData['kodeorg']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['notransaksi']."','".$rData['kodeorg']."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
            }
            echo $tab;
            break;
            case'getDetail':
            $sData="select distinct * from ".$dbname.".kebun_qc_kondisitbmdt where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
            $qData=mysql_query($sData) or die(mysql_error());
            $rData=mysql_fetch_assoc($qData);
            echo $rData['kodeorg']."###".$rData['piringan']."###".$rData['gawangan']."###".$rData['pemupukan']."###".$rData['hpt'];
            break;
            case'delDetailAll':
             $sDel="delete from ".$dbname.".kebun_qc_kondisitbmht where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;

		default:
		break;
	}


?>