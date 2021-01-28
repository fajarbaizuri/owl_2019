<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
$karyawanid=$_GET['karyawanid'];

//=============

//create Header
class PDF extends FPDF
{
	function Header()
	{
	$path='images/logo.jpg';
		//$this->Image(
	    $this->Image($path,10,2,20);
		//$this->Image($path,	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetY(22);   
	    $this->Cell(60,5,'PT. FAJAR BAIZURY & BROTHERS',0,1,'C');	 
		$this->SetFont('Arial','',15);
	    $this->Cell(190,5,strtoupper($_SESSION['lang']['inputdatakaryawan']),0,1,'C');
		$this->SetFont('Arial','',6); 
		$this->SetY(30);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,32,200,32);	   
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}
$str="select *,
      case jeniskelamin when 'L' then 'Laki-Laki'
	  else  'Wanita'
	  end as jk
	  from ".$dbname.".datakaryawan where karyawanid=".$karyawanid ." limit 1";
$res=mysql_query($str);
$defaulsrc='images/user.jpg';
while($bar=mysql_fetch_object($res))
{
	//get pendidikan
	 $pendidikan='';
	 $str1="select kelompok from ".$dbname.".sdm_5pendidikan where levelpendidikan=".$bar->levelpendidikan;
	 $res1=mysql_query($str1);
	 while($bar1=mysql_fetch_object($res1))
	   {$pendidikan=$bar1->kelompok;}
	//Tipe karyawan
	$tipekaryawan='';
	$str2="select * from ".$dbname.".sdm_5tipekaryawan where id=".$bar->tipekaryawan;	  
	$res2=mysql_query($str2);
	while($bar2=mysql_fetch_object($res2))
	{$tipekaryawan=$bar2->tipe;}

	//jabatan
	$jabatan='';
	$str3="select * from ".$dbname.".sdm_5jabatan where kodejabatan=".$bar->kodejabatan." and namajabatan not like '%available' order by kodejabatan";
	$res3=mysql_query($str3);
	while($bar3=mysql_fetch_object($res3))
	{$jabatan=$bar->namajabatan;}
	
	$photo=($bar->photo==''?$defaulsrc:$bar->photo);
	$karyawanid=$bar->karyawanid;
    $nik=$bar->nik;
	$nama=$bar->namakaryawan;
	$ttlahir=$bar->tempatlahir;
	$tgllahir=tanggalnormal($bar->tanggallahir);
	$wn=$bar->warganegara;
	$jk=$bar->jk;
	$stpkw=$bar->statusperkawinan;
	$tglmenikah=tanggalnormal($bar->tanggalmenikah);
	$agama=$bar->agama;
	$goldar=$bar->golongandarah;
	$pendidikan=$pendidikan;
	$telprumah=$bar->noteleponrumah;
	$hp=$bar->nohp;
	$passpor=$bar->nopaspor;
	$ktp=$bar->noktp;
	$tdarurat=$bar->notelepondarurat;
	$tmasuk=tanggalnormal($bar->tanggalmasuk);
	$tkeluar=tanggalnormal($bar->tanggalkeluar);
	$tipekar=$tipekaryawan;	
	$alamataktif=$bar->alamataktif;
	$kota=$bar->kota;
	$provinsi=$bar->provinsi;
	$kodepos=$bar->kodepos;	
	$rekbank=$bar->norekeningbank;
	$bank=$bar->namabank;
	$sisgaji=$bar->sistemgaji;
	$jlhanak=$bar->jumlahanak;
	$tanggungan=$bar->jumlahtanggungan;
	$stpjk=$bar->statuspajak;
	$npwp=$bar->npwp;
	$lokpenerimaan=$bar->lokasipenerimaan;
	$kodeorg=$bar->kodeorganisasi;
	$bagian=$bar->bagian;
	$jabatan=$jabatan;
	$golongan=$bar->kodegolongan;
	$lokasitugas=$bar->lokasitugas;
	$email=$bar->email;
        $subbagian=$bar->subbagian;
        $jms=$bar->jms;
//=============pdf

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);
	$pdf->setY(32);		
	$pdf->Cell(25,5,'1. '.strtoupper($_SESSION['lang']['datapribadi']),0,1,'L');
	$pdf->SetFont('Arial','',6.5);
	$pdf->setY(40);		
	$pdf->SetX(20);
	$pdf->Image($photo,15,40,35);
	$pdf->SetX(60);
    $pdf->Cell(25,5,$_SESSION['lang']['datapribadi'],0,0,'L');
		$pdf->Cell(40,5,': '.$karyawanid,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['nik'],0,0,'L');
		$pdf->Cell(40,5,': '.$nik,0,1,'L');	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['nama'],0,0,'L');
		$pdf->Cell(40,5,': '.$nama,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['tempatlahir'],0,0,'L');
		$pdf->Cell(40,5,': '.$ttlahir,0,1,'L');
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['tanggallahir'],0,0,'L');
		$pdf->Cell(40,5,': '.$tgllahir,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['warganegara'],0,0,'L');
		$pdf->Cell(40,5,': '.$wn,0,1,'L');				
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['jeniskelamin'],0,0,'L');
		$pdf->Cell(40,5,': '.$jk,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['status'],0,0,'L');
		$pdf->Cell(40,5,': '.$stpkw,0,1,'L');	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['tanggalmenikah'],0,0,'L');
		$pdf->Cell(40,5,': '.$tglmenikah,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['agama'],0,0,'L');
		$pdf->Cell(40,5,': '.$agama,0,1,'L');
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['golongandarah'],0,0,'L');
		$pdf->Cell(40,5,': '.$goldar,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['pendidikan'],0,0,'L');
		$pdf->Cell(40,5,': '.$pendidikan,0,1,'L');		
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['telp'],0,0,'L');
		$pdf->Cell(40,5,': '.$telprumah,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['nohp'],0,0,'L');
		$pdf->Cell(40,5,': '.$hp,0,1,'L');		
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['noktp'],0,0,'L');
		$pdf->Cell(40,5,': '.$ktp,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['passport'],0,0,'L');
		$pdf->Cell(40,5,': '.$passpor,0,1,'L');	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['notelepondarurat'],0,0,'L');
		$pdf->Cell(40,5,': '.$tdarurat,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['tanggalmasuk'],0,0,'L');
		$pdf->Cell(40,5,': '.$tmasuk,0,1,'L');
	$pdf->SetX(60);		
    $pdf->Cell(25,5,$_SESSION['lang']['tanggalkeluar'],0,0,'L');
		$pdf->Cell(40,5,': '.$tkeluar,0,0,'L');	
    $pdf->Cell(25,5,$_SESSION['lang']['tipekaryawan'],0,0,'L');
		$pdf->Cell(40,5,': '.$tipekar,0,1,'L');	
 
    $pdf->Cell(32,5,$_SESSION['lang']['alamat'],0,0,'L');
		$pdf->MultiCell(153,5,': '.$alamataktif.", ".$kota.", ".$provinsi.", ".$kodepos,0,'L');			

	
    $pdf->Cell(32,5,$_SESSION['lang']['norekeningbank'],0,0,'L');
		$pdf->Cell(60,5,': '.$rekbank,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['namabank'],0,0,'L');
		$pdf->Cell(70,5,': '.$bank,0,1,'L');
					
    $pdf->Cell(32,5,$_SESSION['lang']['sistemgaji'],0,0,'L');
		$pdf->Cell(60,5,': '.$sisgaji,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['jumlahanak'],0,0,'L');
		$pdf->Cell(70,5,': '.$jlhanak,0,1,'L');

    $pdf->Cell(32,5,$_SESSION['lang']['jumlahtanggungan'],0,0,'L');
		$pdf->Cell(60,5,': '.$tanggungan,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['statuspajak'],0,0,'L');
		$pdf->Cell(70,5,': '.$stpjk,0,1,'L');

    $pdf->Cell(32,5,$_SESSION['lang']['npwp'],0,0,'L');
		$pdf->Cell(60,5,': '.$npwp,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['lokasipenerimaan'],0,0,'L');
		$pdf->Cell(70,5,': '.$lokpenerimaan,0,1,'L');
		
    $pdf->Cell(32,5,$_SESSION['lang']['orgcode'],0,0,'L');
		$pdf->Cell(60,5,': '.$kodeorg,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['bagian'],0,0,'L');
		$pdf->Cell(70,5,': '.$bagian,0,1,'L');
		
    $pdf->Cell(32,5,$_SESSION['lang']['functionname'],0,0,'L');
		$pdf->Cell(60,5,': '.$jabatan,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['levelname'],0,0,'L');
		$pdf->Cell(70,5,': '.$golongan,0,1,'L');
		
    $pdf->Cell(32,5,$_SESSION['lang']['lokasitugas'],0,0,'L');
		$pdf->Cell(60,5,': '.$lokasitugas,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['email'],0,0,'L');
		$pdf->Cell(70,5,': '.$email,0,1,'L');
    $pdf->Cell(32,5,$_SESSION['lang']['subbagian'],0,0,'L');
		$pdf->Cell(60,5,': '.$subbagian,0,0,'L');	
    $pdf->Cell(32,5,$_SESSION['lang']['jms'],0,0,'L');
		$pdf->Cell(70,5,': '.$jms,0,1,'L');
    $pdf->Ln();	
}
//=======================Riwayat Pekerjaan
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'2. '.strtoupper($_SESSION['lang']['pengalamankerja']),0,1,'L');
	$pdf->SetFont('Arial','',5);												
    
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(4,4,'No',1,0,'L',1);
    $pdf->Cell(35,4,$_SESSION['lang']['pt'],1,0,'C',1);
    $pdf->Cell(32,4,$_SESSION['lang']['bidangusaha'],1,0,'C',1);	
    $pdf->Cell(15,4,$_SESSION['lang']['tanggalmasuk'],1,0,'C',1);		
    $pdf->Cell(15,4,$_SESSION['lang']['tanggalkeluar'],1,0,'C',1);
    $pdf->Cell(20,4,$_SESSION['lang']['jabatanterakhir'],1,0,'C',1);	
    $pdf->Cell(15,4,$_SESSION['lang']['bagian'],1,0,'C',1);			
    $pdf->Cell(12,4,$_SESSION['lang']['masakerja'],1,0,'C',1);
    $pdf->Cell(47,4,$_SESSION['lang']['alamat'],1,1,'C',1);
	//$pdf->Cell(
//loop isinya
	 $str="select * from ".$dbname.".sdm_karyawancv where karyawanid=".$karyawanid." order by bulanmasuk desc";
	 $res=mysql_query($str);
	 $no=0;
	 $mskerja=0;
	 while($bar=mysql_fetch_object($res))
	 {
		 $no+=1;	
		  $msk=mktime(0,0,0,substr(str_replace("-","",$bar->bulanmasuk),4,2),1,substr($bar->bulanmasuk,0,4));	
		  $klr=mktime(0,0,0,substr(str_replace("-","",$bar->bulankeluar),4,2),1,substr($bar->bulankeluar,0,4));	
		  $dateDiff = $klr - $msk;
	      $mskerja = floor($dateDiff/(60*60*24))/365; 

	    $pdf->Cell(4,4,$no,1,0,'L',0);
	    $pdf->Cell(35,4,$bar->namaperusahaan,1,0,'L',0);
	    $pdf->Cell(32,4,$bar->bidangusaha,1,0,'C',0);	
	    $pdf->Cell(15,4,$bar->bulanmasuk,1,0,'C',0);		
	    $pdf->Cell(15,4,$bar->bulankeluar,1,0,'C',0);
	    $pdf->Cell(20,4,$bar->jabatan,1,0,'C',0);	
	    $pdf->Cell(15,4,$bar->bagian,1,0,'C',0);			
	    $pdf->Cell(12,4,number_format($mskerja,2,',','.')." Yrs",1,0,'C',0);
	    $pdf->Cell(47,4,$bar->alamatperusahaan,1,1,'C',0);
	 }	

//=======================Riwayat Pendidikan
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'3. '.strtoupper($_SESSION['lang']['pendidikan']),0,1,'L');
	$pdf->SetFont('Arial','',5);												
    
    $pdf->Cell(4,4,'No',1,0,'L',1);
    $pdf->Cell(12,4,$_SESSION['lang']['edulevel'],1,0,'C',1);
    $pdf->Cell(33,4,$_SESSION['lang']['namasekolah'],1,0,'C',1);	
    $pdf->Cell(25,4,$_SESSION['lang']['kota'],1,0,'C',1);		
    $pdf->Cell(30,4,$_SESSION['lang']['jurusan'],1,0,'C',1);
    $pdf->Cell(12,4,$_SESSION['lang']['tahunlulus'],1,0,'C',1);	
    $pdf->Cell(25,4,$_SESSION['lang']['gelar'],1,0,'C',1);			
    $pdf->Cell(10,4,$_SESSION['lang']['nilai'],1,0,'C',1);
    $pdf->Cell(44,4,$_SESSION['lang']['keterangan'],1,1,'C',1);
//loop isinya
	 $str="select a.*,b.kelompok from ".$dbname.".sdm_karyawanpendidikan a,".$dbname.".sdm_5pendidikan b
	 		where a.karyawanid=".$karyawanid." 
	 		and a.levelpendidikan=b.levelpendidikan
			order by a.levelpendidikan desc";
	 $res=mysql_query($str);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
		 $no+=1;	
	     $pdf->Cell(4,4,$no,1,0,'L',0);
	     $pdf->Cell(12,4,$bar->kelompok,1,0,'C',0);
	     $pdf->Cell(33,4,$bar->namasekolah,1,0,'C',0);	
	     $pdf->Cell(25,4,$bar->kota,1,0,'C',0);		
	     $pdf->Cell(30,4,$bar->spesialisasi,1,0,'C',0);
	     $pdf->Cell(12,4,$bar->tahunlulus,1,0,'C',0);	
	     $pdf->Cell(25,4,$bar->gelar,1,0,'C',0);			
	     $pdf->Cell(10,4,number_format($bar->nilai,2,',','.'),1,0,'C',0);
	     $pdf->Cell(44,4,$bar->keterangan,1,1,'C',0);
	 }	
//=======================Riwayat Kursus
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'4. '.strtoupper($_SESSION['lang']['kursus']),0,1,'L');
	$pdf->SetFont('Arial','',5);												
    
    $pdf->Cell(4,4,'No',1,0,'L',1);
    $pdf->Cell(30,4,$_SESSION['lang']['jeniskursus'],1,0,'C',1);
    $pdf->Cell(55,4,$_SESSION['lang']['legend'],1,0,'C',1);	
    $pdf->Cell(55,4,$_SESSION['lang']['penyelenggara'],1,0,'C',1);		
    $pdf->Cell(15,4,$_SESSION['lang']['startdate'],1,0,'C',1);
    $pdf->Cell(16,4,$_SESSION['lang']['tanggalsampai'],1,0,'C',1);	
    $pdf->Cell(20,4,$_SESSION['lang']['sertifikat'],1,1,'C',1);			
//loop isinya
	 $str="select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat 
	       from ".$dbname.".sdm_karyawantraining
	 		where karyawanid=".$karyawanid." 
			order by bulanmulai desc";	
	 $res=mysql_query($str);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
	 $no+=1;	
	     $pdf->Cell(4,4,$no,1,0,'L',0);
	     $pdf->Cell(30,4,$bar->jenistraining,1,0,'C',0);
	     $pdf->Cell(55,4,$bar->judultraining,1,0,'C',0);	
	     $pdf->Cell(55,4,$bar->penyelenggara,1,0,'C',0);		
	     $pdf->Cell(15,4,$bar->bulanmulai,1,0,'C',0);
	     $pdf->Cell(16,4,$bar->bulanselesai,1,0,'C',0);	
	     $pdf->Cell(20,4,$bar->bersertifikat,1,1,'C',0);			
	 }	

//=======================Keluarga
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'5. '.strtoupper($_SESSION['lang']['keluarga']),0,1,'L');
	$pdf->SetFont('Arial','',5);												
    
    $pdf->Cell(4,4,'No',1,0,'L',1);
    $pdf->Cell(40,4,$_SESSION['lang']['keluarga'],1,0,'C',1);
    $pdf->Cell(15,4,$_SESSION['lang']['jeniskelamin'],1,0,'C',1);	
    $pdf->Cell(26,4,$_SESSION['lang']['hubungan'],1,0,'C',1);		
    $pdf->Cell(15,4,$_SESSION['lang']['status'],1,0,'C',1);	
    $pdf->Cell(20,4,$_SESSION['lang']['pendidikan'],1,0,'C',1);
	$pdf->Cell(35,4,$_SESSION['lang']['pekerjaan'],1,0,'C',1);
	$pdf->Cell(20,4,$_SESSION['lang']['telp'],1,0,'C',1);	
	$pdf->Cell(20,4,$_SESSION['lang']['tanggungan'],1,1,'C',1);	
//loop isinya
		 $str="select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, 
		       b.kelompok
			   from ".$dbname.".sdm_karyawankeluarga a,".$dbname.".sdm_5pendidikan b
		 		where a.karyawanid=".$karyawanid." 
				and a.levelpendidikan=b.levelpendidikan
				order by hubungankeluarga";	
		 $res=mysql_query($str);
		 $no=0;
		 while($bar=mysql_fetch_object($res))
		 {
		 $no+=1;	
	     $pdf->Cell(4,4,$no,1,0,'L',0);
	     $pdf->Cell(40,4,$bar->nama,1,0,'C',0);
	     $pdf->Cell(15,4,$bar->jeniskelamin,1,0,'C',0);	
	     $pdf->Cell(26,4,$bar->hubungankeluarga,1,0,'C',0);		
	     $pdf->Cell(15,4,$bar->status,1,0,'C',0);
	     $pdf->Cell(20,4,$bar->kelompok,1,0,'C',0);	
	     $pdf->Cell(35,4,$bar->pekerjaan,1,0,'C',0);
		 $pdf->Cell(20,4,$bar->telp,1,0,'C',0);	
		 $pdf->Cell(20,4,$bar->tanggungan1,1,1,'C',0);			
	 }					
//=======================Alamat
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'6. '.strtoupper($_SESSION['lang']['alamat']),0,1,'L');
	$pdf->SetFont('Arial','',5);												
    
    $pdf->Cell(4,4,'No',1,0,'L',1);
    $pdf->Cell(86,4,$_SESSION['lang']['alamat'],1,0,'C',1);
    $pdf->Cell(25,4,$_SESSION['lang']['kota'],1,0,'C',1);	
    $pdf->Cell(25,4,$_SESSION['lang']['province'],1,0,'C',1);		
    $pdf->Cell(15,4,$_SESSION['lang']['kodepos'],1,0,'C',1);	
    $pdf->Cell(25,4,$_SESSION['lang']['emplasmen'],1,0,'C',1);	
    $pdf->Cell(15,4,$_SESSION['lang']['aktif'],1,1,'C',1);		
//loop isinya
		 $str="select *,case aktif when 1 then 'Yes' when 0 then 'No' end as status from ".$dbname.".sdm_karyawanalamat where karyawanid=".$karyawanid." order by nomor desc";
		 $res=mysql_query($str);
		 $no=0;
		 while($bar=mysql_fetch_object($res))
		 {
		 $no+=1;	
	     $pdf->Cell(4,4,$no,1,0,'L',0);
	     $pdf->Cell(86,4,$bar->alamat,1,0,'C',0);
	     $pdf->Cell(25,4,$bar->kota,1,0,'C',0);	
	     $pdf->Cell(25,4,$bar->provinsi,1,0,'C',0);		
	     $pdf->Cell(15,4,$bar->kodepos,1,0,'C',0);
	     $pdf->Cell(25,4,$bar->emplasemen,1,0,'C',0);	
	     $pdf->Cell(15,4,$bar->status,1,1,'C',0);			
	 }		
	$pdf->Output();	
?>
