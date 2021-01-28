<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language="javascript">
function loadGaji(tahun)
{
    param='tahun='+tahun;		
    post_response_text('sdm_slave_loadGaji.php?', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    mTable = document.getElementById('conts');
                    mTable.innerHTML = con.responseText;
                    //eval(con.responseText);
                    clearData(field);
                    //location.reload(true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }                
}
function copyTahun()
{
    tahun1=document.getElementById('tahun1');
    tahun2=document.getElementById('tahun2');
    tahun1=tahun1.options[tahun1.selectedIndex].value;
    tahun2=tahun2.options[tahun2.selectedIndex].value;
    param='tahun1='+tahun1+'&tahun2='+tahun2;
    if(tahun2<=tahun1) {
        alert('Destination year must larger than the source');
    } else {
        if(confirm('Data pada tahun tujuan akan direplace?')) {
            if(confirm('Are you sure..?')){
                post_response_text('sdm_slave_copyGP.php?', param, respon); 
            }
        }         
    }

    function respon(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            } else {
                                 alert('Done');   
                            }
                    } else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}

function massiveSet() {
    var tBody=document.getElementById('mTabBody'),
        kompEl = document.getElementById('mKomponen'),
        jmlEl = document.getElementById('mJumlah'),
        param='tahun='+getValue('mTahun')+'&komponen='+getValue('mKomponen')+
        '&jumlah='+jmlEl.value+'&target='+getValue('mTarget')+
        '&kompName='+kompEl.options[kompEl.selectedIndex].text+
        '&rowNum='+tBody.childNodes.length;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var detBody = document.getElementById('mTabBody');
                    eval('var res='+con.responseText+';');
                    
                    // Delete Existing Row
                    for(var i=0;i<detBody.childNodes.length;i++) {
                        var tmpTh = document.getElementById('tahun_'+i).getAttribute('value'),
                            tmpKomp = document.getElementById('idkomponen_'+i).getAttribute('value');
                        if(tmpTh==getValue('mTahun') && tmpKomp==getValue('mKomponen')) {
                            var tmpKary = document.getElementById('karyawanid_'+i).getAttribute('value');
                            for(var j in res['listKary']) {
                                if(res['listKary'][j]==tmpKary) {
                                    var tmpTr = document.getElementById('tr_'+i);
                                    tmpTr.style.display = 'none';
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Add New Row
                    for(var i in res['listData']) {
                        var tmpTr = document.createElement('tr');
                        tmpTr.setAttribute('tr_'+tBody.childNodes.length);
                        tmpTr.setAttribute('class','rowcontent');
                        tmpTr.innerHTML = res['listData'][i];
                        
                        tBody.appendChild(tmpTr);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_5gajipokok_mass.php', param, respon); 
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}
function dataKeExcel(ev)
{
    thn=document.getElementById('opttahun').options[document.getElementById('opttahun').selectedIndex].value;
    param='method=dataDetail'+'&thn='+thn;
   // alert(param);
    tujuan='sdm_slave_5gajipokok_excel.php';
    judul='List Data';	
    printFile(param,tujuan,judul,ev)	
}
</script>    
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Gaji Pokok</font></b></u></p>
<?php

//print_r($_SESSION['standard']);exit();

#======Select Prep======
$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in(1,2,3,4,5)");
$optComp = makeOption($dbname,'sdm_ho_component','id,name',"type='basic'");
//$optid = $_SESSION['standard']['userid'];//echo $optid;exit();
$optid = makeOption($dbname,'datakaryawan','karyawanid,karyawanid',"karyawanid='".$_SESSION['standard']['userid']."'");
#======End Select Prep======
#=======Form============
echo "<table><tr valign=top><td><div style='margin-bottom:30px;width:350px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('tahun','label',$_SESSION['lang']['tahun']),
  makeElement('tahun','textnum','',array('style'=>'width:50px','maxlength'=>'20'))
);
$els[] = array(
  makeElement('karyawanid','label',$_SESSION['lang']['karyawanid']),
  makeElement('karyawanid','select','',array('style'=>'width:150px'),$optKary)
);
$els[] = array(
  makeElement('idkomponen','label',$_SESSION['lang']['idkomponen']),
  makeElement('idkomponen','select','',array('style'=>'width:150px'),$optComp)
);
$els[] = array(
  makeElement('jumlah','label',$_SESSION['lang']['jumlah']),
  makeElement('jumlah','textnum','',array('style'=>'width:100px','maxlength'=>'20'))
);
//tambahan indra
$els[] = array(
  makeElement('key','label',''),
  makeElement('key','select','',array('style'=>'width:150px'),$optid)
);



# Fields
$fieldStr = '##tahun##karyawanid##idkomponen##jumlah##key';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Options
$opt = array(
    'karyawanid'=>$optKary,
    'idkomponen'=>$optComp
);
$optJs = str_replace('"',"##",json_encode($opt));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'sdm_5gajipokok',"##tahun##karyawanid##idkomponen",null,null,null,null,'##','##',$optJs)
);

# Generate Field
echo genElement($els);
$opttahun1 = '';
for($x=-2;$x<=8;$x++)
{
    $opttahun1.="<option value='".(date('Y')+$x)."'>".(date('Y')+$x)."</option>";
}
echo "</div>";

$opttahun = '';
for($x=2;$x>=-10;$x--)
{
    if((date('Y')+$x)==date('Y'))
    $opttahun.="<option value='".(date('Y')+$x)."' selected>".(date('Y')+$x)."</option>";
     else    
    $opttahun.="<option value='".(date('Y')+$x)."'>".(date('Y')+$x)."</option>";
}
echo $_SESSION['lang']['tahun'].":<select id=opttahun onchange=loadGaji(this.options[this.selectedIndex].value)>".$opttahun."</select>";
#=======Table===============
# Display Table
echo "<div id=conts style='height:230px;overflow:auto'>";
//==========================updated by ginting
//if($_SESSION['org']['tipeinduk']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='HOLDING') {
//    $where= "1=1 and tahun=".date('Y');
//} else {
    $where= "karyawanid in (";
    $i=0;
    foreach($optKary as $key=>$row) {
        if($i==0) {
            $where.= $key;
        } else {
            $where.= ",".$key;
        }
        $i++;
    }
    $where.= ")  and tahun=".date('Y');
//}
$tablex="sdm_5gajipokok";
//$tablex="sdm_5gajipokok a LEFT JOIN ".$dbname."datakaryawan b ON a.karyawanid=b.karyawanid";

//========================================


echo masterTable($dbname,$tablex,"*",array(),array(),$where,array(),'sdm_slave_5gajipokok_pdf',
    'tahun##karyawanid##idkomponen',true,null,$opt,'Gaji Pokok','1');

echo "</div>";
#=======End Table============

echo "</td>
    <td>
    <fieldset><legend style='font-weight:bold'>Tools</legend>
    <fieldset style='width:350px;'><legend style='font-weight:bold'>Copy</legend>
    Dari tahun:<select id=tahun1>".$opttahun1."</select>
    ke Tahun:<select id=tahun2>".$opttahun1."</select>
    <button onclick=copyTahun() class=mybutton>".$_SESSION['lang']['proses']."</button>    
    <hr>
    Copy gaji pokok dari konfigurasi gaji tahun tertentu ke tahun tertentu
    </fieldset>";
echo "<fieldset style='width:350px;'><legend style='font-weight:bold'>Setting Massal</legend>
    <table>
    <tr><td>Tahun:</td><td><select id=mTahun>".$opttahun1."</select></td></tr>
    <tr><td>Komponen:</td><td>".makeElement('mKomponen','select','',array('style'=>'width:150px'),$optComp)."</td></tr>
    <tr><td>Jumlah (Rp.):</td><td>".makeElement('mJumlah','textnum','0',array('style'=>'width:100px','maxlength'=>'20'))."</td></tr>
    <tr><td>Target:</td>
    <td>
    <select id='mTarget'>
        <option value='all'>Seluruh Karyawan</option>
        <option value='kbl'>KBL</option>
        <option value='kht'>KHT</option>
        <option value='khl'>KHL</option>
    </select>
    </td></tr>
    <tr><td colspan=2>
    <button onclick=massiveSet() class=mybutton>".$_SESSION['lang']['proses']."</button>
    </td></tr>
    </table>
    <hr>
    Setting Jumlah Rupiah Komponen Gaji per Tipe Karyawan atau keseluruhan
    </fieldset></fieldset>
    </td>
    </tr>
    </table>
    ";
#=======End Form============

CLOSE_BOX();
echo close_body();
?>