<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
$kdPbrk=$_POST['kdPbrk']==''?$_GET['kdPbrk']:$_POST['kdPbrk'];
$statBuah=$_POST['statBuah']==''?$_GET['statBuah']:$_POST['statBuah'];
$tglAkhir=tanggalsystem($_POST['tglAkhir']==''?$_GET['tglAkhir']:$_POST['tglAkhir']);
$tglAwal=tanggalsystem($_POST['tglAwal']==''?$_GET['tglAwal']:$_POST['tglAwal']);
$_POST['suppId']==''?$suppId=$_GET['suppId']:$suppId=$_POST['suppId'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$intextId=$_POST['intextId']==''?$_GET['intextId']:$_POST['intextId'];
$BuahStat=$_POST['BuahStat']==''?$_GET['BuahStat']:$_POST['BuahStat'];
$sFr="select * from ".$dbname.".pabrik_5fraksi order by kode asc";
	$qFr=mysql_query($sFr) or die(mysql_error());
	$rNm=mysql_num_rows($qFr);

        while($rFraksi=mysql_fetch_assoc($qFr))
        {
          $kodeFraksi[]=$rFraksi['kode'];
          $nmKeterangan[$rFraksi['kode']]=$rFraksi['keterangan'];
        }
        // kondisi mendapatkan data
        
            
            if($suppId!='')
            {
                 $str="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$suppId."'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $namaspl=$_SESSION['lang']['namasupplier'].":".$bar->namasupplier;
                }
            }
            else if($kdOrg!='')
            {
                 $str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $namaspl=$_SESSION['lang']['unit'].":".$bar->namaorganisasi;
                }
            }
            else
            {
                $namaspl=$_SESSION['lang']['dari'].":".$_SESSION['lang']['all'];
            }
      
switch($proses)
{
	case'preview':
 
        if(($tglAkhir=='')||($tglAwal==''))
	{
		echo"warning:Tanggal Harus Di isi";
		exit();
	}
        $thn=substr($tglAwal,0,4);
        $bln=substr($tglAwal,4,2);
        $dte=substr($tglAwal,6,2);
        $tglAwal1=$thn."-".$bln."-".$dte;
        $thn2=substr($tglAkhir,0,4);
        $bln2=substr($tglAkhir,4,2);
        $dte2=substr($tglAkhir,6,2);
        $tglAkhir1=$thn2."-".$bln2."-".$dte2;
	
	echo"<div style=width:7900px;height:650px;overflow:auto; >";  
	echo"Laporan Sortasi PMKS ".$kdPbrk."  ".$namaspl." periode :".$_POST['tglAwal']."-".$_POST['tglAkhir']."
            <table class=sortable cellspacing=1 border=0 >
	<thead>
 		<tr class=rowheader>
		  <td rowspan=2>No</td>
          <td align=center valign=middle rowspan=2>".$_SESSION['lang']['nospb']."</td>
		  <td align=center valign=middle rowspan=2>".$_SESSION['lang']['noTiket']."</td>
		  <td align=center valign=middle rowspan=2>".$_SESSION['lang']['tanggal']."</td>		  
		  <td align=center rowspan=2 valign=middle>".str_replace(" ","<br>",$_SESSION['lang']['nopol'])."</td>
		  <td align=center rowspan=2 valign=middle>Nama Supir</td>
		  <td align=center rowspan=2 colspan=3 valign=middle>".$_SESSION['lang']['hslTimbangan']."
		  <table border=0 cellspacing=1 width=100%><tr><td align=center>".$_SESSION['lang']['beratMasuk']."</td><td align=center>".$_SESSION['lang']['beratkosong']."</td><td align=center>".$_SESSION['lang']['beratBersih']."</td></tr></table>
		  </td>
		  <td align=center rowspan=2 valign=middle>".str_replace(" ","<br>",$_SESSION['lang']['jmlhTandan'])."</td>
		  <td align=center rowspan=2 valign=middle>BRT</td>
		  <td align=center rowspan=2 valign=middle>JJG Sortasi</td>
              ";//echo"           <td align=center rowspan=2 valign=middle>BRT Sortasi</td>";
		echo "<td align=center rowspan=2 valign=middle colspan=".$rNm.">Hasil Sortasi (%)<table border=0 cellspacing=1 width=100%><tr>";
			foreach ($kodeFraksi as $barisFraksi => $rFr)
			{
				$ar=substr($nmKeterangan[$rFr['kode']],0,1);
				if(is_numeric($ar))
					{
							if(($ar=='0')||($ar=='5'))
							{
									$nmKeterangan[$rFr['kode']]=substr($nmKeterangan[$rFr['kode']],0,2);
							}
					}
				//echo"<td align=center >".str_replace(" ","<br>",$nmKeterangan[$rFr['kode']])."</td>";
				echo"<td align=center width=60>".$nmKeterangan[$rFr['kode']]."</td>";
			}
			echo"</tr></table>
			</td>";
            //echo"<td rowspan=2>% Brondolan</td>";
            echo"<td rowspan=2>KG Potongan</td>";
			
			echo"<td rowspan=2>Total Grading</td>";//total grading
			
			//echo"<td rowspan=2>TBS Normal</td>";//berat bersih - grading semua
			echo"<td rowspan=2>Berat Normal</td>";//berat bersih - kg potongan
			//echo"<td rowspan=2>Biaya Total</td>";
		echo"</tr>
	</thead><tbody>";
	
	if(($kdPbrk!='')&&($statBuah!='5'))
            {
                    if($statBuah==0)
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    elseif($statBuah>0)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."' and intex='".$statBuah."'  ".$add."";
            }
            else if(($kdPbrk!='')&&($statBuah=='5'))
            {
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."'";
            }
            else if(($kdPbrk=='')&&($statBuah!='5'))
            {
                    if($statBuah=='0')
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    elseif($statBuah>1)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and intex='".$statBuah."'   ".$add."";
            }
            else if(($kdPbrk=='')&&($statBuah=='5'))
            {
                    $where= "substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."'";
            }
            $sMax="select notiket,kodefraksi,jumlah from ".$dbname.".pabrik_sortasi_vw where jumlah!=0 and ".$where." order by kodefraksi asc";
            //exit("error".$sMax);
            $qMax=fetchData($sMax);
            foreach($qMax as $brsMax => $rMax)
            {
                $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']]=$rMax['jumlah'];
            }
	$sql="select (a.beratbersih/a.jjgsortasi) as bjr,supir,notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi
            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by notransaksi,`tanggal` asc ";
			
			//echo $sql;
	//echo "warning".$sql;exit();
	//echo $sql;
	
	// Get Harga TBS
	$optTBS = makeOption($dbname,'pabrik_5hargatbs','bjr,harga',"kodeorg='".$kdPbrk."'");
	
	$query=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($query);
	if($row>0)
	{
		while($res=mysql_fetch_assoc($query))
		{
			$jmlhTndn=$res['jumlahtandan1']+$res['jumlahtandan2']+$res['jumlahtandan3'];
			if(($jmlhTndn!=0)||($res['jjgsortasi']!=0))
                        {
                            @$jBrt=$res['beratbersih']/$res['jjgsortasi'];
                            @$jBrt2=$res['beratbersih']/$jmlhTndn;
                        }
                       
                        else
                        {
                            $jBrt=0;
                            $jBrt2=0;
                        }
							$totalgrad+=$grading;
							//$totaltbsnormal+=$tbsnormal;
							$totalberatnormal+=$beratnormal;
                            $subTotal['beratmasuk']+=$res['beratmasuk'];
                            $subTotal['beratkeluar']+=$res['beratkeluar'];
                            $subTotal['beratbersih']+=$res['beratbersih'];
                            $subTotal['jjgSortasitot']+=$res['jjgsortasi'];
                            $subTotal['prsnBrondolan']+=$res['persenBrondolan'];
                            $subTotal['jmlhTndn']+=$jmlhTndn;
                            //$subTotal['jBrt']+=$jBrt;
                            $subTotal['kgpotsortasi']+=$res['kgpotsortasi'];
							$subTotal['biaya']+=$optTBS['5']*($res['beratbersih']-$res['kgpotsortasi']);
			$no+=1;
                       
                            echo"<tr class=rowcontent>
                                    <td>".$no."</td>
                                    <td>".$res['nospb']."</td>
                                    <td>".$res['notransaksi']."</td>
                                    <td>".tanggalnormal($res['tanggal'])."</td>				 
                                    <td>".$res['nokendaraan']."</td>		
									<td>".$res['supir']."</td>	 		
                                    <td align=right>".number_format($res['beratmasuk'],2)."</td>
                                    <td align=right>".number_format($res['beratkeluar'],2)."</td>
                                    <td align=right>".number_format($res['beratbersih'],2)."</td>
                                    <td align=right>".number_format($jmlhTndn,0)."</td>
                                    <td align=right>".number_format($jBrt,2)."</td>
                                    <td align=right>".number_format($res['jjgsortasi'],0)."</td>
                                 ";//echo"   <td align=right>".number_format($jBrt2,2)."</td>";
								 
								 
								 $a1="select * from ".$dbname.".pabrik_sortasi where notiket='".$res['notransaksi']."' group by notiket";
								 $b1=mysql_query($a1);
								 $c1=mysql_fetch_assoc($b1);
								 
	
								 	
								 //indra
                                    foreach($kodeFraksi as $brsKdFraksi =>$listFraksi)
                                    { 
										
                                         //if($listFraksi['kode']=='D'||$listFraksi['kode']=='F')
                                          //{
                                          //    $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
                                          //}   
                                          //else
                                          //{ 
                                           // $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]*$jBrt2;
                                          //}
                                        echo"<td  align=right>".number_format($jmlhFraksi[$res['notransaksi']][$listFraksi['kode']],2)."</td>";
                                        $subTotal[$listFraksi['kode']]+=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
										$j++;
                                    }
									
											
									
								
								
								
								//$tbsnormal=$res['beratbersih']-$grading;
								$beratnormal=$res['beratbersih']-$res['kgpotsortasi'];
									
                                    echo"<td align=right>".number_format($res['kgpotsortasi'],2)."</td>";
									echo"<td align=right>".number_format($grading,2)."</td>";
									//echo"<td align=right>".number_format($tbsnormal,2)."</td>";//tbs normal = berat bersih - grading
                                 	echo"<td align=right>".number_format($beratnormal,2)."</td>";
									//$totalgrad=+$grading;
									//echo"<td align=right>".number_format($res['persenBrondolan'],2)."</td>";
                                //    echo"<td align=right>".number_format($res['kgpotsortasi'],2)."</td>";
									//if($res['bjr']>5) {
									//	echo"<td align=right>".number_format($optTBS['5']*($res['beratbersih']-$res['kgpotsortasi']),2)."</td>";
									//} else {
									//	echo"<td align=right>".number_format($optTBS['1']*($res['beratbersih']-$res['kgpotsortasi']),2)."</td>";
									//}
                            echo"	
                            </tr>
                            ";
							
							
                        
			
		}
		
		
                 echo"<thead><tr class=rowcontent><td colspan=6>".$_SESSION['lang']['total']."</td>
                    <td align=right>".number_format($subTotal['beratmasuk'],2)."</td>
                    <td align=right>".number_format($subTotal['beratkeluar'],2)."</td>
                    <td align=right>".number_format($subTotal['beratbersih'],2)."</td>
                    <td align=right>".number_format($subTotal['jmlhTndn'],2)."</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>".number_format($subTotal['jjgSortasitot'],2)."</td>
                    ";//echo"<td align=right>&nbps;</td>
                      //  ";
                $grandtot=$subTotal['beratbersih']-$subTotal['kgpotsortasi'];
                $sFraksi="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
                $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                while($rFraksi=mysql_fetch_assoc($qFraksi))
                {    
                        echo"<td align=right width=60>".number_format($subTotal[$rFraksi['kode']],2)."</td>";	
                        //$subTotal[$rFraksi['kode']]=0;
                }
               
               // echo"<td align=right>".number_format($subTotal['prsnBrondolan'],2)."</td>";
			     
				echo"<td align=right>".number_format($subTotal['kgpotsortasi'],2)."</td>";
				echo"<td align=right>".number_format($totalgrad,2)."</td>";
				//echo"<td align=right>".number_format($totaltbsnormal,2)."</td>";
				echo"<td align=right>".number_format($grandtot,2)."</td>";
				
				
				
				//echo"<td align=right>".number_format($subTotal['biaya'],2)."</td>";  
                echo"</tr>";
                $subTotal['beratmasuk']=0;
                $subTotal['beratkeluar']=0;
                $subTotal['beratbersih']=0;
                $subTotal['jmlhTndn']=0;
                //$subTotal['jBrt']=0;
                $subTotal['jjgSortasitot']=0;
                $subTotal['prsnBrondolan']=0;
                $subTotal['kgpotsortasi']=0;
	}
	else
	{
		$pnjng=10+$rNm;
		echo"<tr class=rowcontent><td colspan=".$pnjng." align=center>Not Found</td></tr>";
	}
	echo"</tbody></table><div>";
	break;
	
	
	
	
	
	
	
	
	
	
	
	
	
	case'pdf':
	
	$kdPbrk=$_GET['kdPbrk'];
	$statBuah=$_GET['statBuah'];
	$tglAkhir=tanggalsystem($_GET['tglAkhir']);
	$tglAwal=tanggalsystem($_GET['tglAwal']);
        $thn=substr($tglAwal,0,4);
        $bln=substr($tglAwal,4,2);
        $dte=substr($tglAwal,6,2);
        $tglAwal1=$thn."-".$bln."-".$dte;
        $thn2=substr($tglAkhir,0,4);
        $bln2=substr($tglAkhir,4,2);
        $dte2=substr($tglAkhir,6,2);
        $tglAkhir1=$thn2."-".$bln2."-".$dte2;
//	$suppId=$_GET['suppId'];
//	$kdOrg=$_GET['kdOrg'];
//            $str="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$suppId."'";
//            $res=mysql_query($str);
//            while($bar=mysql_fetch_object($res))
//            {
//                $namaspl=$bar->namasupplier;
//            }
//            $statBuah=='0'?'':$namaspl=$kdOrg;

        if(($tglAkhir=='')||($tglAwal==''))
	{
		echo"warning:Tanggal Harus Di isi";
		exit();
	}
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $kdPbrk;
                global $statBuah;
                global $tglAkhir;
                global $tglAwal;
                global $tglAwal1;
                global $tglAkhir1;
                global $suppId;
                global $statBuah;
                global $kdOrg;
                global $namaspl;
                global $jmlhFraksi;
                global $kodeFraksi;
                global $listFraksi2;

				
			    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                $path='images/logo.jpg';
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                
                $this->SetFont('Arial','B',6);
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanSortasi'],'',0,'L');
				$this->Ln();
				$this->Ln();
				$dari="";
				if($statBuah==0)
				{
					$sql="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$suppId."'";
					$query=mysql_query($sql) or die(mysql_error($conn));
					$res=mysql_fetch_assoc($query);
					$dari=" : ".$res['namasupplier'];
				}
				elseif($statBuah!=0||$statBuah!=5)
				{
					$sql="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
					//echo $sql;exit();
					$query=mysql_query($sql) or die(mysql_error($conn));
					$res=mysql_fetch_assoc($query);
					$dari=" : ".$res['namaorganisasi'];
					//$where="kodeorg='".$kdOrg."'";
				}
				else
				{
					$dari=$_SESSION['lang']['all'];
				}
				$this->Cell($width,$height,strtoupper("Rekapitulasi Penerimaan / Penimbangan TBS").$dari,'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ". tanggalnormal($tglAwal)." s.d. ".tanggalnormal($tglAkhir),'',0,'C');
				$this->Ln();
				$this->Ln();
              	$this->SetFont('Arial','B',5);
                $this->SetFillColor(220,220,220);
				$this->Cell(2/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['noTiket'],1,0,'C',1);
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['nospb'],1,0,'C',1);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['nopol'],1,0,'C',1);	
				//$this->Cell(15/100*$width,$height-10,$_SESSION['lang']['hslTimbangan'],1,0,'C',1);
				//$this->SetY($this->GetY());
//				$akhirX=$this->GetX();
//				$this->SetX($akhirX+162);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['beratMasuk'],1,0,'C',1);	
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['beratkosong'],1,0,'C',1);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);		
				//$this->SetY($this->GetY());
				
				//$this->SetX($this->GetX()+305);$_SESSION['lang']['jmlhTandan']
				$this->Cell(4/100*$width,$height,"Jmlh Tandan",1,0,'C',1);
				$this->Cell(3/100*$width,$height,"BRT",1,0,'C',1);
                               
				//$this->Cell(60/100*$width,$height-10,$_SESSION['lang']['hslSortasi'],1,1,'C',1);
				//$this->SetY($this->GetY());
				//$this->SetX($this->GetX()+400);
				$sFr="select * from ".$dbname.".pabrik_5fraksi order by kode asc";
				$qFr=mysql_query($sFr) or die(mysql_error());
				$row=mysql_num_rows($qFr);
				$br=0;
				while($rFr=mysql_fetch_assoc($qFr))
				{
					$ar=substr($rFr['keterangan'],0,1);
					if(is_numeric($ar))
					{
						if(($ar=='0')||($ar=='5'))
						{
							$rFr['keterangan']=substr($rFr['keterangan'],0,2);
						}
					}
					else
					{
						if(substr($rFr['keterangan'],0,1)=='B')
						{
							$as=substr($rFr['keterangan'],0,1);
							$as2=substr($rFr['keterangan'],5,6)	;
							$rFr['keterangan']=$as.".".$as2;
						}
						elseif(substr($rFr['keterangan'],0,1)=='T')
						{
							$as=substr($rFr['keterangan'],0,1);
							$as2=substr($rFr['keterangan'],7,8);
							$rFr['keterangan']=$as.".".$as2;
						}
						elseif(substr($rFr['keterangan'],0,1)=='J')
						{
							$as=substr($rFr['keterangan'],0,1);
							$as2=substr($rFr['keterangan'],7,9);
							$rFr['keterangan']=$as.".".$as2;
						}
						elseif((substr($rFr['keterangan'],0,1)=='L')||(substr($rFr['keterangan'],0,1)=='l'))
						{
							$as="+";
							$as2=substr($rFr['keterangan'],6,9);
							$rFr['keterangan']=$as.$as2;
						}
						
					}
					$this->Cell(4/100*$width,$height,$rFr['keterangan'],1,$r,'C',1);	
				}
                                $this->Cell(4/100*$width,$height,"JJG Sortasi",1,0,'C',1);
								$this->Cell(4/100*$width,$height,"Kg Potongan",1,0,'C',1);
								
								$this->Cell(4/100*$width,$height,"Total Grading",1,0,'C',1);
								//$this->Cell(4/100*$width,$height,"TBS Normal",1,0,'C',1);
								$this->Cell(4/100*$width,$height,"Berat Normal",1,1,'C',1);
                             //   $this->Cell(4/100*$width,$height,"% Brondolan",1,1,'C',1);
				//$this->Ln();
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',7);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',5);
		if(($kdPbrk!='')&&($statBuah!='5'))
                {
		if($statBuah==0)
		{
                    if($suppId!='')
                    {
			$add=" and kodecustomer='".$suppId."'";
                    }
		}
		elseif($statBuah>0)
		{
                    if($kdOrg!='')
                    {
                        $add=" and kodeorg='".$kdOrg."'";
                    }
		}
		$where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."' and intex='".$statBuah."'  ".$add."";
                }
                else if(($kdPbrk!='')&&($statBuah=='5'))
                {
                        $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."'";
                }
                else if(($kdPbrk=='')&&($statBuah!='5'))
                {
                        if($statBuah=='0')
                        {
                            if($suppId!='')
                            {
                                $add=" and kodecustomer='".$suppId."'";
                            }
                        }
                        elseif($statBuah>1)
                        {
                            if($kdOrg!='')
                            {
                                $add=" and kodeorg='".$kdOrg."'";
                            }
                        }
                        $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and intex='".$statBuah."'   ".$add."";
                }
                else if(($kdPbrk=='')&&($statBuah=='5'))
                {
                        $where= "substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."'";
                }
                $sMax="select notiket,kodefraksi,jumlah from ".$dbname.".pabrik_sortasi_vw where jumlah!=0 and ".$where." order by kodefraksi asc";
            //exit("error".$sMax);
            $qMax=fetchData($sMax);
            foreach($qMax as $brsMax => $rMax)
            {
                $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']]=$rMax['jumlah'];
            }
		$i=0;
		$subTotal=array();
                $sql="select notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi
            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by notransaksi,`tanggal` asc";
			
			/*$sql="select (a.beratbersih/a.jjgsortasi) as bjr,notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi
            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by `tanggal` asc ";*/
			
			//indra
			//echo $sql;
			
//		$sql="select notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3` ,a.jjgsortasi,a.persenBrondolan
//            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by `tanggal` asc ";
//		exit("error:".$sql);
		$qDet=mysql_query($sql) or die(mysql_error());
		while($res=mysql_fetch_assoc($qDet))
		{                   
                            $jmlhTndn=$res['jumlahtandan1']+$res['jumlahtandan2']+$res['jumlahtandan3'];
                            if($jmlhTndn!=0)
                            {
                                $jBrt=$res['beratbersih']/$jmlhTndn;
                            }
                            else
                            {
                                $jBrt=0;
                            }
							
							$totalgrad+=$grading;
							$totaltbsnormal+=$tbsnormal;
							$totalberatnormal+=$beratnormal;
                            $subTotal['beratmasuk']+=$res['beratmasuk'];
                            $subTotal['beratkeluar']+=$res['beratkeluar'];
                            $subTotal['beratbersih']+=$res['beratbersih'];
                            $subTotal['jjgSortasitot']+=$res['jjgsortasi'];
                            $subTotal['prsnBrondolan']+=$res['persenBrondolan'];
                            $subTotal['jmlhTndn']+=$jmlhTndn;
                            $subTotal['jBrt']+=$jBrt;
							$subTotal['kgpotsortasi']+=$res['kgpotsortasi'];
                            //$subTotal['beratmasuk']+=$res['beratmasuk'];
                            $no+=1;
                            $i++;
                            $pdf->Cell(2/100*$width,$height,$no,1,0,'C',1);
                            $pdf->Cell(4/100*$width,$height,$res['notransaksi'],1,0,'L',1);	
                            $pdf->Cell(8/100*$width,$height,$res['nospb'],1,0,'L',1);	
                            $pdf->Cell(4/100*$width,$height,tanggalnormal($res['tanggal']),1,0,'L',1);		
                            $pdf->Cell(4/100*$width,$height,$res['nokendaraan'],1,0,'L',1);		
                            $pdf->Cell(4/100*$width,$height,number_format($res['beratmasuk'],2),1,0,'R',1);		
                            $pdf->Cell(4/100*$width,$height,number_format($res['beratkeluar'],2),1,0,'R',1);
                            $pdf->Cell(4/100*$width,$height,number_format($res['beratbersih'],2),1,0,'R',1);	
                            $pdf->Cell(4/100*$width,$height,number_format($jmlhTndn,2),1,0,'R',1);	
                            $pdf->Cell(3/100*$width,$height,number_format($jBrt,2),1,0,'R',1);	
    //			$sFraksi2="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
    //			$qFraksi2=mysql_query($sFraksi2) or die(mysql_error());
                            $j=1;
    //			while($rFraksi=mysql_fetch_assoc($qFraksi2))
                          foreach($kodeFraksi as $brsKdFraksi =>$listFraksi)
                            {
                              if($listFraksi['kode']=='D'||$listFraksi['kode']=='F')
                              {
                                  $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
                              }   
                              else
                              {
                                $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]*$jBrt;
                              }
                              $pdf->Cell(4/100*$width,$height,number_format($jmlhFraksi[$res['notransaksi']][$listFraksi['kode']],2),1,0,'R',1);				
                              $subTotal['fraksi'.$j]+=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
                              $j++;
                            }
							
								/*##
								$ab="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='B' and notiket='".$res['notransaksi']."'";
								$bb=mysql_query($ab);
								$cb=mysql_fetch_assoc($bb);
									$b=$cb['jumlah']*$jBrt;	//echo $ab;
									
								###
								$ah="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='H' and notiket='".$res['notransaksi']."'";
								$bh=mysql_query($ah);
								$ch=mysql_fetch_assoc($bh);
									$h=$ch['jumlah']*$jBrt;
								
								##	
								$ai="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='I' and notiket='".$res['notransaksi']."'";
								$bi=mysql_query($ai);
								$ci=mysql_fetch_assoc($bi);
									$i=$ci['jumlah']*$jBrt;	
									
									
								$aj="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='J' and notiket='".$res['notransaksi']."'";
								$bj=mysql_query($aj);
								$cj=mysql_fetch_assoc($bj);
									$j=$cj['jumlah']*$jBrt;								
								 	
								$ak="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='K' and notiket='".$res['notransaksi']."'";
								$bk=mysql_query($ak);
								$ck=mysql_fetch_assoc($bk);
									$k=$ck['jumlah']*$jBrt;
												
								$as="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='S' and notiket='".$res['notransaksi']."'";
								$bs=mysql_query($as);
								$cs=mysql_fetch_assoc($bs);
									$s=$cs['jumlah']*$jBrt;									
									
								$ad="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='D' and notiket='".$res['notransaksi']."'";
								$bd=mysql_query($as);
								$cd=mysql_fetch_assoc($bs);
									$d=$cd['jumlah'];
									
								$af="select jumlah from ".$dbname.".pabrik_sortasi where kodefraksi='F' and notiket='".$res['notransaksi']."'";
								$bf=mysql_query($af);
								$cf=mysql_fetch_assoc($bf);
									$f=$cf['jumlah'];				*/					
									
								//$grading=$b+$h+$i+$j+$k+$s+$d+$f;
								
								
								$tbsnormal=$res['beratbersih']-$grading;
								$beratnormal=$res['beratbersih']-$res['kgpotsortasi'];
								
								$totalberatbersih=
							
							
                             $pdf->Cell(4/100*$width,$height,number_format($res['jjgsortasi'],2),1,0,'R',1);
                            $pdf->Cell(4/100*$width,$height,number_format($res['kgpotsortasi'],2),1,0,'R',1);
							//$pdf->Cell(4/100*$width,$height,number_format($grading,2),1,0,'R',1);
							//$pdf->Cell(4/100*$width,$height,number_format($tbsnormal,2),1,0,'R',1);
							$pdf->Cell(4/100*$width,$height,number_format($beratnormal,2),1,0,'R',1);
                        
//			{
//		
//			$sMax="select jumlah from ".$dbname.".pabrik_sortasi where notiket='".$res['notransaksi']."' and kodefraksi='".$rFraksi['kode']."'";
//			$qMax=mysql_query($sMax) or die(mysql_error());
//			$rMax=mysql_fetch_assoc($qMax);
//			//echo"<td width=60>".number_format($rMax['jumlah'],2)."</td>";
//			//$pdf->Cell(5/100*$width,$height-10,number_format($rMax['jumlah'],2),1,$r,'C',1);
//			
//			}
                      
			$pdf->Ln();
			if($i==20)
			{
				
//				$subTotal['beratmasuk']=0;
//				$subTotal['beratkeluar']=0;
//				$subTotal['beratbersih']=0;
//				$subTotal['jmlhTndn']=0;
//				$subTotal['jBrt']=0;
//                                $subTotal['jjgSortasitot']=0;
//                                $subTotal['prsnBrondolan']=0;
//				$i=0;
				$pdf->AddPage();
		
                         }
		}
                $pdf->Cell(22/100*$width,$height,"Sub Total",1,0,'R',1);
                $pdf->Cell(4/100*$width,$height,number_format($subTotal['beratmasuk'],2),1,0,'R',1);		
                $pdf->Cell(4/100*$width,$height,number_format($subTotal['beratkeluar'],2),1,0,'R',1);
                $pdf->Cell(4/100*$width,$height,number_format($subTotal['beratbersih'],2),1,0,'R',1);	
                $pdf->Cell(4/100*$width,$height,number_format($subTotal['jmlhTndn'],2),1,0,'R',1);	
                $pdf->Cell(3/100*$width,$height,number_format($subTotal['jBrt'],2),1,0,'R',1);	
                for($j=1;$j<9;$j++)
                {
                    //$pdf->Cell(4/100*$width,$height,number_format($subTotal['fraksi'.$k],2),1,0,'R',1);	  
					 $pdf->Cell(4/100*$width,$height,number_format($subTotal['fraksi'.$j],2),1,0,'R',1);	
                }
				
				$totalberatbersih=$subTotal['beratbersih']-$subTotal['kgpotsortasi'];
				
                $pdf->Cell(4/100*$width,$height,number_format($subTotal['jjgSortasitot'],2),1,0,'R',1);//
				$pdf->Cell(4/100*$width,$height,number_format($subTotal['kgpotsortasi'],2),1,0,'R',1);
				
				//$pdf->Cell(4/100*$width,$height,number_format($totalgrad,2),1,0,'R',1);
				$pdf->Cell(4/100*$width,$height,number_format($totaltbsnormal,2),1,0,'R',1);
				$pdf->Cell(4/100*$width,$height,number_format($totalberatbersih,2),1,0,'R',1);
				
				


                //$pdf->Cell(4/100*$width,$height,number_format($subTotal['prsnBrondolan'],2),1,0,'R',1);
			
        $pdf->Output();
	break;
	
	
	
	
	
	
	
	
	
	
	################
	###############excel
	
	
	case'excel':
	 
	if(($tglAkhir=='')||($tglAwal==''))
	{
		echo"warning:Tanggal Harus Di isi";
		exit();
	}
        $thn=substr($tglAwal,0,4);
        $bln=substr($tglAwal,4,2);
        $dte=substr($tglAwal,6,2);
        $tglAwal1=$thn."-".$bln."-".$dte;
        $thn2=substr($tglAkhir,0,4);
        $bln2=substr($tglAkhir,4,2);
        $dte2=substr($tglAkhir,6,2);
        $tglAkhir1=$thn2."-".$bln2."-".$dte2;
	
	
	$stream="<table><tr><td colspan=13 align=center>Laporan Sortasi PMKS ".$kdPbrk."  ".$namaspl." periode :".$_GET['tglAwal']."-".$_GET['tglAkhir']."</td></tr><tr><td colspan=3></td><td></td></tr></table>
    <table border=1 >
	<thead>
 		<tr >
		  <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>No</td>
          <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>".$_SESSION['lang']['nospb']."</td>
		  <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>".$_SESSION['lang']['noTiket']."</td>
		  <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>".$_SESSION['lang']['tanggal']."</td>		  
		  <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>".$_SESSION['lang']['nopol']."</td>
		  <td bgcolor=#DEDEDE align=center valign=middle rowspan=2>Nama Supir</td>
		  <td bgcolor=#DEDEDE align=center rowspan=2 colspan=3 valign=middle>".$_SESSION['lang']['hslTimbangan']."
		  <table border=1 cellspacing=1 width=100%><tr><td align=center>".$_SESSION['lang']['beratMasuk']."</td><td align=center>".$_SESSION['lang']['beratkosong']."</td><td align=center>".$_SESSION['lang']['beratBersih']."</td></tr></table>
		  </td>
		  <td bgcolor=#DEDEDE align=center rowspan=2 valign=middle>".str_replace(" ","<br>",$_SESSION['lang']['jmlhTandan'])."</td>
		  <td bgcolor=#DEDEDE align=center rowspan=2 valign=middle>BRT</td>
		  <td bgcolor=#DEDEDE align=center rowspan=2 valign=middle>JJG Sortasi</td>
              ";
		$stream.= "<td bgcolor=#DEDEDE align=center rowspan=2 valign=middle colspan=".$rNm.">Hasil Sortasi (%)<table border=1 cellspacing=1 width=100%><tr>";
			foreach ($kodeFraksi as $barisFraksi => $rFr)
			{
				$ar=substr($nmKeterangan[$rFr['kode']],0,1);
				if(is_numeric($ar))
					{
							if(($ar=='0')||($ar=='5'))
							{
									$nmKeterangan[$rFr['kode']]=substr($nmKeterangan[$rFr['kode']],0,2);
							}
					}
				//echo"<td align=center >".str_replace(" ","<br>",$nmKeterangan[$rFr['kode']])."</td>";
				$stream.="<td bgcolor=#DEDEDE align=center width=60>".$nmKeterangan[$rFr['kode']]."</td>";
			}
			$stream.="</tr></table>
			</td>";
            //echo"<td rowspan=2>% Brondolan</td>";
            $stream.="<td bgcolor=#DEDEDE rowspan=2>KG Potongan</td>";
			
			$stream.="<td bgcolor=#DEDEDE rowspan=2>Total Grading</td>";//total grading
			
			//echo"<td rowspan=2>TBS Normal</td>";//berat bersih - grading semua
			$stream.="<td bgcolor=#DEDEDE rowspan=2>Berat Normal</td>";//berat bersih - kg potongan
			//echo"<td rowspan=2>Biaya Total</td>";
		$stream.="</tr>
	</thead><tbody><table border=1>";
	
	if(($kdPbrk!='')&&($statBuah!='5'))
            {
                    if($statBuah==0)
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    elseif($statBuah>0)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."' and intex='".$statBuah."'  ".$add."";
            }
            else if(($kdPbrk!='')&&($statBuah=='5'))
            {
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."'";
            }
            else if(($kdPbrk=='')&&($statBuah!='5'))
            {
                    if($statBuah=='0')
                    {
                        if($suppId!='')
                        {
                            $add=" and kodecustomer='".$suppId."'";
                        }
                    }
                    elseif($statBuah>1)
                    {
                        if($kdOrg!='')
                        {
                            $add=" and kodeorg='".$kdOrg."'";
                        }
                    }
                    $where=" substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and intex='".$statBuah."'   ".$add."";
            }
            else if(($kdPbrk=='')&&($statBuah=='5'))
            {
                    $where= "substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."'";
            }
            $sMax="select notiket,kodefraksi,jumlah from ".$dbname.".pabrik_sortasi_vw where jumlah!=0 and ".$where." order by kodefraksi asc";
            //exit("error".$sMax);
            $qMax=fetchData($sMax);
            foreach($qMax as $brsMax => $rMax)
            {
                $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']]=$rMax['jumlah'];
            }
	$sql="select (a.beratbersih/a.jjgsortasi) as bjr,supir,notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi
            from ".$dbname.".pabrik_timbangan a left join ".$dbname.".pabrik_sortasi b on a.notransaksi=b.notiket where ".$where." and b.jumlah!=0 and kodebarang='40000003' group by notransaksi,notiket  order by notransaksi,`tanggal` asc ";
			
			//echo $sql;
	//echo "warning".$sql;exit();
	//echo $sql;
	
	// Get Harga TBS
	$optTBS = makeOption($dbname,'pabrik_5hargatbs','bjr,harga',"kodeorg='".$kdPbrk."'");
	
	$query=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($query);
	if($row>0)
	{
		while($res=mysql_fetch_assoc($query))
		{
			$jmlhTndn=$res['jumlahtandan1']+$res['jumlahtandan2']+$res['jumlahtandan3'];
			if(($jmlhTndn!=0)||($res['jjgsortasi']!=0))
                        {
                            @$jBrt=$res['beratbersih']/$res['jjgsortasi'];
                            @$jBrt2=$res['beratbersih']/$jmlhTndn;
                        }
                       
                        else
                        {
                            $jBrt=0;
                            $jBrt2=0;
                        }
							$totalgrad+=$grading;
							//$totaltbsnormal+=$tbsnormal;
							$totalberatnormal+=$beratnormal;
                            $subTotal['beratmasuk']+=$res['beratmasuk'];
                            $subTotal['beratkeluar']+=$res['beratkeluar'];
                            $subTotal['beratbersih']+=$res['beratbersih'];
                            $subTotal['jjgSortasitot']+=$res['jjgsortasi'];
                            $subTotal['prsnBrondolan']+=$res['persenBrondolan'];
                            $subTotal['jmlhTndn']+=$jmlhTndn;
                            //$subTotal['jBrt']+=$jBrt;
                            $subTotal['kgpotsortasi']+=$res['kgpotsortasi'];
							$subTotal['biaya']+=$optTBS['5']*($res['beratbersih']-$res['kgpotsortasi']);
			$no+=1;
                       
                            $stream.="<tr >
                                    <td>".$no."</td>
                                    <td>".$res['nospb']."</td>
                                    <td>".$res['notransaksi']."</td>
                                    <td>".tanggalnormal($res['tanggal'])."</td>				 
                                    <td>".$res['nokendaraan']."</td>		
									<td>".$res['supir']."</td>	 		
                                    <td align=right>".number_format($res['beratmasuk'],2)."</td>
                                    <td align=right>".number_format($res['beratkeluar'],2)."</td>
                                    <td align=right>".number_format($res['beratbersih'],2)."</td>
                                    <td align=right>".number_format($jmlhTndn,0)."</td>
                                    <td align=right>".number_format($jBrt,2)."</td>
                                    <td align=right>".number_format($res['jjgsortasi'],0)."</td>
                                 ";//echo"   <td align=right>".number_format($jBrt2,2)."</td>";
								 
								 
								 $a1="select * from ".$dbname.".pabrik_sortasi where notiket='".$res['notransaksi']."' group by notiket";
								 $b1=mysql_query($a1);
								 $c1=mysql_fetch_assoc($b1);
								 
	
								 	 
								 //indra
                                    foreach($kodeFraksi as $brsKdFraksi =>$listFraksi)
                                    { 
										
                                         //if($listFraksi['kode']=='D'||$listFraksi['kode']=='F')
                                          //{
                                          //    $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
                                          //}   
                                          //else
                                          //{ 
                                           // $jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']]*$jBrt2;
                                          //}
                                        $stream.="<td  align=right>".number_format($jmlhFraksi[$res['notransaksi']][$listFraksi['kode']],2)."</td>";
                                        $subTotal[$listFraksi['kode']]+=$jmlhFraksi[$res['notransaksi']][$listFraksi['kode']];
										$j++;
                                    }
									
											
									
								
								
								
							
								$beratnormal=$res['beratbersih']-$res['kgpotsortasi'];
									
                                    $stream.="<td align=right>".number_format($res['kgpotsortasi'],2)."</td>";
									$stream.="<td align=right>".number_format($grading,2)."</td>";	
                             $stream.="<td align=right>".number_format($beratnormal,2)."</td>";
                             $stream.="	</tr>";
							
							
                        
			
		}
		
		
                 $stream.="<thead><tr ><td colspan=6>".$_SESSION['lang']['total']."</td>
                    <td align=right>".number_format($subTotal['beratmasuk'],2)."</td>
                    <td align=right>".number_format($subTotal['beratkeluar'],2)."</td>
                    <td align=right>".number_format($subTotal['beratbersih'],2)."</td>
                    <td align=right>".number_format($subTotal['jmlhTndn'],2)."</td>
                    <td align=right>&nbsp;</td>
                    <td align=right>".number_format($subTotal['jjgSortasitot'],2)."</td>
                    ";
                $grandtot=$subTotal['beratbersih']-$subTotal['kgpotsortasi'];
                $sFraksi="select kode from ".$dbname.".pabrik_5fraksi order by kode asc";
                $qFraksi=mysql_query($sFraksi) or die(mysql_error());
                while($rFraksi=mysql_fetch_assoc($qFraksi))
                {    
                        $stream.="<td align=right width=60>".number_format($subTotal[$rFraksi['kode']],2)."</td>";	
                }
				$stream.="<td align=right>".number_format($subTotal['kgpotsortasi'],2)."</td>";
				$stream.="<td align=right>".number_format($totalgrad,2)."</td>";
				$stream.="<td align=right>".number_format($grandtot,2)."</td>";
                $stream.="</tr>";
                $subTotal['beratmasuk']=0;
                $subTotal['beratkeluar']=0;
                $subTotal['beratbersih']=0;
                $subTotal['jmlhTndn']=0;
                $subTotal['jjgSortasitot']=0;
                $subTotal['prsnBrondolan']=0;
                $subTotal['kgpotsortasi']=0;
	}
	else
	{
		$pnjng=10+$rNm;
		$stream.="<tr ><td colspan=".$pnjng." align=center>Not Found</td></tr>";
	}
	$stream.="</table>";
			/*
		echo"Dalam Masa Perbaikan.., Kontak IT";
		exit();
		*/
			//echo "warning:".$strx;
			//=================================================
		$stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			$tglSkrg=date("Ymd");
			$nop_="rekapSortasiBuah_".$tglSkrg;
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
	
	
	
	
	
	
	
	case'getDetail':
	echo"<link rel=stylesheet type=text/css href=style/generic.css>";
	$nokontrak=$_GET['nokontrak'];
	$sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
	$qHead=mysql_query($sHed) or die(mysql_error());
	$rHead=mysql_fetch_assoc($qHead);
	$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
	$qBrg=mysql_query($sBrg) or die(mysql_error());
	$rBrg=mysql_fetch_assoc($qBrg);
	
	$sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
	$qCust=mysql_query($sCust) or die(mysql_error());
	$rCust=mysql_fetch_assoc($qCust);
	echo"<fieldset><legend>".$_SESSION['lang']['detailPengiriman']."</legend>
	<table cellspacing=1 border=0 class=myinputtext>
	<tr>
		<td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>".$nokontrak."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tglKontrak']."</td><td>:</td><td>".tanggalnormal($rHead['tanggalkontrak'])."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['komoditi']."</td><td>:</td><td>".$rBrg['namabarang']."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['Pembeli']."</td><td>:</td><td>".$rCust['namacustomer']."</td>
	</tr>
	</table><br />
	<table cellspacing=1 border=0 class=sortable><thead>
	<tr class=data>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['nodo']."</td>
	<td>".$_SESSION['lang']['nosipb']."</td>
	<td>".$_SESSION['lang']['beratBersih']."</td>
	<td>".$_SESSION['lang']['kodenopol']."</td>
	<td>".$_SESSION['lang']['sopir']."</td>
	</tr></thead><tbody>
	";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/	

	$sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
	$qDet=mysql_query($sDet) or die(mysql_error());
	$rCek=mysql_num_rows($qDet);
	if($rCek>0)
	{
		while($rDet=mysql_fetch_assoc($qDet))
		{
			echo"<tr class=rowcontent>
			<td>".$rDet['notransaksi']."</td>
			<td>".tanggalnormal($rDet['tanggal'])."</td>
			<td>".$rDet['nodo']."</td>
			<td>".$rDet['nosipb']."</td>
			<td align=right>".number_format($rDet['beratbersih'],2)."</td>
			<td>".$rDet['nokendaraan']."</td>
			<td>".ucfirst($rDet['supir'])."</td>
			</tr>";
		}
	}
	else
	{
		echo"<tr><td colspan=7>Not Found</td></tr>";
	}
	echo"</tbody></table></fieldset>";

	break;
	case'getkbn':
               // $optkdOrg2="<option value=''></option value=''>".$_SESSION['lang']['all']."</option>";
            if($kdPbrk=='')
            {
                exit("Error:Pabrik Tidak Boleh Kosong");
            }
           
		if($BuahStat==0)
		{
                        $optkdOrg2.="<option value=''>".$_SESSION['lang']['all']."</option>";
			$sOrg="SELECT namasupplier,supplierid,kodetimbangan FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan is not null";//echo "warning:".$sOrg;exit();
			$qOrg=mysql_query($sOrg) or die(mysql_error());
			while($rOrg=mysql_fetch_assoc($qOrg))
			{
				$optkdOrg2.="<option value=".$rOrg['supplierid']."".($rOrg['supplierid']==$idCust?'selected':'').">".$rOrg['namasupplier']."</option>";
			}
                        //echo"warning:test";
			echo $optkdOrg2."###".$BuahStat;exit();
		}
                elseif($BuahStat==5)
                {
                    $optkdOrg2.="<option value=''>".$_SESSION['lang']['all']."</option>";
                    echo $optkdOrg2."###".$BuahStat;exit();
                }
		elseif($BuahStat==1)
		{
                    $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."' and millcode='".$kdPbrk."')";//echo "warning:".$sOrg;
			//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk in(select induk from ".$dbname.".organisasi where tipe='PABRIK')";//echo "warning:".$sOrg;
		}
		elseif($BuahStat==2)
		{
                    $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."'  and millcode='".$kdPbrk."')";//echo "warning:".$sOrg;
			//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk not in(select induk from ".$dbname.".organisasi where tipe='PABRIK')"; //echo "warning:".$sOrg;
		}
                $optkdOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
		$qOrg=mysql_query($sOrg) or die(mysql_error());
		while($rOrg=mysql_fetch_assoc($qOrg))
		{
			$optkdOrg.="<option value=".$rOrg['kodeorganisasi']."".($rOrg['kodeorganisasi']==$kdKbn?'selected':'').">".$rOrg['namaorganisasi']."</option>";
		}
		
		echo $optkdOrg."###".$BuahStat;
		break;
	
	break;
}

?>