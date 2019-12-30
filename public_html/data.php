<?php  

include('../db.php');

try { 
 $pdo = new PDO("mysql:host=$db[host];dbname=$db[dbname];port=$db[port];charset=$db[charset]", $db['username'], $db['password']);
} catch (PDOException $e) {
	echo "Databast connection failed.";
	exit;
}


$year = date('Y');
$month = date('m');

//load events                                                                         //asc漸進排序
$sql = 'SELECT * FROM events WHERE year=:year AND month=:month ORDER BY `date`, start_time ASC';
$statement = $pdo->prepare($sql);
$statement->bindValue(':year', $year, PDO::PARAM_INT);   //用年份跟月份去撈資料
$statement->bindValue(':month', $month, PDO::PARAM_INT);
$statement->execute();

$events = $statement->fetchAll(PDO::FETCH_ASSOC);



//把fetch出來的$event陣列做處理，讓10:00:00 > 10:00 
foreach ($events as $key => $event) {
	//注意:在each function中 $event值每次會不同，故新的值要塞進陣列中而非$event
	//$event = $events[$key] 但$event只是暫存的變數故會一直變
	$events[$key][start_time]	=  substr($event['start_time'], 0, 5);
}


//找出這個月的天數
$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//取出第一天星期幾
$firstDateOfTheMonth =  new DateTime("$year-$month-1");
//取出最後一天星期幾
$lastDateOfTheMonth =  new DateTime("$year-$month-$days");

//用將星期幾換成數字的方式，找出前後空格
$frontPadding = $firstDateOfTheMonth->format('w');
$backPadding = 6 - $lastDateOfTheMonth->format('w');


for($i=0; $i<$frontPadding; $i++) {
	$dates[] = null;
}


for($i=0; $i<$days; $i++) {
	$dates[] = $i+1;
}

for($i=0; $i<$backPadding; $i++) {
	$dates[] = null;
}

?>

<script>
	
var events = <?= $events = json_encode($events, JSON_NUMERIC_CHECK) ?>

</script> 



