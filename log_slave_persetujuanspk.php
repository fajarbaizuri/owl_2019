<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$proses = $_GET['proses'];

switch($proses) {
    case 'listSPK':
        ## Get Data
        # List SPK
        $qSPK = selectQuery($dbname,'log_spkht','kodeorg,notransaksi,koderekanan',
            "kodeorg='".$param['kodeorg']."'");
        $resSPK = fetchData($qSPK);
        
        # Options
        $optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan,lokasitugas',
			"tipekaryawan=0",4);
        $optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier');
        
        # Persetujuan SPK
        $qS = selectQuery($dbname,'log_persetujuanspk','*',
            "kodeorg='".$param['kodeorg']."'");
        $resS = fetchData($qS);
        $optS = array();
        foreach($resS as $r) {
            switch($r['status']) {
                case '0':
                    $st='Menunggu Persetujuan';
                    break;
                case '1':
                    $st='Disetujui';
                    break;
                case '2':
                    $st='Ditolak';
                    break;
            }
            $optS[$r['kodeorg']][$r['notransaksi']][$r['level']] = array(
                'penyetujuid'=>$r['penyetuju'],
                'penyetuju'=>$optKary[$r['penyetuju']],
                'tanggal'=>tanggalnormal($r['tanggal']),
                'status'=>$st,
                'catatan'=>$r['catatan']
            );
        }
        
        ## Rearrange Data
        $data = $resSPK;
        
        ## Header
        $tab = "<table class='data' border=0 cellspacing=2 cellpadding=2>";
        $tab .= "<thead><tr class='rowheader'>";
        $tab .= "<td rowspan=2>".$_SESSION['lang']['kodeorganisasi']."</td>";
        $tab .= "<td rowspan=2>".$_SESSION['lang']['notransaksi']."</td>";
        $tab .= "<td rowspan=2>Kontraktor</td>";
        $tab .= "<td rowspan=2>Print PDF</td>";
        $tab .= "<td colspan=4>Persetujuan 1</td>";
        $tab .= "<td colspan=4>Persetujuan 2</td>";
        $tab .= "<td colspan=4>Persetujuan 3</td>";
        $tab .= "<td colspan=4>Persetujuan 4</td>";
        $tab .= "<td colspan=4>Persetujuan 5</td>";
        $tab .= "</tr><tr>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Penyetuju</td>";
        $tab .= "<td>Status</td>";
        $tab .= "<td>Catatan</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Penyetuju</td>";
        $tab .= "<td>Status</td>";
        $tab .= "<td>Catatan</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Penyetuju</td>";
        $tab .= "<td>Status</td>";
        $tab .= "<td>Catatan</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Penyetuju</td>";
        $tab .= "<td>Status</td>";
        $tab .= "<td>Catatan</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Penyetuju</td>";
        $tab .= "<td>Status</td>";
        $tab .= "<td>Catatan</td>";
        $tab .= "</tr></thead>";
        
        ## Content
        $tab .= "<tbody>";
        foreach($data as $key=>$row) {
            if(isset($optS[$row['kodeorg']][$row['notransaksi']])) {
                $tmpS = $optS[$row['kodeorg']][$row['notransaksi']];
            } else {
                $tmpS = null;
            }
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td id='kodeorg_".$key."' value='".$row['kodeorg']."'>".$param['kodeorgName']."</td>";
            $tab .= "<td id='notransaksi_".$key."' value='".$row['notransaksi']."'>".$row['notransaksi']."</td>";
            $tab .= "<td id='koderekanan_".$key."' value='".$row['koderekanan']."'>".$optSupp[$row['koderekanan']]."</td>";
            $tab .= "<td><img src='images/pdf.jpg' onclick='detailPDF(".$key.",event)' style='width:40px;height:15px;cursor:pointer' ></td>";
            
            $jabatan = $_SESSION['empl']['kodejabatan'];
            if(!is_null($tmpS)) {
                ### Level 1
                if(isset($tmpS['1'])) {
                    if($tmpS['1']['tanggal']!='00-00-0000') {
                        $tab .= "<td>".$tmpS['1']['tanggal']."</td>";
                        $tab .= "<td>".$tmpS['1']['penyetuju']."</td>";
                        $tab .= "<td style='font-weight:bold'>".$tmpS['1']['status']."</td>";
                        $tab .= "<td>".$tmpS['1']['catatan']."</td>";
                    } else {
                        if($tmpS['1']['penyetujuid']==$_SESSION['standard']['userid']) {
                            $tab .= "<td colspan=4><table>".
                                "<tr><td>".makeElement('btn10_'.$key,'btn','Setuju',array('onclick'=>"post(".$key.",5,1,1)"))."</td></tr>".
                                "<tr><td>".makeElement('kary1_'.$key,'select','',array('style'=>'width:150px'),$optKary)."</td>".
                                "<td>".makeElement('btn11_'.$key,'btn','Ajukan',array('onclick'=>"post(".$key.",1,1)"))."</td></tr>".
                                "<tr><td>".makeElement('note1_'.$key,'text','',array('style'=>'width:150px'))."</td>".
                                "<td>".makeElement('btn12_'.$key,'btn','Tolak',array('onclick'=>"post(".$key.",1,2)"))."</td></tr></table>".
                                "</td>";
                        } else {
                            $tab .= "<td></td>";
                            $tab .= "<td>".$tmpS['1']['penyetuju']."</td>";
                            $tab .= "<td></td><td></td>";
                        }
                    }
                }
                ### Level 2
                if(isset($tmpS['2'])) {
                    if($tmpS['2']['tanggal']!='00-00-0000') {
                        $tab .= "<td>".$tmpS['2']['tanggal']."</td>";
                        $tab .= "<td>".$tmpS['2']['penyetuju']."</td>";
                        $tab .= "<td style='font-weight:bold'>".$tmpS['2']['status']."</td>";
                        $tab .= "<td>".$tmpS['2']['catatan']."</td>";
                    } else {
                        if($tmpS['2']['penyetujuid']==$_SESSION['standard']['userid']) {
                            $tab .= "<td colspan=4><table>".
                                "<tr><td>".makeElement('btn20_'.$key,'btn','Setuju',array('onclick'=>"post(".$key.",5,1,2)"))."</td></tr>".
                                "<tr><td>".makeElement('kary2_'.$key,'select','',array('style'=>'width:150px'),$optKary)."</td>".
                                "<td>".makeElement('btn21_'.$key,'btn','Ajukan',array('onclick'=>"post(".$key.",2,1)"))."</td></tr>".
                                "<tr><td>".makeElement('note2_'.$key,'text','',array('style'=>'width:150px'))."</td>".
                                "<td>".makeElement('btn22_'.$key,'btn','Tolak',array('onclick'=>"post(".$key.",2,2)"))."</td></tr></table>".
                                "</td>";
                        } else {
                            $tab .= "<td></td>";
                            $tab .= "<td>".$tmpS['2']['penyetuju']."</td>";
                            $tab .= "<td></td><td></td>";
                        }
                    }
                } else {
                    $tab .= "<td colspan=4></td>";
                }
                ### Level 3
                if(isset($tmpS['3'])) {
                    if($tmpS['3']['tanggal']!='00-00-0000') {
                        $tab .= "<td>".$tmpS['3']['tanggal']."</td>";
                        $tab .= "<td>".$tmpS['3']['penyetuju']."</td>";
                        $tab .= "<td style='font-weight:bold'>".$tmpS['3']['status']."</td>";
                        $tab .= "<td>".$tmpS['3']['catatan']."</td>";
                    } else {
                        if($tmpS['3']['penyetujuid']==$_SESSION['standard']['userid']) {
                            $tab .= "<td colspan=4><table>".
                                "<tr><td>".makeElement('btn30_'.$key,'btn','Setuju',array('onclick'=>"post(".$key.",5,1,3)"))."</td></tr>".
                                "<tr><td>".makeElement('kary3_'.$key,'select','',array('style'=>'width:150px'),$optKary)."</td>".
                                "<td>".makeElement('btn31_'.$key,'btn','Ajukan',array('onclick'=>"post(".$key.",3,1)"))."</td></tr>".
                                "<tr><td>".makeElement('note3_'.$key,'text','',array('style'=>'width:150px'))."</td>".
                                "<td>".makeElement('btn32_'.$key,'btn','Tolak',array('onclick'=>"post(".$key.",3,2)"))."</td></tr></table>".
                                "</td>";
                        } else {
                            $tab .= "<td></td>";
                            $tab .= "<td>".$tmpS['3']['penyetuju']."</td>";
                            $tab .= "<td></td><td></td>";
                        }
                    }
                } else {
                    $tab .= "<td colspan=4></td>";
                }
                ### Level 4
                if(isset($tmpS['4'])) {
                    if($tmpS['4']['tanggal']!='00-00-0000') {
                        $tab .= "<td>".$tmpS['4']['tanggal']."</td>";
                        $tab .= "<td>".$tmpS['4']['penyetuju']."</td>";
                        $tab .= "<td style='font-weight:bold'>".$tmpS['4']['status']."</td>";
                        $tab .= "<td>".$tmpS['4']['catatan']."</td>";
                    } else {
                        if($tmpS['4']['penyetujuid']==$_SESSION['standard']['userid']) {
                            $tab .= "<td colspan=4><table>".
                                "<tr><td>".makeElement('btn40_'.$key,'btn','Setuju',array('onclick'=>"post(".$key.",5,1,4)"))."</td></tr>".
                                "<tr><td>".makeElement('kary4_'.$key,'select','',array('style'=>'width:150px'),$optKary)."</td>".
                                "<td>".makeElement('btn41_'.$key,'btn','Ajukan',array('onclick'=>"post(".$key.",4,1)"))."</td></tr>".
                                "<tr><td>".makeElement('note4_'.$key,'text','',array('style'=>'width:150px'))."</td>".
                                "<td>".makeElement('btn42_'.$key,'btn','Tolak',array('onclick'=>"post(".$key.",4,2)"))."</td></tr></table>".
                                "</td>";
                        } else {
                            $tab .= "<td></td>";
                            $tab .= "<td>".$tmpS['4']['penyetuju']."</td>";
                            $tab .= "<td></td><td></td>";
                        }
                    }
                } else {
                    $tab .= "<td colspan=4></td>";
                }
                ### Level 5
                if(isset($tmpS['5'])) {
                    if($tmpS['5']['tanggal']!='00-00-0000') {
                        $tab .= "<td>".$tmpS['5']['tanggal']."</td>";
                        $tab .= "<td>".$tmpS['5']['penyetuju']."</td>";
                        $tab .= "<td style='font-weight:bold'>".$tmpS['5']['status']."</td>";
                        $tab .= "<td>".$tmpS['5']['catatan']."</td>";
                    } else {
                        if($tmpS['5']['penyetujuid']==$_SESSION['standard']['userid']) {
                            $tab .= "<td colspan=4><table>".
                                "<tr><td>".makeElement('note5_'.$key,'text','',array('style'=>'width:150px'))."</td></tr>".
                                "<tr><td>".makeElement('btn51_'.$key,'btn','Setuju',array('onclick'=>"post(".$key.",5,1)")).
                                makeElement('btn52_'.$key,'btn','Tolak',array('onclick'=>"post(".$key.",5,2)"))."</td></tr></table>".
                                "</td>";
                        } else {
                            $tab .= "<td></td>";
                            $tab .= "<td>".$tmpS['5']['penyetuju']."</td>";
                            $tab .= "<td></td><td></td>";
                        }
                    }
                } else {
                    $tab .= "<td colspan=4></td>";
                }
            } else {
                $tab .= "<td colspan=4></td><td colspan=4></td>".
                    "<td colspan=4></td><td colspan=4></td><td colspan=4></td>";
            }
            $tab .= "</tr>";
        }
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        echo $tab;
        break;
    case 'post':
        // Update Persetujuan
        $d1 = array(
            'tanggal'=>date('Ymd'),
            'catatan'=>$param['note'],
            'status'=>$param['status']
        );
        if(isset($param['currLevel'])) {
            $whereUpd = "kodeorg='".$param['kodeorg']."' and notransaksi='".
                $param['notransaksi']."' and level=".$param['currLevel'];
        } else {
            $whereUpd = "kodeorg='".$param['kodeorg']."' and notransaksi='".
                $param['notransaksi']."' and level=".$param['level'];
        }
        $qUpd1 = updateQuery($dbname,'log_persetujuanspk',$d1,$whereUpd);
        if(!mysql_query($qUpd1)) {
            exit("DB Error: ".mysql_error());
        }
        
        if($param['status']==1) {
            if($param['level']<5) {
                // Insert Persetujuan Berikutnya
                $data = array(
                    'kodeorg'=>$param['kodeorg'],
                    'notransaksi'=>$param['notransaksi'],
                    'level'=>$param['level']+1,
                    'status'=>0,
                    'tanggal'=>'0000-00-00',
                    'penyetuju'=>$param['kary'],
                    'catatan'=>''
                );
                $qIns = insertQuery($dbname,'log_persetujuanspk',$data);
                if(!mysql_query($qIns)) {
                    // Rollback
                    $dRB = array('tanggal'=>'0000-00-00','catatan'=>'');
                    $qUpdRB = updateQuery($dbname,'log_persetujuanspk',$dRB,$whereUpd);
                    if(!mysql_query($qUpdRB)) {
                        exit("Rollback Error: ".mysql_error());
                    }
                    
                    // Throw Error
                    exit("DB Error: ".mysql_error());
                }
            } else {
                $d = array('posting'=>'1');
                $where = "kodeorg='".$param['kodeorg']."' and notransaksi='".
                    $param['notransaksi']."'";
                $qUpd = updateQuery($dbname,'log_spkht',$d,$where);
                if(!mysql_query($qUpd)) {
                    exit("DB Error: ".mysql_error());
                }
            }
        }
        break;
    default:
}
?>