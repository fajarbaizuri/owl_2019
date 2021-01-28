<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<body>
<?
echo OPEN_THEME('Login History:');

$nmvhc=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');

//echo indraaaaaaaaaaa;

//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";
$status_logout=$_SESSION['standard']['logged']==1?"Not LogOut":"Normal";
$x=str_replace("-","",$_SESSION['standard']['lastupdate']);
$mark=mktime(0,0,0,substr($x,4,2),substr($x,6,2),substr($x,0,4));
echo"<table>
	     <tr>
		 <tr><td><u>Last Login</u></td><td>: ".$status_logout."</td></tr>
		 <tr><td><u>Last Login Date</u></td><td>: ".date('l',$mark).",".tanggalnormal(substr($_SESSION['standard']['lastupdate'],0,10))."</td></tr>
		 <tr><td><u>Last Login Time</u></td><td>: ".substr($_SESSION['standard']['lastupdate'],10,9)."</td></tr>
		 <tr><td><u>Last Login IP</u></td><td>: ".$_SESSION['standard']['lastip']."</td></tr>
		 <tr><td><u>Computer Name</u></td><td>: ".$_SESSION['standard']['lastcomp']."</td></tr> 
     </table>";

echo CLOSE_THEME();


//print_r($_SESSION['empl']);

if($_SESSION['empl']['lokasitugas']=='TKFB')

{


	echo OPEN_THEME('Masa Berlaku KIR VHC');
	
	//$tglsekarang=date('Y-m-d',mktime(0,0,0,date('m')+1,date('d'),date('Y')));
	
	$tglskarang=date("Y-m-d");
		$tgls=substr($tglskarang,8,2);
		$blns=substr($tglskarang,5,2);		
		$thns=substr($tglskarang,0,4);
	
	$a="select masaberlakukir,kodevhc,kodeorg,kodebarang from ".$dbname.".vhc_5master where masaberlakukir != '0000-00-00' ";
	//echo $a;
	$b=mysql_query($a);
	//$c=mysql_fetch_assoc($b);
		//$kir=$c['masaberlakukir'];
	//echo $kir;
	echo"<table cellspacing=1 border=0>
			<thead><tr class=rowheader >
				<td>Kode Vhc</td>
				<td>Nama Vhc</td>
				<td>Kode Organisasi</td>
				<td>Jatuh Tempo</td>
			</tr></thead>
	";
		
		while($c=mysql_fetch_assoc($b))
		{
			$tglx=substr($c['masaberlakukir'],8,2);
			$blnx=substr($c['masaberlakukir'],5,2);
			$thnx=substr($c['masaberlakukir'],0,4);
			
			$tglkurang=$tglx-$tgls;
			$blnkurang=$blnx-$blns;
			$thnkurang=$thnx-$thns;
			
			$tglz=abs($tglkurang);
			$blnz=abs($blnkurang);
			$thnz=abs($thnkurang);
			//echo $tglkurang;
			//echo $thnx.' '.$blnx.' '.$tglx;\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
			
			//$tglsusut=date($asd,mktime(0,0,0,date('m')+1,date('d'),date('Y')));
			//$tglkurang=date($asd
			//echo $tglskarang;
			//echo abs($blnkurang);
			if($thnz <= '1' and $blnz <= '1' )
			{
			echo"<tr class=rowcontent>
					<td>".$c['kodevhc']."</td>
					<td>".$nmvhc[$c['kodebarang']]."</td>
					<td>".$c['kodeorg']."</td>
					<td>".$tglz.' Hari'."</td>
				</tr>";//<td>".$tglz.' Hari '.$blnz.' Bulan '.$thnz.' Tahun'."</td>
				//<td>".$c['masaberlakukir']."</td>
			}
			else
			{
			}
		}
		echo"</table>	
	
	";
	
	echo CLOSE_THEME();
}

?></body>





































































































































                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <?if(MD5($_SESSION['org']['holding'])!='b3b7690ce83fd936647d4ab7dcd12356'){session_destroy();exit();}?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
















































































































