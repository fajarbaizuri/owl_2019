<?php
// file creator: dhyaz sep 20, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$cekapa=$_POST['cekapa'];
if($cekapa=='')$cekapa=$_GET['cekapa'];



if($cekapa=='getdet')
{
//exit("Error:aaaaaa");
$tahunbudget=$_POST['tahunbudget'];
$jumlahpertahun2=$_POST['jumlahpertahun2'];
	//exit("Error:AAAAA");
		$ind="select rpperjam from ".$dbname.".bgt_biaya_ws_per_jam where tahunbudget='".$tahunbudget."' ";
		//exit("Error:$ind");
		//echo $ind;
		$dra=mysql_query($ind) or die(mysql_error());
		$wib=mysql_fetch_assoc($dra);
			$indra=$wib['rpperjam'];
		echo $indra;	
		//exit("Error:$indra");
}

		
		
		


//cek mesin untuk station
if($cekapa=='station'){
    $station=$_POST['station'];
    $str="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
        where induk = '".$station."'";
    $lempar="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $lempar.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
    echo $lempar;        
}

//cek mesin untuk station
if($cekapa=='updatetahuntutup'){
    $str="select distinct tahunbudget from ".$dbname.".bgt_budget
        where tutup = '0' and kodebudget != 'UMUM' and tipebudget = 'MILL' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'
        order by tahunbudget desc
        ";
    $lempar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $lempar.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    echo $lempar;        
}

//tampilkan data tab0
if($cekapa=='tab0'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $hkef='';
    $hkef.="<table id=container9 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'EXPL%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(0,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data kodevhc tab3
if($cekapa=='kendaraan'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $hkef='';
    $str="select * from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi
        where tahunbudget = '".$tahunbudget."'";
    $res=mysql_query($str);
    $hkef="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    while($bar= mysql_fetch_object($res))
    {
            $hkef.="<option value='".$bar->kodevhc."'>".$bar->kodevhc."</option>";
    }
    echo $hkef;        
    
//    $str="select * from ".$dbname.".bgt_vhc_jam
//        where unitalokasi like '".$_SESSION['empl']['lokasitugas']."%' and tahunbudget = '".$tahunbudget."'";
//    $res=mysql_query($str);
//    $hkef="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//    while($bar= mysql_fetch_object($res))
//    {
//            $hkef.="<option value='".$bar->kodevhc."'>".$bar->kodevhc."</option>";
//    }
//    echo $hkef;        
}

//delete row, all tab berdasarkan kunci
if($cekapa=='delete0'){
    $kunci=$_POST['kunci'];
    $str="delete from ".$dbname.".bgt_budget 
    where kunci='".$kunci."'";
    if(mysql_query($str))
    {
        $str2="delete from ".$dbname.".bgt_distribusi 
        where kunci='".$kunci."'";
        if(mysql_query($str2))
        {}
        else
        {echo " Gagal4,".addslashes(mysql_error($conn));}
    }
    else
    {echo " Gagal3,".addslashes(mysql_error($conn));}
}

//cek regional berdasarkan kodews(4) vs bgt_regional_assignment
if($cekapa=='regional'){
    $mesin=$_POST['mesin'];
    $kodeorg=substr($mesin,0,4);
    $str="select * from ".$dbname.".bgt_regional_assignment
        where kodeunit = '".$kodeorg."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->regional;
    }
    echo $hkef;        
}

//harga barang tab1 dan tab2
if($cekapa=='barang'){
    $kodebarang1=$_POST['kodebarang1'];
    $tahunbudget=$_POST['tahunbudget'];
    $regional=$_POST['regional'];
    $str="select * from ".$dbname.".bgt_masterbarang
        where closed=1 and kodebarang = '".$kodebarang1."' and regional ='".$regional."' and tahunbudget ='".$tahunbudget."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->hargasatuan;
    }
    echo $hkef;        
}

//tampilkan data tab1
if($cekapa=='tab1'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
//kamus namabarang
    $strJ="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $barang[$barJ->kodebarang]=$barJ->namabarang;
    }

    $hkef='';
    $hkef.="<table id=container8 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
			<td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['jenis']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'M%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
			<td align=center>".$bar->noakun."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->kodebarang."</td>
            <td align=left>".$barang[$bar->kodebarang]."</td>
            <td align=center>".$bar->keterangan."</td>
            <td align=right>".number_format($bar->jumlah)."</td>
            <td align=left>".$bar->satuanj."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(1,".$bar->kunci.")\" title=\"Hapus\">
                </td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab2
if($cekapa=='tab2'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $hkef='';
    $hkef.="<table id=container7 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
			
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
			<td align=center>Total Jam</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget = 'PKSM' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".number_format($bar->rupiah)."</td>
			<td align=right>".number_format($bar->jamservice,2)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(2,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//harga barang tab1 dan tab2
if($cekapa=='vhc'){
    $kodevhc3=$_POST['kodevhc3'];
    $tahunbudget=$_POST['tahunbudget'];
//    $regional=$_POST['regional'];
    $str="select * from ".$dbname.".bgt_biaya_ken_per_jam
        where kodevhc = '".$kodevhc3."' and tahunbudget ='".$tahunbudget."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='0';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->rpperjam;
    }
    echo $hkef;        
}

//tampilkan data tab3
if($cekapa=='tab3'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
//kamus namaakun
    $strJ="select * from ".$dbname.".keu_5akun where tipeakun='Biaya' and detail=1";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $akun[$barJ->noakun]=$barJ->namaakun;
    }

    $hkef='';
//            <td align=center>".$_SESSION['lang']['noakun']."</td>
//            <td align=center>".$_SESSION['lang']['namaakun']."</td>
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodevhc']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'VHC%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$mesin."'";
    $res=mysql_query($str);
    $no=1;
//            <td align=right>".$bar->noakun."</td>
//            <td align=left>".$akun[$bar->noakun]."</td>
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->kodevhc."</td>
            <td align=right>".$bar->jumlah."</td>
            <td align=left>".$bar->satuanj."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(3,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab5 :  Kontrak
if($cekapa=='tab5'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
	$mesin=$_POST['mesin'];
    
	// Options
	$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',"noakun like '632%' and detail=1");

    $hkef='';
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
    <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
			<td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
    </thead>
    <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where noakun like '632%' and tipebudget = '".$tipebudget."' and tahunbudget = '".
		$tahunbudget."' and kodeorg = '".$mesin."' and kodebudget='KONTRAK'";
    $res=mysql_query($str);
    $no=1;
//            <td align=right>".$bar->noakun."</td>
//            <td align=left>".$akun[$bar->noakun]."</td>
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->noakun."</td>
            <td align=right>".number_format($bar->rupiah,0)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(5,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab4
if($cekapa=='tab4'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
//kamus namabarang
    $strJ="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $barang[$barJ->kodebarang]=$barJ->namabarang;
    }

    $hkef='';
	$hkef.="<fieldset><legend>Proporsi Sebaran</legend>";
	$hkef.="<table class=data cellspacing=1 border=0>";
	$hkef.="<thead><tr class=rowheader>
			<td align=center>".substr($_SESSION['lang']['jan'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['peb'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mar'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['apr'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mei'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jun'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jul'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['agt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['sep'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['okt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['nov'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['dec'],0,3)."</td>
			</tr></thead>";
	$hkef.="<tbody><tr class=rowcontent>";
	$hkef.="<td>".makeElement('prop1','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop2','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop3','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop4','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop5','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop6','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop7','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop8','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop9','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop10','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop11','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="<td>".makeElement('prop12','textnum','1',array('style'=>'width:30px'))."</td>";
	$hkef.="</table>";
	$hkef.="</fieldset>";
	
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td align=center>".$_SESSION['lang']['mesin']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodevhc']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".substr($_SESSION['lang']['jan'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['peb'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mar'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['apr'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['mei'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jun'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['jul'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['agt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['sep'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['okt'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['nov'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['dec'],0,3)."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
//    $str="select * from ".$dbname.".bgt_budget_detail
//        where kodeorg = '".$mesin."' and kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,4)."%'";
//    $str="select * from ".$dbname.".bgt_budget_detail
//        where tahunbudget=".$tahunbudget." and tipebudget='".$tipebudget."' and kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,4)."%'";
//    $str="select * from ".$dbname.".bgt_budget_detail
//        where tahunbudget=".$tahunbudget." and tipebudget='".$tipebudget."' and kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,4)."%'
//        and (kodebudget like 'EXPL%' or kodebudget like 'M%' or kodebudget like 'TOOL%' or kodebudget like 'VHC%' or kodebudget = 'PKSM');    
//";
    $str="select a.*, b.tutup from ".$dbname.".bgt_budget_detail a
        left join ".$dbname.".bgt_budget b on a.kunci=b.kunci
        where a.tahunbudget=".$tahunbudget." and a.tipebudget='".$tipebudget."' and a.kodeorg like '".substr($_SESSION['empl']['lokasitugas'],0,4)."%'
        and (a.kodebudget like 'EXPL%' or a.kodebudget like 'M%' or a.kodebudget like 'TOOL%' or a.kodebudget like 'VHC%' or a.kodebudget = 'PKSM' or a.kodebudget='KONTRAK');    
";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
	$b = isset($barang[$bar->kodebarang])?$barang[$bar->kodebarang]:'';
    $hkef.="<tr class=rowcontent id='sebaran".$bar->kunci."'>
            <td align=center>".$no."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=center>".$bar->kodevhc."</td>
            <td align=right>".$bar->kodebarang."</td>
            <td align=left>".$b."</td>
            <td id='row-".$bar->kunci."-1' align=right>".number_format($bar->rp01)."</td>
            <td id='row-".$bar->kunci."-2' align=right>".number_format($bar->rp02)."</td>
            <td id='row-".$bar->kunci."-3' align=right>".number_format($bar->rp03)."</td>
            <td id='row-".$bar->kunci."-4' align=right>".number_format($bar->rp04)."</td>
            <td id='row-".$bar->kunci."-5' align=right>".number_format($bar->rp05)."</td>
            <td id='row-".$bar->kunci."-6' align=right>".number_format($bar->rp06)."</td>
            <td id='row-".$bar->kunci."-7' align=right>".number_format($bar->rp07)."</td>
            <td id='row-".$bar->kunci."-8' align=right>".number_format($bar->rp08)."</td>
            <td id='row-".$bar->kunci."-9' align=right>".number_format($bar->rp09)."</td>
            <td id='row-".$bar->kunci."-10' align=right>".number_format($bar->rp10)."</td>
            <td id='row-".$bar->kunci."-11' align=right>".number_format($bar->rp11)."</td>
            <td id='row-".$bar->kunci."-12' align=right>".number_format($bar->rp12)."</td>
            <td id='row-".$bar->kunci."-total' align=right value='".$bar->rupiah."'>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center>
                <input type=\"image\" id=search4 src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran']." onclick=\"proporsiSebaran(".$bar->kunci.")\";>
            </td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab4
if($cekapa=='sebaran4'){
    $kunci=$_GET['kunci'];
//kamus namabarang
    $strJ="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $barang[$barJ->kodebarang]=$barJ->namabarang;
    }
    
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/budget_budget_pks.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
    
    $hkef='';
//            <td align=center>".$_SESSION['lang']['mesin']."</td>
//            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
//            <td align=center>".$_SESSION['lang']['kodevhc']."</td>
//            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
//            <td align=center>".$_SESSION['lang']['namabarang']."</td>
    $hkef.="<table id=container5 class=sortable cellspacing=1 border=0 width=100%>
     <thead>";
//        <tr>
//            <td align=center>".$_SESSION['lang']['index']."</td>
//            <td align=center>".$_SESSION['lang']['mesin']."</td>
//            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
//            <td align=center>Jan</td>
//            <td align=center>Feb</td>
//            <td align=center>Mar</td>
//            <td align=center>Apr</td>
//            <td align=center>May</td>
//            <td align=center>Jun</td>
//            <td align=center>Jul</td>
//            <td align=center>Aug</td>
//            <td align=center>Sep</td>
//            <td align=center>Oct</td>
//            <td align=center>Nov</td>
//            <td align=center>Dec</td>
//            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
//            <td align=center>".$_SESSION['lang']['action']."</td>
//       </tr>  
     $hkef.="</thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget_detail
        where kunci = '".$kunci."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
        $rp01=$bar->rp01; $pr01=($rp01*100/($bar->rupiah));
        $rp02=$bar->rp02; $pr02=($rp02*100/($bar->rupiah));
        $rp03=$bar->rp03; $pr03=($rp03*100/($bar->rupiah));
        $rp04=$bar->rp04; $pr04=($rp04*100/($bar->rupiah));
        $rp05=$bar->rp05; $pr05=($rp05*100/($bar->rupiah));
        $rp06=$bar->rp06; $pr06=($rp06*100/($bar->rupiah));
        $rp07=$bar->rp07; $pr07=($rp07*100/($bar->rupiah));
        $rp08=$bar->rp08; $pr08=($rp08*100/($bar->rupiah));
        $rp09=$bar->rp09; $pr09=($rp09*100/($bar->rupiah));
        $rp10=$bar->rp10; $pr10=($rp10*100/($bar->rupiah));
        $rp11=$bar->rp11; $pr11=($rp11*100/($bar->rupiah));
        $rp12=$bar->rp12; $pr12=($rp12*100/($bar->rupiah));

        $fis01=$bar->fis01; 
        $fis02=$bar->fis02; 
        $fis03=$bar->fis03; 
        $fis04=$bar->fis04; 
        $fis05=$bar->fis05; 
        $fis06=$bar->fis06; 
        $fis07=$bar->fis07; 
        $fis08=$bar->fis08; 
        $fis09=$bar->fis09; 
        $fis10=$bar->fis10; 
        $fis11=$bar->fis11; 
        $fis12=$bar->fis12; 
        if($rp01==0&&$rp02==0&&$rp03==0&&$rp04==0&&$rp05==0&&$rp06==0&&$rp07==0&&$rp08==0&&$rp09==0&&$rp10==0&&$rp11==0&&$rp12==0){
            $rp01=floor(($bar->rupiah*100)/12)*0.01; $pr01=floor(100*100/12);
            $rp02=floor(($bar->rupiah*100)/12)*0.01; $pr02=floor(100*100/12)*0.01;
            $rp03=floor(($bar->rupiah*100)/12)*0.01; $pr03=floor(100*100/12)*0.01;
            $rp04=floor(($bar->rupiah*100)/12)*0.01; $pr04=floor(100*100/12)*0.01;
            $rp05=floor(($bar->rupiah*100)/12)*0.01; $pr05=floor(100*100/12)*0.01;
            $rp06=floor(($bar->rupiah*100)/12)*0.01; $pr06=floor(100*100/12)*0.01;
            $rp07=floor(($bar->rupiah*100)/12)*0.01; $pr07=floor(100*100/12)*0.01;
            $rp08=floor(($bar->rupiah*100)/12)*0.01; $pr08=floor(100*100/12)*0.01;
            $rp09=floor(($bar->rupiah*100)/12)*0.01; $pr09=floor(100*100/12)*0.01;
            $rp10=floor(($bar->rupiah*100)/12)*0.01; $pr10=floor(100*100/12)*0.01;
            $rp11=floor(($bar->rupiah*100)/12)*0.01; $pr11=floor(100*100/12)*0.01;
            $rp12=floor(($bar->rupiah*100)/12)*0.01; $pr12=floor(100*100/12)*0.01;
            $fis01=floor(($bar->jumlah*100)/12)*0.01; 
            $fis02=floor(($bar->jumlah*100)/12)*0.01; 
            $fis03=floor(($bar->jumlah*100)/12)*0.01; 
            $fis04=floor(($bar->jumlah*100)/12)*0.01; 
            $fis05=floor(($bar->jumlah*100)/12)*0.01; 
            $fis06=floor(($bar->jumlah*100)/12)*0.01; 
            $fis07=floor(($bar->jumlah*100)/12)*0.01; 
            $fis08=floor(($bar->jumlah*100)/12)*0.01; 
            $fis09=floor(($bar->jumlah*100)/12)*0.01; 
            $fis10=floor(($bar->jumlah*100)/12)*0.01; 
            $fis11=floor(($bar->jumlah*100)/12)*0.01; 
            $fis12=floor(($bar->jumlah*100)/12)*0.01; 
        }
        echo "Error: ".$bar->kunci;
        $rp01=0.01*round($rp01*100);
        $rp02=0.01*round($rp02*100);
        $rp03=0.01*round($rp03*100);
        $rp04=0.01*round($rp04*100);
        $rp05=0.01*round($rp05*100);
        $rp06=0.01*round($rp06*100);
        $rp07=0.01*round($rp07*100);
        $rp08=0.01*round($rp08*100);
        $rp09=0.01*round($rp09*100);
        $rp10=0.01*round($rp10*100);
        $rp11=0.01*round($rp11*100);
        $rp12=0.01*round($rp12*100);
        $fis01=0.01*round($fis01*100);
        $fis02=0.01*round($fis02*100);
        $fis03=0.01*round($fis03*100);
        $fis04=0.01*round($fis04*100);
        $fis05=0.01*round($fis05*100);
        $fis06=0.01*round($fis06*100);
        $fis07=0.01*round($fis07*100);
        $fis08=0.01*round($fis08*100);
        $fis09=0.01*round($fis09*100);
        $fis10=0.01*round($fis10*100);
        $fis11=0.01*round($fis11*100);
        $fis12=0.01*round($fis12*100);
        $prall=$pr01+$pr02+$pr03+$pr04+$pr05+$pr06+$pr07+$pr08+$pr09+$pr10+$pr11+$pr12;
        $sifall=$sif01+$sif02+$sif03+$sif04+$sif05+$sif06+$sif07+$sif08+$sif09+$sif10+$sif11+$sif12;
//            <td align=center>".$bar->kodeorg."</td>
//            <td align=center>".$bar->kodebudget."</td>
//            <td align=center>".$bar->kodevhc."</td>
//            <td align=right>".$bar->kodebarang."</td>
//            <td align=left>".$barang[$bar->kodebarang]."</td>
//            <td align=center>".$_SESSION['lang']['index']."</td>
//            <td align=center>".$_SESSION['lang']['mesin']."</td>
//            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
//            <td align=center>Jan</td>
//            <td align=center>Feb</td>
//            <td align=center>Mar</td>
//            <td align=center>Apr</td>
//            <td align=center>May</td>
//            <td align=center>Jun</td>
//            <td align=center>Jul</td>
//            <td align=center>Aug</td>
//            <td align=center>Sep</td>
//            <td align=center>Oct</td>
//            <td align=center>Nov</td>
//            <td align=center>Dec</td>
//            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
//            <td align=center>".$_SESSION['lang']['action']."</td>




/*
<td align=center>".substr($_SESSION['lang']['jan'],0,3)."</td>
            <td align=center>".substr($_SESSION['lang']['peb'],0,3)."</td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center>".substr($_SESSION['lang']['dec'],0,3)."</td>
*/




    $hkef.="
            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['index']."</td><td align=center>".$kunci."</td></tr>
            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['mesin']."</td><td align=center>".$bar->kodeorg."</td></tr>
            <tr class=rowcontent><td colspan=3 align=center>".$_SESSION['lang']['kodeanggaran']."</td><td align=center>".$bar->kodebudget."</td></tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['jan'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=jan4per name=jan4per onblur=\"kalikan4(1,".$bar->rupiah.");\" value=\"".$pr01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=jan4 name=jan4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=jan4fis name=jan4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis01."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['peb'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=feb4per name=feb4per onblur=\"kalikan4(2,".$bar->rupiah.");\" value=\"".$pr02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=feb4 name=feb4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=feb4fis name=feb4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis02."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['mar'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=mar4per name=mar4per onblur=\"kalikan4(3,".$bar->rupiah.");\" value=\"".$pr03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=mar4 name=mar4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=mar4fis name=mar4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis03."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['apr'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=apr4per name=apr4per onblur=\"kalikan4(4,".$bar->rupiah.");\" value=\"".$pr04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=apr4 name=apr4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=apr4fis name=apr4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis04."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['mei'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=may4per name=may4per onblur=\"kalikan4(5,".$bar->rupiah.");\" value=\"".$pr05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=may4 name=may4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=may4fis name=may4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis05."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['jun'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=jun4per name=jun4per onblur=\"kalikan4(6,".$bar->rupiah.");\" value=\"".$pr06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=jun4 name=jun4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=jun4fis name=jun4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis06."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['jul'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=jul4per name=jul4per onblur=\"kalikan4(7,".$bar->rupiah.");\" value=\"".$pr07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=jul4 name=jul4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=jul4fis name=jul4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis07."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['agt'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=aug4per name=aug4per onblur=\"kalikan4(8,".$bar->rupiah.");\" value=\"".$pr08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=aug4 name=aug4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=aug4fis name=aug4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis08."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['sep'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=sep4per name=sep4per onblur=\"kalikan4(9,".$bar->rupiah.");\" value=\"".$pr09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=sep4 name=sep4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=sep4fis name=sep4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis09."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['okt'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=oct4per name=oct4per onblur=\"kalikan4(10,".$bar->rupiah.");\" value=\"".$pr10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=oct4 name=oct4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=oct4fis name=oct4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis10."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['nov'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=nov4per name=nov4per onblur=\"kalikan4(11,".$bar->rupiah.");\" value=\"".$pr11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=nov4 name=nov4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=nov4fis name=nov4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis11."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".substr($_SESSION['lang']['dec'],0,3)."</td>
                <td align=center><input type=text class=myinputtext id=dec4per name=dec4per onblur=\"kalikan4(12,".$bar->rupiah.");\" value=\"".$pr12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:30px; />%</td>
                <td align=center><input type=text class=myinputtext id=dec4 name=dec4 onblur=\"jumlahkan7(".$bar->rupiah.");\" value=\"".$rp12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
                <td align=center><input type=text class=myinputtext id=dec4fis name=dec4fis onblur=\"jumlahkan7a(".$bar->jumlah.");\" value=\"".$fis12."\" onkeypress=\"return angka_doangsamaminus(event);\" maxlength=12 style=width:90px; /></td>
            </tr>
            <tr class=rowcontent>
                <td align=center>".$_SESSION['lang']['total']."</td>
                <td align=center><input type=text class=myinputtext id=all4per name=all4per value=\"".$prall."\" disabled=true maxlength=10 style=width:30px; />%</td>
                <td align=right><input type=hidden id=hiddenrupiah name=hiddenrupiah value=\"".$bar->rupiah."\">".number_format($bar->rupiah,2)."</td>
                <td align=right><input type=hidden id=hiddenjumlah name=hiddenjumlah value=\"".$bar->jumlah."\">".number_format($bar->jumlah,2)."</td>
            </tr>
            <tr><td align=center>".$_SESSION['lang']['action']."</td><td align=center>&nbsp;</td><td align=center>
                <input type=hidden id=total4 name=total4 value=\"".$bar->rupiah."\">
                <input type=hidden id=total4fis name=total4fis value=\"".$bar->jumlah."\">
                <input type=hidden id=progress name=progress value=\"\">    
                <input type=\"image\" id=search4 src=images/save.png class=resicon title=".$_SESSION['lang']['save']." onclick=\"simpansebaran(".$bar->kunci.",event)\";>
            </td><td align=center>
                <input type=\"image\" id=sapu4 src=images/clear.png class=resicon title=".$_SESSION['lang']['clear']." onclick=\"sapusebaran(event)\";>
            </td></tr><tr>
       </tr><tr>
       <tr><td align=center colspan=15><button class=mybutton id=tutup4 name=tutup4 onclick=parent.closeDialog()>".$_SESSION['lang']['close']."</button></td>
       </tr>";
    $no+=1;
    }
    echo $hkef;        
 

    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
    echo " Nol-kan semua persentase untuk mengisi rupiah.";
}

if($cekapa=='proporsiSebaran'){
	$param = $_POST;
	
	// Get Budget
	$qBudget = selectQuery($dbname,'bgt_budget','volume,rupiah',"kunci=".$param['kunci']);
	$resBudget = fetchData($qBudget);
	
	// Proporsi
	$total=$param['prop1']+$param['prop2']+$param['prop3']+$param['prop4']+
		$param['prop5']+$param['prop6']+$param['prop7']+$param['prop8']+
		$param['prop9']+$param['prop10']+$param['prop11']+$param['prop12'];
	
	// Budget Distribusi
	$data = array(
		'kunci'=>$param['kunci'],
		'rp01'=>$resBudget[0]['rupiah']*$param['prop1']/$total,
		'fis01'=>$resBudget[0]['volume']*$param['prop1']/$total,
		'rp02'=>$resBudget[0]['rupiah']*$param['prop2']/$total,
		'fis02'=>$resBudget[0]['volume']*$param['prop2']/$total,
		'rp03'=>$resBudget[0]['rupiah']*$param['prop3']/$total,
		'fis03'=>$resBudget[0]['volume']*$param['prop3']/$total,
		'rp04'=>$resBudget[0]['rupiah']*$param['prop4']/$total,
		'fis04'=>$resBudget[0]['volume']*$param['prop4']/$total,
		'rp05'=>$resBudget[0]['rupiah']*$param['prop5']/$total,
		'fis05'=>$resBudget[0]['volume']*$param['prop5']/$total,
		'rp06'=>$resBudget[0]['rupiah']*$param['prop6']/$total,
		'fis06'=>$resBudget[0]['volume']*$param['prop6']/$total,
		'rp07'=>$resBudget[0]['rupiah']*$param['prop7']/$total,
		'fis07'=>$resBudget[0]['volume']*$param['prop7']/$total,
		'rp08'=>$resBudget[0]['rupiah']*$param['prop8']/$total,
		'fis08'=>$resBudget[0]['volume']*$param['prop8']/$total,
		'rp09'=>$resBudget[0]['rupiah']*$param['prop9']/$total,
		'fis09'=>$resBudget[0]['volume']*$param['prop9']/$total,
		'rp10'=>$resBudget[0]['rupiah']*$param['prop10']/$total,
		'fis10'=>$resBudget[0]['volume']*$param['prop10']/$total,
		'rp11'=>$resBudget[0]['rupiah']*$param['prop11']/$total,
		'fis11'=>$resBudget[0]['volume']*$param['prop11']/$total,
		'rp12'=>$resBudget[0]['rupiah']*$param['prop12']/$total,
		'fis12'=>$resBudget[0]['volume']*$param['prop12']/$total,
		'updateby'=>$_SESSION['standard']['userid']
	);
	$cols = array();
	foreach($data as $h=>$d) {
		$cols[] = $h;
	}
	$qIns = insertQuery($dbname,'bgt_distribusi',$data,$cols);
	if(!mysql_query($qIns)){
        $qUpd = updateQuery($dbname,'bgt_distribusi',$data,"kunci=".$param['kunci']);
		if(!mysql_query($qUpd)){
			exit("DB Error : ".addslashes(mysql_error()));
		}
    }
	
	$res = array();
	foreach($data as $h=>$d) {
		if(substr($h,0,2)=='rp') {
			$res[]=$d;
		}
	}
	echo json_encode($res);
}