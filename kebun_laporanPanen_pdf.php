<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/fpdf.php');
    require_once('lib/nangkoelib.php');
    require_once('lib/zLib.php');

    $pt=$_GET['pt'];
    $gudang=$_GET['gudang'];
    $tgl1=$_GET['tgl1'];
    $tgl2=$_GET['tgl2'];
    $optPanen = makeOption($dbname,'setup_kegiatan','kodekegiatan,kodekegiatan',
		"kelompok='PNN'");
	$kegPanen = getFirstKey($optPanen);

    class PDF extends FPDF
    {
        function Header() {
            global $conn;
            global $dbname;
            global $align;
            global $length;
            global $colArr;
            global $title;
            global $pt;
            global $gudang;
            global $periode;
            global $tgl1;
            global $tgl2;

            $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
            $qAlamat=mysql_query($sAlmat) or die(mysql_error());
            $rAlamat=mysql_fetch_assoc($qAlamat);

            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 11;
            $path='images/logo.jpg';
            $this->Image($path,$this->lMargin,$this->tMargin,40);	
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255,255,255);	
            $this->SetX(80);   
            $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
            $this->SetX(80); 		
            $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
            $this->SetX(80); 			
            $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
            $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
            $this->Ln();	
            $this->Ln();
            $this->SetFont('Arial','B',11);
            $this->Cell($width,$height, $_SESSION['lang']['laporanpanen'],0,1,'C');	
            $this->Cell($width,$height, $_SESSION['lang']['periode'].":".$tgl1." S/d ".$tgl2 ." ".$_SESSION['lang']['unit'].":" .($gudang!=''?$gudang:$_SESSION['lang']['all']),0,1,'C');	
            $this->SetFont('Arial','',8);

            $this->Ln();
            $this->SetFont('Arial','B',7);	
            $this->SetFillColor(220,220,220);

            $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
            $this->Cell(9/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['afdeling'],1,0,'C',1);
            $this->Cell(9/100*$width,$height,$_SESSION['lang']['lokasi'],1,0,'C',1);
            $this->Cell(9/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);
            $this->Cell(9/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);
            $this->Cell(9/100*$width,$height,"BJR Aktual",1,0,'C',1);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['upahkerja'],1,0,'C',1);	
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['upahpremi'],1,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['jumlahhk'],1,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['penalti'],1,0,'C',1);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['keterangan'],1,1,'C',1);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    $pdf=new PDF('P','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 9;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
    if($gudang=='')
    {
        $str="select a.notransaksi,a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
            sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi
        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal,a.kodeorg";
        
        $qKontan="SELECT a.*,b.*,d.tahuntanam FROM ".$dbname.".log_baspk a LEFT JOIN ".$dbname.".
			log_spkht b ON a.notransaksi=b.notransaksi left join ".$dbname.".organisasi c
			on b.kodeorg=c.kodeorganisasi left join ".$dbname.".setup_blok d on a.kodeblok=d.kodeorg ".
			"WHERE a.kodekegiatan='".$kegPanen.
			"' and c.induk = '".$pt."' and b.tanggal between ".tanggalsystem($tgl1).
			" and ".tanggalsystem($tgl2)."";
    }
    else
    {
        $str="select a.notransaksi,a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
            sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a
        where unit = '".$gudang."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal, a.kodeorg";
        
        $qKontan="SELECT a.*,b.*,c.tahuntanam FROM ".$dbname.".log_baspk a LEFT JOIN ".$dbname.".
			log_spkht b ON a.notransaksi=b.notransaksi left join ".$dbname.".setup_blok c on ".
			"a.kodeblok=c.kodeorg WHERE a.kodekegiatan='".$kegPanen.
			"' and b.kodeorg= '".$gudang."' and b.tanggal between ".tanggalsystem($tgl1).
			" and ".tanggalsystem($tgl2)."";
    }
    $resKontan = fetchData($qKontan);

    $res=mysql_query($str);
    $no=0;
    $totberat = $totUpah = $totJjg = $totPremi = $totHk = $totPenalty = 0;
    while($bar=mysql_fetch_object($res))
    {
        $periode=date('Y-m-d H:i:s');
        $notransaksi=$bar->notransaksi;
        $tanggal=$bar->tanggal; 
        $kodeorg 	=$bar->kodeorg;
        $no+=1;
        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
        $pdf->Cell(9/100*$width,$height,tanggalnormal($tanggal),1,0,'C',1);
        $pdf->Cell(7/100*$width,$height,substr($kodeorg,0,6),1,0,'L',1);
        $pdf->Cell(9/100*$width,$height,$kodeorg,1,0,'L',1);
        $pdf->Cell(9/100*$width,$height,$bar->tahuntanam,1,0,'C',1);	
        $pdf->Cell(7/100*$width,$height,number_format($bar->jjg,0),1,0,'R',1);	
        $pdf->Cell(9/100*$width,$height,number_format($bar->berat,0),1,0,'R',1);
        $pdf->Cell(9/100*$width,$height,number_format($bar->berat/$bar->jjg,2),1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,number_format($bar->upah,0),1,0,'R',1);	
        $pdf->Cell(8/100*$width,$height,number_format($bar->premi,0),1,0,'R',1);	
        $pdf->Cell(7/100*$width,$height,number_format($bar->jumlahhk,0),1,0,'R',1);	
        $pdf->Cell(7/100*$width,$height,number_format($bar->penalty,0),1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,'',1,1,'L',1);

        $totberat+=$bar->berat;
        $totUpah+=$bar->upah;
        $totJjg+=$bar->jjg;
        $totPremi+=$bar->premi;
        $totHk+=$bar->jumlahhk;
        $totPenalty+=$bar->penalty;

    }
    
    # Kontanan
    foreach($resKontan as $row) {
        $no++;
        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
        $pdf->Cell(9/100*$width,$height,tanggalnormal($row['tanggal']),1,0,'C',1);
        $pdf->Cell(7/100*$width,$height,$row['divisi'],1,0,'L',1);
        $pdf->Cell(9/100*$width,$height,$row['kodeblok'],1,0,'L',1);
        $pdf->Cell(9/100*$width,$height,$row['tahuntanam'],1,0,'C',1);	
        $pdf->Cell(7/100*$width,$height,number_format($row['hasilkerjarealisasi'],0),1,0,'R',1);	
        $pdf->Cell(9/100*$width,$height,number_format(0,0),1,0,'R',1);
        $pdf->Cell(9/100*$width,$height,number_format(0,2),1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,number_format($row['jumlahrealisasi'],0),1,0,'R',1);	
        $pdf->Cell(8/100*$width,$height,number_format(0,0),1,0,'R',1);	
        $pdf->Cell(7/100*$width,$height,number_format($row['hkrealisasi'],0),1,0,'R',1);	
        $pdf->Cell(7/100*$width,$height,number_format(0,0),1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,'Kontanan',1,1,'L',1);
        $totUpah+=$row['jumlahrealisasi'];
        $totJjg+=$row['hasilkerjarealisasi'];
        $totHk+=$row['hkrealisasi'];
    }
    
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell(37/100*$width,$height,"TOTAL",1,0,'C',1);
    $pdf->Cell(7/100*$width,$height,number_format($totJjg,0),1,0,'R',1);
    $pdf->Cell(9/100*$width,$height,number_format($totberat,2),1,0,'R',1);
    $pdf->Cell(9/100*$width,$height,number_format($totberat/$totJjg,2),1,0,'R',1);
    $pdf->Cell(8/100*$width,$height,number_format($totUpah,2),1,0,'R',1);
    $pdf->Cell(8/100*$width,$height,number_format($totPremi,2),1,0,'R',1);
    $pdf->Cell(7/100*$width,$height,number_format($totHk,0),1,0,'R',1);
    $pdf->Cell(7/100*$width,$height,number_format($totPenalty,2),1,0,'R',1);
    $pdf->Cell(8/100*$width,$height,'',1,1,'L',1);
    $pdf->Output();
?>