<?
//http://www.emailtextmessages.com/
$contents = file_get_contents("/Users/cesargonzalez/Desktop/carrier-list.txt");


$list = array();
$contents = explode("\n", $contents);
$contents = array_filter($contents);
$contents = array_values($contents);

//set the initial list
$list = array();
foreach($contents as $key => $value) 
{
	if(preg_match("/@/", $value)) {


		$type = 'pager';
		if(preg_match("/^(10digitphonenumber|number)@/", $value)) {
			$type = 'phone';
		} elseif(preg_match("/(pinnumber)@/", $value)) {
			$type = 'pin';
		}

		$list[$key-1] = array(
			'name' => $list[$key - 1]['name'],
			'email' => str_replace("@@","@", $value),
			'type' => $type
		);
	} else {
		$list[$key] = array(
			'name' => $value
		);
	}
}

//filter the next list
$count=0;
foreach($list as $l) {
	if(isset($l['email'])) {
		$sql = "INSERT INTO sms_providers (name, email, type) VALUES ('".mysql_real_escape_string($l['name'])."','{$l['email']}','{$l['type']}');";
		echo $sql."\n";
		$count++;
	}
}

echo "Found $count providers - complete\n";
?>