<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

// get post =========================================================================
$proses=$_GET['proses'];
$periode=$_POST['periode'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
if($periode=='')$periode=$_GET['periode'];
if($kdOrg=='')$kdOrg=$_GET['kdOrg'];
if($kdOrg=='')$kdOrg=$_SESSION['empl']['lokasitugas'];

// get namaorganisasi =========================================================================
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $nmOrg=$rOrg['namaorganisasi'];
        }
        if(!$nmOrg)$nmOrg=$kdOrg;

// determine begin end =========================================================================
        $lok=substr($kdOrg,0,4); //$_SESSION['empl']['lokasitugas'];
        $sDatez = "select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode = '".$periode."' and kodeorg= '".$lok."'";
        $qDatez=mysql_query($sDatez) or die(mysql_error($conn));
        while($rDatez=mysql_fetch_assoc($qDatez))
        {
                $tanggalMulai=$rDatez['tanggalmulai'];
                $tanggalSampai=$rDatez['tanggalsampai'];
        }

 //ambil kemandoran
     $stq="select a.nikmandor,a.karyawanid b.namakaryawan from ".$dbname.".kebun_5nourutmandor a
                left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid where aktif=1";
     $resq=mysql_query($stq);
     while($barq=mysql_fetch_object($resq)){
         $mand[$barq->karyawanid]=$barq->namakaryawan;
     }
        
function dates_inbetween($date1, $date2)
{
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
    for($x = 1; $x < $days_diff; $x++)
        {
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[] = date('Y-m-d',$date2);
    return $dates_array;
}
$tgltgl = dates_inbetween($tanggalMulai, $tanggalSampai);
#ambil data premi
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	
		
$str="select a.karyawanid,a.tanggal,sum(a.upahkerja+a.upahpremi-a.rupiahpenalty) as upahpremi,b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a.karyawanid";  
//ambil data di perawatan
$sql="select a.karyawanid,a.tanggal,sum(a.umr+a.insentif) as upahpremi,b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".kebun_kehadiran_new_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a. karyawanid";
/*
$sql="select a.karyawanid,a.tanggal,sum(a.umr+a.insentif) as upahpremi,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_kehadiran_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a. karyawanid";
*/
//ambil data kemandoran
//$sql2="select a.karyawanid,a.tanggal,sum(a.premiinput) as upahpremi,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_premikemandoran a 
//     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
//     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
//     where substr(a.tanggal,1,7)='".$periode."' and premiinput!=0 and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
//     and a.posting=1
//     group by a.karyawanid,tanggal 
//     order by a. karyawanid";
//ambil data traksi
//premi traksi
$sql3="select a.idkaryawan as karyawanid,a.tanggal,sum(a.upah+a.premi-a.penalty) as upahpremi, b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".vhc_runhk a 
     left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.idkaryawan,tanggal 
     order by a. idkaryawan";

}
else
{
    if(strlen($kdOrg)>4)
    {
        $whr="and b.subbagian='".$kdOrg."' ";
        $whrw=" subbagian='".$kdOrg."' ";
    }
    else
    {
        $whr="and b.lokasitugas='".$kdOrg."' ";
        $whrw=" lokasitugas='".$kdOrg."' ";
    }
$str="select a.karyawanid,a.tanggal,sum(a.upahkerja+a.upahpremi-a.rupiahpenalty) as upahpremi,b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."' ".$whr." and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid";   

//ambil data di perawatan
$sql="select a.karyawanid,a.tanggal,sum(a.umr+a.insentif) as upahpremi,b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".kebun_kehadiran_new_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  ".$whr." and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')

     group by a.karyawanid,tanggal
     order by a.karyawanid";    
	 
/*
$sql="select a.karyawanid,a.tanggal,sum(a.umr+a.insentif) as upahpremi,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_kehadiran_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  ".$whr." and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')

     group by a.karyawanid,tanggal
     order by a.karyawanid";    
*/
//ambil data kemandoran
//$sql2="select a.karyawanid,a.tanggal,sum(a.premiinput) as upahpremi,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_premikemandoran a 
//     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
//     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
//     where substr(a.tanggal,1,7)='".$periode."' and premiinput!=0  ".$whr."  and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
//     and a.posting=1
//     group by a.karyawanid,tanggal 
//     order by a. karyawanid";
//ambil data traksi
//premi traksi
$sql3="select a.idkaryawan as karyawanid,a.tanggal,sum(a.upah+a.premi-a.penalty) as upahpremi, b.kodejabatan,c.namajabatan,b.kodegolongan from ".$dbname.".vhc_runhk a 
     left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
     left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'    ".$whr."  and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.idkaryawan,tanggal 
     order by a. idkaryawan";
}
 //echo $sql3;
$res=mysql_query($str);
$jab=Array();
$sts=Array();
$prem=Array();
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->karyawanid]=$bar->namajabatan;
	$sts[$bar->karyawanid]=$bar->kodegolongan;
    $prem[$bar->karyawanid][$bar->tanggal]=$bar->upahpremi;    
}
$qData=mysql_query($sql);
//
while($rData=mysql_fetch_object($qData))
{
    $jab[$rData->karyawanid]=$rData->namajabatan;
	$sts[$rData->karyawanid]=$rData->kodegolongan;
    $prem[$rData->karyawanid][$rData->tanggal]+=$rData->upahpremi;   
}
//$qData2=mysql_query($sql2);
//while($rData2=mysql_fetch_object($qData2))
//{
//    $jab[$rData2->karyawanid]=$rData2->namajabatan;
//    $prem[$rData2->karyawanid][$rData2->tanggal]=$rData2->upahpremi;   
//}
$qData3=mysql_query($sql3);
//echo mysql_error($conn);
while($rData3=mysql_fetch_object($qData3))
{
    if($rData3->upahpremi!=0)
    {
    $jab[$rData3->karyawanid]=$rData3->namajabatan;
	$sts[$rData3->karyawanid]=$rData3->kodegolongan;
    $prem[$rData3->karyawanid][$rData3->tanggal]+=$rData3->upahpremi;   
    }
}

//Ambil lembur:
$stry="select karyawanid,tanggal,uangkelebihanjam from ".$dbname.".sdm_laburdt where substr(a.tanggal,1,7)='".$periode."'
            and left(kodeorg,4)='".substr($kdOrg,0,4)."'";
$resy=mysql_query($stry);
while($bary=mysql_fetch_object($resy)){
     $prem[$bary->karyawanid][$bary->tanggal]+=$bary->uangkelebihanjam;     
}


#ambil karyawan
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".substr($kdOrg,0,4)."'
    and (tanggalkeluar>'".$tanggalSampai."' or tanggalkeluar='0000-00-00')";   
}
 else {
$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$whrw."
    and (tanggalkeluar>'".$tanggalSampai."' or tanggalkeluar='0000-00-00')";    
}


$res=mysql_query($str);
$karid=Array();
while($bar=mysql_fetch_object($res))
{
    if($jab[$bar->karyawanid]!='')#jika terdaftar pada premi maka sertakan
        $karid[$bar->karyawanid]=$bar->namakaryawan;
}
$brd=0;
$bgclr="align='center'";
if($proses=='excel')
{
    $brd=1;
    $bgclr="bgcolor='#DEDEDE' align='center'";
}
$stream="Laporan_Pendapatan_per_hari_".$kdOrg."_".$periode; 
#preview: nampilin header ================================================================================
        $stream.="<table cellspacing='1' border='".$brd."' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td ".$bgclr.">No</td>
        <td ".$bgclr.">".$_SESSION['lang']['nama']."</td>
        <td ".$bgclr.">".$_SESSION['lang']['jabatan']."</td>
		<td ".$bgclr.">Status</td>
         <td ".$bgclr.">".$_SESSION['lang']['mandor']."</td>";
        foreach($tgltgl as $ar => $isi)
        {
                $qwe=date('D', strtotime($isi));
                $stream.="<td width=5px  ".$bgclr.">";
                if($qwe=='Sun')
                    $stream.="<font color=red>".substr($isi,8,2)."</font>"; 
                else 
                    $stream.=(substr($isi,8,2)); 
                $stream.="</td>";
        }
        $stream.="<td ".$bgclr.">Jumlah</td></tr></thead>
        <tbody>";
           # preview: nampilin data ================================================================================
        foreach($karid as $id=>$val)
        {
            $no+=1;
            $totperkar=0;
            $stream.="<tr class=rowcontent><td>".$no."</td>
            <td>".$val."</td>
            <td>".$jab[$id]."</td>
			<td>".$sts[$id]."</td>
            <td>".$mand[$id]."</td>";
            foreach($tgltgl as $key=>$tangval)
            {	             
                    $stream.="<td align=right>".number_format($prem[$id][$tangval])."</td>";
                    $tottgl[$tangval]+=$prem[$id][$tangval];
                    $totperkar+=$prem[$id][$tangval];
            }
            $stream.="<td align=right>".number_format($totperkar)."</td></tr>";
        }  
           # preview: nampilin total ================================================================================
        $stream.="<thead class=rowheader>
        <tr>
        <td colspan=5>Total</td>";
        foreach($tgltgl as $ar => $isi)
        {
                $stream.="<td align=right>".number_format($tottgl[$isi])."</td>";
                $total+=$tottgl[$isi];
        }
        $stream.="<td align=right>".number_format($total)."</td>";
        $stream.="</tbody></table>";

switch($proses)
{
        case'preview':
          echo $stream;
        break;
        case 'excel':
            $nop_="Laporan_premi_per_hari_".$kdOrg."_".$periode;
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
        case'pdf':

        class PDF extends FPDF
        {
                function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $periode;
                global $nmOrg;
                global $tgltgl;       


                                $jmlHari=count($tgltgl);
                                $jmlHari=$jmlHari*10;
                                $cols=247.5;
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

                $this->SetFont('Arial','B',10);
                                $this->Cell((20/100*$width)-5,$height,strtoupper($_SESSION['lang']['laporanPremi']),'',0,'L');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['unit'])." :". $nmOrg,'',0,'L');
                                $this->Ln();
                                $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". $periode,'',0,'L');
                                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','B',6);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);		
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);	

                                $smpng=$this->GetX();
                                $atas=$this->GetY();
                                $this->SetY($atas);
                                $this->SetX($smpng);
                                $this->SetFont('Arial','B',4);
                                foreach($tgltgl as $ar => $isi)
                                {
                                        $this->Cell(2.5/100*$width,$height,substr($isi,8,2),1,0,'C',1);	
                                        $akhirX=$this->GetX();
                                }	
                                $this->SetY($this->GetY());
                                $this->SetX($akhirX);
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);
            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;

                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);

                foreach($karid as $id=>$val)
                {
                    $nor+=1;
                    $totperkar=0;
                    $pdf->SetFont('Arial','',6);
                    $pdf->Cell(3/100*$width,$height,$nor,1,0,'C',1);
                    $pdf->Cell(8/100*$width,$height,$val,1,0,'L',1);		
                    $pdf->Cell(6/100*$width,$height,$jab[$id],1,0,'L',1);	
                    $pdf->SetFont('Arial','',4);
                    foreach($tgltgl as $ar => $isi)
                    {
                            $pdf->Cell(2.5/100*$width,$height,number_format($tottgl[$isi]),1,0,'R',1);	
                            $total+=$tottgl[$isi];
                    }	
                     $pdf->Cell(5/100*$width,$height,number_format($total),1,1,'R',1);
                }

              $pdf->Output();
            break;
}    
?>