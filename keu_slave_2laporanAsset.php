<?php
// -- ind --
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
//fungsi selisih waktu

function datediff($tgl1, $tgl2){

$tgl1 = (is_string($tgl1) ? strtotime($tgl1) : $tgl1);
$tgl2 = (is_string($tgl2) ? strtotime($tgl2) : $tgl2);
$diff_secs = abs($tgl1 - $tgl2);
$base_year = min(date("Y", $tgl1), date("Y", $tgl2));
$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
return array( "years" => date("Y", $diff) - $base_year, "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1, "months" => date("n", $diff) - 1, "days_total" => floor($diff_secs / (3600 * 24)), "days" => date("j", $diff) - 1, "hours_total" => floor($diff_secs / 3600), "hours" => date("G", $diff), "minutes_total" => floor($diff_secs / 60), "minutes" => (int) date("i", $diff), "seconds_total" => $diff_secs, "seconds" => (int) date("s", $diff) );
}
function bulanian($date1,$date2){
	$years1=$date1->format('Y');
	$years2=$date2->format('Y');
	$month1=$date1->format('m');
	$month2=$date2->format('m');
	$jumYears=abs($years2-$years1);
	$jumMonth=abs($month2-$month1);
	if(($jumMonth==0)&&($jumYears!=0)){
		
	$hasilMonth=($jumYears*12)+$jumMonth;
	}else{
		if(intval($month2) > intval($month1)){
			$hasilMonth=($jumYears*12)+$jumMonth;
		}else{
		   $hasilMonth=($jumYears*12)-$jumMonth;
		}
	}
	return $hasilMonth;
}




##### declarasi variabel ##### 
$proses=$_GET['proses'];
$kdOrg=$_POST['kdOrg'];
$kdAst=$_POST['kdAst'];
$tpAsset=$_POST['tpAsset'];
$kdunit=$_POST['kdunit'];

if($kdOrg=='')$kdOrg=$_GET['kdOrg'];
if($kdAst=='')$kdAst=$_GET['kdAst'];
if($tpAsset=='')$tpAsset=$_GET['tpAsset'];
if($kdunit=='')$kdunit=$_GET['kdunit'];




##### selesai ##### 

## nama karyawan
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where karyawanid=".$_SESSION['standard']['userid']. "";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namakar[$bar->karyawanid]=$bar->namakaryawan;
}
## selesai nama karyawan

##### ambil kode aset dan organisasi untuki option text ##### 
//get org
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
	$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
	$rOrg=mysql_fetch_assoc($qOrg);
	$nmOrg=$rOrg['namaorganisasi'];
	
//	if(!$nmOrg)$nmOrg=$kdOrg;
//		
////get ast
//	$sAst="select namatipe,kodetipe from ".$dbname.".sdm_5tipeasset where kodetipe ='".$kdAst."' ";	
//	$qAst=mysql_query($sAst) or die(mysql_error($conn));
//	while($rAst=mysql_fetch_assoc($qAst))
//	{
//		$nmAst=$rAst['namatipe'];
//	}
//	if(!$nmAst)$nmAst=$kdAst;
##### selesai ##### 
$brd=0;
$bgBelakang="bgcolor=#00FF40 align=center";
if($proses=='excel')	
{
    $brd=1;
    $bgBelakang="bgcolor=#00FF40 ";
}
  if($tpAsset!='')
    {
        $where=" and tipeasset='".$tpAsset."'";
    }		
		
##### preview ##### 
$data="Daftar Asset :  ".$nmOrg." ".$_SESSION['lang']['periode'].":".$kdAst;
$data.="<table class=sortable cellspacing=1 border=".$brd."><thead>";
         $data.=" <tr class=rowheader>";
			$data.="<td align=center ".$bgBelakang.">No</td>";
		        $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['kodeorganisasi']."</td>";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['kodeasset']."</td> ";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['thnperolehan']."</td>";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['namaasset']."</td>";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['status']."</td> ";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['hargaperolehan']."</td> ";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['jumlahbulanpenyusutan']."</td> ";	
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['usia']." (".$_SESSION['lang']['bulan'].")</td> ";	
                        $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['sisa']." (".$_SESSION['lang']['bulan'].")</td> ";	
					    $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['akumulasipenyusutan']."</td> ";	
                        $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['nilaibuku']."</td> ";	
                        $data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['keterangan']."</td> ";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['awalpenyusutan']."</td> ";
			$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['bulanan']."</td> ";
            $data.="</tr> </thead><tbody>";
          
if($kdunit=='')
{
	$sList="select * from ".$dbname.".sdm_daftarasset where  kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."') and awalpenyusutan<='".$kdAst."' ".$where." order by kodeasset";
}
else
{
	$sList="select * from ".$dbname.".sdm_daftarasset where  kodeorg='".$kdunit."' and awalpenyusutan<='".$kdAst."' ".$where." order by kodeasset";
}
$qList=mysql_query($sList) or die(mysql_error());
while($bar=mysql_fetch_assoc($qList))
{
	$tgl=$bar['awalpenyusutan']."-01";
	$tahun=substr($tgl,0,4);
	$bulan=substr($tgl,5,2);
	$hari='01';
	
	list($year, $month, $day) = explode('-',  $tgl);
	$tgl1=date("Y-m-d", mktime(0, 0, 0, $month-1,$day,$year));
	
	
	
    $no+=1;
    $tgl2=$kdAst."-01";
	$date1=new DateTime($tgl);
	$date2=new DateTime($tgl2);
    $date= bulanian($date1,$date2);
    //$selisih=datediff($tgl,$tgl2);
	$selisih=$date;
	
	
    $data.="<tr class=rowcontent>";
    $data.="<td align=center>".$no."</td>";
	$data.="<td>".$bar['kodeorg']."</td>";    
	$data.="<td>".$bar['kodeasset']."</td>";    
	$data.="<td align=right>".$bar['tahunperolehan']."</td>";    
	$data.="<td>".$bar['namasset']."</td>";    
	


	if($bar['status']==0)
	{	
		$data.="<td>Sudah pensiun</td>";
	}
	else if($bar['status']==1)
	{	
		$data.="<td>Aktif</td>";
	}
	else if($bar['status']==2)
	{	
		$data.="<td>Rusak tidak dapat di pakai</td>";
	}
	else
	{	
		$data.="<td>Hilang</td>";
	}	
	//2008-12
	//echo substr($bar['awalpenyusutan'],5,2);exit();
	

	//if(substr($bar['awalpenyusutan'],5,2)=='03')
	//{
		//$selisih[months_total]=$selisih[months_total]+1;	
		$selisih=$selisih+1;	
		
	//}
	//else
	//{
		//$selisih[months_total]=$selisih[months_total];
		//$selisih=$selisih;
	//}
	
	
			
        $tgl1=$bar['awalpenyusutan']."-01";
        //if($selisih[months_total]>$bar['jlhblnpenyusutan'])
		if($selisih>$bar['jlhblnpenyusutan'])
        {
            //$selisih[months_total]=$bar['jlhblnpenyusutan'];
			$selisih=$bar['jlhblnpenyusutan'];
        }
		//else if ($selisih[months_total]==0)
		else if ($selisih==0)
		{
			 //$selisih[months_total]='1';
			 $selisih='1';
		}
        // $sisabln=$bar['jlhblnpenyusutan']-$selisih[months_total];
		$sisabln=$bar['jlhblnpenyusutan']-$selisih;
        if(substr($sisabln,0,1)=='-')
        {
            $sisabln=0;
        }
        //$akumulasiBulanan=$bar['bulanan']*$selisih[months_total];
		//$akumulasiBulanan=$bar['bulanan']*$selisih;
		$akumulasiBulanan=($bar['tahunan']/12)*$selisih;
        if($akumulasiBulanan>$bar['hargaperolehan'])
        {
            $akumulasiBulanan=$bar['hargaperolehan'];
        }
        $nilai=$bar['hargaperolehan']-$akumulasiBulanan;
		
	

	
	
	
		
	//$data.="<td>".$bar['status']."</td>";    
	$data.="<td align=right>".number_format($bar['hargaperolehan'],2)."</td>";    
	$data.="<td align=right>".$bar['jlhblnpenyusutan']."</td>";  
       // $data.="<td align=right>".$selisih[months_total]."</td>";  
	   $data.="<td align=right>".$selisih."</td>";  
        $data.="<td align=right>".number_format($sisabln,2)."</td>";  
        $data.="<td align=right>".number_format($akumulasiBulanan,2)."</td>"; 
        $data.="<td align=right>".number_format($nilai,2)."</td>"; 
	$data.="<td>".$bar['keterangan']."</td>";    				
	$data.="<td>".$bar['awalpenyusutan']."</td>"; 
	//$data.="<td>".tanggalnormal($bar['awalpenyusutan'])."</td>";
	$data.="<td align=right>".number_format($bar['bulanan'],2)."</td>";
        $data.="</tr>";
        $totHarga+=$bar['hargaperolehan'];
        $totHargaAkumul+=$akumulasiBulanan;
        $totNilai+=$nilai;
        $bulanan+=$bar['bulanan'];
        
}  
$data.="<thead><tr><td colspan=6>".$_SESSION['lang']['total']."</td>";
$data.="<td align=right>".number_format($totHarga,2)."</td>";
$data.="<td colspan=3>&nbsp;</td>";
$data.="<td align=right>".number_format($totHargaAkumul,2)."</td>";
$data.="<td align=right>".number_format($totNilai,2)."</td>";
$data.="<td colspan=2>&nbsp;</td>";
$data.="<td align=right>".number_format($bulanan,2)."</td></tr>";
$data.="</tbody></table>";
#####  tutup preveiw ##### 

 


##### tutup EXEL ##### 



##### untuk menu button dan panggil menu dr atas ##### 
switch($proses)
{
	// tampilkan preview //
	case'preview':
		if($kdOrg=='')
		{
			echo "warning : Kode Organisasi Masih Kosong";
			exit();	
		}
		else if($kdAst=='')
		{
			echo "warning : Periode Masih Kosong";
			exit();	


		}
		else 
		{
			echo $data;	//panggil data dari preview di atas
		}
	break;
	// tutup tampilkan preview //
		
/*	if(($kdOrg=='')||($kdOrg==''))
		{
			echo"warning:a";
			exit();
		}
		
		echo"<div style=overflow:auto; height:650px;>";
		echo"Laporan Daftar Asset ".$kdOrg."  ".$tpAst."	
				<table class=sortable cellspacing=1 border=0>
			  <thead>
			  <tr class=rowheader>
				<td>No</td>;
				<td>Kode Organisasi</td>
				<td>Kode Asset</td>
				<td>Tahun  Perolehan</td>
				<td>Nama Asset</td>
				<td>Status (1=Aktif 0=Tidak)</td>
				<td>Harga Perolehan</td>
				<td>Jumlah Bulan Penyusutan</td>
				<td>Keterangan</td>
				<td>Awal Penyusutan</td>
				<td>Bulanan</td>
			  </tr>
			  </thead>
			  <tbody>";
		
		$sql="select * from ".$dbname.".sdm_daftarasset where kodeasset='".$kdAst."' order by kodeorg";
		$query=mysql_query($sql) or die(mysql_error());
		$row=mysql_num_rows($query);
		if($row>0)
		{
			while($res=mysql_fetch_assoc($query))
			{
	
				$no+=1;
				echo"<tr class=rowcontent>
										<td>".$no."</td>
										<td>".$res['kodeorg']."</td>
										<td>".$res['kodeasset']."</td>
										<td>".$res['tahunperolehan']."</td>
										<td>".$res['hargaperolehan']."</td>
										<td>".$res['namasset']."</td>
										<td>".$res['status']."</td>
										<td>".$res['hargaperolehan']."</td>
										<td>".$res['jlhblnpenyusutan']."</td>
										<td>".$res['keterangan']."</td>
										<td>".$res['awalpenyusutan']."</td>
										<td>".$res['bulanan']."</td>	 
									";	
				echo"</tr>";
			}
		}*/
	
		

	// tampilkan exel //
	case 'excel':
		
		if($kdOrg=='')
		{
			echo "warning : Kode Organisasi Tidak Boleh Kosong";
			exit();	
		}
		else if($kdAst=='')
		{
			echo "warning : Periode Tidak Boleh Kosong";
			exit();	
		}
		
		$data.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Daftar_Asset_".$tglSkrg;
		//$nop_"Laporan Daftar Asset ".$nmOrg."_".$nmAst;
		//$nop_="Daftar Asset : ".$nmOrg." ".$nmAst;
		if(strlen($data)>0)
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
			if(!fwrite($handle,$data))
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
		// tutup tampilakn panggil exel //
			
	// tampilkan PDF //		
	case'pdf':
	
		if($kdOrg=='')
		{
			echo "warning : Kode Organisasi Masih Kosong";
			exit();	
		}
		else if($kdAst=='')
		{
			echo "warning : Periode Masih Kosong";
			exit();	
		}
		
		//buat header pdf
		class PDF extends FPDF
				{
					function Header() {
						//declarasi header variabel
						global $conn;
						global $dbname;
						global $align;
						global $length;
						global $colArr;
						global $title;
						
						global $nmOrg;
						global $kdOrg;
						global $kdAst;
						global $nmAst;
						global $thnPer;
						global $nmAsst;
						global $namakar;
                                                global $selisih;
                                                global $where;

						
						//alamat PT minanga dan logo
						$query = selectQuery($dbname,'organisasi','alamat,telepon',
							"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
						$orgData = fetchData($query);
						
						$width = $this->w - $this->lMargin - $this->rMargin;
						$height = 20;
						
						
						if($kdOrg=='FBB')
						{
							$path='images/logo2.jpg';
						}
						else if ($kdOrg=='USJ')
						{
							$path='images/logo3.jpg';	
						}
						else
						{
							$path='images/logo.jpg';
						}
						
						
						
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
						//tutup logo dan alamat
						
						//untuk sub judul
						$this->SetFont('Arial','B',10);
						$this->Cell((20/100*$width)-5,$height,"Daftar Asset",'',0,'L');
						$this->Ln();
						$this->SetFont('Arial','',8);
						$this->Cell((100/100*$width)-5,$height,"Printed By : ".$namakar[$_SESSION['standard']['userid']],'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Date : ".date('d-m-Y'),'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Time : ".date('h:i:s'),'',0,'R');
						$this->Ln();
						$this->Ln();
						//tutup sub judul
						
						//judul tengah
						$this->SetFont('Arial','B',12);
						$this->Cell($width,$height,strtoupper("Daftar Asset "."$nmAst")." ".$_SESSION['lang']['periode'].":".$kdAst,'',0,'C');
						$this->Ln();
						$this->Cell($width,$height,strtoupper("$nmOrg"),'',0,'C');
						$this->Ln();
						$this->Ln();
						//tutup judul tengah
						
						//isi atas tabel
						$this->SetFont('Arial','B',6);
						$this->SetFillColor(220,220,220);
						$this->Cell(2/100*$width,$height,"No",1,0,'C',1);
						$this->Cell(7/100*$width,$height,$_SESSION['lang']['kodeorganisasi'],1,0,'C',1);
						$this->Cell(7/100*$width,$height,$_SESSION['lang']['kodeasset'],1,0,'C',1);
						$this->Cell(7/100*$width,$height,$_SESSION['lang']['thnperolehan'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['namaasset'],1,0,'C',1);
						//$this->Cell(5/100*$width,$height,$_SESSION['lang']['status'],1,0,'C',1);
						$this->Cell(9/100*$width,$height,$_SESSION['lang']['hargaperolehan'],1,0,'C',1);
						$this->Cell(9/100*$width,$height,$_SESSION['lang']['jumlahbulanpenyusutan'],1,0,'C',1);
                                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['usia']." (".$_SESSION['lang']['bulan'].")",1,0,'C',1);
                                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['sisa']." (".$_SESSION['lang']['bulan'].")",1,0,'C',1);
                                                $this->Cell(9/100*$width,$height,$_SESSION['lang']['akumulasipenyusutan'],1,0,'C',1);
                                                $this->Cell(9/100*$width,$height,$_SESSION['lang']['nilaibuku'],1,0,'C',1);
                                                
						$this->Cell(9/100*$width,$height,$_SESSION['lang']['awalpenyusutan'],1,0,'C',1);
						$this->Cell(6/100*$width,$height,$_SESSION['lang']['bulanan'],1,1,'C',1);
//$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['usia']." (".$_SESSION['lang']['bulan'].")</td> ";	
//$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['sisa']." (".$_SESSION['lang']['bulan'].")</td> ";	
//$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['akumulasipenyusutan']."</td> ";	
//$data.="<td align=center ".$bgBelakang.">".$_SESSION['lang']['nilaibuku']."</td> ";	
					
						
						//tutup isi tabel
					}//tutup header pdfnya
					
					
					function Footer()
					{
						$this->SetY(-15);
						$this->SetFont('Arial','I',8);
						$this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
					}
				}
				//untuk tampilan setting pdf
				$pdf=new PDF('L','pt','Legal');//untuk kertas L=len p=pot
				$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
				$height = 20;
				$pdf->AddPage();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('Arial','',6);//ukuran tulisan
				//tutup tampilan setting
		
		
				//isi tabel dan tabelnya
				$no=0;
				$sql="select * from ".$dbname.".sdm_daftarasset where kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."') ".$where." order by kodeasset";
                //$sql="select * from ".$dbname.".sdm_daftarasset where  kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdOrg."') and awalpenyusutan<='".$kdAst."' ".$where." order by kodeasset";
				                //$sql="select * from ".$dbname.".sdm_daftarasset where tipeasset='".$kdAst."' and kodeorg='".$kdOrg."' order by kodeorg";
				//echo $sql;
				$qDet=mysql_query($sql) or die(mysql_error());
				while($res=mysql_fetch_assoc($qDet))
				{
					$no+=1;
                                        $tgl1=$res['awalpenyusutan']."-01";
                                        $tgl2=$kdAst."-01";
                                        
										$date1=new DateTime($tgl1);
										$date2=new DateTime($tgl2);
										$date= bulanian($date1,$date2);
										
										//$selisih=datediff($tgl,$tgl2);
										$selisih=$date+1;
										
                                        //if($selisih[months_total]>$res['jlhblnpenyusutan'])
										if($selisih>$res['jlhblnpenyusutan'])
                                        {
                                        //$selisih[months_total]=$res['jlhblnpenyusutan'];
										$selisih=$res['jlhblnpenyusutan'];
                                        }
                                        //$sisabln=$res['jlhblnpenyusutan']-$selisih[months_total];
										$sisabln=$res['jlhblnpenyusutan']-$selisih;
                                        if(substr($sisabln,0,1)=='-')
                                        {
                                        $sisabln=0;
                                        }
                                        //$akumulasiBulanan=$res['bulanan']*$selisih[months_total];
										//$akumulasiBulanan=$res['bulanan']*$selisih;
										$akumulasiBulanan=($res['tahunan']/12)*$selisih;
                                        if($akumulasiBulanan>$res['hargaperolehan'])
                                        {
                                        $akumulasiBulanan=$res['hargaperolehan'];
                                        }
                                        $nilai=$res['hargaperolehan']-$akumulasiBulanan;
					$pdf->Cell(2/100*$width,$height,$no,1,0,'C',1);
					$pdf->Cell(7/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
					$pdf->Cell(7/100*$width,$height,$res['kodeasset'],1,0,'L',1);	
					$pdf->Cell(7/100*$width,$height,$res['tahunperolehan'],1,0,'R',1);	
					$pdf->Cell(15/100*$width,$height,$res['namasset'],1,0,'L',1);
					$pdf->Cell(9/100*$width,$height,number_format($res['hargaperolehan'],2),1,0,'R',1);	
					$pdf->Cell(9/100*$width,$height,number_format($res['jlhblnpenyusutan'],2),1,0,'R',1);	
                                        //$pdf->Cell(6/100*$width,$height,$selisih[months_total],1,0,'C',1);
										$pdf->Cell(6/100*$width,$height,$selisih,1,0,'C',1);
                                        $pdf->Cell(6/100*$width,$height,$sisabln,1,0,'C',1);
                                        $pdf->Cell(9/100*$width,$height,number_format($akumulasiBulanan,2),1,0,'C',1);
                                        $pdf->Cell(9/100*$width,$height,number_format($nilai,2),1,0,'C',1);
					$pdf->Cell(9/100*$width,$height,$res['awalpenyusutan'],1,0,'L',1);	
					$pdf->Cell(6/100*$width,$height,number_format($res['bulanan'],2),1,1,'R',1);	                      
					//$pdf->Ln();	
                                        $totHargaPDF+=$res['hargaperolehan'];
                                        $totHargaAkumulPDF+=$akumulasiBulanan;
                                        $totNilaiPDF+=$nilai;
                                        $bulananPDF+=$res['bulanan'];
				}
                                $pdf->Cell(38/100*$width,$height,$_SESSION['lang']['total'],1,0,'R',1);
                                $pdf->Cell(9/100*$width,$height,number_format($totHargaPDF,2),1,0,'R',1);
                                $pdf->Cell(21/100*$width,$height,'',1,0,'R',1);
                                $pdf->Cell(9/100*$width,$height,number_format($totHargaAkumulPDF,2),1,0,'R',1);
                                $pdf->Cell(9/100*$width,$height,number_format($totNilaiPDF,2),1,0,'R',1);
                                $pdf->Cell(9/100*$width,$height,'',1,0,'R',1);

                                $pdf->Cell(6/100*$width,$height,number_format($bulananPDF,2),1,0,'R',1);
			$pdf->Output();
	##### Tutup PDF #####
	
	break;
	default;
	
	
}    
?>