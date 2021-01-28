<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');


include_once('lib/zLib.php');

//exit("masuk");
//include_once('lib/formTable.php');
//include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;






//exit("Error:$notran");
$notran=$param['notransaksi'];
$kdorg=$param['kodeorg'];
//exit("Error:$notran");
//diambil 2 untuk persetujuan
if($notran=='')
{
	$tmp=explode(',',$_GET['column']);
	$notran=$tmp[0];
}
//echo $notran;

$namaorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$namakegiatan=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$optnamasup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');


//exit("Error:$param");
	//exit("Error:$notran");
	
//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
    global $userid;
	global $notran;
	global $kdorg;
	//global $notransaksi;
				
				$sInduk="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
				$qInduk=mysql_query($sInduk) or die(mysql_error());
				$rInduk=mysql_fetch_assoc($qInduk);
				
				$str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'"; 
				$res1=mysql_query($str1);
				while($bar1=mysql_fetch_object($res1))
			    {
					$nama=$bar1->namaorganisasi;
					$alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
					$telp=$bar1->telepon;				 
			    }    
			   
				$sIsi="select * from ".$dbname.".log_spkht a join ".$dbname.".log_5supplier b on a.koderekanan=b.supplierid  where a.notransaksi='".$notran."' and a.kodeorg='".$kdorg."' "; //echo $str;exit();
				$qIsi=mysql_query($sIsi) or die(mysql_error());
				$rIsi=mysql_fetch_assoc($qIsi);
						$posatas=$rIsi['posting'];
						
						
						
						
		
			//echo $tanggalsetuju;
		
						
					
					
				
		$path='images/logo.jpg';
	    $this->Image($path,10,3,25);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(40);   
	    $this->Cell(60,5,$nama,0,1,'L');	 
		$this->SetX(40); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(40); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','',8);
		if($posatas==0)
		{
			$this->Line(10,30,285,30);	
		}
		else
		{
			$this->Line(10,30,200,30);	
		}
		
		
		if($posatas==0)
		{
			$this->Cell(35,5,'No. Transaksi','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,$rIsi['notransaksi'],'',1,'L');
		
			/*$this->Cell(35,5,$_SESSION['lang']['kodeorg'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,$rIsi['kodeorg'],'',1,'L');*/
					
			$this->Cell(35,5,$_SESSION['lang']['namaorganisasi'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,$kdorg,0,1,'L');
			
			$this->Cell(35,5,'Kode Rekaan','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			//$this->Cell(35,5,$optnamasup[$rIsi['koderekanan']],0,1,'L');//indra
			$this->Cell(35,5,$rIsi['namasupplier'],0,1,'L');
			
			$this->Cell(35,5,'Tanggal','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			//$this->Cell(35,5,$optnamasup[$rIsi['koderekanan']],0,1,'L');//indra
			$this->Cell(35,5,tanggalnormal($rIsi['tanggal']),0,1,'L');
			
			
		}
		else
		{
		}
	 
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}
	
	$sql1="select tanggal from ".$dbname.".log_persetujuanspk where notransaksi='".$notran."' and kodeorg='".$kdorg."' order by level desc limit 1  ";
		$qry1=mysql_query($sql1);
		$data1=mysql_fetch_assoc($qry1);
			$tanggalsetuju=tanggalnormal($data1['tanggal']);
	
	
	$i="select * from ".$dbname.".log_spkht a join ".$dbname.".log_5supplier b on a.koderekanan=b.supplierid  where a.notransaksi='".$notran."' and a.kodeorg='".$kdorg."' "; //echo $str;exit();
	$n=mysql_query($i) or die(mysql_error());
	$d=mysql_fetch_assoc($n);
		$pos=$d['posting'];
	
	if($pos==0)
	{	
	$pdf=new PDF('L','mm','A4');
	}
	else
	{
		$pdf=new PDF('P','mm','A4');
	}
	$pdf->AddPage();
			
	$pdf->Ln();
	
	$pdf->SetFont('Arial','B',14);
	
	
	
	
		
		
	if($pos==0)
	{
		$pdf->SetY(50);
		$pdf->Cell(273,5,'USULAN SURAT PERINTAH KERJA',0,1,'C');	
		$pdf->Ln();	
		$pdf->Ln();	
		$pdf->SetFont('Arial','B',8);	
		$pdf->SetFillColor(220,220,220);
			$pdf->Cell(8,5,'No',1,0,'C',1);
			$pdf->Cell(40,5,'Sub Unit',1,0,'C',1);	
			$pdf->Cell(50,5,'Kode Kegiatan',1,0,'C',1);
			$pdf->Cell(20,5,'Hari Kerja',1,0,'C',1);
			$pdf->Cell(30,5,'Rencana SPK',1,0,'C',1);
			$pdf->Cell(45,5,'Volume yang telah dikerjakan',1,0,'C',1);
			$pdf->Cell(20,5,'Satuan',1,0,'C',1);
			$pdf->Cell(30,5,'Harga Satuan',1,0,'C',1);
			$pdf->Cell(30,5,'Jumlah',1,1,'C',1);
		
		//$pdf->Cell(25,5,'Total',1,1,'C',1);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		
			$str1="select * from ".$dbname.".log_spkdt where notransaksi='".$notran."'";// echo $str;exit();
			//exit("Error:$str1"); //$str1;
			$re1=mysql_query($str1) or die (mysql_error());
			$no=0;
			while($res1=mysql_fetch_assoc($re1))	
			{			
				$no+=1;
					
					$pdf->Cell(8,5,$no,1,0,'C',1);
					$pdf->Cell(40,5,$namaorg[$res1['kodeblok']],1,0,'L',1);
					$pdf->Cell(50,5,$namakegiatan[$res1['kodekegiatan']],1,0,'L',1);
					$pdf->Cell(20,5,$res1['hk'],1,0,'R',1);
					$pdf->Cell(30,5,$res1['hasilkerjajumlah'],1,0,'R',1);
					$pdf->Cell(45,5,$res1['hasilkerjalalu'],1,0,'R',1);
					$pdf->Cell(20,5,$res1['satuan'],1,0,'L',1);
					$pdf->Cell(30,5,number_format($res1['hargasatuan'],2),1,0,'R',1);
					$pdf->Cell(30,5,number_format($res1['jumlahrp'],2),1,1,'R',1);
					//$pdf->Cell(40,5,$res[''],1,1,'L',1);
					
				$total+=$res1['jumlahrp'];
			}
			$pdf->SetFillColor(220,220,220);
			$pdf->Cell(243,5,'TOTAL',1,0,'C',1);
			$pdf->Cell(30,5,number_format($total,2),1,0,'R',1);
	}
	
	
	else //ini jika posting 1
	
	{
			$pdf->Cell(200,5,'SURAT PERINTAH KERJA',0,1,'C');	
			$pdf->Cell(200,5,'No. : '.$notran,0,1,'C');
			$pdf->Cell(200,5,'Tanggal : '.$tanggalsetuju,0,1,'C');
			$pdf->Ln();	
			$pdf->Ln();	
			$pdf->SetFont('Arial','',12);
			$pdf->MultiCell(165,5,'Direksi PT. Fajar Baizury Dengan ini memerintahkan kepada sdr : '.$d['namasupplier'].', untuk melaksanakan pekerjaan :');	
			$pdf->Ln();	
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(220,220,220);
				$pdf->Cell(8,5,'No',1,0,'C',1);
				$pdf->Cell(50,5,'Pekerjaan',1,0,'C',1);
				$pdf->Cell(20,5,'Satuan',1,0,'C',1);
				$pdf->Cell(20,5,'Volume',1,0,'C',1);
				$pdf->Cell(30,5,'Harga Satuan',1,0,'C',1);
				$pdf->Cell(30,5,'Total',1,1,'C',1);
			
			//$pdf->Cell(25,5,'Total',1,1,'C',1);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','',8);
			
				$str1="select * from ".$dbname.".log_spkdt where notransaksi='".$notran."'";// echo $str;exit();
				$re1=mysql_query($str1) or die (mysql_error());
				$no=0;
				while($res1=mysql_fetch_assoc($re1))	
				{			
					$no+=1;
						
						$pdf->Cell(8,5,$no,1,0,'C',1);
						$pdf->Cell(50,5,$namakegiatan[$res1['kodekegiatan']],1,0,'L',1);
						$pdf->Cell(20,5,$res1['satuan'],1,0,'L',1);
						$pdf->Cell(20,5,$res1['hasilkerjajumlah'],1,0,'L',1);	
						$pdf->Cell(30,5,number_format($res1['hargasatuan'],2),1,0,'R',1);
						$pdf->Cell(30,5,number_format($res1['jumlahrp'],2),1,1,'R',1);
						//$pdf->Cell(40,5,$res[''],1,1,'L',1);
						
					$total+=$res1['jumlahrp'];
				}
				$pdf->SetFillColor(220,220,220);
				$pdf->Cell(128,5,'TOTAL',1,0,'C',1);
				$pdf->Cell(30,5,number_format($total,2),1,0,'R',1);
			
			$pdf->Ln();	
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial','',12);
			$pdf->MultiCell(165,5,'Berkenaan dengan spek teknis dan ketentuan/persyaratan lainnya, akan di atur  didalam perjanjian kerja yang akan dibuat kemudian');	
			//$pdf->Cell(273,5,'kerja yang akan dibuat kemudian.',0,1,'L');		
			
			$pdf->Ln();	
			$pdf->Ln();
			$pdf->Ln();
			
		$sql="select a.penyetuju,b.namakaryawan from ".$dbname.".log_persetujuanspk a join ".$dbname.".datakaryawan b on a.penyetuju=b.karyawanid where a.notransaksi='".$notran."' and kodeorg='".$kdorg."' order by level desc limit 1  ";
		$qry=mysql_query($sql);
		$data=mysql_fetch_assoc($qry);
		
		$penyetuju=$data['namakaryawan'];
			
			
			$pdf->SetX(25);
			$pdf->Cell(50,5,'Kontraktor',0,0,'C');
			$pdf->SetX(100);
			$pdf->Cell(50,5,'Direksi',0,1,'C');
			$pdf->Ln(30);
			$pdf->SetX(25);
			$pdf->Cell(50,5,$d['namasupplier'],0,0,'C');
			$pdf->SetX(100);
			$pdf->Cell(50,5,$penyetuju,0,0,'C');
			
	}
	


	

		
	
	$pdf->Output();
?>
