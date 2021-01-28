<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/bgt_prproduksi.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','Proporsi biaya produk dan Harga Produk Budget:');

$optkodeorg='';
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optkodeorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
if($optkodeorg=='')
{
  exit('Please login as Mill Employee');
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type=text id=tahun size=5 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg>".$optkodeorg."</select></td></tr>    
      <tr><td>Nilai Stok Awal CPO (Rp.)</td><td><input type=text id=stokcpo size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber></td></tr>
      <tr><td>Nilai Stok Awal PK (Rp.)</td><td><input type=text id=stokpk size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber></td></tr>
      <tr><td>Harga CPO (Rp.)/(Kg.)</td><td><input type=text id=hargacpo size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber></td></tr>
      <tr><td>Harga PK (Rp.)/(Kg.)</td><td><input type=text id=hargapk size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber></td></tr>
      <tr><td>Proporsi Biaya CPO(%)</td><td><input type=text id=porsicpo size=12 onkeypress=\"return angka_doang(event);\" onblur=getSisa(this.value) class=myinputtextnumber></td></tr>
      <tr><td>Proporsi Biaya PK(%)</td><td><input type=text id=porsipk size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber></td></tr>      
</table>
        <input type=hidden id=method value='insert'>
        <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
        <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
        </fieldset>";
echo open_theme($_SESSION['lang']['list']);
echo "<div>";
       $str1="select a.*,b.namakaryawan from ".$dbname.".bgt_prproduk a left join ".$dbname.".datakaryawan b
            on a.updateby=b.karyawanid where a.kodeorg='".$_SESSION['empl']['lokasitugas']."' order by a.tahunbudget desc";
       $res1=mysql_query($str1);
       echo"<table class=sortable cellspacing=1 border=0 style='width:800px;'>
            <thead>
                <tr class=rowheader>
                   <td>".$_SESSION['lang']['nourut']."</td>
                       <td>".$_SESSION['lang']['budgetyear']."</td>
                       <td>".$_SESSION['lang']['kodeorg']."</td>
                       <td>Nilai Awal CPO(Rp)</td>
                       <td>Nilai Awal PK (Rp)</td>
                       <td>Harga/Kg CPO</td>
                       <td>Harga/Kg PK</td>
                       <td>Proporsi Biaya CPO(%)</td>
                       <td>Proporsi Biaya PK(%)</td>               
                       <td>UpdateBy</td> 
                       <td style='width:30px;'>*</td></tr>
                </thead>
                <tbody id=container>"; 
       $no=0;
       while($bar1=mysql_fetch_object($res1))
       {
           $no+=1;
               echo"<tr class=rowcontent>
                          <td >".$no."</td>
                                  <td>".$bar1->tahunbudget."</td>
                                  <td>".$bar1->kodeorg."</td>
                                  <td>".$bar1->rupiahstokawalcpo."</td>
                                  <td>".$bar1->rupiahstokawalpk."</td>
                                  <td>".$bar1->hargasatuancpo."</td>
                                   <td>".$bar1->hargasatuanpk."</td>
                                    <td>".$bar1->peroporsibiayacpo."</td>
                                    <td>".$bar1->proporsibiayapk."</td>
                                    <td>".$bar1->namakaryawan."</td>    
                                  <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->kodeorg."','".$bar1->rupiahstokawalcpo."','".$bar1->rupiahstokawalpk."','".$bar1->hargasatuancpo."','".$bar1->hargasatuanpk."','".$bar1->peroporsibiayacpo."','".$bar1->proporsibiayapk."');\"></td></tr>";
       }	 
       echo"	 
                </tbody>
                <tfoot>
                </tfoot>
                </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>