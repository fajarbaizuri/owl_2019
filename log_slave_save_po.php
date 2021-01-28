<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');

	isset($_POST['supplier_id']) ? $supplier_id=$_POST['supplier_id'] : $supplier_id='';
	isset($_POST['proses']) ? $proses=$_POST['proses'] : $proses='';
	isset($_POST['nopo']) ? $nopo=$_POST['nopo'] : $nopo='';
	isset($_POST['tglpo']) ? $tgl_po=tanggalsystem($_POST['tglpo']) : $tgl_po='0000-00-00';
	isset($_POST['subtot']) ? $sub_total=$_POST['subtot'] : $sub_total='';
	isset($_POST['diskon']) ? $disc=$_POST['diskon'] : $disc='';
	isset($_POST['nildiskon']) ? $nilai_dis=$_POST['nildiskon'] : $nilai_dis='';
	isset($_POST['ppn']) ? $nppn=$_POST['ppn'] : $nppn='';
	isset($_POST['tgl_krm']) ? $tanggl_kirim=tanggalsystemd($_POST['tgl_krm']) : $tanggl_kirim='0000-00-00';
	isset($_POST['lok_kirim']) ? $lokasi_krm=$_POST['lok_kirim'] : $lokasi_krm='';
	isset($_POST['cara_pembayarn']) ? $cr_pembayaran=$_POST['cara_pembayarn'] : $cr_pembayaran='';
	isset($_POST['grand_total']) ? $nilai_po=$_POST['grand_total'] : $nilai_po='';
	isset($_POST['purchser_id']) ? $purchaser=$_POST['purchser_id'] : $purchaser='';
	isset($_POST['lokasi_krm']) ? $lokasi_kirim=$_POST['lokasi_krm'] : $lokasi_kirim='';
	isset($_POST['id_user']) ? $persetujuan=$_POST['id_user'] : $persetujuan='';
	isset($_POST['cm_hasil']) ? $comment=$_POST['cm_hasil'] : $comment='';
	isset($_POST['jmlh_realisasi']) ? $jmlh_realisasi=$_POST['jmlh_realisasi'] : $jmlh_realisasi='';
	isset($_POST['jmlh_diminta']) ? $jmlh_diminta=$_POST['jmlh_diminta'] : $jmlh_diminta='';
	isset($_POST['jnopp']) ? $jnopp=$_POST['jnopp'] : $jnopp='';
	isset($_POST['jkdbrg']) ? $jkdbrg=$_POST['jkdbrg'] : $jkdbrg='';
	isset($_POST['ketUraian']) ? $ketUraian=$_POST['ketUraian'] : $ketUraian='';
	isset($_POST['mtUang']) ? $mtUang=$_POST['mtUang'] : $mtUang='';
	isset($_POST['Kurs']) ? $Kurs=intval($_POST['Kurs']) : $Kurs='';
	isset($_POST['nmSupplier']) ? $nmSupplier=$_POST['nmSupplier'] : $nmSupplier='';
	isset($_POST['ttd2']) ? $ttd2=$_POST['ttd2'] : $ttd2='';
	
	## Field Baru FBG
	isset($_POST['pembuat']) ? $pembuat=$_POST['pembuat'] : $pembuat='';
	isset($_POST['tujuanpo']) ? $tujuanpo=$_POST['tujuanpo'] : $tujuanpo='';
	isset($_POST['ketbarang']) ? $ketbarang=$_POST['ketbarang'] : $ketbarang='';
	
	
	isset($_POST['tgljthtempo']) ? $tgljthtempo=tanggalsystem($_POST['tgljthtempo']) : $tgljthtempo='0000-00-00';
		
	switch($proses)
	{
		case 'cek_supplier':
			$sql="select * from ".$dbname.".log_5supplier where supplierid='".$supplier_id."'";
			$query=mysql_query($sql) or die(mysql_error());
			$res=mysql_fetch_assoc($query);
			echo $res['bank'].",";
			echo $res['rekening'].",";
			echo $res['an'].",";
			echo $res['npwp'];
			break;
		case 'insert':
			## Empty Field
			if(($supplier_id=='')||($nopo=='')||($disc=='')||($tanggl_kirim=='')||($cr_pembayaran=='')||($lokasi_kirim=='')) {
				echo"warning:Lengkapi pengisian terlebih dahulu";
				exit();
			}
			
			$sGetKurs="select distinct kurs,kode from ".$dbname.".setup_matauangrate where daritanggal<='".$tglSkrng."' and kode='".$mtUang."' order by daritanggal desc";
			$qGetKurs=mysql_query($sGetKurs) or die(mysql_error());
			$rGetKurs=mysql_fetch_assoc($qGetKurs);
			if($rGetKurs['kode']!='IDR') {
				if($Kurs<$rGetKurs['kurs']) {
					exit("Error:Masukan Kurs Sesuai Dengan Mata Uang,Kurs Untuk ".$rGetKurs['kode']." :".$rGetKurs['kurs']);   
				}
			}
			$awl=0;
			$i=1;
			foreach($_POST['kdbrg'] as $row =>$cntn) {
      			$kdbrg=$cntn;
				$b=count($_POST['kdbrg']);
				$nopp=$_POST['nopp'][$row];
				$jmlh_pesan=$_POST['rjmlh_psn'][$row];
				$hrg_satuan=$_POST['rhrg_sat'][$row];
				$hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
				$satuan=$_POST['rsatuan_unit'][$row];
				$diskon=($hrg_sblmdiskon*$disc)/100;
				$hrg_diskon=$hrg_sblmdiskon-$diskon;
				
				$sqjmlh="select selisih,jlpesan,realisasi,purchaser from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
				//echo "warning:".$sqjmlh;exit();
				$qujmlh=mysql_query($sqjmlh) or die(mysql_error());
				$resjmlh=mysql_fetch_assoc($qujmlh);
				$jmlh_pesan=$resjmlh['jlpesan']+$jmlh_pesan;
				if(($jmlh_pesan=='')||($hrg_satuan==''))
				{
					echo "warning: Lengkapi pengisian terlebih dahulu";
					exit();
				}
				if($purchaser!=$resjmlh['purchaser'])
				{
					$purchaser=$resjmlh['purchaser'];
				}
				
				if($resjmlh['realisasi']<$jmlh_pesan) {
					echo "warning : \nTotal Jumlah Pesan (".$jmlh_pesan.") Dari Kode Barang ".$kdbrg.".(".$jmlh_pesan.") =
					\nJumlah Pesan Sebelumnya (".$resjmlh['jlpesan'].")\nJumlah Pesan Saat Ini (".$_POST['rjmlh_psn'][$row].")
					\nLebih Besar dari Jumlah Yang Disetujui (".$resjmlh['realisasi'].").";
					exit();
				}
            }
			$sKd="select kodeorg from ".$dbname.".log_prapoht where nopp='".$nopp."'";
			$qKd=mysql_query($sKd) or die(mysql_error());
			$rKdorg=mysql_fetch_assoc($qKd);
            
			$sql="select nopo from ".$dbname.".log_poht where nopo='".$nopo."'";
			$query=mysql_query($sql) or die(mysql_error());
			$res=mysql_fetch_row($query);
			if(intval($lokasi_kirim))
			{
				$field="`idFranco`";
			} else {
				$field="`lokasipengiriman`";
			}
			$thisDate=date('Y-m-d');
            if($nilai_dis=='') {
                $nilai_dis=0;
            }
            $Kurs=intval($Kurs);
			$strx="update ".$dbname.".log_poht set `kodesupplier`='".$supplier_id."',`subtotal`='".$sub_total."',`diskonpersen`='".$disc."',`nilaidiskon`='".$nilai_dis."',`ppn`='".$nppn."',`nilaipo`='".$nilai_po."',`tanggalkirim`='".$tanggl_kirim."',
				".$field."='".$lokasi_kirim."',`syaratbayar`='".$cr_pembayaran."',`uraian`='".$ketUraian."',`purchaser`='".$purchaser."',`lokalpusat`='0',`matauang`='".$mtUang."',`kurs`='".$Kurs."',`persetujuan1`='".$persetujuan."',`hasilpersetujuan1`='1',
				`tglp1`='".$thisDate."',`statuspo`='2',`persetujuan2`='".$ttd2."',`hasilpersetujuan2`='1',`tglp2`='".$thisDate."',tgledit='".$thisDate."',`pembuat`='".$pembuat."',`tujuanpo`='".$tujuanpo."',`keteranganbarang`='".$ketbarang."',tgljthtempo='".$tgljthtempo."'
				where nopo='".$nopo."'";
			
			if(!mysql_query($strx)) {
				echo "Gagal,".(mysql_error($conn));exit();
			} else {
				foreach($_POST['kdbrg'] as $row =>$isi) {
					$kdbrg=$isi;
					$nopp=$_POST['nopp'][$row];
					$jmlh_pesan=$_POST['rjmlh_psn'][$row];
					$hrg_satuan=$_POST['rhrg_sat'][$row];
					$hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
					$satuan=$_POST['rsatuan_unit'][$row];
					$diskon=($hrg_sblmdiskon*$disc)/100;
					$hrg_diskon=$hrg_sblmdiskon-$diskon;
					$spekBrg=$_POST['spekBrg'][$row];
					$sqjmlh="select selisih,jlpesan,realisasi from ".$dbname.".log_sudahpo_vsrealisasi_vw where nopp='".$nopp."' and kodebarang='".$kdbrg."'";
					$qujmlh=mysql_query($sqjmlh) or die(mysql_error());
					$resjmlh=mysql_fetch_assoc($qujmlh);
					$sql="update ".$dbname.".log_podt set `jumlahpesan`='".$jmlh_pesan."',`hargasatuan`='".$hrg_diskon."',`nopp`='".$nopp."',`hargasbldiskon`='".$hrg_sblmdiskon."',`satuan`='".$satuan."',`catatan`='".$spekBrg."' where nopo='".$nopo."' and kodebarang='".$kdbrg."'";
					if(!mysql_query($sql))
					{
					   echo $sql."-----";
					   echo "Gagal,".(mysql_error($conn));exit();
					}
					$snopp="select nopo from ".$dbname.".log_prapoht where nopp='".$nopp."'";
					$qnopp=mysql_query($snopp) or die(mysql_error());
					$rnopp=mysql_fetch_row($qnopp);
					if($rnopp<1) {
						$supp="update ".$dbname.".log_prapoht set `nopo`='".$nopo."' where nopp='".$nopp."'";
						//echo"warning:test".$supp;exit();
						if(mysql_query($supp))
						{echo"";}
						else
						{echo "Gagal,".(mysql_error($conn));exit();}
						$sdpp="update ".$dbname.".log_prapodt set `create_po`='1' where `nopp`='".$nopp."' and `kodebarang`='".$kdbrg."'";
						if(mysql_query($sdpp))
						{echo"";}
						else
						{echo "Gagal,".(mysql_error($conn));exit();	}
					}
					
					$sdpp="update ".$dbname.".log_prapodt set `create_po`='1' where `nopp`='".$nopp."' and `kodebarang`='".$kdbrg."'";	
					if(mysql_query($sdpp)) {echo"";}
					else {echo "Gagal,".$sdpp."__".(mysql_error($conn));exit();	}				   
				}
			}
			break;
			
		case 'update_data' :
			echo"
			<table cellspacing='1' border='0' class='sortable'>
			<thead>
				<tr class=rowheader>
					<td>No</td>
					<td>".$_SESSION['lang']['nopo']."</td>
					<td>".$_SESSION['lang']['namasupplier']."</td>
					<td>".$_SESSION['lang']['tgl_po']."</td>
					<td>".$_SESSION['lang']['tgl_kirim']."</td>
					<td>".$_SESSION['lang']['syaratPem']."</td>
					 <td>".$_SESSION['lang']['status']."</td>
					<td>action</td>
				</tr>
			 </thead>
			<tbody>";
			$limit=20;
			$page=0;
			if(isset($_POST['page'])) {
				$page=$_POST['page'];
				if($page<0)
					$page=0;
			}
			$offset=$page*$limit;
			if($_SESSION['empl']['kodejabatan']=='5') {
				$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where lokalpusat='0'  order by tanggal desc ";
				$sql="select * from ".$dbname.".log_poht where lokalpusat='0' order by tanggal desc limit ".$offset.",".$limit."";
			} else {
				$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where lokalpusat='0' and purchaser='".$_SESSION['standard']['userid']."' order by tanggal desc ";
				$sql="select * from ".$dbname.".log_poht where lokalpusat='0' and purchaser='".$_SESSION['standard']['userid']."'  order by tanggal desc limit ".$offset.",".$limit."";
			}
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
			}
			
			$no=0;
			$query=mysql_query($sql) or die(mysql_error());
            while ($res = mysql_fetch_object($query)) {
				$no+=1;
				$sql2="select * from ".$dbname.".log_5supplier where supplierid='".$res->kodesupplier."'";
				$query2=mysql_query($sql2) or die(mysql_error());
				$res2=mysql_fetch_object($query2);
                
				$skry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res->purchaser."'";// echo $skry;
				$qkry=mysql_query($skry) or die(mysql_error());
				$rkry=mysql_fetch_assoc($qkry);
				
				if($res->stat_release==0) {
					$stat_po=$_SESSION['lang']['un_release_po'];
				} elseif($res->stat_release==1) {
					$stat_po=$_SESSION['lang']['release_po'];
				} elseif($res->stat_release==2) {
					$stat_po="<a href=# onclick=getKoreksi('".$res->nopo."')>".$_SESSION['lang']['koreksi']."</a>";
				}
                echo"
					<tr class=rowcontent>
					<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".$no."</td>
					<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".$res->nopo."</td>";
				if(!empty($res2)) 
				{
					echo "<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".$res2->namasupplier.")</td>";
				} else 
				{
					echo "<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." ></td>";
				}
				echo "<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".tanggalnormal($res->tanggal)."</td>
					<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".tanggalnormal($res->tanggalkirim)."</td>
					<td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".$res->syaratbayar."</td><td ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"")." >".$stat_po."</td> ";
				
				if(($res->purchaser==$_SESSION['standard']['userid'])||($_SESSION['empl']['kodejabatan']=='5')) {
					if($res->stat_release!=1) {
						if(empty($res2)) {
							$res2->persetujuan2='';
							$res2->rekening = '';
							$res2->npwp = '';
						}
						echo"<td ".($res->stat_release==2?"bgcolor='orange'":"")."><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res->nopo."','".tanggalnormal($res->tanggal)."','".$res->kodesupplier."','".$res->subtotal."','".$res->diskonpersen."','".$res->ppn."','".$res->nilaipo."','".$res2->rekening."','".$res2->npwp."','".$res->nilaidiskon."','".$res->stat_release."','".tanggalnormal($res->tanggalkirim)."','".$res->matauang."','".$res->kurs."','".$res->persetujuan1."','".$res->idFranco."','".$res->persetujuan2."','".$res->pembuat."','".$res->tujuanpo."','".$res->keteranganbarang."','".$res2->bank."','".$res2->an."','".tanggalnormal($res->tgljthtempo)."');\">";
						echo"<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPo('".$res->nopo."','".$res->stat_release."');\" >".
							"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_detail_po',event);\">".
							"</td></tr>";
					} else {
						echo"<td ".($res->stat_release==2?"bgcolor='orange'":"")."><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_detail_po',event);\"></td></tr>";
					}
				} else {
					echo"<td ".($res->stat_release==2?"bgcolor='orange'":"")."><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_detail_po',event);\"></td></tr>";
				}
            }
			echo"<tr><td colspan=9 align=center>".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />".
				"<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>".
				"<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>";
			//echo "</td></tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";
			echo "</td></tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='' />";
			echo"</tbody></table>";
			break;
		case 'edit_po':
            $tglSkrng=date("Y-m-d");
			if(($supplier_id=='')||($nopo=='')||($disc==''))
			{
				echo"warning:Please Complete The Form";
				exit();
			}
            
			$sGetKurs="select distinct kurs,kode from ".$dbname.".setup_matauangrate where daritanggal<='".$tglSkrng."' and kode='".$mtUang."' order by daritanggal desc";
			$qGetKurs=mysql_query($sGetKurs) or die(mysql_error());
			$rGetKurs=mysql_fetch_assoc($qGetKurs);
			
			if($rGetKurs['kode']!='IDR')
			{
				if($Kurs<$rGetKurs['kurs'])
				{
				  exit("Error:Masukan Kurs Sesuai Dengan Mata Uang,Kurs Untuk ".$rGetKurs['kode']." :".$rGetKurs['kurs']);   
				}
			   
			}
			foreach($_POST['kdbrg'] as $row =>$isi)
			{
				$kdbrg=$isi;
				$nopp=$_POST['nopp'][$row];
				$jmlh_pesan=$_POST['rjmlh_psn'][$row];
				$hrg_satuan=$_POST['rhrg_sat'][$row];
				$hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
				$diskon=($hrg_sblmdiskon*$disc)/100;
				$hrg_diskon=$hrg_sblmdiskon-$diskon;
				$mat_uang=$_POST['rmat_uang'][$row];
				$satuan=$_POST['rsatuan_unit'][$row];
				$spekBrg=$_POST['spekBrg'][$row];
				
				if(($jmlh_pesan=='')||($hrg_satuan=='')||($tanggl_kirim=='')||($cr_pembayaran=='')||($lokasi_kirim=='')) {
					echo "warning: Please Complete The Form";
					exit();
				} else {
					$scek="select stat_release from ".$dbname.".log_poht where nopo='".$nopo."'";
					$qcek=mysql_query($scek) or die(mysql_error($conn));
					$rcek=mysql_fetch_assoc($qcek);
					if($rcek['stat_release']==1)
					{
						echo"warning : No. PO : ".$nopo." Sudah Release";
						exit();
					}
					
					if(intval($lokasi_kirim))
					{
					$field="`idFranco`";
					}
					else
					{
					$field="`lokasipengiriman`";
					}
					
					$strx="update ".$dbname.".log_poht set `kodesupplier`='".$supplier_id."',`subtotal`='".$sub_total."',tgledit='".$tglSkrng."',`diskonpersen`='".$disc."',`nilaidiskon`='".$nilai_dis."',`ppn`='".$nppn."',`nilaipo`='".$nilai_po."',
                        `tanggalkirim`='".$tanggl_kirim."',".$field."='".$lokasi_kirim."',`syaratbayar`='".$cr_pembayaran."',`uraian`='".$ketUraian."',matauang='".$mtUang."',kurs='".$Kurs."',persetujuan1='".$persetujuan."',persetujuan2='".$ttd2."',pembuat='".$pembuat."',tujuanpo='".$tujuanpo."',keteranganbarang='".$ketbarang."',tgljthtempo='".$tgljthtempo."'
                         where nopo='".$nopo."'";
					
					if(!mysql_query($strx))
					{
						//echo $sqp; 
						echo "Gagal,".(mysql_error($conn));exit();
					} else {
						foreach($_POST['kdbrg'] as $row =>$isi)
						{
							$kdbrg=$isi;
							$nopp=$_POST['nopp'][$row];
							$jmlh_pesan=$_POST['rjmlh_psn'][$row];
							$hrg_satuan=$_POST['rhrg_sat'][$row];
							$hrg_sblmdiskon=str_replace(',','',$hrg_satuan);
							$diskon=($hrg_sblmdiskon*$disc)/100;
							$hrg_diskon=$hrg_sblmdiskon-$diskon;
							$mat_uang=$_POST['rmat_uang'][$row];
							$satuan=$_POST['rsatuan_unit'][$row];
							$spekBrg=$_POST['spekBrg'][$row];
							$sql="update ".$dbname.".log_podt 
								set `jumlahpesan`='".$jmlh_pesan."',`hargasatuan`='".$hrg_diskon."',`matauang`='".$mat_uang."',`hargasbldiskon`='".$hrg_sblmdiskon."',
								`satuan`='".$satuan."',catatan='".$spekBrg."' 
								where nopo='".$nopo."' and kodebarang='".$kdbrg."' and nopp='".$nopp."'";
							if(!mysql_query($sql)) {
								echo "Gagal,".(mysql_error($conn));exit();
							} else {
								$sCek="select distinct create_po from ".$dbname.".log_prapodt where nopp='".$_POST['nopp'][$row]."' and kodebarang='".$isi."'";
								$qCek=mysql_query($sCek) or die(mysql_error());
								$rCek=mysql_fetch_assoc($qCek);
								if($rCek['create_po']==''||$rCek['create_po']=='0') {
									$sUpdate="update ".$dbname.".log_prapodt set create_po=1 where nopp='".$_POST['nopp'][$row]."' and kodebarang='".$isi."'";
									if(!mysql_query($sUpdate))
									{
									echo "Gagal,".(mysql_error($conn));exit();
									}
                                }
                            }
						}
					}
				}
			}
			break;
		case 'delete_all':
		$scek="select stat_release from ".$dbname.".log_poht where nopo='".$nopo."'";
		$qcek=mysql_query($scek) or die(mysql_error($conn));
		$rcek=mysql_fetch_assoc($qcek);
		if($rcek['stat_release']==2)
		{
			echo"warning : No. PO : ".$nopo." Sedang Dalam Proses Koreksi";
			exit();
		}
		else
		{
                    $sListPP="select distinct nopp,kodebarang from ".$dbname.".log_podt where nopo='".$nopo."'";
                    $qListPP=mysql_query($sListPP) or die(mysql_error());
                    $row=mysql_num_rows($qListPP);
                    
                        while($rListPP=mysql_fetch_assoc($qListPP))
                        {
                            $sUpd="update ".$dbname.".log_prapodt set create_po=0 where kodebarang='".$rListPP['kodebarang']."' and nopp='".$rListPP['nopp']."'";
                            if(mysql_query($sUpd))
                            {
                                $sql="delete from ".$dbname.".log_podt where kodebarang='".$rListPP['kodebarang']."' and nopp='".$rListPP['nopp']."'"; //echo "warning:".$sql;exit();
                                if(!mysql_query($sql))
                                {
                                echo "Gagal,".(mysql_error($conn));exit();
                                }
                            }
                             $row--;
                        }
                    if($row==0)
                    {
			$sql2="delete from ".$dbname.".log_poht where nopo='".$nopo."'";
			if(!mysql_query($sql2))
			{
				echo "Gagal,".(mysql_error($conn));exit();
			}
                    }
		}
		
		break;
		
		case 'insert_forward_po' :
		
		if($persetujuan==$_SESSION['standard']['userid'])
		{
			echo "Warning: Nama Tidak Boleh Sama Dengan User Id yang Mengajukan";
		}
		else
		{		
			$tgl=date("Y-m-d");
			$sql="update ".$dbname.".log_poht set persetujuan1='".$persetujuan."',statuspo='2',tglp1='".$tgl."',hasilpersetujuan1='1' where nopo='".$nopo."'";
			//$sql="update ".$dbname.".log_poht set persetujuan1='".$persetujuan."',statuspo='1' where nopo='".$nopo."'";
			//echo "warning".$sql; exit();
			if(!mysql_query($sql))
			{
				echo "Gagal,".(mysql_error($conn));exit();
			}
		}
		break;
         case 'get_form_approval' :
	$sql="select nopo from ".$dbname.".log_poht where nopo='".$nopo."' and lokalpusat='0'";
	$query=mysql_query($sql) or die(mysql_error());
	$rCek=mysql_num_rows($query);
	if($rCek>0)
	{
					$rest=mysql_fetch_assoc($query);
					echo"<br />
					<div id=test style=display:block>
					<fieldset>
					<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$nopo."  /></legend>
					<table cellspacing=1 border=0>
					<tr>
					<td colspan=3>
					Diajukan Untuk Verifikasi Berikutnya :</td>
					</tr>
					<td>".$_SESSION['lang']['namakaryawan']."</td>
					<td>:</td>
					<td valign=top>";

					$optPur='';
					$klq="select namakaryawan,karyawanid,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."' and lokasitugas!='' and (kodejabatan<6 or kodejabatan=11) order by namakaryawan asc"; 
					//echo $klq;
					$qry=mysql_query($klq) or die(mysql_error());
					while($rst=mysql_fetch_object($qry))
					{
						$sBag="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
						$qBag=mysql_query($sBag) or die(mysql_error());
						$rBag=mysql_fetch_assoc($qBag);
						
						$optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan." [".$rst->lokasitugas."] [".$rBag['nama']."]</option>";
					}

					echo"
						<select id=persetujuan_id name=persetujuan_id>
							$optPur;
						</select></td></tr>
						<tr>
						<td colspan=3 align=center>
						<button class=mybutton onclick=forward_po() title=\"Diajukan Kembali Untuk Verivikasi\" >".$_SESSION['lang']['diajukan']."</button>
						<button class=mybutton onclick=cancel_po() title=\"Menutup Form Ini\">".$_SESSION['lang']['cancel']."</button>
						</td></tr></table><br />
						<input type=hidden name=proses id=proses  />
						</fieldset></div>
						
						<div id=close_po style=\"display:none;\">	
						<fieldset><legend><input type=text id=snopo name=snopo disabled value='".$nopo."' /></legend>
						<p align=center>Apakah Anda Yakin Ingin Memproses PO Ini !!</p><br />
						<button class=mybutton onclick=proses_release_po() title=\"Untuk Memproses PO Ini\" >".$_SESSION['lang']['approve']."</button>
						<button class=mybutton onclick=cancel_po() title=\"Menutup Form Ini\">".$_SESSION['lang']['cancel']."</button>
						</fieldset></div>
						";
	}
	else
	{
		echo"warning:Data Belum Terinput";
		exit();
	}
		break;
		case 'proses_release_po':
		$sql="update ".$dbname.".log_poht set statuspo='2',hasilpersetujuan1='1' where nopo='".$nopo."'";	
		mysql_query($sql) or die(mysql_error());
			
		break;
		case 'cari_nopo':
		echo"<div style=\"overflow:auto;height:400px;\">
		<table cellspacing='1' border='0'>
        <thead>
            <tr class=rowheader>
                <td>".$_SESSION['lang']['nopo']."</td>
                <td>".$_SESSION['lang']['namasupplier']."</td>
				<td>".$_SESSION['lang']['tgl_po']."</td>
                <td>".$_SESSION['lang']['tgl_kirim']."</td>
				<td>".$_SESSION['lang']['purchaser']."</td> 
                <td>".$_SESSION['lang']['syaratPem']."</td>
				 <td>".$_SESSION['lang']['status']."</td>
                <td>action</td>
            </tr>
         </thead>
	 <tbody>";
		
	
		//$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht order by nopo desc ";
		
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
				$where=" nopo LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" nopo LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
			}
		
			if(($txt_search=='')&&($txt_tgl==''))
			{
				$strx="SELECT * FROM ".$dbname.".log_poht where lokalpusat='0' order by nopo desc ";//echo $str;	
				$sql2="SELECT count(*) as jmlhrow FROM ".$dbname.".log_poht where lokalpusat='0' order by nopo desc";
			}
			else
			{
				$strx="SELECT * FROM ".$dbname.".log_poht where lokalpusat='0' and ".$where." order by nopo desc";//echo $strx;	
				$sql2="SELECT count(*) as jmlhrow FROM ".$dbname.".log_poht where lokalpusat='0' and ".$where." order by nopo desc";	 
			}	
			//echo "warning:".$strx;exit();
			if(mysql_query($strx))
			{
                            $query=mysql_query($strx);
			$numrows=mysql_num_rows($query);
			if($numrows<1)
			{
				echo"<tr class=rowcontent><td colspan=10>Not Found</td></tr>";
			}
			else
			{
				//echo $sql2;
				$query2=mysql_query($sql2) or die(mysql_error());
				while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
				}
			  while ($res = mysql_fetch_object($query)) {
                    $sql2="select * from ".$dbname.".log_5supplier where supplierid='".$res->kodesupplier."'";
                    $query2=mysql_query($sql2) or die(mysql_error());
                    $res2=mysql_fetch_object($query2);
                   		
					$skry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res->purchaser."'";// echo $skry;
					$qkry=mysql_query($skry) or die(mysql_error());
					$rkry=mysql_fetch_assoc($qkry);
						
					if($res->tglp1=='0000-00-00')
					{
						$stat=0;
					}
					elseif($res->tglp1!='0000-00-00')
					{
						$stat=1;
					}
					if($res->stat_release==0)
					{
					$stat_po=$_SESSION['lang']['un_release_po'];
					}           
					elseif($res->stat_release==1)
					{
					$stat_po=$_SESSION['lang']['release_po'];
					}
					elseif($res->stat_release==2)
					{
						$stat_po="<a href=# onclick=getKoreksi('".$res->nopo."')>".$_SESSION['lang']['koreksi']."</a>";
					}
                      echo"
                        <tr ".($res->stat_release==2?"bgcolor='orange' onclick=getKoreksi('".$res->nopo."')":"class=rowcontent").">
                            <td>".$res->nopo."</td>
                     
                            <td>".$res2->namasupplier."</td>
							<td>".tanggalnormal($res->tanggal)."</td>
                            <td>".tanggalnormal($res->tanggalkirim)."</td>
							<td>".$rkry['namakaryawan']."</td>
                            <td>".$res->syaratbayar."</td><td>".$stat_po."</td>";
                           if(($res->purchaser==$_SESSION['standard']['userid'])||($_SESSION['empl']['kodejabatan']=='5'))
							{
							echo"<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res->nopo."','".tanggalnormal($res->tanggal)."','".$res->kodesupplier."','".$res->subtotal."','".$res->diskonpersen."','".$res->ppn."','".$res->nilaipo."','".$res2->rekening."','".$res2->npwp."','".$res->nilaidiskon."','".$res->stat_release."','".tanggalnormal($res->tanggalkirim)."','".$res->matauang."','".$res->kurs."');\">";
                            echo"<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPo('".$res->nopo."','".$stat."');\" >
							<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_detail_po',event);\">
							</td>";
							}
							else
							{
							echo"
							<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$res->nopo."','','log_slave_print_detail_po',event);\">
							</td>";
							}
                        echo"</tr>";
                      
                }
				echo"<tbody></table></div>";
			}
		}
		break;
	
		case 'cek_pembuat_po':
		//echo "warning:Please See Your Username";
			$user_id=$_SESSION['standard']['userid'];
			$skry="select purchaser from ".$dbname.".log_poht where nopo='".$nopo."'";
			$qkry=mysql_query($skry) or die(mysql_error());
			$rkry=mysql_fetch_assoc($qkry);
			if($rkry['purchaser']!=$user_id)
			{
				echo "warning:Please See Your Username";
				exit();
			}
		break;
		case'getKurs':
		$sGet="select kurs from ".$dbname.".setup_matauangrate where kode='".$mtUang."' and daritanggal='".$tgl_po."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		//echo "warning:".$rGet['kurs'];
		if($mtUang=='IDR')
		{
			$rGet['kurs']=1;
		}
		else
		{
			$rGet['kurs']=$rGet['kurs'];
		}
		echo $rGet['kurs'];
		break;
		case'getKoreksi':
		$sql="select  catatanrelease from ".$dbname.".log_poht where nopo='".$nopo."' and lokalpusat='0'";
		//echo $sql;
	$query=mysql_query($sql) or die(mysql_error());
	$rCek=mysql_num_rows($query);
	if($rCek>0)
	{
					$rest=mysql_fetch_assoc($query);
					echo"<br />
					<div id=test>
					<fieldset>
					<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$nopo."  /></legend>
					<table class=sortable border=0 cellspacing=1 width=\"300\">
					<thead><tr class=rowheader><td align=center>".$_SESSION['lang']['koreksi']."</td></tr></thead>
					<tbody>
					<tr class=rowcontent><td align=justify>".$rest['catatanrelease']."</td></tr>
					<tr><td align=center><button class=mybutton onclick=doneKoreksi() title=\"Selesai Koreksi\" >".$_SESSION['lang']['done']."</button>
						<button class=mybutton onclick=cancel_po() title=\"Menutup Form Ini\">".$_SESSION['lang']['cancel']."</button></td></tr>
					</tbody>
					</table>
						
						</fieldset></div>
						";
	}
	else
	{
		echo"warning:Data Belum Terinput";
		exit();
	}
		break;
		case'updateKoreksi':
		$sUpd="update ".$dbname.".log_poht set stat_release='0' where nopo='".$nopo."'";
		if(!mysql_query($sUpd))
		{
			echo $sUpd."Gagal,".(mysql_error($conn));
		}
		break;
		case'getNotifikasi':
		$Sorg="select kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
		$qOrg=mysql_query($Sorg) or die(mysql_error());
		while($rOrg=mysql_fetch_assoc($qOrg))
		{
		if($_SESSION['empl']['kodejabatan']=='5')
                {
		$sList="select count(*) as jmlhJob from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$rOrg['kodeorganisasi']."' and lokalpusat='0' and status!='3') and (selisih>0 or selisih is null)";
                }
                else
                {
                   $sList="select count(*) as jmlhJob from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$rOrg['kodeorganisasi']."' and purchaser='".$_SESSION['standard']['userid']."' and lokalpusat='0' and status!='3') and (selisih>0 or selisih is null)"; 
                }
		//$sList="select count(*) as jmlhJob from  ".$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$rOrg['kodeorganisasi']."'  and purchaser='".$_SESSION['standard']['userid']."' and lokalpusat='0' and status!='3') and (selisih>0 or selisih is null) group by kodept";
		$qList=mysql_query($sList) or die(mysql_error());
                $rBaros=mysql_num_rows($qList);
                    if($rBaros!=0)
                    {
                        $rList=mysql_fetch_assoc($qList);
                        if($rList['jmlhJob']=='')
                        {
                        $rList['jmlhJob']=0;
                        }
                            if(isset($_POST['status']) and $_POST['status']==1)
                            {
                                echo"[".$rOrg['kodeorganisasi']." : ".$rList['jmlhJob']." ]";
                            }
                            else
                            {
                                echo"[".$rOrg['kodeorganisasi']." : <a href='#' onclick=\"cek_pp_pt('".$rOrg['kodeorganisasi']."')\">".$rList['jmlhJob']."</a> ]";
                            }
                    }
                }
		break;
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
                 $sSupplier="select namasupplier,supplierid from ".$dbname.".log_5supplier where namasupplier like '%".$nmSupplier."%' and kodekelompok='S001'";
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
		default:
		break;
	}
	
      
	
	 ?>
	 
     
     