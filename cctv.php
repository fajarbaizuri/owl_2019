<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<?php
#=======Table============
# Display Table
echo "<div style='clear:both;float:left'>";
echo "<OBJECT classid='clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921'";
echo "codebase='http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab'";
echo "width='320' height='240' id='vlc' events='True'>";
echo "<param name='Src' value='rtsp://202.95.134.166:2000/video.mp4' />";
echo "<param name='ShowDisplay' value='True' />";
echo "<param name='AutoLoop' value='False' />";
echo "<param name='AutoPlay' value='True' />";
echo "<embed id='vlcEmb'  type='application/x-google-vlc-plugin' version='VideoLAN.VLCPlugin.2' autoplay='yes' loop='no' width='320' height='240'";
echo "target='rtsp://202.95.134.166:2000/video.mp4' ></embed>";
echo "</OBJECT>";
echo "<OBJECT classid='clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921'";
echo "codebase='http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab'";
echo "width='320' height='240' id='vlc' events='True'>";
echo "<param name='Src' value='rtsp://202.95.134.166:1000/video.mp4' />";
echo "<param name='ShowDisplay' value='True' />";
echo "<param name='AutoLoop' value='False' />";
echo "<param name='AutoPlay' value='True' />";
echo "<embed id='vlcEmb'  type='application/x-google-vlc-plugin' version='VideoLAN.VLCPlugin.2' autoplay='yes' loop='no' width='320' height='240'";
echo "target='rtsp://202.95.134.166:1000/video.mp4' ></embed>";
echo "</OBJECT>";
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>