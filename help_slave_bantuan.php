<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    include_once('lib/zLib.php');
	

    if($proses=='loaddata'){
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
       
        $q="select count(*) as jmlhrow from ".$dbname.".owl_help order by `kode` asc";
        $query=mysql_query($q) or die(mysql_error());
        while($jsl=mysql_fetch_object($query)){
            $jlhbrs= $jsl->jmlhrow;
        }
        
        $offset=$page*$limit;
        if($jlhbrs<($offset))$page-=1;
        $offset=$page*$limit;
        $no=$offset;
        
        $q2="select * from ".$dbname.".owl_help order by `kode` asc,'kode',`tentang`,`modul`,`isi` limit ".$offset.",".$limit." ";
        $query2=mysql_query($q2) or die(mysql_error());
        //$user_online=$_SESSION['standard']['userid'];
        while($row=mysql_fetch_assoc($query2))
        {
            $no+=1;

            echo"<tr class=rowcontent>
            <td id='no'>".$no."</td>
            <td id='index_".$row['kode']."' value='".$row['kode']."'>".$row['kode']."</td>
            <td id='modul_".$row['kode']."' value='".$row['modul']."'>".$row['modul']."</td>
            <td id='tentang_".$row['kode']."' value='".$row['tentang']."'>".$row['tentang']."</td>
            <td><img onclick=\"detailHelp(event,'".str_replace(" ","",$row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>";
        }
        echo"
        </tr><tr class=rowheader><td colspan=3 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr>";
    }
    
    if((isset($_POST['cariindex']))!='')
    {
        $indexfind=$_POST['cariindex'];
	$str="select * from ".$dbname.".owl_help where (kode like '%".$indexfind."%') or (tentang like '%".$indexfind."%') or (modul like '%".$indexfind."%')  ";
	if($res=mysql_query($str))
	{
            $no=0;	 
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
		echo"<tr class=rowcontent>
                    <td id='no'>".$no."</td>
                    <td id='index_".$bar->kode."' value='".$bar->kode."'>".$bar->kode."</td>
                    <td id='modul_".$bar->kode."' value='".$bar->modul."'>".$bar->modul."</td>
                    <td id='tentang_".$bar->kode."' value='".$bar->tentang."'>".$bar->tentang."</td>
                    <td><img onclick=\"detailHelp(event,'".str_replace(" ","",$bar->kode)."','".$bar->modul."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>
                    
                    </tr>";
               
		}	 
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}	
    }
    
  
?>