<?
require_once('master_validation.php');
require_once('config/connection.php');
/*
$karyawanid=$_POST['karyawanid'];
$path='photokaryawan';
if(($_FILES['photo']['size']) <= $_POST['MAX_FILE_SIZE'])
{
//the full path is photokaryawan/$karyawanid.ext
  if(is_dir($path))
  {
  	writeFile($path);
	chmod($path, 0777);
  }
  else
  {
  	if(mkdir($path))
	{
      writeFile($path);
	  chmod($path, 0777);
	}
	else
	{
		echo "<script> alert('Gagal, Can`t create folder for uploaded file');</script>";
		exit(0);
	}
  } 
}
else
{
echo "<script>File size is ".filesize($_FILES['photo']['tmp_name']).", greater then allowed</script>";	
}
  
function writeFile($path)
{ 
	   global $karyawanid;
	   global $conn;
	   global $dbname;
	   $dir=$path;
	     $ext=split('[.]', basename( $_FILES['photo']['name']));
		 $ext=$ext[count($ext)-1];
		 $ext=strtolower($ext);
		 if($ext=='jpg' or $ext=='jpeg' or $ext=='gif' or $ext=='png' or $ext=='bmp')
		 {
		 $path = $dir."/".$karyawanid.".".$ext;
		 //remove if exist;
         //write to a file
		 try{
		 	if(move_uploaded_file($_FILES['photo']['tmp_name'], $path))
			{ 
			   	$str="update ".$dbname.".datakaryawan set photo='".$path."'
				      where karyawanid=".$karyawanid;
				mysql_query($str);
				//echo mysql_error($conn);
				if(mysql_affected_rows($conn)>0)
				{	  
				echo"<script>
					parent.document.getElementById('displayphoto').removeAttribute('src');
					parent.document.getElementById('displayphoto').setAttribute('src','".$path."');
					//parent.document.getElementById('displayphoto').getAttribute('src').value;
					</script>";
				}	
		     		 chmod($path, 0775);					
			}
		  }
		  catch(Exception $e)
		  {
		  	echo "<script>alert(\"Error Writing File".addslashes($e->getMessage())."\");</script>";
		  }
		 }
		 else
		 {
		  	echo "<script>alert('Filetype not support');</script>";		 	
		 }
}
?>
*/
chmod($target_file, 0777);
$karyawanid=$_POST['karyawanid'];
$target_dir = "photokaryawan/";
$target_file = $target_dir . basename($_FILES["photo"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if($check !== false) {
        echo "<script>alert('File is an image - " . $check["mime"] . ".')</script>";
        $uploadOk = 1;
    } else {
        echo "<script>alert('File is not an image.')</script>";
        $uploadOk = 0;
	
    }

// Check if file already exists
if (file_exists($target_file)) {
    echo "<script>alert('Sorry, file already exists.')</script>";
    $uploadOk = 0;

}
// Check file size
if ($_FILES["photo"]["size"] > 100000) {
    echo "<script>alert('Sorry, your file is too large.')</script>";
    $uploadOk = 0;

}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   echo "<script>alert('Sorry, your file was not uploaded.')</script>";
// if everything is ok, try to upload file
} else {
	
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
			$str="update ".$dbname.".datakaryawan set photo='".$target_file."'
				      where karyawanid=".$karyawanid;
				mysql_query($str);
				//echo mysql_error($conn);
				if(mysql_affected_rows($conn)>0)
				{	  
				echo"<script>
					parent.document.getElementById('displayphoto').removeAttribute('src');
					parent.document.getElementById('displayphoto').setAttribute('src','".$target_file."');
					//parent.document.getElementById('displayphoto').getAttribute('src').value;
					</script>";
				}	
		     		 chmod($path, 0775);	
        echo "<script>alert('The file ". basename( $_FILES["photo"]["name"]). " has been uploaded.')</script>";
    } else {
        echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
    }
}

?>
