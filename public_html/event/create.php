<?php  
header('Content-Type: application/json; charset=utf-8');
include('../../db.php');
include('../httpStatusCode.php');

try { 
 $pdo = new PDO("mysql:host=$db[host];dbname=$db[dbname];port=$db[port];charset=$db[charset]", $db['username'], $db['password']);
} catch (PDOException $e) {
	echo "Databast connection failed.";
	exit;
}


//validation
//title
if(empty($_POST['title'])) 
	//error
	new HttpStatusCode(400, "Title cannto be blank.");

//time range

$startTime = explode(':', $_POST['start_time']);
$endTime = explode(':', $_POST['end_time']);
if($startTime[0]>$endTime[0] || $startTime[0]==$endTime[0] && $startTime[1]>$endTime[1]) 

	new HttpStatusCode(400, "Time range error.");




$sql = 'INSERT INTO events (title, year, month, `date`, start_time, end_time, description)
		VALUES (:title, :year, :month, :date, :start_time, :end_time, :description)';

$statement = $pdo->prepare($sql);
$statement->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
$statement->bindValue(':year', $_POST['year'], PDO::PARAM_INT);
$statement->bindValue(':month', $_POST['month'], PDO::PARAM_INT);
$statement->bindValue(':date', $_POST['date'], PDO::PARAM_INT);
$statement->bindValue(':start_time', $_POST['start_time'], PDO::PARAM_STR); //無時間格式一樣用字串
$statement->bindValue(':end_time', $_POST['end_time'], PDO::PARAM_STR);
$statement->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
$result = $statement->execute();

if($result) {
	$id = $pdo->lastInsertId();
	//若成功存入資料，撈出此筆資料（藉由id去找)傳給前端印出
	$sql = 'SELECT id, title, start_time FROM events WHERE id=:id'; 

	$statement = $pdo->prepare($sql);
	$statement->bindValue(':id', $id, PDO::PARAM_INT);  //從id去撈資料，填入要找的那個id
	$statement->execute();
	$event = $statement->fetch(PDO::FETCH_ASSOC);
	//原顯示10:00:00 用substr截短字串取第0-5個字元，將秒數剪掉
	$event['start_time'] = substr($event['start_time'], 0, 5);

	echo json_encode($event);

} else {
	echo "error";
}
