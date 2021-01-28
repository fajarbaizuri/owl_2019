<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kodeorg=$_POST['kodeorg'];
$kodebudget=$_POST['kodebudget'];
$tahunbudget=$_POST['tahunbudget'];


if($kodebudget!='')
    $where=" where kodeorg like '".$kodeorg."%' and tahunbudget=".$tahunbudget." and kodebudget='".$kodebudget."'";
else
    $where=" where kodeorg like '".$kodeorg."%' and tahunbudget=".$tahunbudget;

$str="select kunci,rupiah,jumlah from ".$dbname.".bgt_budget ".$where;

$res=mysql_query($str);
$res1=mysql_query($str);
//hapus dulu
while($bar=mysql_fetch_object($res))
{
    $strdel="delete from ".$dbname.".bgt_distribusi where kunci=".$bar->kunci;
    if(mysql_query($strdel))
    {}
    else
    {
        echo " Gagal(delete): ".addslashes(mysql_error($conn));
        exit();
    } 
}

//insert ke distribusi
while($bar=mysql_fetch_object($res1))
{
    $rupiah=$bar->rupiah;
    $fisik=$bar->jumlah;
    $strins="insert into ".$dbname.".bgt_distribusi (kunci, rp01, fis01, rp02, fis02, rp03, fis03, rp04, fis04, 
          rp05, fis05, rp06, fis06, rp07, fis07, rp08, fis08, rp09, fis09, rp10, fis10, rp11, fis11, rp12, 
          fis12, updateby)
          values(".$bar->kunci.",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".@($rupiah/12).",".@($fisik/12).",
              ".$_SESSION['standard']['userid']."    
          )";
    if(mysql_query($strins))
    {}
    else
    {
        echo " Gagal(delete): ".addslashes(mysql_error($conn));
        exit();
    }   
}

