<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_5rencanatraining.js'></script>
<?
include('master_mainMenu.php');

OPEN_BOX('',"<b>".$_SESSION['lang']['rencanatraining'].":</b>");

//get golongan
$str="select * from ".$dbname.".sdm_5jabatan order by kodejabatan";
$res=mysql_query($str);
$optgol="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    $optgol.="<option value='".$bar->kodejabatan."'>".$bar->namajabatan."</option>";
}

//get tahunbudget
$str="select distinct tahunbudget from ".$dbname.".sdm_5training order by tahunbudget desc";
$res=mysql_query($str);
$opttahun="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
    $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
}

//get host
$str="select * from ".$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res=mysql_query($str);
$opthost="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
    $host[$bar->supplierid]=$bar->namasupplier;
    $opthost.="<option value='".$bar->supplierid."'>".$bar->namasupplier."</option>";
}

//kamus jabatan
$str="select * from ".$dbname.".sdm_5jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
}

echo "<fieldset style='width:700px;'>
    <legend>".$_SESSION['lang']['form'].":</legend>
    <table><tr><td valign=top>
        <table>
        <tr>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\"></td>	
        </tr>
        <tr> 
            <td>".$_SESSION['lang']['kodetraining']."</td>
            <td><input type=text class=myinputtext id=kodetraining name=kodetraining onkeypress=\"return tanpa_kutip();\" maxlength=30 style=\"width:150px;\" /></td>	
        </tr>
        <tr>
            <td>".$_SESSION['lang']['namatraining']."</td>
            <td><input type=text class=myinputtext id=namatraining name=namatraining maxlength=30 style=\"width:150px;\"></td>
        </tr>
        <tr>
            <td>".$_SESSION['lang']['levelpeserta']."</td>
            <td><select id=levelpeserta name=levelpeserta>".$optgol."</select></td>
        </tr>
        <tr>
            <td>".$_SESSION['lang']['penyelenggara']."</td>
            <td><select id=penyelenggara name=penyelenggara>".$opthost."</select></td>
        </tr>
        <tr>
            <td>".$_SESSION['lang']['hargaperpeserta']."</td>
            <td><input type=text class=myinputtextnumber id=hargaperpeserta name=hargaperpeserta onkeypress=\"return angka_doang(event);\" maxlength=12 style=\"width:150px;\"></td>
        </tr>
        </table>	  
    </td>
    <td>  
        <table>
        <tr>
        <td> 
            <fieldset><legend>".$_SESSION['lang']['deskripsitraining']."</legend>
            <table>
                <tr><td><textarea id='deskripsitraining'></textarea></td>
                </tr>
            </table>
            </fieldset>
            <fieldset><legend>".$_SESSION['lang']['hasildiharapkan']."</legend>
            <table>
                <tr><td><textarea id='hasildiharapkan'></textarea></td>
                </tr>
            </table>
            </fieldset>
        </td>
        </tr>
        </table>	
    </td>
    </tr>	  
    </table>	
    <center><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
    <button class=mybutton onclick=batal()>".$_SESSION['lang']['cancel']."</button></center>
    </fieldset>
	 ";
CLOSE_BOX();

OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
     ".$_SESSION['lang']['budgetyear']." : <select onchange=displayList() id=listtahun name=listtahun>".$opttahun."</select>
     <input type=hidden id=pilihantahun name=pilihantahun value='' />
    <table class=sortable cellspacing=1 border=0 width=100%>
        <thead>
            <tr class=rowheader>
            <td align=center>".$_SESSION['lang']['nourut']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodetraining']."</td>
            <td align=center>".$_SESSION['lang']['namatraining']."</td>
            <td align=center>".$_SESSION['lang']['jabatan']."</td>
            <td align=center>".$_SESSION['lang']['penyelenggara']."</td>
            <td align=center>".$_SESSION['lang']['hargaperpeserta']."</td>
            <td colspan=3 align=center>".$_SESSION['lang']['action']."</td>
            </tr>
        </thead>
	<tbody id=container>";
$str="select * from ".$dbname.".sdm_5training
      ";
$res=mysql_query($str);
$no=1;
while($bar=mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$bar->tahunbudget."</td>
    <td>".$bar->kode."</td>
    <td>".$bar->namatraining."</td>
    <td>".$jab[$bar->jabatan]."</td>
    <td>".$host[$bar->penyelenggara]."</td>

    <td align=right>".number_format($bar->hargasatuan,2,'.',',')."</td>
    <td>
        <img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->jabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".$bar->desctraining."','".$bar->output."');\">
    </td>
    <td>
        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">
    </td>
    <td>
        <img src=images/application/application_form.png class=resicon  title='desc and result' onclick=\"desctraining('".$bar->kode."',event);\">
    </td>
    </tr>";	
    $no+=1;
}	  
		
echo"	
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    </fieldset>";
CLOSE_BOX();

close_body();
?>