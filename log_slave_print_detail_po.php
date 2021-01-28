<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zPdfMaster.php');

$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

$optnamapembuat=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
/*$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
$rOrg=mysql_fetch_assoc($qOrg);
$nmOrg=$rOrg['namaorganisasi'];*/
//=============

//create Header
class PDF extends FPDF
{
	function Header() {
		//global $nmOrg;
		global $conn;
		global $dbname;
		global $userid;
		global $posted;
		global $tanggal;
		global $norek_sup;
		global $npwp_sup;
		global $nm_kary;
		global $nm_pt;
		global $nmSupplier;
		global $almtSupplier;
		
		global $tlpSupplier;
		global $faxSupplier;
		global $nopo;
		global $tglPo;
		global $kdBank;
		global $an;
		global $kontaksup;
		global $faxpt;
		global $pmb;
		global $tpo;
		global $ketbrg;
		global $kota;
		global $wilayah;
		
		$str="select kodeorg,kodesupplier,purchaser,nopo,tanggal,pembuat,tujuanpo,keteranganbarang from ".$dbname.".log_poht  where nopo='".$_GET['column']."'";
		//echo $str;exit();
		$res=mysql_query($str);
		$bar=mysql_fetch_object($res);
		
		if($bar->kodeorg=='') {
			$bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
		}
		$str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
		$res1=mysql_query($str1);
		while($bar1=mysql_fetch_object($res1))
		{
			$namapt=$bar1->namaorganisasi;
			//$alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
			$alamatpt=$bar1->alamat;
			$wilayah=$bar1->wilayahkota;
			$telp=$bar1->telepon;	
			$faxpt=$bar1->fax;			 
		} 
		$sNpwp="select npwp,alamatnpwp from ".$dbname.".setup_org_npwp where kodeorg='".$bar->kodeorg."'";
		$qNpwp=mysql_query($sNpwp) or die(mysql_error());
		$rNpwp=mysql_fetch_assoc($qNpwp);
		
		$sql="select * from ".$dbname.".log_5supplier where supplierid='".$bar->kodesupplier."'"; //echo $sql;
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_object($query);
		
		$sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->purchaser."'";
		$query2=mysql_query($sql2) or die(mysql_error());
		$res2=mysql_fetch_object($query2);
	   
		$sql3="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
		$query3=mysql_query($sql3) or die(mysql_error());
		$res3=mysql_fetch_object($query3); 
		
		
		
		$norek_sup=$res->rekening;
		$kdBank=$res->bank;
		$npwp_sup=$res->npwp;
		$an=$res->an;   
		$nm_kary=$res2->namakaryawan;
		$nm_pt=$res3->namaorganisasi;
	   // $wilayah=$res3->wilayahkota;
		//data PO
		$pmb=$bar->pembuat;
		$tpo=$bar->tujuanpo;
		$ketbrg=$bar->keteranganbarang;	
		$nopo=$bar->nopo;
		$tglPo=tanggalnormal($bar->tanggal);
		//data supplier
		$nmSupplier=$res->namasupplier;
		$almtSupplier=$res->alamat;
		$kota=$res->kota;
		$tlpSupplier=$res->telepon;
		$faxSupplier=$res->fax;
		$kontaksup=$res->kontakperson;
		
		$a1="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$pmb."'";
		$a2=mysql_query($a1);
		$a3=mysql_fetch_assoc($a2);
			$namakarpembuat=$a3['namakaryawan'];
		
		$this->SetMargins(15,10,0);
		
		// Width and Height
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 4;
		
		if($bar->kodeorg=='FBB')
		{
			$path='images/logo2.jpg';
		}
		else if ($bar->kodeorg=='USJ')
		{
			$path='images/logo3.jpg';
		}
		else if ($bar->kodeorg=='FPS')
		{
			$path='images/logo4.jpg';
		}
		else
			$path='images/logo.jpg';
		
		$this->Image($path,15,10,20);	
		$this->SetFont('Arial','BI',12);
		$this->SetFillColor(255,255,255);	
		$this->SetXY(38,10);
		$this->Cell(100,5,$namapt,0,0,'L');
		$this->SetFont('Arial','BIU',12);
		$this->Cell(1,5,$_SESSION['lang']['pesananpembelian'],0,1,'L');
		$this->SetFont('Arial','',9);
		$this->SetX(38);
		$this->Cell(100,4,$alamatpt,0,0,'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(15,4,$_SESSION['lang']['nopo'],0,0,'L');
		$this->Cell(4,4,":",0,0,'L');
		$this->Cell(1,4,$nopo,0,1,'L');
		$this->SetX(38);
		$this->SetFont('Arial','',9);
		$this->Cell(100,4,$wilayah,0,0,'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(15,4,$_SESSION['lang']['cperson'],0,0,'L');
		$this->Cell(4,4,":",0,0,'L');
		$this->Cell(1,4,$namakarpembuat,0,1,'L');
		$this->SetX(38);
		$this->SetFont('Arial','',9);
		$this->Cell($width-38,4,"Tel: ".$telp,0,1,'L');
		$this->SetX(38); 
		$this->Cell($width-38,4,"Fax: ".$faxpt,0,1,'L');	
		/*$this->SetFont('Arial','B',7);
		$this->Cell(60,5,"NPWP: ".$rNpwp['npwp'],0,1,'L');	
		$this->SetX(50);
		$this->Cell(60,5,$_SESSION['lang']['alamat']." NPWP: ".$rNpwp['alamatnpwp'],0,1,'L');	*/
		
		$this->Line(15,32,$width,32);
		$this->SetXY(0,28);
		$this->SetFont('Arial','',6); 	
		$this->Cell($width,5,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'R');
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}

#################################################################### Init PDF ##
$pdf=new PDF('P','mm','A4');
#$pdf=new PDF('P','mm','legal');
$pdf->AddPage();
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;

############################################################# Data Organisasi ##
if(!isset($bar->kodeorg)) {
	$bar->kodeorg='';
}
if($bar->kodeorg=='') {
	$bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
}
$str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
	$namapt=$bar1->namaorganisasi;
	$alamatpt=$bar1->alamat;
	$wilayah=$bar1->wilayahkota;
	$telp=$bar1->telepon;
	$faxpt=$bar1->fax;				 
}

######################################################### Data from Header PO ##
$slopoht="select * from ".$dbname.".log_poht where nopo='".$_GET['column']."'";
$qlopoht=mysql_query($slopoht) or die(mysql_error());
$rlopoht=mysql_fetch_object($qlopoht);
$sb_tot=$rlopoht->subtotal;
$nil_diskon=$rlopoht->nilaidiskon;
$nppn=$rlopoht->ppn;
$stat_release=$rlopoht->stat_release ;
$user_release=$rlopoht->useridreleasae;
$gr_total=($sb_tot-$nil_diskon)+$nppn;

############################################################## Data Detail PO ##
$str="select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$_GET['column']."'";
$resDetail = fetchData($str);
$kodeBrg = "";$i=0;
foreach($resDetail as $row) {
	if($i>0) {$kodeBrg.=',';}
	$kodeBrg .= "'".$row['kodebarang']."'";
	$i++;
}

## Photo Barang
$optPhotoBrg = makeOption($dbname,'log_5photobarang','kodebarang,spesifikasi',
	"kodebarang in (".$kodeBrg.")");

## Nama Barang & Satuan
$sSat="select satuan,namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang in (".$kodeBrg.")";
$tmpSat = fetchData($sSat);
$optBrg = array();
foreach($tmpSat as $row) {
	$optBrg[$row['kodebarang']] = array(
		'nama'=>$row['namabarang'],
		'satuan'=>$row['satuan']
	);
}

################################################################# Data Franco ##
if((is_null($rlopoht->idFranco))||($rlopoht->idFranco=='')||($rlopoht->idFranco==0))
{
	$pdf->Cell(35,4,$_SESSION['lang']['almt_kirim'],0,0,'L'); 
	$pdf->Cell(40,4,": ".$rlopoht->lokasipengiriman,0,1,'L'); 		
}
else
{
	$sFr="select * from ".$dbname.".setup_franco where id_franco='".$rlopoht->idFranco."'";
	$qFr=mysql_query($sFr) or die(mysql_error());
	$rFr=mysql_fetch_assoc($qFr);
}

## Draw Box
$pdf->SetDrawColor(0,0,0);
## Box Supplier
$pdf->Rect(25,36.4,13.4,5);
$pdf->Line(15,39,25,39);
$pdf->Line(38.4,39,70,39);
$pdf->Line(15,39,15,70);
$pdf->Line(70,39,70,70);
$pdf->Line(15,70,70,70);


## Box Franco
$pdf->Rect(80,36.4,27.5,5);
$pdf->Line(73,39,80,39);
$pdf->Line(107.5,39,130,39);
$pdf->Line(73,39,73,70);
$pdf->Line(130,39,130,70);
$pdf->Line(73,70,130,70);

################################################## Header di atas List Barang ##
## Judul
$pdf->SetFont('Arial','B',8);
$pdf->SetXY(25,37);
$pdf->Cell(55,4,$_SESSION['lang']['supplier'],0,0,'L');
$pdf->Cell(55,4,$_SESSION['lang']['almt_kirim'],0,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetXY(135,39);
$pdf->Cell(20,4,$_SESSION['lang']['tgl_po'],0,0,'L');
$pdf->Cell(3,4,':',0,0,'L');
$pdf->Cell(3,4,$tglPo,0,0,'L');
$pdf->Ln();


$newHeight = 3.5;


## Nama
$pdf->SetX(16);
$pdf->SetFont('Arial','U',8);
$pdf->Cell(59,$newHeight,$nmSupplier,0,0,'L');
$pdf->Cell(60,$newHeight,$rFr['franco_name'],0,0,'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(20,$newHeight,$_SESSION['lang']['nopp'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,$resDetail[0]['nopp'],0,0,'L');
$pdf->Ln();

## Alamat
$almSupp = explode(' - ',$almtSupplier);
$almFran = explode(' - ',$rFr['alamat']);


## Alamat 1 part 1
$pdf->SetX(16);
$pdf->Cell(59,$newHeight,substr($almSupp[0],0,40)."-",0,0,'L');
$pdf->Cell(60,$newHeight,substr($almFran[0],0,40)."-",0,0,'L');
$pdf->Cell(20,$newHeight,$_SESSION['lang']['unit'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,substr($resDetail[0]['nopp'],15,4),0,0,'L');
$pdf->Ln();

##alamat 1 part 2

$pdf->SetX(16);
$pdf->Cell(59,$newHeight,substr($almSupp[0],40,80),0,0,'L');
$pdf->Cell(60,$newHeight,substr($almFran[0],40,80),0,0,'L');
$pdf->Cell(20,$newHeight,$_SESSION['lang']['divisi'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,$rlopoht->tujuanpo,0,0,'L');
$pdf->Ln();

## Alamat 2
$pdf->SetX(16);
$pdf->Cell(14,$newHeight,'Kota',0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(40,$newHeight,$kota,0,0,'L');
$pdf->Cell(60,$newHeight,$almFran[1],0,0,'L');
$pdf->Cell(20,$newHeight,$_SESSION['lang']['peruntukan'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,$rlopoht->keteranganbarang,0,0,'L');
$pdf->Ln();

## Telp
$pdf->SetX(16);
$pdf->Cell(14,$newHeight,$_SESSION['lang']['telp'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(40,$newHeight,$tlpSupplier,0,0,'L');
$pdf->Cell(10,$newHeight,$_SESSION['lang']['telp'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(45,$newHeight,$rFr['handphone'],0,0,'L');
$pdf->Cell(20,$newHeight,$_SESSION['lang']['matauang'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,$rlopoht->matauang,0,0,'L');

$pdf->Ln();

## Fax
$pdf->SetX(16);
$pdf->Cell(14,$newHeight,$_SESSION['lang']['fax'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(40,$newHeight,$faxSupplier,0,0,'L');
$pdf->Cell(10,$newHeight,$_SESSION['lang']['fax'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(45,$newHeight,'',0,0,'L');
$pdf->Cell(20,$newHeight,$_SESSION['lang']['tgl_kirim'],0,0,'L');
$pdf->Cell(3,$newHeight,':',0,0,'L');
$pdf->Cell(3,$newHeight,tanggalnormal(substr($rlopoht->tanggalkirim,0,10)),0,0,'L');
$pdf->Ln();

## CP
$pdf->SetX(16);
$pdf->Cell(14,$newHeight,$_SESSION['lang']['cperson'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(40,$newHeight,$kontaksup,0,0,'L');
$pdf->Cell(10,$newHeight,$_SESSION['lang']['cperson'],0,0,'L');
$pdf->Cell(5,$newHeight,":",0,0,'R');
$pdf->Cell(45,$newHeight,$rFr['contact'],0,0,'L');
$pdf->Ln(4);

################################################# /Header di atas List Barang ##

$pdf->SetFont('Arial','U',12);
$pdf->ln();

$ar=$pdf->GetY();
$pdf->SetFont('Arial','B',9);	
$pdf->SetFillColor(220,220,220);
$pdf->Cell(8,5,'No',1,0,'L',1);
$pdf->Cell($width-107,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
//$pdf->Cell(12,5,$_SESSION['lang']['nopp'],1,0,'C',1);	
//$pdf->Cell(12,5,$_SESSION['lang']['untukunit'],1,0,'C',1);		
$pdf->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
$pdf->Cell(14,5,$_SESSION['lang']['satuan'],1,0,'C',1);	
//$pdf->Cell(15,5,$_SESSION['lang']['kurs'],1,0,'C',1);
$pdf->Cell(29,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
$pdf->Cell(32,5,'Total',1,1,'C',1);

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',8);

## Print Detail
$no=0;$i=0;
foreach($resDetail as $row) {
	$no+=1;
	$kodebarang=$row['kodebarang'];
	$jumlah=$row['jumlahpesan'];
	$harga_sat=$row['hargasbldiskon'];
	$total=$jumlah*$harga_sat;
	$unit=substr($row['nopp'],15,4);
	$satuan=$row['satuan'];
	$namabarang='';
	
	if(isset($optPhotoBrg[$row['kodebarang']])) {
		$spek = $optPhotoBrg[$row['kodebarang']];
	} else {
		$spek = "";
	}
	
	$nopp=substr($row['nopp'],0,3);
	//$satuan = $optBrg[$row['kodebarang']]['satuan'];
	$namabarang = $optBrg[$row['kodebarang']]['nama'];
	
	$i++;
	if($no!=1) {
		$pdf->SetY($akhirY);
	}
	$posisiY=$pdf->GetY();
	$pdf->Cell(8,5,$no,0,0,'C',0);
	$pdf->SetX($pdf->GetX());
	//$pdf->Cell(96,5,$namabarang." (".$spek.") : ".$row['catatan'],0,1,'J',0);
	$pdf->MultiCell(96,5,$namabarang." (".$spek.") : ".$row['catatan'],0,1,'J',0);
	
	
	$akhirY=$pdf->GetY();
	  
	$pdf->SetY($posisiY);
	$pdf->SetX($pdf->GetX()+96);
	
	//$pdf->Cell(12,5,$nopp,0,0,'C',0);
	//$pdf->Cell(12,5,$unit,0,0,'C',0);
	$pdf->Cell(15,5,number_format($jumlah,2,'.',','),0,0,'R',0);
	$pdf->Cell(14,5,$satuan,0,0,'C',0);
	//$pdf->Cell(29,5,$row['matauang']." ".number_format($harga_sat,2,'.',','),0,0,'R',0);	
	$pdf->Cell(7,5,$row['matauang'],0,0,'L',0);
	$pdf->Cell(23,5,number_format($harga_sat,2,'.',','),0,0,'R',0);
	$pdf->Cell(7,5,$row['matauang'],0,0,'L',0);
	$pdf->Cell(25,5,number_format($total,2,'.',','),0,1,'R',0);
	/*if($i==20) {
		$i=0;
		$akhirY=$akhirY-20;
		$akhirY=$pdf->GetY()-$akhirY;
		$akhirY=$akhirY+25;
		$pdf->AddPage();
	}*/
	#indra
	
	if($i==19) {
		$i=0;
		$akhirY=$akhirY-20;
		$akhirY=$pdf->GetY()-$akhirY;
		$akhirY=$akhirY+27;
		$pdf->AddPage();
	}
	
}
$akhirSubtot=$pdf->GetY();
$pdf->SetY($akhirY);

$newHeight=4.5;
$pdf->Line($pdf->GetX(),$pdf->GetY(),$pdf->GetX()+$width-9,$pdf->GetY());
$pdf->Ln(3);
$akhirY = $pdf->GetY();
$pdf->Cell(35,$newHeight,$_SESSION['lang']['syaratPem'],0,0,'L'); 

if (strlen($rlopoht->syaratbayar)>=120){
	$pdf->Cell(100,$newHeight,": ".substr($rlopoht->syaratbayar,0,60),0,0,'L');	
	$pdf->Ln(3.5);
	$pdf->SetX(52);
	$pdf->Cell(100,$newHeight,substr($rlopoht->syaratbayar,60,60),0,0,'L'); 	
	$pdf->Ln(3.5);
	$pdf->SetX(52);
	$pdf->Cell(100,$newHeight,substr($rlopoht->syaratbayar,120,strlen($rlopoht->syaratbayar)),0,0,'L'); 	
}else{
	if (strlen($rlopoht->syaratbayar)>=60){
		$pdf->Cell(100,$newHeight,": ".substr($rlopoht->syaratbayar,0,60),0,0,'L');	
		$pdf->Ln(3.5);
		$pdf->SetX(52);
		$pdf->Cell(100,$newHeight,substr($rlopoht->syaratbayar,60,strlen($rlopoht->syaratbayar)),0,0,'L'); 	
	}else{
		$pdf->Cell(100,$newHeight,": ".$rlopoht->syaratbayar,0,0,'L');
	}
}

$pdf->SetY($akhirY);
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
//$pdf->Cell(5,$newHeight,IDR,0,0,'L',0);	
$pdf->Cell(24,$newHeight,$_SESSION['lang']['subtotal'],0,0,'L',1);
$pdf->SetX(170);
$pdf->Cell(8,$newHeight,$row['matauang'],0,0,'L',0);		
$pdf->Cell(24,$newHeight,number_format($rlopoht->subtotal,2,'.',','),0,1,'R',1);

/*
$pdf->SetFont('Arial','',8);
$pdf->Cell(35,$newHeight,$_SESSION['lang']['tgljatuhtempo'],0,0,'L'); 
//$pdf->Cell(100,$newHeight,": ".tanggalnormald($rlopoht->tgljthtempo),0,0,'L');
if($rlopoht->tgljthtempo=='0000-00-00')
{
	$pdf->Cell(100,$newHeight,': -',0,0,'L');	
}
else
{
	$pdf->Cell(100,$newHeight,": ".tanggalnormald($rlopoht->tgljthtempo),0,0,'L');
}
*/


$pdf->SetY($pdf->GetY());
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(29,$newHeight,'Discount'." (".$rlopoht->diskonpersen."% )",0,0,'L',1);	
	$pdf->SetX(170);
	$pdf->Cell(8,$newHeight,$rlopoht->matauang,0,0,'L',0);	
$pdf->Cell(24,$newHeight,number_format($rlopoht->nilaidiskon,2,'.',','),0,1,'R',1);

$pdf->SetFont('Arial','',8);
$pdf->Cell(35,$newHeight,"Transfer Rekening",0,0,'L');
$pdf->Cell(35,$newHeight,":",0,0,'L');
$pdf->SetY($pdf->GetY());
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(29,$newHeight,'PPN',0,0,'L',1);	
$pdf->SetX(170);
$pdf->Cell(8,$newHeight,$rlopoht->matauang,0,0,'L',0);
$pdf->Cell(24,$newHeight,number_format($rlopoht->ppn,2,'.',','),0,1,'R',1);

$pdf->SetFont('Arial','',8);
$pdf->SetX($pdf->GetX()+10);
$pdf->Cell(25,4,$_SESSION['lang']['namabank'],0,0,'L'); 
$pdf->Cell(40,4,": ".$kdBank,0,0,'L');
$pdf->SetY($pdf->GetY());
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(29,$newHeight,'Down Payment',0,0,'L',1);	
$pdf->SetX(170);
$pdf->Cell(8,$newHeight,$rlopoht->matauang,0,0,'L',0);
$pdf->Cell(24,$newHeight,'0.00',0,1,'R',1);

$pdf->SetFont('Arial','',8);
$pdf->SetX($pdf->GetX()+10);
$pdf->Cell(25,4,$_SESSION['lang']['norekeningbank'],0,0,'L'); 
$pdf->Cell(40,4,": ".$norek_sup,0,0,'L');
$pdf->SetY($pdf->GetY());
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(29,$newHeight,'Lain - lain',0,0,'L',1);	
$pdf->SetX(170);
$pdf->Cell(8,$newHeight,$rlopoht->matauang,0,0,'L',0);
$pdf->Cell(24,$newHeight,'0.00',0,1,'R',1);

$pdf->SetFont('Arial','',8);
$pdf->SetX($pdf->GetX()+10);
$pdf->Cell(25,4,$_SESSION['lang']['atasnama'],0,0,'L'); 
//$pdf->Cell(40,4,": ".ucfirst(strtolower($an)),0,0,'L');
$pdf->Cell(40,4,": ".$an,0,0,'L');
$pdf->SetY($pdf->GetY());
$pdf->SetX($width-55);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(29,5,$_SESSION['lang']['grnd_total'],0,0,'L',1);	
$pdf->SetX(170);
$pdf->Cell(8,$newHeight,$rlopoht->matauang,0,0,'L',0);
$pdf->Cell(24,5,number_format($gr_total,2,'.',','),0,1,'R',1);	
if(strlen($rlopoht->uraian)>350)
{
	$tmbhBrs=65;
	$tmbhBrs2=115;
}
else
{
	$tmbhBrs=30;
	$tmbhBrs2=95;
}
$pdf->SetY($akhirY+$tmbhBrs+5);
$pdf->SetFont('Arial','',8);
//$pdf->MultiCell(100,4,$_SESSION['lang']['note']." :"."\n".$rlopoht->uraian,0,1,'J',0);
//$pdf->MultiCell(100,4,'',0,1,'J',0);

if($stat_release==1) {
	## Draw Box for Signature
	$currX = $pdf->GetX();
	$pdf->Rect($currX+110,$pdf->GetY(),70,30);
	
	
	##############get persetujuan 1
	$a="select * from ".$dbname.".`log_poht` where `nopo`='".$column."'"; //echo $qp;
	$b=mysql_query($a) or die(mysql_error());
	$c=mysql_fetch_object($b);
	
	$d="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$c->persetujuan1."'"; //echo $d;//exit();
	$e=mysql_query($d) or die(mysql_error());
	$f=mysql_fetch_object($e);	
	
	$g="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$f->kodejabatan."'";
	$h=mysql_query($g) or die(mysql_error());
	$i=mysql_fetch_object($h);			
	
	##############get persetujuan 2
	/*$a1="select * from ".$dbname.".`log_poht` where `nopo`='".$column."'"; //echo $qp;
	$b1=mysql_query($a1) or die(mysql_error());
	$c1=mysql_fetch_object($b1);
	//$qyr=fetchData($qp);	
	$d1="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$c1->persetujuan2."'"; //echo $d;//exit();
	$e1=mysql_query($d1) or die(mysql_error());
	$f1=mysql_fetch_object($e1);	
	
	$g1="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$f1->kodejabatan."'";
	$h1=mysql_query($g1) or die(mysql_error());
	$i1=mysql_fetch_object($h1);				*/
	
	$pdf->SetFont('Arial','',7);
	//$pdf->Cell(8,20,'','',0,'C');
	$pdf->SetY($akhirY+55);
	#$pdf->SetY($akhirY+67);	
	$pdf->SetX(110);
	$pdf->Cell(100,1,$f->namakaryawan,'',0,'C');
	$pdf->SetX(110);
	$pdf->Cell(100,3,'_____________________________________','',0,'C');
	$pdf->SetX(100);
	$pdf->Cell(100,3,'','',1,'C');
	$pdf->SetX(110);
	$pdf->Cell(100,4,$i->namajabatan,'',0,'C');
}
$pdf->Ln(-10);
$pdf->MultiCell(100,4,"Keterangan :"."\n".$rlopoht->uraian,0,1,'J',0);
$pdf->Output();
?>