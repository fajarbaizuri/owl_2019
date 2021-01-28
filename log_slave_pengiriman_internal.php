<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method";
$method=$_POST['method'];
$id_supplier=$_POST['id_supplier'];
$jlhKoli=$_POST['jlhKoli'];
$kpd=$_POST['kpd'];
$srtJalan=$_POST['srtJalan'];
$biaya=$_POST['biaya'];
$lokPenerimaan=$_POST['lokPenerimaan'];
$tglKrm=tanggalsystem($_POST['tglKrm']);
$ket=$_POST['ket'];
$karyId=$_POST['karyId'];
$idLokasi=$_POST['idLokasi'];
$optSupplier=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nopo=$_POST['nopo'];
$idNomor=$_POST['idNomor'];


	switch($method)
	{
		case'insert':
		if($id_supplier==''||$tglKrm==''||$kpd==''||$jlhKoli==''||$biaya=='')
		{
			echo"warning:Field tidak boleh kosong";
			exit();
		}
                
                $sIns="insert into ".$dbname.".log_pengiriman_ht ( jumlahkoli, expeditor, tanggalkirim, pengirim, lokasipengirim, nosj, kepada, keterangan, biaya,lokasipenerima) 
                       values ('".$jlhKoli."','".$id_supplier."','".$tglKrm."','".$_SESSION['standard']['userid']."','".$_SESSION['empl']['lokasitugas']."'
                               ,'".$srtJalan."','".$kpd."','".$ket."','".$biaya."','".$lokPenerimaan."')";
                if(!mysql_query($sIns))
                {
                        echo"Gagal".mysql_error($conn);
                }
			
		break;
		case'loadData':
		$no=0;	 
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $sql2="select * from ".$dbname.".log_pengiriman_ht 
                      where lokasipengirim ='".$_SESSION['empl']['lokasitugas']."' and pengirim='".$_SESSION['standard']['userid']."' order by nomor desc";
                //echo $sql2;
                $query2=mysql_query($sql2) or die(mysql_error());
                $jlhbrs=mysql_num_rows($query2);
                if($jlhbrs!=0)
                {
		$str="select * from ".$dbname.".log_pengiriman_ht 
                      where lokasipengirim ='".$_SESSION['empl']['lokasitugas']."' and pengirim='".$_SESSION['standard']['userid']."' order by nomor desc limit ".$offset.",".$limit." ";
		$res=mysql_query($str);
                
		while($bar=mysql_fetch_assoc($res))
		{
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$optSupplier[$bar['expeditor']]."</td>
		<td>".tanggalnormal($bar['tanggalkirim'])."</td>
		<td align=right>".$bar['jumlahkoli']."</td>
		<td>".$optKary[$bar['kepada']]."</td>
		<td>".$bar['lokasipenerima']."</td>
                <td>".$bar['nosj']."</td>
                <td align=right>".number_format($bar['biaya'],2)."</td>
		<td>
                    <img src=images/application/application_add.png class=resicon  title='Add Detail' onclick=\"addData('".$bar['nomor']."','".$_SESSION['lang']['find']."',event);\">
                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['nomor']."');\"> 
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['nomor']."');\">
		  </td>
		
		</tr>";	
                
		} 
                echo"
		<tr class=rowheader><td colspan=10 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
                }
                else
                {
                    echo "<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
		case'update':
                  
                    if($id_supplier==''||$tglKrm==''||$kpd==''||$jlhKoli==''||$biaya=='')
                    {
                            echo"warning:Field tidak boleh kosong";
                            exit();
                    }
		   $sIns="update ".$dbname.".log_pengiriman_ht set jumlahkoli='".$jlhKoli."', expeditor='".$id_supplier."', tanggalkirim='".$tglKrm."',nosj='".$srtJalan."', kepada='".$kpd."', keterangan='".$ket."', biaya='".$biaya."',lokasipenerima='".$lokPenerimaan."' 
                        where nomor='".$idNomor."'";
                    //exit("Error:".$sIns);
			if(!mysql_query($sIns))
			{
				echo"Gagal".mysql_error($conn);
			}
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".log_pengiriman_ht where nomor='".$idNomor."'";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select * from ".$dbname.".log_pengiriman_ht where nomor='".$idNomor."'";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		echo $rDet['nomor']."###".$rDet['expeditor']."###".tanggalnormal($rDet['tanggalkirim'])."###".$rDet['jumlahkoli']."###".$rDet['kepada']."###".$rDet['lokasipenerima']."###".$rDet['nosj']."###".$rDet['biaya']."###".$rDet['keterangan'];
		break;
                case'getLokasi':
                    if($idLokasi=='')
                    {
                    $sLokTgs="select distinct lokasitugas from ".$dbname.".datakaryawan where karyawanid='".$kpd."'";
                    $qLokTgs=mysql_query($sLokTgs) or die(mysql_error());
                    $rLokTgs=mysql_fetch_assoc($qLokTgs);
                    echo $rLokTgs['lokasitugas'];
                    }
                    else
                    {
                        echo $idLokasi;
                    }
                    unset($karyId,$idLokasi);
                break;
                case'cariData':
                           
                            $tab.="<table cellspacing=1 border=0 class=data>
                            <thead>
                            <tr class=rowheader><td>No</td>
                            <td>".$_SESSION['lang']['nopo']."</td>
                            <td>".$_SESSION['lang']['pt']."</td>
                            <td>".$_SESSION['lang']['tanggal']."</td>
                            <td>".$_SESSION['lang']['purchaser']."</td>
                            </tr>
                            </thead>
                            </tbody>";
                            if($nopo!='')
                            {
                                    $str="select * from ".$dbname.".log_poht where nopo like '%".$nopo."%'
                                    order by tanggal desc,nopo desc";
                                    $res=mysql_query($str);
                                    $no=0;
                                    while($bar=mysql_fetch_object($res))
                                    {
                                    //ambil userid purchaser
                                    $purchaser='';
                                    if($bar->karyawanid!='')
                                    {
                                    $str="select namauser from ".$dbname.".user where karyawanid=".$bar->karyawanid;
                                    $resv=mysql_query($str);
                                    while($barv=mysql_fetch_object($resv))
                                    {
                                    $purchaser=$barv->namauser;
                                    }
                                    }
                                    $no+=1;
                                    $tab.="
                                    <tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickPo('".$bar->nopo."')><td>".$no."</td>
                                    <td>".$bar->nopo."</td>
                                    <td>".$bar->kodeorg."</td>
                                    <td>".tanggalnormal($bar->tanggal)."</td>
                                    <td>".$purchaser."</td>
                                    </tr>
                                    ";


                                    }	 	
                            }
                            $tab.="</tbody>
                            <tfoot>
                            </tfoot>
                            </table>";	
                            
                            $tab2.="<table class=sortable cellpadding=1 cellspacing=1 border=0>";
                            $tab2.="<thead><tr class=rowheader><td>".$_SESSION['lang']['nopo']."</td><td>Action</td></tr></thead><tbody>";
                            $sData="select distinct * from ".$dbname.".log_pengiriman_dt where nomor='".$idNomor."' order by nopo desc";
                            // echo $sData;
                            $qData=mysql_query($sData) or die(mysql_error());
                            while($rData=mysql_fetch_assoc($qData))
                            {
                                $tab2.="<tr class=rowcontent><td>".$rData['nopo']."</td>";
                                $tab2.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['nomor']."','".$rData['nopo']."');\"></td>";
                            }
                            
                            $tab2.="</tbody></table>";
                           
                               echo $tab."###".$tab2;
                            
                break;
                case'insertDetail':
                    $sCek="select distinct * from ".$dbname.".log_pengiriman_dt where nomor='".$idNomor."' and nopo='".$nopo."'";
                    //echo $sCek;
                    $qCek=mysql_query($sCek) or die(mysql_error());
                    $rRow=mysql_num_rows($qCek);
                    if($rRow>0)
                    {
                        exit("Error:Nopo Sudah Ada".$sCek);
                    }
                    else
                    {
                        $sIns="insert into ".$dbname.".log_pengiriman_dt (nomor,nopo) value ('".$idNomor."','".$nopo."')";
                        if(!mysql_query($sIns))
                        {
                                echo"Gagal".mysql_error($conn);
                        }
                        
                    }
                break;
                case'deleteDetail':
                    $sDel="delete from ".$dbname.".log_pengiriman_dt where nomor='".$idNomor."' and nopo='".$nopo."'";
                     if(!mysql_query($sDel))
                     {
                            echo"Gagal".mysql_error($conn);
                     }
                break;
		default:
		break;
	}
?>