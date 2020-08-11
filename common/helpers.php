<?php 
function p_() {

	$args = func_get_args();
	$num_args = func_num_args();
	$label = "";
	if($num_args>0){
		$last_arg = func_get_arg($num_args-1);
		echo "<div><pre>";
		echo "<div style='margin: 10px; margin-top: 70px; border:0px; padding: 2px;'>";
		$background_color = 'green';
		if(is_string($last_arg) && ($last_arg!="") && substr($last_arg,0,6)==='__lab:'){
			$label = substr($last_arg, 6, strlen($last_arg));
			unset($args[$num_args-1]);
			$label_error = strtolower($label);
			if($label_error == 'error' || $label_error == 'exception'){
				$label = 'Exception';
				$background_color = '#C42732';
			}
		}else{
			$label = "PRINT";
		}

		$file_info_used = print_debug('1', false, true);

		// if(is_string($last_arg) && ($last_arg!="") && substr($last_arg,0,4)==='__^:'){
		// 	$key_begins_with = substr($last_arg, 3, strlen($last_arg));
		// 	unset($args[$num_args-1]);
		// 	$label = "BEGINS WITH";
		// }

		echo "<div style='margin:10px; margin-bottom:10px;'>".
		"<span style=\"background-color: $background_color; color: white; font-size: 12px; padding: 5px; border: 2px solid black;\"><b>"
		. $label . "</b></span></div>";
	
		$count = 1;

		foreach($args as $arg){

			echo "<div style='margin: 10px 10px 2px 10px; border:2px solid black; padding: 10px;'>";
			if(count($args)>1){			
				echo "<span style='font-size: 12px; font-weight: bold; color: red; padding:2px;'>Variable: ".$count."</span></br>";
			}
			if(is_string($arg)){
				if(is_null($arg) || $arg == 'null'){
					echo "<span style='color:red; font-weight: bold; font-size:14px;'>".htmlentities($arg)."</span>"; 
				}else{
					echo "<span style='color:green; font-weight: bold; font-size: 14px;'>".htmlentities($arg)."</span>";
				}
			}else{
				print_r($arg);
			}
			echo "</div>";
			// echo "<div style=\"height:10px;\"></div>";
			++$count;
		}
		echo "<div style='font-style:italic; padding-left: 10px; font-size: 10px; text-align:right; margin:0px; padding: 0px;'>$file_info_used</div></div>";
		echo "</pre></div>";
		// echo "<br/>";
	}
	return;
}

function d_( $data, $name = null, $obj = null ) {
	$objClass = ($obj ? get_class( $obj ) : null);
	$msg = "";
	if( $objClass )  { $msg .= "<div>class: <span style='color:blue;'>$objClass</span></div>"; }
	$msg .= "<div><b>$name</b></div>";
	echo "<div style='margin: 3px; border:1px solid silver; padding: 2px;'>";
	echo $msg;
	if( $data ) { echo "<pre>"; print_r( $data ); echo "</pre>"; }
	echo "</div>";
}

function print_debug($step_back=2, $fb=false, $file_info_only=false){

    $debug = debug_backtrace();
    $function = $debug[$step_back]['function'];
    $line = $debug[$step_back]['line'];
    $args = $debug[$step_back]['args'];
    $file = $debug[$step_back]['file'];
    if($file_info_only){
    	return $file.' => LINE:'.$line;
    }

    if($fb==false){
	    d_('called function:'.$function);
	    d_('called line:'.$line);
	    d_('called arguments:'.$args);
	    d_('called file:'.$file);
	}else{
		fb_('called function:'.$function);
		fb_('called line:'.$line);
		fb_('called arguments:'.$args);
		fb_('called file:'.$file);
	}
}

function browser_fb_compatible(){
	$b = browser_info();
	if(!isset($b['UA'])){
		return false;
	}
	$info = $b['UA'];

	if (strlen(strstr($info, 'Firefox')) > 0) {
		return  true;
	}

	if (strlen(strstr($info, 'Chrome')) > 0) {
		return  true;
	}
	return false;
}



function fb_($param1, $param2='', $exit=false){
	if(CONFIG::$ENV!='development'){
		return;
	}
	$is_compatible = browser_fb_compatible();

	if($is_compatible && function_exists ( 'fb' )){
		fb($param1, $param2);	
		if($exit){
			die();
		}
	}
	return;
}

function fb__($param1, $param2=''){
	fb_($param1, $param2, true);
}
?>

