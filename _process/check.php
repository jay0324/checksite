<?php
//==================================================
//網站檢查程式
//Date: 20150914
//Program: Jay Hsu
//==================================================

include_once("../_class/simple_html_dom.php");
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
//using file_get_contents get front end html content
function fnGet($url) {
	 	$opts = array(  
			'http'=>array(  
				'method'=>"GET",  
				'timeout'=>60
			)  
		);  
		   
		$context = stream_context_create($opts);  

		$result = file_get_contents($url, false, $context);
		if ($result){
			return $result; 
		}else{
			printf("網頁[".$url."]無法透過此程式解析內容,請聯絡程式人員進行評估!");die();
		}
}

//using curl get front end html content
function fnCurl($curl){
	$url = $curl;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	if (curl_errno($ch)) {
		return false;
	}else{
		curl_close($ch);
		return $data;
	}
}

//check front end html content
function fnCheck(){
	global $check,$insiteLink;

	$search  = explode(",",$check);
	
	// converts the array into a regex friendly or list
	$patterns_flattened = implode('|', $search);

	for($i = 0; $i < count($insiteLink); $i++) {
		$pageContent = fnCurl($insiteLink[$i]);
		$pageContent = preg_replace('/<!--(.*)-->/Uis',"",$pageContent);

		if ( preg_match('/'. $patterns_flattened .'/', $pageContent, $matches) ) {
			$matchNote = $matches;
			return false;
		}

	}

	return true;
}

//找尋內頁連結
function fnFindLinks($url,$pageContent){
	global $insiteLink, $siteDNS;

	$html = file_get_html($url);
	$getLink = 0;

	foreach($html->find('a') as $element) {
		$updateStr = str_replace("../","",$element->href);
		if (!preg_match('/#|javascript|mailto|e-catalog|http/', $updateStr, $matches)) {
			if (!empty($updateStr)) {
				if ($updateStr != "/") {
					array_push($insiteLink, $siteDNS.$updateStr);
				}
       		}
       	}
    }

    /*foreach($html->find('link') as $element) {
		$updateStr = str_replace("../","",$element->href);
		if (!preg_match('/#|cdn|https/', $updateStr, $matches)) {
			if (!empty($updateStr)) {
				if ($updateStr != "/") {
					array_push($insiteLink, $siteDNS.$updateStr);
				}
       		}
       	}
    }*/

    $insiteLink = array_unique($insiteLink);
}

//action===========================================================================================
if ($action == "check"){

	if (isset($page) && !empty($page)) {

		$pageContent = fnCurl($url); //using curl to get page content
		fnFindLinks($url,strtolower($pageContent));

		for ($i = 0; $i < count($insiteLink); $i++){
			$checkPageList .= "[".$insiteLink[$i]."]<br>";
		}

		if (fnCheck()){
			$checkEcho = "OK";
			$class = 'ok';
		}else{
			$checkEcho = "需要修改";
			$class = 'not-ok';
		}

		echo "<div class='".$class." result-item'>(".date("Y-m-d H:i:s").") 檢查[".$url."]結果: ".$checkEcho." <br></div>";

	}
}

?>