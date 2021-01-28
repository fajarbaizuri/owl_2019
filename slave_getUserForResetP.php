<?
require_once('master_validation.php');
require_once('config/connection.php');
$uname=$_POST['uname'];

	$str="select * from ".$dbname.".user where namauser like '%".$uname."%'";
	$res=mysql_query($str);
	
	if(mysql_num_rows($res)>0)
	{
		echo"<b>Click on choosen row to show \"reset password form\".</b><hr>
            <table class=sortable cellspacing=1 border=0 onmousedown=sorttable.makeSortable(this)>
		     <thead>
			   <tr>
			   <td>Uname</td>
			   <td>UserId</td>
			   <td>Status</td>
			   </tr>
			 </theader>
			 <tbody>";
		while($bar=mysql_fetch_object($res))
		 {
			$opt='';
			if($bar->status==0)
			{
				$opt.="<font color=#aa3333>Not Active</font>"; 
			}
			else
			{
				$opt.="<font color=#00ff00>Active</font>"; 
			}
			echo" <tr class=rowcontent id='row".$bar->namauser."' title='Click to show dialog' style='cursor:pointer;' onclick=\"showDial('".$bar->namauser."','".$bar->karyawanid."',event,this);\">
			      <td class=firsttd>".$bar->namauser."</td>
				  <td>".$bar->karyawanid."</td>
				  <td align=center>".$opt."</td>
			 </tr>";
	      }
		echo"	 
			 </tbody>
		    </table>
			";
	}
	else
	{
		echo "No data found..";
	}
?>
