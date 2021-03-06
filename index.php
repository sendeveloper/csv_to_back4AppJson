<?php
error_reporting(E_ALL | E_WARNING); 

function array2json($arr) { 
    if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality. 
    $parts = array(); 
    $is_list = false; 

    //Find out if the given array is a numerical array 
    $keys = array_keys($arr); 
    $max_length = count($arr)-1; 
    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
        $is_list = true; 
        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
            if($i != $keys[$i]) { //A key fails at position check. 
                $is_list = false; //It is an associative array. 
                break; 
            } 
        } 
    } 

    foreach($arr as $key=>$value) { 
        if(is_array($value)) { //Custom handling for arrays 
            if($is_list) $parts[] = array2json($value); /* :RECURSION: */ 
            else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
        } else { 
            $str = ''; 
            if(!$is_list) $str = '"' . $key . '":'; 

            //Custom handling for multiple data types 
            if(is_numeric($value)) $str .= $value; //Numbers 
            elseif($value === false) $str .= 'false'; //The booleans 
            elseif($value === true) $str .= 'true'; 
            else $str .= '"' . addslashes($value) . '"'; //All other things 
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?) 

            $parts[] = $str; 
        } 
    } 
    $json = implode(',',$parts); 
     
    if($is_list) return '[' . $json . ']';//Return numerical JSON 
    return '{' . $json . '}';//Return associative JSON 
} 

$row = 1;
$result = array("results" => []);
if (($handle = fopen("1.csv", "r")) !== FALSE) {
  	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    $num = count($data);
	    // echo "<p> $num fields in line $row: <br /></p>\n";
	    $row++;
	    if ($row < 5379)
	    {
	        $str = utf8_encode($data[0]);
	        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	        echo "<p> $row: $str</p>\n";
	        if ($str)
	        {
	        	$each = array();
	        	$each["Composer"] = $str;
	        	$each["Genre"] = $data[1];
		        $each["createdAt"] = "2018-08-14T02:55:04.226Z";
		        $each["updatedAt"] = "2018-08-14T02:55:04.226Z";
		        array_push($result["results"], $each);
	        }
	    }
 	}
	fclose($handle);
	$json = json_encode($result, 0, 5);
	file_put_contents("cg.json", $json);
}
?>