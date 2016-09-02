<?php
//==================================================
//網站檢查程式
//Date: 20150914
//Program: Jay Hsu
//==================================================

ignore_user_abort(true); //允許背景處理機制
set_time_limit(0);	//不限制處理時間

//param===========================================================================================
$action = (isset($_GET["action"]) && !empty($_GET["action"]))?$_GET["action"]:"";
$check = (isset($_REQUEST["check"]) && !empty($_REQUEST["check"]))?trim(urldecode($_REQUEST["check"])):"";
$page = (isset($_REQUEST["page"]) && !empty($_REQUEST["page"]))?trim(urldecode($_REQUEST["page"])):"";
$url = (strstr($page,'http://') || strstr($page,'https://'))?$page:'http://'.$page;
$siteDNS = (substr($url,-1) != "/")?$url."/":$url;
$insiteLink = array($url);
$matchNote = array();

//function===========================================================================================



//action===========================================================================================
if ($action == "convert"){

	if (isset($page) && !empty($page)) {
		echo '<a href="'.$url.'" target="new">'.$url.'</a><br>';
	}

}else if ($action == "loading_time") {

	   $udid = md5(time().rand());
	   $frame_id = "frame_".$udid;
	   $my_frame_id = "myframe_".$udid;
	   $set_time_out_second = 30; //設定逾時秒
	   $set_time_out = $set_time_out_second*1000;

	   echo '<div id="'.$frame_id.'" class="frame_container">
	            <iframe width="1" height="1" id="'.$my_frame_id.'" src="'.$url.'" style="position:absolute;top:-1000px;left:-1000px;z-index:-1000;visibility:hidden" sandbox="allow-same-origin"></iframe>
	            <span>URL: <a href="'.$url.'" target="new">'.$url.'</a> ----- Loading Time: <span class="loadingtime"><span style="color:blue">Caculate...</span></span></span>
	         </div>
	         <script>
				var beforeLoad_'.$udid.' = (new Date()).getTime();

				var setTimeID_'.$udid.' = setTimeout(function(){
				    $(\'#'.$my_frame_id.'\').remove();
				    $(\'#'.$frame_id.' .loadingtime\').html(\'<span style="color:red">Time out for over '.$set_time_out_second.'sec </span>\');
				},'.$set_time_out.');

				$(\'#'.$my_frame_id.'\').on(\'load\', function() {
				    var afterLoad_'.$udid.' = (new Date()).getTime();
				    var result_'.$udid.' = (afterLoad_'.$udid.' - beforeLoad_'.$udid.') / 1000;
				    var output_'.$udid.' = (result_'.$udid.' > 5) ? \'<span style="color:orange">\'+result_'.$udid.'+\'</span>\' : \'<span style="color:green">\'+result_'.$udid.'+\'</span>\';
				    $(\'#'.$frame_id.' .loadingtime\').html(output_'.$udid.'+\'sec\');
				    clearTimeout(setTimeID_'.$udid.');
				});
			</script>
	    ';

}

?>