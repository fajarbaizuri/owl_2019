<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

//$arr3="##kdPabrik__3##kdUnit__3##periode__3";
$kdPabrik=$_POST['kdPabrik__3'];
$kdUnit=$_POST['kdUnit__3'];
$periode=$_POST['periode__3'];

switch($proses)
{
    case'preview':
    if($periode=='')
    {
        echo"Warning: Silakan mengisi periode.";
        exit();
    }

    if($kdUnit==''){ // kode unit = seluruhnya, kolom menampilkan unit
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$isi['kodeorg']][$tanggal]+=$isi['beratbersih'];
                $kolom[$isi['kodeorg']]=$isi['kodeorg'];
            }
        }
    }else{ // kode unit = seluruhnya, kolom menampilkan afdeling
        // cari afdeling buat kolom
        $qweri="select kodeorganisasi from ".$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        while($isi=mysql_fetch_assoc($datanya))
        {
            $kolom[$isi['kodeorganisasi']]=$isi['kodeorganisasi'];
        }
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $qwe=explode('/',$isi['nospb']);
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$qwe[1]][$tanggal]+=$isi['beratbersih'];
            }
        }
    }
    
    // cari tanggal terakhir dari bulan
    $anyDate = $periode.'-25';    // date format should be yyyy-mm-dd
    list($yr,$mn,$dt) = split('-',$anyDate);    // separate year, month and date
    $timeStamp = mktime(0,0,0,$mn,1,$yr);    //Create time stamp of the first day from the give date.
    list($y,$m,$t) = split('-',date('Y-m-t',$timeStamp)); //Find the last date of the month and separating it
    $lastDayTimeStamp = mktime(0,0,0,$m,$t,$y);//create time stamp of the last date of the give month
    $lastDate = date('d',$lastDayTimeStamp);// Find last day of the month
    
    sort($kolom);
    
    echo $_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['bulan'];
    
    if(empty($isinya)){
        echo "<br><br>Tidak ada data.";
    }else{
        echo"<table cellspacing=1 border=0 class=sortable>
        <thead class=rowheader>
        <tr>
            <td align=center>".$_SESSION['lang']['tanggal']."</td>";
            foreach($kolom as $kol){
                echo "<td align=center>".$kol."</td>";
            }
        echo"<td align=center>".$_SESSION['lang']['total']."</td></tr>
        </thead>
        <tbody>";
        
        $asd=explode('-',$periode);
        $bulan=$asd[1]; $tahun=$asd[0];
        $totalnya=array();
        for ($i = 1; $i <= $lastDate; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $hari=date("D", mktime(0, 0, 0, $bulan, $i, $tahun));
            if($hari=='Sun')$class="class=rowheader"; else $class="class=rowcontent";
            echo "<tr ".$class." bgcolor=#FFAAAA><td align=center>".$ii."</td>";
            $total=0;
            foreach($kolom as $kol){
                echo "<td align=right>".number_format($isinya[$kol][$ii])."</td>";
                $totalnya[$kol]+=$isinya[$kol][$ii];
                $total+=$isinya[$kol][$ii];
            }
            echo "<td align=right>".number_format($total)."</td>";
            echo "</tr>";
        }
            echo "<thead><tr class=rowheader><td align=center>".$_SESSION['lang']['total']."</td>";
            $total=0;
            foreach($kolom as $kol){
                echo "<td align=right>".number_format($totalnya[$kol])."</td>";
                $total+=$totalnya[$kol];
            }
            echo "<td align=right>".number_format($total)."</td>";
            echo "</tr>";
    echo "</tbody></table>";        
    }
    
    break;
    case'pdf':
    $kdPabrik=$_GET['kdPabrik__3'];
    $kdUnit=$_GET['kdUnit__3'];
    $periode=$_GET['periode__3'];
    
    class PDF extends FPDF
    {
        function Header() {
            global $conn;
            global $dbname;
            global $align;
            global $length;
            global $colArr;
            global $title;
        global $kdPabrik;
        global $kdUnit;
        global $periode;

            $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
            $qAlamat=mysql_query($sAlmat) or die(mysql_error());
            $rAlamat=mysql_fetch_assoc($qAlamat);

            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 11;
            $path='images/logo.jpg';
            //$this->Image($path,$this->lMargin,$this->tMargin,70);	
             $this->Image($path,30,5,60);
			$this->SetFont('Arial','B',9);
            $this->SetFillColor(255,255,255);	
            $this->SetX(100);   
            $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
            $this->SetX(100); 		
            $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
            $this->SetX(100); 			
            $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
            $this->Line($this->lMargin,$this->tMargin+($height*4),
            $this->lMargin+$width,$this->tMargin+($height*4));
            $this->Ln();	
            $this->Ln();
            $this->SetFont('Arial','B',11);
            $this->Cell($width,$height,$_SESSION['lang']['rPenerimaanTbs']." / ".$_SESSION['lang']['afdeling']." / ".$_SESSION['lang']['bulan'],0,1,'C');	
            $this->Cell($width,$height,$kdPabrik." ".$periode,0,1,'C');	
            $this->SetFont('Arial','B',7);
            if($kdUnit=='')$kdUnitz=$_SESSION['lang']['all']; else $kdUnitz=$kdUnit;
            
              	$this->Cell(50,$height,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,$kdUnitz,'',0,'L');		
              	$this->Cell(50,$height,'Printed By','',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,$_SESSION['empl']['name'],'',1,'L');		
              	$this->Cell(50,$height,'','',0,'L');
                $this->Cell(5,$height,'','',0,'L');
               	$this->Cell(350,$height,'','',0,'L');		
              	$this->Cell(50,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(350,$height,date('d-m-Y H:i:s'),'',1,'L');		

            $this->SetFont('Arial','B',6);	
            $this->SetFillColor(220,220,220);

    if($kdUnit==''){ // kode unit = seluruhnya, kolom menampilkan unit
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $kolom[$isi['kodeorg']]=$isi['kodeorg'];
            }
        }
    }else{ // kode unit = seluruhnya, kolom menampilkan afdeling
        // cari afdeling buat kolom
        $qweri="select kodeorganisasi from ".$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        while($isi=mysql_fetch_assoc($datanya))
        {
            $kolom[$isi['kodeorganisasi']]=$isi['kodeorganisasi'];
        }
    }
    sort($kolom);
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
            foreach($kolom as $kol){
                $this->Cell(10/100*$width,$height,$kol,1,0,'C',1);		
            }
			
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
            $this->Ln();
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
            $pdf->SetFont('Arial','',6);
    if($periode=='')
    {
        echo"Warning: Silakan mengisi periode.";
        exit();
    }

    if($kdUnit==''){ // kode unit = seluruhnya, kolom menampilkan unit
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$isi['kodeorg']][$tanggal]+=$isi['beratbersih'];
                $kolom[$isi['kodeorg']]=$isi['kodeorg'];
            }
        }
    }else{ // kode unit = seluruhnya, kolom menampilkan afdeling
        // cari afdeling buat kolom
        $qweri="select kodeorganisasi from ".$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        while($isi=mysql_fetch_assoc($datanya))
        {
            $kolom[$isi['kodeorganisasi']]=$isi['kodeorganisasi'];
        }
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $qwe=explode('/',$isi['nospb']);
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$qwe[1]][$tanggal]+=$isi['beratbersih'];
            }
        }
    }
    
    // cari tanggal terakhir dari bulan
    $anyDate = $periode.'-25';    // date format should be yyyy-mm-dd
    list($yr,$mn,$dt) = split('-',$anyDate);    // separate year, month and date
    $timeStamp = mktime(0,0,0,$mn,1,$yr);    //Create time stamp of the first day from the give date.
    list($y,$m,$t) = split('-',date('Y-m-t',$timeStamp)); //Find the last date of the month and separating it
    $lastDayTimeStamp = mktime(0,0,0,$m,$t,$y);//create time stamp of the last date of the give month
    $lastDate = date('d',$lastDayTimeStamp);// Find last day of the month
    
    sort($kolom);
    
    if(empty($isinya)){
        echo "<br><br>Tidak ada data.";
    }else{
        $asd=explode('-',$periode);
        $bulan=$asd[1]; $tahun=$asd[0];
        $totalnya=array();
        for ($i = 1; $i <= $lastDate; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $hari=date("D", mktime(0, 0, 0, $bulan, $i, $tahun));
            if($hari=='Sun')            
            $pdf->SetFillColor(255,192,192);	
            $pdf->Cell(10/100*$width,$height,$ii,1,0,'C',1);		
            $total=0;
            foreach($kolom as $kol){
                $pdf->Cell(10/100*$width,$height,number_format($isinya[$kol][$ii]),1,0,'R',1);		
                $totalnya[$kol]+=$isinya[$kol][$ii];
                $total+=$isinya[$kol][$ii];
            }
            $pdf->Cell(10/100*$width,$height,number_format($total),1,0,'R',1);		
            $pdf->SetFillColor(255,255,255);	
            $pdf->Ln();
			 
        }$pdf->SetFillColor(220,220,220);
            $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);		
            $total=0;
            foreach($kolom as $kol){
                $pdf->Cell(10/100*$width,$height,number_format($totalnya[$kol]),1,0,'R',1);		
                $total+=$totalnya[$kol];
            }
			//$pdf->SetFillColor(220,220,220);
            $pdf->Cell(10/100*$width,$height,number_format($total),1,0,'R',1);		
            $pdf->Ln();
    }    
    
    $pdf->Output();

    break;
    
    case'excel':
    $kdPabrik=$_GET['kdPabrik__3'];
    $kdUnit=$_GET['kdUnit__3'];
    $periode=$_GET['periode__3'];
    
    if($periode=='')
    {
        echo"Warning: Silakan mengisi periode.";
        exit();
    }

    $tab=$_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['bulan']."<br>
        Periode: ".$periode;
    if($kdPabrik)$tab.="<br>Pabrik: ".$kdPabrik;
    if($kdUnit)$tab.="<br>Unit: ".$kdUnit;
    if($kdUnit==''){ // kode unit = seluruhnya, kolom menampilkan unit
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$isi['kodeorg']][$tanggal]+=$isi['beratbersih'];
                $kolom[$isi['kodeorg']]=$isi['kodeorg'];
            }
        }
    }else{ // kode unit = seluruhnya, kolom menampilkan afdeling
        // cari afdeling buat kolom
        $qweri="select kodeorganisasi from ".$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        while($isi=mysql_fetch_assoc($datanya))
        {
            $kolom[$isi['kodeorganisasi']]=$isi['kodeorganisasi'];
        }
        // ambil data timbangan
        $qweri="select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from ".$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
        $datanya=mysql_query($qweri) or die(mysql_error());
        $barisnya=mysql_num_rows($datanya);
        if($barisnya>0)
        {
            while($isi=mysql_fetch_assoc($datanya))
            {
                $qwe=explode('/',$isi['nospb']);
                $tanggal=substr($isi['tanggal'],8,2);
                $isinya[$qwe[1]][$tanggal]+=$isi['beratbersih'];
            }
        }
    }
    
    // cari tanggal terakhir dari bulan
    $anyDate = $periode.'-25';    // date format should be yyyy-mm-dd
    list($yr,$mn,$dt) = split('-',$anyDate);    // separate year, month and date
    $timeStamp = mktime(0,0,0,$mn,1,$yr);    //Create time stamp of the first day from the give date.
    list($y,$m,$t) = split('-',date('Y-m-t',$timeStamp)); //Find the last date of the month and separating it
    $lastDayTimeStamp = mktime(0,0,0,$m,$t,$y);//create time stamp of the last date of the give month
    $lastDate = date('d',$lastDayTimeStamp);// Find last day of the month
    
    sort($kolom);
    
    if(empty($isinya)){
        $tab.= "<br><br>Tidak ada data.";
    }else{
        $tab.="<table cellspacing=1 border=1 class=sortable>
        <thead>
        <tr bgcolor=#dedede>
            <td align=center>".$_SESSION['lang']['tanggal']."</td>";
            foreach($kolom as $kol){
                $tab.= "<td align=center>".$kol."</td>";
            }
        $tab.="<td align=center>".$_SESSION['lang']['total']."</td></tr>
        </thead>
        <tbody>";
        
        $asd=explode('-',$periode);
        $bulan=$asd[1]; $tahun=$asd[0];
        $totalnya=array();
        for ($i = 1; $i <= $lastDate; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $hari=date("D", mktime(0, 0, 0, $bulan, $i, $tahun));
            if($hari=='Sun')$bgcolor="bgcolor=#FFAAAA"; else $bgcolor="";
            $tab.= "<tr ".$bgcolor."><td align=center>".$ii."</td>";
            $total=0;
            foreach($kolom as $kol){
                $tab.= "<td align=right>".number_format($isinya[$kol][$ii])."</td>";
                $totalnya[$kol]+=$isinya[$kol][$ii];
                $total+=$isinya[$kol][$ii];
            }
            $tab.= "<td align=right>".number_format($total)."</td>";
            $tab.= "</tr>";
        }
            $tab.= "<tr bgcolor=#dedede><td align=center>".$_SESSION['lang']['total']."</td>";
            $total=0;
            foreach($kolom as $kol){
                $tab.= "<td align=right>".number_format($totalnya[$kol])."</td>";
                $total+=$totalnya[$kol];
            }
            $tab.= "<td align=right>".number_format($total)."</td>";
            $tab.= "</tr>";
    $tab.= "</tbody></table>";        
    }


                    $tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                    $tglSkrg=date("Ymd");
                    $nop_="LaporanPenerimaanTbs3".$tglSkrg;
                    if(strlen($tab)>0)
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
                    if(!fwrite($handle,$tab))
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
    default:
    break;
}
?>