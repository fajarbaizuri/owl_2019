<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;

// Get Karyawan
$where = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in (";
switch($param['target']) {
    case 'all':
        $where .= "1,2,3,4,5";
        break;
    case 'kbl':
        $where .= "1";
        break;
    case 'kht':
        $where .= "3";
        break;
    case 'khl':
        $where .= "4";
        break;
}
$where .= ")";
$query = selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan',$where);
$res = fetchData($query);

$result = array();
$kary=array();
foreach($res as $row) {
    $data = array(
        'tahun'=>$param['tahun'],
        'karyawanid'=>$row['karyawanid'],
        'idkomponen'=>$param['komponen'],
        'jumlah'=>$param['jumlah']
    );
    
    $qIns = insertQuery($dbname,'sdm_5gajipokok',$data);
    if(mysql_query($qIns)) {
        $kary[] = $row['karyawanid'];
        $result[] = "<td id='tahun_".$param['rowNum']."' value='".$param['tahun']."'>".$param['tahun']."</td>".
            "<td id='karyawanid_".$param['rowNum']."' value='".$row['karyawanid']."'>".$row['namakaryawan']."</td>".
            "<td id='idkomponen_".$param['rowNum']."' value='".$param['komponen']."'>".$param['kompName']."</td>".
            "<td id='jumlah_".$param['rowNum']."' value='".$param['jumlah']."'>".$param['jumlah']."</td>".
            "<td><img id='editRow".$param['rowNum']."' title='Edit' onclick=\"editRow(".$param['rowNum'].",'##tahun##karyawanid##idkomponen##jumlah',".
            "'##".$param['tahun']."##".$row['karyawanid']."##".$param['komponen']."##".$param['jumlah']."','tahun##karyawanid##idkomponen')\" ".
            "class='zImgBtn' src='images/001_45.png'></td>".
            "<td><img id='delRow".$param['rowNum']."' title='Hapus' onclick=\"delRow(".$param['rowNum'].",'##tahun##karyawanid##idkomponen',".
            "'##".$param['tahun']."##".$row['karyawanid']."##".$param['komponen']."##".$param['jumlah']."',null,'sdm_5gajipokok')\" ".
            "class='zImgBtn' src='images/delete_32.png'></td>";
    } else {
        $qUpd = updateQuery($dbname,'sdm_5gajipokok',$data,
            "tahun=".$param['tahun']." and karyawanid=".$row['karyawanid'].
            " and idkomponen=".$param['komponen']);
        if(mysql_query($qUpd)) {
            $kary[] = $row['karyawanid'];
            $result[] = "<td id='tahun_".$param['rowNum']."' value='".$param['tahun']."'>".$param['tahun']."</td>".
                "<td id='karyawanid_".$param['rowNum']."' value='".$row['karyawanid']."'>".$row['namakaryawan']."</td>".
                "<td id='idkomponen_".$param['rowNum']."' value='".$param['komponen']."'>".$param['kompName']."</td>".
                "<td id='jumlah_".$param['rowNum']."' value='".$param['jumlah']."'>".$param['jumlah']."</td>".
                "<td><img id='editRow".$param['rowNum']."' title='Edit' onclick=\"editRow(".$param['rowNum'].",'##tahun##karyawanid##idkomponen##jumlah',".
                "'##".$param['tahun']."##".$row['karyawanid']."##".$param['komponen']."##".$param['jumlah']."','tahun##karyawanid##idkomponen')\" ".
                "class='zImgBtn' src='images/001_45.png'></td>".
                "<td><img id='delRow".$param['rowNum']."' title='Hapus' onclick=\"delRow(".$param['rowNum'].",'##tahun##karyawanid##idkomponen',".
                "'##".$param['tahun']."##".$row['karyawanid']."##".$param['komponen']."##".$param['jumlah']."',null,'sdm_5gajipokok')\" ".
                "class='zImgBtn' src='images/delete_32.png'></td>";
        }
    }
    $param['rowNum']++;
}

$res = array(
    'listKary'=>$kary,
    'listData'=>$result
);
echo json_encode($res);
?>