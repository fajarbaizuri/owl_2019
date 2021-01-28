<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
	$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
	$rOrg=mysql_fetch_assoc($qOrg);
	$nmOrg=$rOrg['namaorganisasi'];
//$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

//=============

//create Header
class PDF extends FPDF
{
	
function Header()
	{
	global $nmOrg;
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
	//global $optNm;
	//$optNm=makeoption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
		$str="select kodeorg,kodesupplier,purchaser,nopo,tanggal,pembuat,tujuanpo,keteranganbarang from ".$dbname.".log_poht  where nopo='".$_GET['column']."'";
		//echo $str;exit();
		$res=mysql_query($str);
		$bar=mysql_fetch_object($res);
		
			//ambil nama pt
			  // $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			  if($bar->kodeorg=='')
			  {
				 $bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
			  }
			  $str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
			   //echo $str;exit();
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
	  // echo"<pre>";print_r($_SESSION);echo"</pre>";echo $sNpwp;exit();
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
	   
	 //  $this->SetMargins(15,10,0);
	    $this->SetMargins(15,10,0);
		$path='images/logo.jpg';
		//$this->Image(
	    $this->Image($path,15,6,25);	
		$this->SetFont('Arial','B',9);
		$this->SetFillColor(255,255,255);	
		$this->SetX(42);   
		$this->Cell(60,5,$namapt,0,1,'L');	 
		$this->SetX(42); 		
		$this->Cell(60,5,$alamatpt.", ".$wilayah,0,1,'L');	
		$this->SetX(42); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');
		$this->SetX(42); 
		$this->Cell(60,5,"Fax: ".$faxpt,0,1,'L');	
		/*$this->SetFont('Arial','B',7);
		$this->Cell(60,5,"NPWP: ".$rNpwp['npwp'],0,1,'L');	
		$this->SetX(50); 			
		$this->Cell(60,5,$_SESSION['lang']['alamat']." NPWP: ".$rNpwp['alamatnpwp'],0,1,'L');	*/
		
		$this->Line(15,35,205,35);	
		$this->SetFont('Arial','',6); 	
		//$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,15,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');
		//$this->c
		
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

	if($bar->kodeorg=='')
			  {
				 $bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
			  }
			  $str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
			  //print("$str1");
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat;
				 $wilayah=$bar1->wilayahkota;
				 $telp=$bar1->telepon;
				 $faxpt=$bar1->fax;				 
			   } 	

		$pdf->SetFont('Arial','B',8);	
		
		$pdf->SetXY(15,45);
		$pdf->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
		$pdf->SetXY(110,45);
		$pdf->Cell(30,4,"DARI :",0,0,'L'); 
		//$pdf->SetXY(100,20);	
		//$pdf->Cell('',4,'DIBUAT OLEH :',0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY(15,50);
		$pdf->Cell(27,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$nmSupplier,0,0,'L'); 	
		$pdf->SetXY(110,50);
		$pdf->Cell(27,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$namapt,0,1,'L'); 
		
		
		$pdf->setxy(15,54);
		$pdf->Cell(27,4,'Kontak Person',0,0,'L'); 
		$pdf->Cell(40,4,": ".$kontaksup,0,1,'L'); 
		$pdf->SetXY(110,54);
		$pdf->Cell(27,4,'Nama Kontak',0,0,'L'); 
		$pdf->Cell(40,4,": ".$pmb,0,1,'L');
		
		$pdf->SetXY(15,58);
		$pdf->Cell(27,4,$_SESSION['lang']['alamat'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$almtSupplier,0,0,'L'); 
		$pdf->SetXY(110,58);
		$pdf->Cell(27,4,$_SESSION['lang']['alamat'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$alamatpt,0,1,'L');
		
		$pdf->SetXY(15,62);
		$pdf->Cell(27,4,$_SESSION['lang']['kota'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$kota,0,0,'L'); 
		$pdf->SetXY(110,62);
		$pdf->Cell(27,4,$_SESSION['lang']['kota'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$wilayah,0,1,'L');
		
		
		$pdf->SetXY(15,66);
		$pdf->Cell(27,4,$_SESSION['lang']['telp'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$tlpSupplier,0,0,'L'); 
		$pdf->SetXY(110,66);
		$pdf->Cell(27,4,$_SESSION['lang']['telp'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$telp,0,1,'L');
		

		$pdf->SetXY(15,70);
		$pdf->Cell(27,4,$_SESSION['lang']['fax'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$faxSupplier,0,1,'L'); 
		$pdf->SetXY(110,70);
		$pdf->Cell(27,4,$_SESSION['lang']['fax'],0,0,'L');
		$pdf->Cell(40,4,": ".$faxpt,0,1,'L');
		
		$pdf->setxy(15,74);
		$pdf->Cell(27,4,$_SESSION['lang']['namabank'],0,0,'L'); 
		//$pdf->Cell(40,4,": ".ucfirst(strtolower($kdBank))." ".$kdBank,0,1,'L'); 
		$pdf->Cell(40,4,": ".$kdBank,0,1,'L'); 
		//$pdf->setxy(110,70);
		//$pdf->Cell(35,4,'e',0,0,'L');
		//$pdf->Cell(40,4,': isi e',0,1,'L');
		
		$pdf->setxy(15,78);
		$pdf->Cell(27,4,$_SESSION['lang']['norekeningbank'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$norek_sup." a/n ".ucfirst(strtolower($an)),0,1,'L'); 
		//$pdf->setxy(110,74);
		//$pdf->Cell(35,4,'f',0,0,'L');
		//$pdf->Cell(40,4,': isi f',0,1,'L');
		
		
 		$pdf->SetFont('Arial','U',12);
		$pdf->ln();

		
		//$pdf->SetY(75);
		$ar=$pdf->GetY();
		$pdf->SetY($ar+10);
		$pdf->Cell(190,5,strtoupper("Purchase Order"),0,1,'C');		
		//$pdf->Ln();
		//$pdf->Ln();

		//$this->SetFont('Arial','',15);
	    //$this->Cell(190,5,strtoupper($_SESSION['lang']['permintaan_harga']),0,1,'C');
		//$pdf->SetY(80);
		$pdf->SetY($ar+25);
	    $pdf->SetFont('Arial','',9);		
		$pdf->Cell(10,4,"No.PO",0,0,'L'); 
		$pdf->Cell(20,4,": ".$nopo,0,0,'L'); 
		//$pdf->SetY(80);
		$pdf->SetX(163);
		$pdf->Cell(20,4,"Tanggal PO.",0,0,'L'); 
		$pdf->Cell(20,4,": ".$tglPo,0,1,'L'); 
		
		$pdf->SetY($ar+29);
		$pdf->Cell(20,4,'Tujuan',0,0,'L'); 
		$pdf->Cell(20,4,": ".$tpo,0,1,'L'); 
		
		$pdf->SetY($ar+33);
		$pdf->Cell(20,4,'Keterangan',0,0,'L'); 
		$pdf->Cell(20,4,": ".$ketbrg,0,1,'L'); 
		
		$pdf->SetY($ar+40);
		$pdf->SetFont('Arial','B',9);	
		$pdf->SetFillColor(220,220,220);
		$pdf->Cell(8,5,'No',1,0,'L',1);
		$pdf->Cell(72,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
		$pdf->Cell(12,5,$_SESSION['lang']['nopp'],1,0,'C',1);	
		$pdf->Cell(12,5,$_SESSION['lang']['untukunit'],1,0,'C',1);		
		$pdf->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
		$pdf->Cell(14,5,$_SESSION['lang']['satuan'],1,0,'C',1);	
		//$pdf->Cell(15,5,$_SESSION['lang']['kurs'],1,0,'C',1);
		$pdf->Cell(29,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
		$pdf->Cell(26,5,'Total',1,1,'C',1);

		$pdf->SetFillColor(255,255,255);
	    $pdf->SetFont('Arial','',8);
		
		$str="select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$_GET['column']."'";
		//echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($bar=mysql_fetch_object($re))
		{
			$no+=1;
			
		   $kodebarang=$bar->kodebarang;
		   $jumlah=$bar->jumlahpesan;
		   $harga_sat=$bar->hargasbldiskon;
		   $total=$jumlah*$harga_sat;
		   $unit=substr($bar->nopp,15,4);
		   $namabarang='';
 
		   $strv="select b.spesifikasi from  ".$dbname.".log_5photobarang b  where b.kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
		   $resv=mysql_query($strv);
		   $barv=mysql_fetch_object($resv);
		   
			   if($barv->spesifikasi!='')
			   {
				   	$spek=$barv->spesifikasi;					
			   }
			   else
			   {
				   $spek="";
   					
			   }
		   	$nopp=substr($bar->nopp,0,3);
			$sSat="select satuan,namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
			$qSat=mysql_query($sSat) or die(mysql_error());
			$rSat=mysql_fetch_assoc($qSat);
			$satuan=$rSat['satuan'];
			$namabarang=$rSat['namabarang'];
		 
			$i++;
	  		    
			   if($no!=1)
				{
					
						$pdf->SetY($akhirY);
					
				}
			   $posisiY=$pdf->GetY();
			   $pdf->Cell(8,5,$no,0,0,'C',0);
			   $pdf->SetX($pdf->GetX());
			  // $pdf->SetY($akhirY);
               //$pdf->SetY($pdf->GetY());
			   //$pdf->SetX($pdf->GetX()+8);
			   $pdf->MultiCell(72,5,$namabarang."\n".$spek."\n".$bar->catatan,0,'J',0);
			   $akhirY=$pdf->GetY();
			  
			  /* $pdf->SetY($pdf->GetY());
			   $pdf->SetX($pdf->GetX()+68);*/
			   $pdf->SetY($posisiY);
			   //$pdf->SetX($pdf->GetX()+75);
			   $pdf->SetX($pdf->GetX()+80);
			  
			   $pdf->Cell(12,5,$nopp,0,0,'C',0);	
			   $pdf->Cell(12,5,$unit,0,0,'C',0);
			  // $pdf->SetY($pdf->GetY()-15);
			 //  $pdf->SetX($pdf->GetX());
				//$pdf->SetY($pdf->GetY()-4);
			   $pdf->Cell(15,5,number_format($jumlah,2,'.',','),0,0,'R',0);
			  // $pdf->SetY($pdf->GetY()-15);
			  // $pdf->SetX($pdf->GetX()+113);
			 $pdf->Cell(14,5,$bar->satuan,0,0,'C',0);
			 //  $pdf->SetY($pdf->GetY()-15);
			  // $pdf->SetX($pdf->GetX()+126);
			// $pdf->Cell(15,5,$bar->matauang,0,0,'C',0);			  
 			  // $pdf->SetY($pdf->GetY()-15);
			 //  $pdf->SetX($pdf->GetX()+141);
			  $pdf->Cell(29,5,$bar->matauang." ".number_format($harga_sat,2,'.',','),0,0,'R',0);	
			  // $pdf->SetY($pdf->GetY()-15);
			 //  $pdf->SetX($pdf->GetX()+166);
		  	 $pdf->Cell(26,5,number_format($total,2,'.',','),0,1,'R',0);
			 if($i==20)
			 {
				$i=0;
				$akhirY=$akhirY-20;
				$akhirY=$pdf->GetY()-$akhirY;
				$akhirY=$akhirY+25;
				//$pdf->SetY($posisiY+25);
				$pdf->AddPage();
			 }
			
			    	   
		}
		$akhirSubtot=$pdf->GetY();
		$pdf->SetY($akhirY);
		$slopoht="select * from ".$dbname.".log_poht where nopo='".$_GET['column']."'";
		$qlopoht=mysql_query($slopoht) or die(mysql_error());
		$rlopoht=mysql_fetch_object($qlopoht);
		$sb_tot=$rlopoht->subtotal;
		$nil_diskon=$rlopoht->nilaidiskon;
		$nppn=$rlopoht->ppn;
		$stat_release=$rlopoht->stat_release ;
		$user_release=$rlopoht->useridreleasae;
		$gr_total=($sb_tot-$nil_diskon)+$nppn;
	/*	$pdf->MultiCell(126,50,$_SESSION['lang']['tgl_kirim'].": ".tanggalnormald($rlopoht->tanggalkirim).$pdf->Ln('4').
		$_SESSION['lang']['almt_kirim'].": ".$rlopoht->lokasipengiriman.$_SESSION['lang']['norekeningbank'].": ".$norek_sup,'1',0,'L');*/
		
	   $pdf->MultiCell(136,4,"Keterangan :"."\n".$rlopoht->uraian,'T',1,'J',0);
	   $pdf->SetY($akhirY);
	   $pdf->SetX($pdf->GetX()+133);
	   $pdf->SetFont('Arial','B',8);
	   $pdf->Cell(29,5,$_SESSION['lang']['subtotal'],'T',0,'L',1);	
	   $pdf->Cell(26,5,number_format($rlopoht->subtotal,2,'.',','),'T',1,'R',1);
	  // $pdf->Cell(65,5,'',1,1,'C',1);	
	  // $pdf->Cell(96,5,": ".tanggalnormald($rlopoht->tanggalkirim),0,0,'L');
	   $pdf->SetY($pdf->GetY());
	   $pdf->SetX($pdf->GetX()+133);
	   $pdf->Cell(29,5,'Discount'." (".$rlopoht->diskonpersen."% )",0,0,'L',1);	
	   $pdf->Cell(26,5,number_format($rlopoht->nilaidiskon,2,'.',','),0,1,'R',1);
	   $pdf->SetY($pdf->GetY());
	   $pdf->SetX($pdf->GetX()+133);
	   $pdf->Cell(29,5,'PPh/PPn (10 %)',0,0,'L',1);	
	   $pdf->Cell(26,5,number_format($rlopoht->ppn,2,'.',','),0,1,'R',1);
	    $pdf->SetFont('Arial','B',8);
		$pdf->SetY($pdf->GetY());
		$pdf->SetX($pdf->GetX()+133);
		
		$pdf->Cell(29,5,$_SESSION['lang']['grnd_total'],0,0,'L',1);	
		$pdf->Cell(26,5,$rlopoht->matauang." ".number_format($gr_total,2,'.',','),0,1,'R',1);	
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
		$pdf->SetY($akhirY+$tmbhBrs);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(35,4,$_SESSION['lang']['syaratPem'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$rlopoht->syaratbayar,0,1,'L'); 
		
		$pdf->Cell(35,4,$_SESSION['lang']['tgl_kirim'],0,0,'L'); 
		$pdf->Cell(40,4,": ".tanggalnormald($rlopoht->tanggalkirim),0,1,'L'); 		
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
			$pdf->Cell(35,4,$_SESSION['lang']['almt_kirim'],0,0,'L'); 
			$pdf->Cell(40,4,": ".$rFr['alamat'],0,1,'L'); 		
			$pdf->Cell(35,4,"Kontak Person",0,0,'L'); 
			$pdf->Cell(40,4,": ".$rFr['contact'],0,1,'L'); 	
			$pdf->Cell(35,4,"Telp / Handphone No.",0,0,'L'); 
			$pdf->Cell(40,4,": ".$rFr['handphone'],0,1,'L');
			
			
			//$pdf->Cell(35,4,$_SESSION['lang']['tandatangan'].':',0,0,'L');
			
			
		
		}
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetFont('Arial','B',12);
	
	$pdf->Cell(120,$height,$_SESSION['lang']['tandatangan'].':','',0,'L');
  	$pdf->SetY($akhirY+$tmbhBrs2);
	//$pdf->SetY($pdf->GetY()+900);
	//$pdf->Cell(120,$height,$_SESSION['lang']['tandatangan'].':','',0,'L');
	//$pdf->SetY($akhirY+65);
	
	$pdf->SetFont('Arial','B',8);
	$ko=0;
	$qp="select * from ".$dbname.".`log_poht` where `nopo`='".$column."'"; //echo $qp;
	$qyr=fetchData($qp);
	//$pdf->SetFillColor(225,225,225);
/*	$pdf->Cell(8,10,'','',0,'C');
	$pdf->Cell(40,10,'','',0,'C');
	$pdf->Cell(35,10,'','',0,'C');
	$pdf->Cell(20,10,'','',0,'C');
	$pdf->Cell(20,10,'','',0,'C');*/
	
	//$pdf->MultiCell(72,5,$namabarang." ".$spek." ".$bar->catatan,0,'J',0);
	
	
	
	//$pdf->Cell(
	//$pdf->Cell(20,4,$_SESSION['lang']['tanggal'],'TBLR',0,'C');
	//$pdf->Cell(30,4,$_SESSION['lang']['status'],'TBLR',0,'C');
	//$pdf->Cell(30,4,$_SESSION['lang']['keputusan'],'TBLR',0,'C');
	//$pdf->Cell(50,4,$_SESSION['lang']['note'],'TBLR',0,'C');
	//$pdf->Ln();	
	
		foreach($qyr as $hsl)
		{
		
			
			for($i=1;$i<4;$i++)
			{
			//print $i;
				if($hsl['persetujuan'.$i]!='')
				{
					$sql="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$hsl['persetujuan'.$i]."'"; //echo $sql;//exit();
					$keterangan=$hsl['komentar'.$i];
				}
				/*else
				{
					break;
				}
					if($hsl['hasilpersetujuan'.$i]==1)
					{
						$b['status']=$_SESSION['lang']['disetujui'];
					}
					elseif($hsl['hasilpersetujuan'.$i]==3)
					{
						$b['status']=$_SESSION['lang']['ditolak'];
					}
					elseif($hsl['hasilpersetujuan'.$i]==''||$hsl['hasilpersetujuan'.$i]==0)
					{
						$b['status']=$_SESSION['lang']['wait_approve'];
					}*/
				

							}
		}
		
/*				if($hsl['tglp'.$i]!='0000-00-00')
				{ $tgl=tanggalnormal($hsl['tglp'.$i]);}
				else
				{$tgl='';}*/
				
				$i=1;
				if($hsl['persetujuan'.$i]!='')
				{
					$sql="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$hsl['persetujuan'.$i]."'"; //echo $sql;//exit();
					$keterangan=$hsl['komentar'.$i];
				}
				/*else
				{
					break;
				}
					if($hsl['hasilpersetujuan'.$i]==1)
					{
						$b['status']=$_SESSION['lang']['disetujui'];
					}
					elseif($hsl['hasilpersetujuan'.$i]==3)
					{
						$b['status']=$_SESSION['lang']['ditolak'];
					}
					elseif($hsl['hasilpersetujuan'.$i]==''||$hsl['hasilpersetujuan'.$i]==0)
					{
						$b['status']=$_SESSION['lang']['wait_approve'];
					}*/
				
				$query=mysql_query($sql) or die(mysql_error());
				$res3=mysql_fetch_object($query);
				
				$sql2="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$res3->kodejabatan."'";
				$query2=mysql_query($sql2) or die(mysql_error());
				$res2=mysql_fetch_object($query2);
				
			
				
				
				$pdf->SetFont('Arial','',7);
				//$pdf->Cell(8,20,'','',0,'C');
				$pdf->SetY($akhirY+85);
				$pdf->SetX(50);
				$pdf->Cell(100,10,$res3->namakaryawan,'',0,'C');
				
				$pdf->SetY($akhirY+86.5);
				$pdf->SetX(50);
				$pdf->Cell(100,10,'_____________________________________','',1,'C');
				
				$pdf->SetY($akhirY+90);
				$pdf->SetX(50);
				//$pdf->Cell(100,10,$res2->namajabatan,'',0,'C');
				$pdf->Cell(100,10,$res2->namajabatan,'',0,'C');
				
				
				
				//$pdf->Cell(20,30,$res3->lokasitugas,'',0,'L');
				//$pdf->Cell(20,30,'','',0,'L');
				//$pdf->Cell(20,4,$tgl,'TBLR',0,'L');
				//$pdf->Cell(30,4,$b['status'],'TBLR',0,'L');
				/*$pdf->Cell(50,4,$keterangan,'TBLR',0,'L');*/
				$pdf->Ln();	

	$pdf->Output();?>
