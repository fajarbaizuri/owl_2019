<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

    $kdBrg=$_GET['kdBrg'];
    $kdPbrk=$_GET['kdPbrk'];
    $tgl=$_GET['tgl'];
//$tgl=$_POST['tgl'];
                $txt_tgl_a=substr($tgl,0,2);
                $txt_tgl_b=substr($tgl,3,2);
                $txt_tgl_c=substr($tgl,6,4);
                $tgl=$txt_tgl_c."-".$txt_tgl_b."-".$txt_tgl_a;

/*	print"<pre>";
    print_r($_GET);
    print"<pre>";*/

//======================================


//ambil namapt
$sOrg="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdPbrk."' ";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$rOrg=mysql_fetch_assoc($qOrg);

$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rOrg['induk']."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namapt=strtoupper($bar->namaorganisasi);
}
    if($kdBrg=='0')
    {
                    //echo"warning:masuk vvv";
                    $strx="select * from ".$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' order by tanggal asc";
                    //echo $strx;exit();
                    $stream.="
                    <table>
                    <tr><td colspan=12 align=center>".$_SESSION['lang']['laporanPabrikTimbangan']."</td></tr>
                    <tr><td colspan=3>".$_SESSION['lang']['pt']." : ".$namapt."</td></tr>";
                    $stream.="<tr><td colspan=3>".$_SESSION['lang']['tanggal']." : ".$tgl."</td></tr>
                    <tr><td colspan=3>".$_SESSION['lang']['kdpabrik']." : ".$kdPbrk."</td></tr>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    </table>
                    <table border=1>
                                <tr>
                                  <td bgcolor=#DEDEDE align=center>No.</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
                                   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noTiket']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodenopol']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['transportasi']."</td>    
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratMasuk']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratKeluar']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']."</td>
                                    <td>Potongan</td>
                                    <td>Normal</td>                                                 
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammasuk']."</td>	
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamkeluar']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['unit']."</td>
                                   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>
                                </tr>";

            $resx=mysql_query($strx);
            $no=0;
            while($barx=mysql_fetch_assoc($resx))
            {
                    $no+=1;
                    $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barx['kodebarang']."'";
                    $qBrg=mysql_query($sBrg) or die(mysql_error());
                    $rBrg=mysql_fetch_assoc($qBrg);		
                    if($barx['kodecustomer']!='')
                    {
                            if(($barx['kodebarang']=='40000001')||($barx['kodebarang']=='40000005')||($barx['kodebarang']=='40000002')||($barx['kodebarang']=='40000004'))
                            {
                                    $sKontrak="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$barx['nokontrak']."'";//echo $sKontrak;exit();
                                    $qKontrak=mysql_query($sKontrak) or die(mysql_error($conn));
                                    $rKontrak=mysql_fetch_assoc($qKontrak);
                                    $sSupp="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'"; //echo $sSupp;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namacustomer'];
                            }
                            elseif($barx['kodebarang']=='40000003')
                            {
                                    $sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$barx['kodecustomer']."'"; //echo $sCust;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namasupplier'];

                            }
                    }
                   #transporter
                            $rTRP='';
                            $sTRP="select TRPNAME  from ".$dbname.".pabrik_transporter where TRPCODE='".$barx['trpcode']."'"; //echo $sCust;exit();
                            $qTRP=mysql_query($sTRP) or die(mysql_error());
                            $rTRP=mysql_fetch_assoc($qTRP) ;

                    $stream.="	<tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$rBrg['namabarang']."</td>
                            <td>".$barx['notransaksi']."</td>
                            <td>".$barx['nokendaraan']."</td>
                            <td>".$rTRP['TRPNAME']."</td>    
                            <td>".number_format($barx['beratmasuk'],2)."</td>
                            <td>".number_format($barx['beratkeluar'],2)."</td>
                            <td>".number_format($barx['beratbersih'],2)."</td>
                            <td>".number_format($barx['kgpotsortasi'],2)."</td>
                            <td>".number_format($barx['beratbersih']-$barx['kgpotsortasi'],2)."</td>    
                            <td>".$barx['jammasuk']."</td>
                            <td>".$barx['jamkeluar']."</td>";


                            if($barx['kodeorg']!='')
                            {
                            $stream.="<td>".$barx['kodeorg']."</td>
                            <td></td>";
                            }
                            else
                            {
                                    $stream.="<td></td>
                            <td>".$rSupp['namasupplier']."</td>";
                            }

                            $stream.="
                            <td>".$barx['supir']."</td>
                            <td>".$barx['brondolan']."</td>	
                            </tr>";
                            $totBeratMsk+=$barx['beratmasuk'];
                            $totBeratKlr+=$barx['beratkeluar'];
                            $totBeratBrs+=$barx['beratbersih'];
                            $totBrondolan+=$barx['brondolan'];
                            $nor+=$barx['beratbersih']-$barx['kgpotsortasi'];
                            $tpot+=$barx['kgpotsortasi'];
            }
            $stream.="<tr class=rowcontent><td colspan=5>Total</td><td>".$totBeratMsk."</td><td>".$totBeratKlr."</td>
                <td>".$totBeratBrs."</td>
                  <td>".$tpot."</td>
                  <td>".$nor."</td>    
                    <td colspan=5></td><td>".$totBrondolan."</td></tr>";
    }
    elseif($kdBrg!='0')
    {
            $strx="select * from ".$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' and kodebarang='".$kdBrg."' order by tanggal asc";
                    //echo $strx;exit();
            $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
            $qBrg=mysql_query($sBrg) or die(mysql_error());
            $rBrg=mysql_fetch_assoc($qBrg);		
                    $stream.="
                    <table>
                    <tr><td colspan=12 align=center>".$_SESSION['lang']['laporanPabrikTimbangan']."</td></tr>
                    <tr><td colspan=3>".$_SESSION['lang']['pt']." : ".$namapt."</td></tr>";
                    $stream.="<tr><td colspan=3>".$_SESSION['lang']['tanggal']." : ".$tgl."</td></tr>
                    <tr><td colspan=3>".$_SESSION['lang']['kdpabrik']." : ".$kdPbrk."</td></tr>
                    <tr><td colspan=3>".$_SESSION['lang']['namabarang']." : ".$rBrg['namabarang']."</td></tr>
                    <tr><td colspan=3>&nbsp;</td></tr>
                    </table>
                    <table border=1>
                                <tr>
                                  <td bgcolor=#DEDEDE align=center>No.</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noTiket']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodenopol']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['transportasi']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratMasuk']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratKeluar']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']."</td>
                                    <td>Potongan</td>
                                    <td>Normal</td>                                            
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jammasuk']."</td>	
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jamkeluar']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['unit']."</td>
                                   <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sopir']."</td>
                                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['brondolan']."</td>
                                </tr>";

            $resx=mysql_query($strx);
            $no=0;
            while($barx=mysql_fetch_assoc($resx))
            {
                    $no+=1;

                    if($barx['kodecustomer']!='')
                    {
                            if(($barx['kodebarang']=='40000001')||($barx['kodebarang']=='40000005')||($barx['kodebarang']=='40000002')||($barx['kodebarang']=='40000004'))
                            {
                                    $sKontrak="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$barx['nokontrak']."'";//echo $sKontrak;exit();
                                    $qKontrak=mysql_query($sKontrak) or die(mysql_error($conn));
                                    $rKontrak=mysql_fetch_assoc($qKontrak);
                                    $sSupp="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'"; //echo $sSupp;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                            }
                            elseif($barx['kodebarang']=='40000003')
                            {

                                    $tes="select kodetimbangan from ".$dbname.".log_5supplier";
                                    $tes2=mysql_query($tes);
                                    $tes3=mysql_fetch_assoc($tes2);

                                    if ($tes3['kodetimbangan']=='')
                                    {
                                            //$sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();		
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$barx['kodecustomer']."'";
                                    }
                                    else
                                    {
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$barx['kodecustomer']."'";
                                    }

                                    //$sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$barx['kodecustomer']."'"; //echo $sCust;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;


                            }
                    }
                   #transporter
                            $rTRP='';
                            $sTRP="select TRPNAME  from ".$dbname.".pabrik_transporter where TRPCODE='".$barx['trpcode']."'"; //echo $sCust;exit();
                            $qTRP=mysql_query($sTRP) or die(mysql_error());
                            $rTRP=mysql_fetch_assoc($qTRP) ;
                    $stream.="	<tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$barx['notransaksi']."</td>
                            <td>".$barx['nokendaraan']."</td>
                            <td>".$rTRP['TRPNAME']."</td>    
                            <td>".number_format($barx['beratmasuk'],2)."</td>
                            <td>".number_format($barx['beratkeluar'],2)."</td>
                            <td>".number_format($barx['beratbersih'],2)."</td>	
                            <td>".number_format($barx['kgpotsortasi'],2)."</td>
                            <td>".number_format($barx['beratbersih']-$barx['kgpotsortasi'],2)."</td>                                   
                            <td>".$barx['jammasuk']."</td>
                            <td>".$barx['jamkeluar']."</td>";


                            if($barx['kodeorg']!='')
                            {
                            $stream.="<td>".$barx['kodeorg']."</td>
                            <td></td>";
                            }
                            else
                            {
                                    $stream.="<td></td>
                            <td>".$rSupp['namasupplier']."</td>";
                            }

                            $stream.="
                            <td>".$barx['supir']."</td>
                            <td>".$barx['brondolan']."</td>	
                            </tr>";
                            $totBeratMsk+=$barx['beratmasuk'];
                            $totBeratKlr+=$barx['beratkeluar'];
                            $totBeratBrs+=$barx['beratbersih'];
                            $totBrondolan+=$barx['brondolan'];
                            $nor+=$barx['beratbersih']-$barx['kgpotsortasi'];
                            $tpot+=$barx['kgpotsortasi'];                            
            }
            $stream.="<tr class=rowcontent><td colspan=4>Total</td><td>".$totBeratMsk."</td>
                <td>".$totBeratKlr."</td><td>".$totBeratBrs."</td>
                  <td>".$tpot."</td>
                  <td>".$nor."</td>      
                 <td colspan=5></td><td>".$totBrondolan."</td></tr>";
    }



    //echo "warning:".$strx;
//=================================================

    $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="ReportWB";
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
?>