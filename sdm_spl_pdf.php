<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

	$tmp=explode(',',$_GET['column']);
	$notran=$tmp[0];
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
			   
				$sIsi="select * from ".$dbname.".sdm_splht where notransaksi='".$notran."'";
				$qIsi=mysql_query($sIsi) or die(mysql_error());
				$rIsi=mysql_fetch_assoc($qIsi);
	
				$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rIsi['kodeorg']."'";
				$qOrg=mysql_query($sOrg) or die(mysql_error());
				$rOrg=mysql_fetch_assoc($qOrg);
						
						$bagian=$rIsi['kode'];
					
						$ind2=" SELECT * 
						FROM ".$dbname.".sdm_5departemen
						WHERE kode='".$bagian."' ";
						//exit("Error:$ind2");
						$indr2=mysql_query($ind2) or die(mysql_error());
						$indra2=mysql_fetch_assoc($indr2);
						
						$namabagian=$indra2['nama'];
				
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
		$this->Line(10,30,200,30);	
		
			$this->Cell(35,5,'No. Dokumen','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,$rIsi['notransaksi'],'',1,'L');
		
			/*$this->Cell(35,5,$_SESSION['lang']['kodeorg'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,$rIsi['kodeorg'],'',1,'L');*/
					
			$this->Cell(35,5,$_SESSION['lang']['namaorganisasi'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,$rOrg['namaorganisasi'],0,1,'L');
			
			$this->Cell(35,5,Divisi,'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,$namabagian,0,1,'L');
			
			$this->Cell(35,5,$_SESSION['lang']['tanggal'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,tanggalnormal($rIsi['tanggal']),'',1,'L');	
			
			$this->Cell(35,5,Hari,'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,hari($rIsi['tanggal']),'',1,'L');	
			
			$this->Cell(35,5,Jam,'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(75,5,substr($rIsi['lemburdari'],0,5)." s/d ".substr($rIsi['lembursampai'],0,5)." ".WIB,'',1,'L');	
//$this->Cell(80,5,$_SESSION['standard']['username'],'',1,'L');

		
			//	        $this->Ln();	
     $this->Ln();
	 
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
			
	$pdf->Ln();
	
	$pdf->SetFont('Arial','B',14);
	$pdf->SetY(75);
	$pdf->Cell(190,5,'SURAT PERINTAH KERJA LEMBUR',0,1,'C');	
	$pdf->SetFont('Arial','',6);
	$pdf->Cell(190,5,'BERSAMA INI KAMI INFORMASIKAN BAHWA PEGAWAI YANG TERSEBUT DIBAWAH INI, DIWAJIBKAN UNTUK KERJA LEMBUR',0,1,'C');
	$pdf->Ln();	
	$pdf->SetFont('Arial','B',8);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
	$pdf->Cell(45,5,'N A M A',1,0,'C',1);	
	$pdf->Cell(30,5,'N I K',1,0,'C',1);
	$pdf->Cell(40,5,JABATAN,1,0,'C',1);
	$pdf->Cell(70,5,'TUGAS YANG DI LAKUKAN',1,1,'C',1);		
	
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
		$str="select * from ".$dbname.".sdm_spldt where notransaksi='".$notran."'";// echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($res=mysql_fetch_assoc($re))
		
		{
			$sKry="select * from ".$dbname.".datakaryawan where karyawanid='".$res['karyawanid']."'";
			$qKry=mysql_query($sKry) or die(mysql_error());
			$rKry=mysql_fetch_assoc($qKry);
			
				$kdjabatan=$rKry['kodejabatan'];
				//exit("Error:$kdjabatan");
				$ind3=" SELECT * 
				FROM ".$dbname.".sdm_5jabatan
				WHERE kodejabatan='".$kdjabatan."' ";
				//exit("Error:$ind3");
				$indr3=mysql_query($ind3) or die(mysql_error());
				$indra3=mysql_fetch_assoc($indr3);
				
				$nmjabatan=$indra3['namajabatan'];
			
					
			$no+=1;
			if($no>=25)
			{
				$pdf->AddPage();
				$pdf->Cell(8,5,$no,1,0,'C',1);
				$pdf->Cell(45,5,$rKry['namakaryawan'],1,0,'L',1);
				$pdf->Cell(30,5,$rKry['nik'],1,0,'L',1);
				$pdf->Cell(40,5,$nmjabatan,1,0,'L',1);
				$pdf->Cell(70,5,$res['keterangan'],1,1,'L',1);
			}
			else
			{
				$pdf->Cell(8,5,$no,1,0,'C',1);
				$pdf->Cell(45,5,$rKry['namakaryawan'],1,0,'L',1);
				$pdf->Cell(30,5,$rKry['nik'],1,0,'L',1);
				$pdf->Cell(40,5,$nmjabatan,1,0,'L',1);
				$pdf->Cell(70,5,$res['keterangan'],1,1,'L',1);
			}
		}
		
		
		
	$sIsi="select * from ".$dbname.".sdm_splht where notransaksi='".$notran."'";
	$qIsi=mysql_query($sIsi) or die(mysql_error());
	$rIsi=mysql_fetch_assoc($qIsi);
	
	#####aproval
	$apv=" SELECT * 
	FROM ".$dbname.".datakaryawan
	JOIN ".$dbname.".sdm_splht
	WHERE karyawanid=".$rIsi['approvedby'] ."";
	//exit("Error:$ind");
	$apv2=mysql_query($apv) or die(mysql_error());
	$apv3=mysql_fetch_assoc($apv2);
		$nmapv=$apv3['namakaryawan'];
		$jabapv=$apv3['kodejabatan'];
			//exit("Error:$kdjabatan");
			$a1=" SELECT * 
			FROM ".$dbname.".sdm_5jabatan
			WHERE kodejabatan='".$jabapv."' ";
			//exit("Error:$ind3");
			$a2=mysql_query($a1) or die(mysql_error());
			$a3=mysql_fetch_assoc($a2);
				$nmjabapv=$a3['namajabatan'];
	
	#####Pengecek
	$cek=" SELECT * 
	FROM ".$dbname.".datakaryawan
	JOIN ".$dbname.".sdm_splht
	WHERE karyawanid=".$rIsi['checkedby'] ."";
	//exit("Error:$ind");
	$cek2=mysql_query($cek) or die(mysql_error());
	$cek3=mysql_fetch_assoc($cek2);
		$nmcek=$cek3['namakaryawan'];
		$jabcek=$cek3['kodejabatan'];
			$b1=" SELECT * 
			FROM ".$dbname.".sdm_5jabatan
			WHERE kodejabatan='".$jabcek."' ";
			$b2=mysql_query($b1) or die(mysql_error());
			$b3=mysql_fetch_assoc($b2);
				$nmjabcek=$b3['namajabatan'];
	
	#####Pembuat	
	$buat=" SELECT * 
	FROM ".$dbname.".datakaryawan
	JOIN ".$dbname.".sdm_splht
	WHERE karyawanid=".$rIsi['updateby'] ."";
	//exit("Error:$ind");
	$buat2=mysql_query($buat) or die(mysql_error());
	$buat3=mysql_fetch_assoc($buat2);
		$nmbuat=$buat3['namakaryawan'];	
		$jabbuat=$buat3['kodejabatan'];	
			$c1=" SELECT * 
			FROM ".$dbname.".sdm_5jabatan
			WHERE kodejabatan='".$jabbuat."' ";
			$c2=mysql_query($c1) or die(mysql_error());
			$c3=mysql_fetch_assoc($c2);
				$nmjabbuat=$c3['namajabatan'];
		
		
	
	$pdf->Ln(10);
	
	$pdf->SetX(10);
	$pdf->Cell(45,5,'DISETUJUI',0,0,'C',0);
	//$pdf->SetX(80);
	//$pdf->Cell(45,5,'DIPERIKSA',0,0,'C',0);
	$pdf->SetX(150);
	$pdf->Cell(45,5,'DIBUAT OLEH',0,0,'C',1);
	
	$pdf->Ln(30);
	
	$pdf->SetFont('Arial','U');
	$pdf->SetX(10);
	$pdf->Cell(45,5,$nmapv,0,0,'C',0);
	//$pdf->SetX(80);
	//$pdf->Cell(45,5,$nmcek,0,0,'C',0);
	$pdf->SetX(150);
	$pdf->Cell(45,5,$nmbuat,0,0,'C',1);
	
	$pdf->Ln();
	$pdf->SetFont('Arial','');
	$pdf->SetX(10);
	$pdf->Cell(45,5,$nmjabapv,0,0,'C',0);
	//$pdf->SetX(80);
	//$pdf->Cell(45,5,$nmjabcek,0,0,'C',0);
	$pdf->SetX(150);
	$pdf->Cell(45,5,$nmjabbuat,0,0,'C',1);
		
	
	$pdf->Output();
?>
