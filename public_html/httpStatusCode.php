<?php 


class HttpStatusCode {
	function __construct($code, $msg) {
		http_response_code($code);
		echo $msg;
		exit();  //記得離開程式否則會繼續執行下方插入資料庫
		
	}
}
