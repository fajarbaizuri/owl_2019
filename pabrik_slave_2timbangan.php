<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdBrg=$_POST['kdBrg'];
//$tgl=tanggalsystem($_POST['tgl']);
$kdPbrk=$_POST['kdPbrk'];
$tgl=$_POST['tgl'];
                    $txt_tgl_a=substr($tgl,0,2);
                    $txt_tgl_b=substr($tgl,3,2);
                    $txt_tgl_c=substr($tgl,6,4);
                    $tgl=$txt_tgl_c."-".$txt_tgl_b."-".$txt_tgl_a;


    switch($proses)
    {
            case'getData':
            if($kdBrg=='0')
            {
                    echo"<table cellspacing=1 border=0 class=sortable>
                            <thead class=rowheader>
                            <tr>
                                    <td>No.</td>
                                    <td>".$_SESSION['lang']['namabarang']."</td>
                                    <td>".$_SESSION['lang']['noTiket']."</td>
                                    <td>".$_SESSION['lang']['kodenopol']."</td>
                                    <td>".$_SESSION['lang']['transportasi']."</td>    
                                    <td>".$_SESSION['lang']['beratMasuk']."</td>
                                    <td>".$_SESSION['lang']['beratKeluar']."</td>
                                    <td>".$_SESSION['lang']['beratBersih']."</td>
                                    <td>Potongan</td>
                                    <td>Normal</td>
                                    <td>".$_SESSION['lang']['jammasuk']."</td>
                                    <td>".$_SESSION['lang']['jamkeluar']."</td>
                                    <td>".$_SESSION['lang']['unit']."</td>
                                    <td>".$_SESSION['lang']['supplier']."</td>
                                    <td>".$_SESSION['lang']['sopir']."</td>
                                    <td>".$_SESSION['lang']['brondolan']."</td>
                            </tr>
                            </thead>
                            <tbody>
                    ";

                    $sTrans="select * from ".$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."'";
                    $qTrans=mysql_query($sTrans) or die(mysql_error());
//echo($sTrans);
                    while($rTrans=mysql_fetch_assoc($qTrans))
                    {
                            $no+=1;
                            $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rTrans['kodebarang']."'";
                            $qBrg=mysql_query($sBrg) or die(mysql_error());
                            $rBrg=mysql_fetch_assoc($qBrg);

                            if(($rTrans['kodebarang']=='40000001')||($rTrans['kodebarang']=='40000005')||($rTrans['kodebarang']=='40000002')||($rTrans['kodebarang']=='40000004'))
                            {
                                    $sKontrak="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$rTrans['nokontrak']."'";//echo $sKontrak;exit();
                                    $qKontrak=mysql_query($sKontrak) or die(mysql_error($conn));
                                    $rKontrak=mysql_fetch_assoc($qKontrak);
                                    $sSupp="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'"; //echo $sSupp;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namacustomer'];
                            }
                            elseif($rTrans['kodebarang']=='40000003')
                            {
                                    //$sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();
                                    $tes="select kodetimbangan from ".$dbname.".log_5supplier";
                                    $tes2=mysql_query($tes);
                                    $tes3=mysql_fetch_assoc($tes2);

                                    if ($tes3['kodetimbangan']=='')
                                    {
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();		
                                    }
                                    else
                                    {
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$rTrans['kodecustomer']."'";
                                    }

                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namasupplier'];

                            }

                            #transporter
                            $rTRP='';
                            $sTRP="select TRPNAME  from ".$dbname.".pabrik_transporter where TRPCODE='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();
                            $qTRP=mysql_query($sTRP) or die(mysql_error());
                            $rTRP=mysql_fetch_assoc($qTRP) ;

                            echo"<tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$rBrg['namabarang']."</td>
                            <td>".$rTrans['notransaksi']."</td>
                            <td>".$rTrans['nokendaraan']."</td>
                            <td>".$rTRP['TRPNAME']."</td>
                            <td align=\"right\">".number_format($rTrans['beratmasuk'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratkeluar'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratbersih'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['kgpotsortasi'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratbersih']-$rTrans['kgpotsortasi'],2)."</td>    
                            <td>".$rTrans['jammasuk']."</td>
                            <td>".$rTrans['jamkeluar']."</td>";
                            if($rTrans['kodeorg']!='')
                            {
                            echo"<td>".$rTrans['kodeorg']."</td>
                                    <td></td>";
                            }
                            else
                            {
                                    echo"<td></td>
                                    <td>".$hsl."</td>";
                            }
                            echo"<td>".$rTrans['supir']."</td>
                            <td align=\"right\">".number_format($rTrans['brondolan'],2)."</td>
                            </tr>
                            ";
                            $totBeratMsk+=$rTrans['beratmasuk'];
                            $totBeratKlr+=$rTrans['beratkeluar'];
                            $totBeratBrs+=$rTrans['beratbersih'];
                            $totBrondolan+=$rTrans['brondolan'];
                            $pot+=$rTrans['kgpotsortasi'];
                            $tnor+=$rTrans['beratbersih']-$rTrans['kgpotsortasi'];
                    }
                    echo"<tr class=rowcontent><td colspan=5>Total</td>
                        <td align=\"right\">".number_format($totBeratMsk,2)."</td>
                        <td align=\"right\">".number_format($totBeratKlr,2)."</td>
                            <td align=\"right\">".number_format($totBeratBrs,2)."</td>
                             <td align=\"right\">".number_format($pot,2)."</td>
                             <td align=\"right\">".number_format($tnor,2)."</td>    
                            <td colspan=5></td><td>".number_format($totBrondolan,2)."</td></tr>";
                    echo"</tbody></table>";

            }
            elseif($kdBrg!='0')
            {
                    echo"<table cellspacing=1 border=0 class=sortable>
                            <thead class=rowheader>
                            <tr>
                                    <td>No.</td>
                                    <td>".$_SESSION['lang']['noTiket']."</td>
                                    <td>".$_SESSION['lang']['kodenopol']."</td>
                                    <td>".$_SESSION['lang']['transportasi']."</td>    
                                    <td>".$_SESSION['lang']['beratMasuk']."</td>
                                    <td>".$_SESSION['lang']['beratKeluar']."</td>
                                    <td>".$_SESSION['lang']['beratBersih']."</td>
                                    <td>Potongan</td>
                                    <td>Normal</td>    
                                    <td>".$_SESSION['lang']['jammasuk']."</td>
                                    <td>".$_SESSION['lang']['jamkeluar']."</td>
                                    <td>".$_SESSION['lang']['unit']."</td>
                                    <td>".$_SESSION['lang']['supplier']."</td>
                                    <td>".$_SESSION['lang']['sopir']."</td>
                                    <td>".$_SESSION['lang']['brondolan']."</td>
                            </tr>
                            </thead>
                            <tbody>
                    ";

                    $sTrans="select * from ".$dbname.".pabrik_timbangan where tanggal like '".$tgl."%' and millcode='".$kdPbrk."' and kodebarang='".$kdBrg."'";
                    //echo $sTrans;
                    $qTrans=mysql_query($sTrans) or die(mysql_error());
                    while($rTrans=mysql_fetch_assoc($qTrans))
                    {
                            $no+=1;
                            $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rTrans['kodebarang']."'";
                            $qBrg=mysql_query($sBrg) or die(mysql_error());
                            $rBrg=mysql_fetch_assoc($qBrg);

                            if(($rTrans['kodebarang']=='40000001')||($rTrans['kodebarang']=='40000005')||($rTrans['kodebarang']=='40000002')||($rTrans['kodebarang']=='40000004'))
                            {
                                    $sKontrak="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$rTrans['nokontrak']."'";//echo $sKontrak;exit();
                                    $qKontrak=mysql_query($sKontrak) or die(mysql_error($conn));
                                    $rKontrak=mysql_fetch_assoc($qKontrak);
                                    $sSupp="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'"; //echo $sSupp;exit();//echo $sCust;exit();
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namacustomer'];
                            }
                            elseif($rTrans['kodebarang']=='40000003')
                            {
                                    $tes="select kodetimbangan from ".$dbname.".log_5supplier";
                                    $tes2=mysql_query($tes);
                                    $tes3=mysql_fetch_assoc($tes2);

                                    if ($tes3['kodetimbangan']=='')
                                    {
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();		
                                    }
                                    else
                                    {
                                            $sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$rTrans['kodecustomer']."'";
                                    }
                                    //$sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$rTrans['kodecustomer']."'"; //echo $sCust;exit();		
                                    #indra sementara sampe jelas kebijakan
                                    //$sSupp="select namasupplier  from ".$dbname.".log_5supplier where kodetimbangan='".$rTrans['kodecustomer']."'";
                                    $qSupp=mysql_query($sSupp) or die(mysql_error());
                                    $rSupp=mysql_fetch_assoc($qSupp) ;
                                    $hsl=$rSupp['namasupplier'];

                            }
               // $a= $rTrans['kodeorg'];           
              // exit("Error:$a");
                                                #transporter
                            $rTRP='';
                            $sTRP="select TRPNAME  from ".$dbname.".pabrik_transporter where TRPCODE='".$rTrans['trpcode']."'"; //echo $sCust;exit();
                            $qTRP=mysql_query($sTRP) or die(mysql_error());
                            $rTRP=mysql_fetch_assoc($qTRP) ;

                            echo"<tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$rTrans['notransaksi']."</td>
                            <td>".$rTrans['nokendaraan']."</td>
                            <td>".$rTRP['TRPNAME']."</td>                                    
                            <td align=\"right\">".number_format($rTrans['beratmasuk'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratkeluar'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratbersih'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['kgpotsortasi'],2)."</td>
                            <td align=\"right\">".number_format($rTrans['beratbersih']-$rTrans['kgpotsortasi'],2)."</td>    
                            <td align=\"right\">".$rTrans['jammasuk']."</td>
                            <td align=\"right\">".$rTrans['jamkeluar']."</td>";
                            if($rTrans['kodeorg']!='')
                            {
                            echo"<td>".$rTrans['kodeorg']."</td>
                                    <td></td>";
                            }
                            else
                            {
                                    echo"<td></td>
                                    <td>".$hsl."</td>";
                            }
                            echo"<td>".$rTrans['supir']."</td>
                            <td>".number_format($rTrans['brondolan'],2)."</td>
                            </tr>
                            ";
                            $totBeratMsk+=$rTrans['beratmasuk'];
                            $totBeratKlr+=$rTrans['beratkeluar'];
                            $totBeratBrs+=$rTrans['beratbersih'];
                            $totBrondolan+=$rTrans['brondolan'];
                            $pot+=$rTrans['kgpotsortasi'];
                            $tnor+=$rTrans['beratbersih']-$rTrans['kgpotsortasi'];                            
                    }
                    echo"<tr class=rowcontent><td colspan=4>Total</td>
                        <td  align=\"right\">".number_format($totBeratMsk,2)."</td>
                        <td  align=\"right\">".number_format($totBeratKlr,2)."</td>
                        <td  align=\"right\">".number_format($totBeratBrs,2)."</td>
                        <td  align=\"right\">".number_format($pot,2)."</td>
                        <td  align=\"right\">".number_format($tnor,2)."</td>    
                         <td colspan=5></td><td  align=\"right\">".number_format($totBrondolan,2)."</td></tr>";
                    echo"</tbody></table>";

            }
            break;

            default:
            break;
    }
?>