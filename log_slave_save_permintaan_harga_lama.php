<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
include_once('lib/zLib.php');


    $_POST['method']!=''?$method=$_POST['method']:$method=$_GET['method'];
    $_POST['no_permintaan']!=''? $nomor=$_POST['no_permintaan']:$nomor=$_GET['no_permintaan'];
   // echo"warning".$method;
    $_POST['ckno_permintaan']!=''?$no_prmntan=$_POST['ckno_permintaan']:$no_prmntan=$_GET['ckno_permintaan'];
    $tgl=tanggalsystem($_POST['tgl']);
    $supplier_id=$_POST['id_supplier'];
    $id_user=$_POST['user_id'];
    $kd_brg=$_POST['kdbrg'];
    $mtUang=$_POST['mtUang'];
   
    $optBarang=makeOption($dbname,'log_5masterbarang', 'kodebarang,namabarang');
    $optSat=makeOption($dbname,'log_5masterbarang', 'kodebarang,satuan');
    $optNmkry=makeOption($dbname,'datakaryawan', 'karyawanid,namakaryawan');
    $kdNopp=$_POST['kdNopp'];
    $nilDiskon=$_POST['nilDiskon'];
    $diskonPersen=$_POST['diskonPersen'];
    $nilPPn=$_POST['nilPPn'];
    $nilaiPermintaan=$_POST['nilaiPermintaan'];
    $subTotal=$_POST['subTotal'];
    $termPay=$_POST['termPay'];
    $idFranco=$_POST['idFranco'];
    $stockId=$_POST['stockId'];
    $ketUraian=$_POST['ketUraian'];
         
        
    switch ($method)
    {
       case'getSupplierNm':
                    echo"<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodesupplier']."</td>
                        <td>".$_SESSION['lang']['namasupplier']."</td>
                        </tr><tbody>
                        ";
                 $sSupplier="select namasupplier,supplierid from ".$dbname.".log_5supplier where namasupplier like '%".$_POST['nmSupplier']."%' and kodekelompok='S001'";
                 $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
                 while($rSupplier=mysql_fetch_assoc($qSupplier))
                 {
                     $no+=1;
                     echo"<tr class=rowcontent onclick=setData('".$rSupplier['supplierid']."')>
                         <td>".$no."</td>
                         <td>".$rSupplier['supplierid']."</td>
                         <td>".$rSupplier['namasupplier']."</td>
                    </tr>";
                 }
                    echo"</tbody></table></div>";
         break;
        case'getNopp':
                    echo"<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 cellspacing=1 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['nopp']."</td>
                        
                        </tr><tbody>
                        ";
                 //$sSupplier="select a.nopp  from ".$dbname.".log_prapoht a left join ".$dbname.".log_podt b on a.nopp=b.nopp where a.nopp like '%".$kdNopp."%' and close='2' and b.nopo is null";
                 $sSupplier="select distinct nopp from ".$dbname.".log_prapoht where nopp like '%".$kdNopp."%'";
                 //exit("Error".$sSupplier);
                 $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
                 while($rSupplier=mysql_fetch_assoc($qSupplier))
                 {
                     $no+=1;
                     echo"<tr class=rowcontent onclick=setDataNopp('".$rSupplier['nopp']."')>
                         <td>".$no."</td>
                         <td>".$rSupplier['nopp']."</td>
                         
                    </tr>";
                 }
                    echo"</tbody></table></div>";
         break;
        case 'create_no':
        $tgl=  date('Ymd');
        $bln = substr($tgl,4,2);
        $thn = substr($tgl,0,4);
        $no="/".date('Y')."/DPH/MA";
        $ql="select `nomor` from ".$dbname.".`log_perintaanhargaht` where nomor like '%".$no."%' order by `nomor` desc limit 0,1";
        $qr=mysql_query($ql) or die(mysql_error());
        $rp=mysql_fetch_object($qr);
        $awal=substr($rp->nomor,0,3);
        $awal=intval($awal);
        $cekbln=substr($rp->nomor,4,2);
        $cekthn=substr($rp->nomor,7,4);
        //if(($bln!=$cekbln)&&($thn!=$cekthn))
        if($thn!=$cekthn)
        {
        //echo $awal; exit();
        $awal=1;
        }
        else
        {
        $awal++;
        }
        $counter=addZero($awal,3);
        $no_permintaan=$counter."/".$bln."/".$thn."/DPH/MA";
        echo $no_permintaan;
            break;
            case 'insert':
			//echo "warning: masuk";
                
		foreach($_POST['kdbrg']	as $rey=>$Opr)
		{
			if($Opr==''||$_POST['jmlh'][$rey]=='')
			{
				echo "Warning :  Data Barang, Jumlah dan kurs Tidak Boleh Kosong";
				exit();
			}
			else
			{
				$sql="select * from ".$dbname.". log_perintaanhargaht where `nomor`='".$no_prmntan."'"; //echo "warning:".$sql;exit();
				$query=mysql_query($sql) or die(mysql_error());
				$res=mysql_fetch_row($query);
				//echo $res; exit();
				if($res<1)
				{
                                    $idFranco==''?$idFranco=0:$idFranco=$idFranco;
                                    $stockId==''?$stockId=0:$stockId=$stockId;
                                    $termPay==''?$termPay=0:$termPay=$termPay;
					$ins="insert into ".$dbname.".log_perintaanhargaht (nomor, tanggal, purchaser, supplierid, nopp, id_franco, stock, catatan, sisbayar, ppn, subtotal, diskonpersen, nilaidiskon, nilaipermintaan) values 
                                            ('".$no_prmntan."','".$tgl."','".$id_user."','".$supplier_id."','".$kdNopp."','".$idFranco."','".$stockId."','".$ketUraian."','".$termPay."','".$nilPPn."','".$subTotal."','".$diskonPersen."','".$nilDiskon."','".$nilaiPermintaan."')";
					//$qry=mysql_query($ins) or die(mysql_error());
                                        //exit("Error".$ins);
                                        if(mysql_query($ins))
                                        {
					$sql2="select * from ".$dbname.".log_permintaanhargadt where `nomor`='".$no_prmntan."'";
					$query2=mysql_query($sql2) or die(mysql_error());
					$res2=mysql_fetch_row($query2);
					if($res2<1)
					{
						foreach($_POST['kdbrg'] as $row=>$Act)
						{

								$kdbrg=$Act;
								$spec=$_POST['rspek'][$row];
								$hrg=$_POST['price'][$row];
								$hrg=str_replace($hrg,',','');
								$jmlh=$_POST['jmlh'][$row];
								$mtuang=$_POST['kurs'][$row];
								$jmlhKurs=$_POST['jmlhKurs'][$row];
								//$jmlh2=str_replace($jmlh,',','');
								$nopp=$_POST['nopp'][$row];
                                                                $tglDr=$_POST['tglDari'][$row];
                                                                $tglSmp=$_POST['tglSamp'][$row];
                                                                if($tglSmp=='')
                                                                {
                                                                    $tglSmp='00-00-0000';
                                                                }
                                                                if($tglDr=='')
                                                                {
                                                                    $tglDr='00-00-0000';
                                                                }
                                                                if($mtuang=='')
                                                                {
                                                                    $mtuang=null;
                                                                }
                                                                if($jmlhKurs=='')
                                                                {
                                                                    $jmlhKurs=1;
                                                                }
								$sqp="insert into ".$dbname.".log_permintaanhargadt (`nomor`,`kodebarang`,`spec`,`jumlah`,`kurs`,`matauang`,`tgldari`,`tglsmp`) 
                                                                    values('".$no_prmntan."','".$kdbrg."','".$spec."','".$jmlh."','".$jmlhKurs."','".$mtuang."','".$tglDr."','".$tglSmp."')";// echo"warning". $sqp; exit();
									if(!mysql_query($sqp))
									{
										echo $sqp;
										echo "Gagal,".(mysql_error($conn));exit();
									}
						}
//						$test=count($_POST['kdbrg']);
//						echo $test;
					}
                                        else
                                        {
                                            $sDel="delete from ".$dbname.".log_perintaanhargaht where nomor='".$no_prmntan."'";
                                            if(mysql_query($sDel))
                                            {
                                                exit("Error:Barang itu sudah terinput");
                                            }
                                        }
                                    }
                                    else
                                    {
                                        echo "Gagal,".$ins."__".(mysql_error($conn));
                                    }
                                }
			}
		}	
                break;
                case 'cari_pp':
				$limit=25;
				$page=0;
				if(isset($_POST['page']))
				{
				$page=$_POST['page'];
				if($page<0)
				$page=0;
				}
				$offset=$page*$limit;
				$sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc LIMIT ".$offset.",".$limit."";
				$sql2="select count(*) as jmlhrow from ".$dbname.".log_perintaanhargaht order by nomor desc";
				$query2=mysql_query($sql2) or die(mysql_error());
				while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
				}
				//$sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc";
				if($query=mysql_query($sql))
				{
				while($res2=mysql_fetch_assoc($query))
				{
					$no+=1;
					$dtkr="select * from ".$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'"; //echo $dtkr;
					$qdtkr=mysql_query($dtkr) or die(mysql_error());
					$rdtkr=mysql_fetch_object($qdtkr);
				
					$splr="select * from ".$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'"; //echo $splr;
					$qsuplr=mysql_query($splr) or die(mysql_error());
					$rsplr=mysql_fetch_object($qsuplr);
                                        if($res2['ppn']!=0)
                                        {
                                        $ppn=($res2['ppn']/($res2['subtotal']-$res2['nilaidiskon']))*100;
                                        }
					echo
					"<tr class=rowcontent>
						<td>".$no."</td>
						<td>".$res2['nomor']."</td>
						<td>".tanggalnormal($res2['tanggal'])."</td>
						<td>".$rdtkr->namakaryawan."</td>
						<td>".$rsplr->namasupplier."</td>";
						if($res2['purchaser']==$_SESSION['standard']['userid'])
							{
							echo"
                            <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res2['nomor']."','".tanggalnormal($res2['tanggal'])."','".$res2['purchaser']."','".$res2['supplierid']."','".$res2['nopp']."','".$res2['sisbayar']."','".$res2['id_franco']."','".$res2['stock']."','".$ppn."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$res2['nomor']."');\">
                            <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                            <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>      
                            </td>";
							}
							else
							{
								echo"<td>
                                                                    <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                                                                    <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>          
                                                                    </td>";
								
							}
							echo"
                        </tr>";
				}
				echo"
				 <tr><td colspan=6 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />"; 
				}
				else
				{
				echo "Gagal,".(mysql_error($conn));
				}
						
						break;
                case 'delete':
                    $strx="delete from ".$dbname.".log_perintaanhargaht where nomor='".$nomor."'";
					if(mysql_query($strx))
					{
						/* $sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc"; //echo $sql2;
						//$sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc";
						if($query=mysql_query($sql))
						{
						while($res2=mysql_fetch_assoc($query))
						{
							$no+=1;
							$dtkr="select * from ".$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'"; //echo $dtkr;
							$qdtkr=mysql_query($dtkr) or die(mysql_error());
							$rdtkr=mysql_fetch_object($qdtkr);
						
							$splr="select * from ".$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'"; //echo $splr;
							$qsuplr=mysql_query($splr) or die(mysql_error());
							$rsplr=mysql_fetch_object($qsuplr);
						
							echo
							"<tr class=rowcontent>
								<td>".$no."</td>
								<td>".$res2['nomor']."</td>
								<td>".tanggalnormal($res2['tanggal'])."</td>
								<td>".$rdtkr->namakaryawan."</td>
								<td>".$rsplr->namasupplier."</td>
								<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res2['nomor']."','".tanggalnormal($res2['tanggal'])."','".$res2['purchaser']."','".$res2['supplierid']."','".$rsplr->namasupplier."');\"></td>
								<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$res2['nomor']."');\"></td>
								<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\"></td>
							</tr>";
						}
						}
						else
						{
						echo "Gagal,".(mysql_error($conn));
						}
							*/
					}	
					else
					{
					//echo $strx;
					echo " Gagal,".addslashes(mysql_error($conn));
					}	
                 break;
				case 'update':
                                    $subTotal=str_replace(',', '', $subTotal);
                                    $nilaiPermintaan=str_replace(',', '', $nilaiPermintaan);
                                    $nilDiskon=str_replace(',', '', $nilDiskon);
                                    $sUpdate="update ".$dbname.".log_perintaanhargaht set supplierid='".$supplier_id."',id_franco='".$idFranco."', stock='".$stockId."', catatan='".$ketUraian."',sisbayar='".$termPay."', ppn='".$nilPPn."', subtotal='".$subTotal."', diskonpersen='".$diskonPersen."', nilaidiskon='".$nilDiskon."', nilaipermintaan='".$nilaiPermintaan."'
                                            where nomor='".$no_prmntan."'";
                                  if(mysql_query($sUpdate))
                                  {
                                          foreach($_POST['kdbrg'] as $row=>$Act)
                                           {

                                            $kdbrg=$Act;
                                            $spec=$_POST['rspek'][$row];
                                            $hrg=$_POST['price'][$row];
                                            $hrg=str_replace(',','',$hrg);
                                            $jmlh=$_POST['jmlh'][$row];
                                            $mtuang=$_POST['kurs'][$row];
                                            $jmlhKurs=$_POST['jmlhKurs'][$row];
                                            //$jmlh2=str_replace($jmlh,',','');
                                            $nopp=$_POST['nopp'][$row];
                                            $tglDr=$_POST['tglDari'][$row];
                                            $tglSmp=$_POST['tglSamp'][$row];
                                            if($tglSmp=='')
                                            {
                                                $tglSmp='00-00-0000';
                                            }
                                            if($tglDr=='')
                                            {
                                                $tglDr='00-00-0000';
                                            }
                                            if($mtuang=='')
                                            {
                                                $mtuang=null;
                                            }
                                            if($jmlhKurs=='')
                                            {
                                                $jmlhKurs=1;
                                            }
                                            $sUpdate2="update ".$dbname.".log_permintaanhargadt set `spec`='".$spec."',`jumlah`='".$jmlh."',`kurs`='".$jmlhKurs."',`matauang`='".$mtuang."',`tgldari`='".$tglDr."',`tglsmp`='".$tglSmp."',`harga`='".$hrg."' 
                                                                    where nomor='".$no_prmntan."' and kodebarang='".$kdbrg."'";
                                            //exit("Error".$sUpdate2);
                                            if(mysql_query($sUpdate2))
                                            echo"";
                                            else 
                                            echo " Gagal,".$sUpdate2."\n detail".addslashes(mysql_error($conn));
                                       }
                                  }
                                  else
                                  {
                                      echo $sUpdate."\n";
                                      echo " Gagal,".addslashes(mysql_error($conn));
                                  }
//				$strx="update ".$dbname.".log_perintaanhargaht set supplierid='".$supplier_id."' where nomor='".$nomor."'";
//				//echo "warning:".$strx;exit();
//				if(mysql_query($strx))
//					{
//						 $sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc"; //echo $sql2;
//						//$sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc";
//						if($query=mysql_query($sql))
//						{
//						while($res2=mysql_fetch_assoc($query))
//						{
//							$no+=1;
//							$dtkr="select * from ".$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'"; //echo $dtkr;
//							$qdtkr=mysql_query($dtkr) or die(mysql_error());
//							$rdtkr=mysql_fetch_object($qdtkr);
//						
//							$splr="select * from ".$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'"; //echo $splr;
//							$qsuplr=mysql_query($splr) or die(mysql_error());
//							$rsplr=mysql_fetch_object($qsuplr);
//						
//							echo
//							"<tr class=rowcontent>
//								<td>".$no."</td>
//								<td>".$res2['nomor']."</td>
//								<td>".tanggalnormal($res2['tanggal'])."</td>
//								<td>".$rdtkr->namakaryawan."</td>
//								<td>".$rsplr->namasupplier."</td>
//								<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res2['nomor']."','".tanggalnormal($res2['tanggal'])."','".$res2['purchaser']."','".$res2['supplierid']."','".$rsplr->namasupplier."');\"></td>
//								<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$res2['nomor']."');\"></td>
//								<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\"></td>
//							</tr>";
//						}
//						}
//						else
//						{
//						echo "Gagal,".(mysql_error($conn));
//						}
//							
//					}	
//					else
//					{
//					//echo $strx;
//					echo " Gagal,".addslashes(mysql_error($conn));
//					}
				break;
				case 'cari_permintaan':
					$limit=25;
				$page=0;
				if(isset($_POST['page']))
				{
				$page=$_POST['page'];
				if($page<0)
				$page=0;
				}
				$offset=$page*$limit;
				if(isset($_POST['txtSearch']))
				{
					$txt_search=$_POST['txtSearch'];
					$txt_tgl=tanggalsystem($_POST['tglCari']);
					$txt_tgl_t=substr($txt_tgl,0,4);
					$txt_tgl_b=substr($txt_tgl,4,2);
					$txt_tgl_tg=substr($txt_tgl,6,2);
					$txt_tgl=$txt_tgl_t."-".$txt_tgl_b."-".$txt_tgl_tg;
					//echo "warning:".$txt_tgl;
				}
				else
				{
					$txt_search='';
					$txt_tgl='';			
				}
				if($txt_search!='')
			{
				$where=" nomor LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" nomor LIKE '%".$txt_search."%' or tanggal LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
			if(($txt_search=='')&&($txt_tgl==''))
			{
				$strx="SELECT * FROM ".$dbname.".log_perintaanhargaht ORDER BY nomor DESC LIMIT ".$offset.",".$limit."";//echo $str;	
				$sql2="select count(*) as jmlhrow from ".$dbname.".log_perintaanhargaht order by nomor desc";
					 
			}
			else
			{
				$strx="SELECT * FROM ".$dbname.".log_perintaanhargaht where ".$where." ORDER BY nomor DESC LIMIT ".$offset.",".$limit."";//echo $strx;	
				$sql2="select count(*) as jmlhrow from ".$dbname.".log_perintaanhargaht where ".$where." order by nomor desc";	 
			}		
			//echo "warning:".$strx;exit();
			$query2=mysql_query($sql2) or die(mysql_error());
				while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
				}
				if($query=mysql_query($strx))
				{
				while($res2=mysql_fetch_assoc($query))
				{
					$no+=1;
					$dtkr="select * from ".$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'"; //echo $dtkr;
					$qdtkr=mysql_query($dtkr) or die(mysql_error());
					$rdtkr=mysql_fetch_object($qdtkr);
				
					$splr="select * from ".$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'"; //echo $splr;
					$qsuplr=mysql_query($splr) or die(mysql_error());
					$rsplr=mysql_fetch_object($qsuplr);
				        if($res2['ppn']!=0)
                                        {
                                         $ppn=($res2['ppn']/($res2['subtotal']-$res2['nilaidiskon']))*100;
                                        }
					echo
					"<tr class=rowcontent>
						<td>".$no."</td>
						<td>".$res2['nomor']."</td>
						<td>".tanggalnormal($res2['tanggal'])."</td>
						<td>".$rdtkr->namakaryawan."</td>
						<td>".$rsplr->namasupplier."</td>";
						if($res2['purchaser']==$_SESSION['standard']['userid'])
							{
							echo"
                            <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res2['nomor']."','".tanggalnormal($res2['tanggal'])."','".$res2['purchaser']."','".$res2['supplierid']."','".$res2['nopp']."','".$res2['sisbayar']."','".$res2['id_franco']."','".$res2['stock']."','".$ppn."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$res2['nomor']."');\">
                            <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                            <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>   
                            </td>";
							}
							else
							{
								echo"<td>
                                                                    <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                                                                    <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>   
                                                                </td>";
								
							}
							echo"
                        </tr>";
				}
				echo"
				 <tr><td colspan=6 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />"; 
				}
				else
				{
				echo "Gagal,".(mysql_error($conn));
				}
						
				break;
				case'get_nopp':
				$optNopp='';
				$sql="SELECT a.nopp FROM ".$dbname.".`log_prapodt` a left join ".$dbname.".`log_prapoht` b on a.nopp=b.nopp where b.close='2' 
				and (a.create_po is null or create_po='') 
				and a.kodebarang='".$kd_brg."'"; //echo "warning".$sql;
				$query=mysql_query($sql) or die(mysql_error());
				while($res=mysql_fetch_assoc($query))
				{
					$optNopp.="<option value=".$res['nopp'].">".$res['nopp']."</option>";
				}
				echo $optNopp;
				break;
				case'getSpek':
				$sSpek="select spesifikasi from ".$dbname.".log_5photobarang where kodebarang='".$kd_brg."'";
				$qSpek=mysql_query($sSpek) or die(mysql_error());
				$rSpek=mysql_fetch_assoc($qSpek);
				echo $rSpek['spesifikasi'];
				break;
				case'getKurs':
				$tgl=date("Ymd");
				$sGet="select distinct kurs from ".$dbname.".setup_matauangrate where kode='".$mtUang."' and daritanggal='".$tgl."'";
				$qGet=mysql_query($sGet) or die(mysql_error());
				$rGet=mysql_fetch_assoc($qGet);
				//echo "warning:".$rGet['kurs'];
				if($mtUang=='IDR')
				{
					$rGet['kurs']=1;
				}
				else
				{
					if($rGet['kurs']!=0)
					{
						$rGet['kurs']=$rGet['kurs'];
					}
					else
					{
						$rGet['kurs']=1;
					}
				}
				echo $rGet['kurs'];
				break;
				
				
				
				
				
				
				
				
				
				
				
				
				
				
                                case'printExcel':
/*echo"warning:rusak";
echo"<script language=JavaScript1.2 src=js/generic.js></script>";
echo"<script language=JavaScript1.2 src=js/log_pnwrharga.js></script>";
*/                                $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
                                $qAlamat=mysql_query($sAlmat) or die(mysql_error());
                                $rAlamat=mysql_fetch_assoc($qAlamat);
                                $sSupp="select * from ".$dbname.".log_5supplier where supplierid in (select supplierid from ".$dbname.".log_perintaanhargaht where nomor='".$nomor."')";
                                $qSupp=mysql_query($sSupp) or die(mysql_error($conn));
                                $rSupp=mysql_fetch_assoc($qSupp);
								
								$sNpwp="select npwp,alamatnpwp from ".$dbname.".setup_org_npwp where kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
								// echo"<pre>";print_r($_SESSION);echo"</pre>";echo $sNpwp;exit();
								$qNpwp=mysql_query($sNpwp) or die(mysql_error());
								$rNpwp=mysql_fetch_assoc($qNpwp);
                                $stream="<table cellspacing=1 cellpadding=1 style=\"border-bottom:solid 1px;\">
                                    <tr><td rowspan=3 align=left valign=top colspan=6>
                                    <font size=5><b>".strtoupper($rAlamat['namaorganisasi'])."</b></font><br />
                                    <font size=3>".$rAlamat['alamat']."</font><br />    
                                    <font size=3>Telp : ".$rAlamat['telepon']."</font><br />  
									<font size=2>NPWP : ".$rNpwp['npwp']."</font><br />  
									<font size=2>".$_SESSION['lang']['alamat']." NPWP : ".$rNpwp['alamatnpwp']."</font><br />  
                                    </td></tr></table>";
                                $stream.="
                                    <table cellspacing=1 cellpadding=1 border=0 align=center>
                                    <tr><td colspan=6 align=center><font size=4><b>".strtoupper($_SESSION['lang']['permintaan'])."</b></font></td></tr>
                                    <tr><td colspan=6 align=center><font size=2>No.:".$nomor."</font></td></tr>
                                    <tr><td colspan=6 align=center><font size=2>".$_SESSION['lang']['tanggal'].":".date("d-m-Y")."</font></td></tr>
                                    </table><br /><br />";
                                $stream.="<table cellspacing=2 cellpadding=2 border=0>
                                        <tr><td>
                                        <table cellspacing=2 cellpadding=2 border=0>
                                         <tr><td  valign=top align=left>".$_SESSION['lang']['kepada']."</td><td valign=top align=center>:</td><td valign=top align=left><b>".strtoupper($rSupp['namasupplier'])."</b><br />".$rSupp['alamat']."</td></tr>
                                         <tr><td  valign=top align=left>".$_SESSION['lang']['kntprson']."</td><td valign=top align=center>:</td><td valign=top align=left>".strtoupper($rSupp['kontakperson'])."</td></tr>
                                         <tr><td  valign=top align=left>".$_SESSION['lang']['telp']."</td><td valign=top align=center>:</td><td valign=top align=left>".$rSupp['telepon']."</td></tr>
                                         <tr><td  valign=top align=left>".$_SESSION['lang']['fax']."</td><td valign=top align=center>:</td><td valign=top align=left>".$rSupp['fax']."</td></tr>
                                         </table>
                                             </td><td>
                                        &nbsp;
                                             </td></tr>
                                        </table>
                                         ";
                                $stream.="<br /><br /><table cellspacing=1 cellpadding=1 border=0>
                                    <tr><td colspan=6 align=justify>".$_SESSION['lang']['ktPnwran']." :</td></tr>
                                </table><br />";
                                $stream.="<table cellspacing=2 cellpadding=2  style=\"border-bottom:solid 1px; border-top:solid 1px; \">
                                    <tr>
                                    <td align=center bgcolor=#DEDEDE style=\"border-bottom:double 1px;\">No.</td>
                                    <td align=center bgcolor=#DEDEDE colspan=2 style=\"border-bottom:double 1px;\">".$_SESSION['lang']['namabarang']."</td>
                                    <td align=center bgcolor=#DEDEDE style=\"border-bottom:double 1px;\">".$_SESSION['lang']['satuan']."</td>
                                    <td align=center bgcolor=#DEDEDE style=\"border-bottom:double 1px;\">".$_SESSION['lang']['harga']."</td>
                                     <td align=center bgcolor=#DEDEDE style=\"border-bottom:double 1px;\">".$_SESSION['lang']['keterangan']."</td>
                                    </tr>";
                               $sPermintaan="select a.*,b.* from ".$dbname.".log_perintaanhargaht a left join ".$dbname.".log_permintaanhargadt b
                                   on a.nomor=b.nomor where a.nomor='".$nomor."'";
                               //exit("Error".$sPermintaan);
                               $qPermintaah=mysql_query($sPermintaan) or die(mysql_error($conn));
                               while($rPermintaan=mysql_fetch_assoc($qPermintaah))
                               {
                                   $no+=1;
                                   $purchaser=$rPermintaan['purchaser'];
                                 $stream.="<tr>
                                    <td align=center>".$no."</td>
                                    <td colspan=2>".$optBarang[$rPermintaan['kodebarang']]." <br /> ".$rPermintaan['spec']."</td>
                                    <td>".$optSat[$rPermintaan['kodebarang']]."</td>
                                    <td>".$rPermintaan['harga']."</td>
                                    <td>&nbsp;</td>
                                   </tr>"; 
                               }
                               $stream.=" </table><br />";
                               $stream.="<table cellspacing=1 cellpadding=1 border=0>
                                    <tr><td colspan=3>Tempat Penyerahan </td><td>:</td><td style=\"border-bottom:dotted 1px;\" colspan=2>&nbsp;</td></tr>
                                    <tr> <td colspan=3>Jangka Waktu Pengiriman </td><td>:</td><td style=\"border-bottom:dotted 1px;\" colspan=2>&nbsp;</td></tr>
                                    <tr><td colspan=3>Cara Pembayaran </td><td>:</td><td style=\"border-bottom:dotted 1px;\" colspan=2>&nbsp;</td></tr>
                                    <tr>
                                    <td colspan=7>
                                    <ol type=a>
                                       <li><input type=checkbox> ".$_SESSION['lang']['kntn']."</li>
                                       <li><input type=checkbox> ".$_SESSION['lang']['krdit']."</li>
                                       <li><input type=checkbox> ".$_SESSION['lang']['lc']."</li>
                                       <li><input type=checkbox> ................</li>
                                    </ol>
                                    </td></tr>
                                   </table>";
                               $stream.="<table cellspacing=1 cellpadding=1 border=0>
                                        <tr><td colspan=7>Harga harap di jelaskan secara lengkap termasuk PPn atau tidak. Terima kasih atas perhatian dan kerjasamanya.</td></tr>
                                        
                                        </table><br /><br />
                               <table cellspacing=1 cellpadding=1 border=0>
                                <tr><td colspan=7>Salam</td></tr>
                                <tr><td colspan=7>&nbsp;</td></tr>
                                <tr><td colspan=7>&nbsp;</td></tr>
                               <tr><td colspan=7>&nbsp;</td></tr>
                                <tr><td colspan=7>$optNmkry[$purchaser]</td></tr>
                               </table>
                               
                               ";
$nop_="form_permintaan_harga";

                                        if(strlen($stream)>0)
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
                                        if(!fwrite($handle,$stream))
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

                                break;
				
            default :
            break;
    }


?>