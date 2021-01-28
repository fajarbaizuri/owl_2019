<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/asset.js></script>
<?
include('master_mainMenu.php');

//limit/page
$limit=20;
$page=0;
  if(isset($_POST['page']))
     {
	 	$page=$_POST['page'];
	    if($page<0)
		  $page=0;
	 }
  $offset=$page*$limit;
//===========================
//===========================

	$str="select a.*		  
		  from ".$dbname.".sdm_daftarasset a
		  where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'";
	$res=mysql_query($str);	 
	$jlhbrs=mysql_num_rows($res);
	//===================================================
//ambil option organisasi
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where 
      tipe in('HOLDING','KEBUN','KANWIL','PABRIK','LINE')
	  and kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by namaorganisasi";
$res=mysql_query($str);
$optOrg="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
}  
//=====================
//tipe asset
$str=" select * from ".$dbname.".sdm_5tipeasset order by namatipe";
$res=mysql_query($str);
$optAss="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optAss.="<option value='".$bar->kodetipe."'>".$bar->namatipe."</option>";
}
//=========================================
//awal penyusutan
$optper="<option value=''></option>";
for($x=0;$x<=200;$x++)
{
	$d=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$da=date('Y-m',$d);
	$di=date('m-Y',$d);
	$optper.="<option value='".$da."'>".$di."</option>";
}
//===========================
//option status
$optStat="<option value='1'>".$_SESSION['lang']['aktif']."</option>";
$optStat.="<option value='2'>".$_SESSION['lang']['rusak']."</option>";
$optStat.="<option value='3'>".$_SESSION['lang']['hilang']."</option>";
$optStat.="<option value='0'>".$_SESSION['lang']['pensiun']."</option>";

//===========================

OPEN_BOX('','');
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/plus.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/book_icon.gif title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['caripadanama'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo"<button class=mybutton onclick=cariAsset(0)>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
CLOSE_BOX();


OPEN_BOX('',$_SESSION['lang']['aset']);
$frm[0]="<fieldset><legend>".$_SESSION['lang']['inputaset']."</legend>
     <table><tr><td>
	 ".$_SESSION['lang']['kodeorganisasi']."</td><td><select id=kodeorg>".$optOrg."</select></td>
	 </td><td>".$_SESSION['lang']['namakelompok']."</td><td><select id=tipe onchange=cek(this)>".$optAss."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['kodeasset']."</td><td><input type=text id=kodeaset maxlength=20 class=myinputtext onkeypress=\"return tanpa_kutip(event)\" size=40></td>
	 </td><td>".$_SESSION['lang']['namaaset']."</td><td><input type=text id=kodebarang onkeypress=\"return false;\" onclick=\"showWindowBarang('Cari Barang',event);\" class=myinputtext size=10 maxlength=11> <input type=text id=namaaset maxlength=45 class=myinputtext onkeypress=\"return tanpa_kutip(event)\" size=40></td></tr>
	 <tr><td>".$_SESSION['lang']['tahunperolehan']."</td><td><input type=text id=tahunperolehan  class=myinputtextnumber  onkeypress=\"return angka_doang(event);\" size=5 maxlength=4></td>
	 </td><td>".$_SESSION['lang']['status']."</td><td><select id=status>".$optStat."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['hargaperolehan']."</td><td><input type=text value=0 class=myinputtextnumber id=nilaiperolehan onkeypress=\"return angka_doang(event);\" size=12 maxlength=15></td>
	 </td><td>".$_SESSION['lang']['jumlahbulanpenyusutan']."</td><td><input type=text value=0 class=myinputtextnumber id=jumlahbulan onkeypress=\"return angka_doang(event);\" size=5 maxlength=3></td></tr>
	 <tr><td>".$_SESSION['lang']['awalpenyusutan']."</td><td><select id=bulanawal>".$optper."</select></td>
	 </td><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text class=myinputtext  id=keterangan size=40 maxlenth=100  onkeypress=\"return tanpa_kutip(event)\"></td></tr>
	 </table>
	 <input type=hidden value=insert id=method>
	<button class=mybutton onclick=simpanAssetBaru()>".$_SESSION['lang']['save']."</button>
	<button class=mybutton onclick=cancelAsset()>". $_SESSION['lang']['cancel']."</button>
	</fieldset>	 
	 ";
$frm[1]="<fieldset><legend>".$_SESSION['lang']['list']."</legend>
         <div style='height:400px;overflow:scroll;'>
		 <table class=sortable  border=0 cellspacing=1>
		 <thead>
		   <tr class=rowheader>
		      <td align=center>".$_SESSION['lang']['nourut']."</td>
			  <td align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
			  <td align=center>".$_SESSION['lang']['namakelompok']."</td>
			  <td align=center>".$_SESSION['lang']['kodeasset']."</td>
			  <td align=center>".$_SESSION['lang']['namaaset']."</td>
			  <td align=center>".$_SESSION['lang']['tahunperolehan']."</td>
			  <td align=center>".$_SESSION['lang']['status']."</td>
			  <td align=center>".$_SESSION['lang']['hargaperolehan']."</td>
			  <td width=20 align=center>".$_SESSION['lang']['jumlahbulanpenyusutan']."</td>
			  <td align=center>".$_SESSION['lang']['awalpenyusutan']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>*</td>
		   </tr>
		   </thead>		   
		 <tbody id=containeraset>
		   ";
	$str="select a.*,b.namatipe, 
	      CASE a.status
		  when 0 then '".$_SESSION['lang']['pensiun']."'
		  when 1 then '".$_SESSION['lang']['aktif']."' 
		  when 2 then '".$_SESSION['lang']['rusak']."' 
		  when 3 then '".$_SESSION['lang']['hilang']."' 
		  else 'Unknown'
          END as stat		  
		  from ".$dbname.".sdm_daftarasset a
	      left join  ".$dbname.".sdm_5tipeasset b
	      on a.tipeasset=.b.kodetipe
		  where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by tahunperolehan desc,awalpenyusutan desc,namatipe asc
          limit ".$offset.",".$limit;
		  
	$res=mysql_query($str);
	$no=$offset;
	while($bar=mysql_fetch_object($res))
	{
	  $no+=1;
	  $frm[1].=" <tr class=rowcontent>
	          <td>".$no."</td>
		      <td width=10 align=center>".$bar->kodeorg."</td>
			  <td>".$bar->namatipe."</td>
			  <td width=20 align=center>".$bar->kodeasset."</td>
			  <td>".$bar->namasset."</td>
			  <td width=20 align=center>".$bar->tahunperolehan."</td>
			  <td width=20 align=center>".$bar->stat."</td>
			  <td width=100 align=right>".number_format($bar->hargaperolehan,2,'.',',')."</td>
			  <td width=20 align=right>".$bar->jlhblnpenyusutan."</td>
			  <td width=20 align=center>".substr($bar->awalpenyusutan,5,2)."-".substr($bar->awalpenyusutan,0,4)."</td>
			  <td>".$bar->keterangan."</td>
			  <td>
			   <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAsset('".trim($bar->kodeorg)."','".$bar->tipeasset."','".$bar->kodeasset."','".$bar->namasset."','".$bar->kodebarang."','".$bar->tahunperolehan."','".$bar->stat."','".$bar->hargaperolehan."','".$bar->jlhblnpenyusutan."','".$bar->awalpenyusutan."');\">
		      &nbsp </td>
		   </tr>";		
	}	/*<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAsset('".$bar->kodeorg."','".$bar->kodeasset."');\">
			  */
  $frm[1].="<tr><td colspan=12 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariAsset(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariAsset(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	  	   
$frm[1].="
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>
		 </div>
		 </fieldset>
		";	 

$hfrm[0]=$_SESSION['lang']['inputaset'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1000);
CLOSE_BOX();
echo close_body();
?>