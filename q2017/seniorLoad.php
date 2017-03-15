<html>
<head>
<title>Title</title>
</head>
<body>
<?php
	$dbq = new SQLite3('quotations2017.sqlite3'); //connect
	$dbm = new SQLite3('masterStudent16-17.sqlite3'); //connect
	$statement = $dbm -> prepare('SELECT * FROM master WHERE Grade = 12;');
	$result = $statement -> execute();
	$lastName[] = [];
	$id[] = [];
	//get first name
	/*
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		array_push($lastName, $row['Last']);
		array_push($id, $row['Student Number']);
	}
	$i = 0;
	foreach($lastName as &$name){
		$num = $id[$i];
		echo "$num, $name <br>";
		$i++;
	}
	*/
	while($row = $result->fetchArray(SQLITE3_ASSOC)){
		$last = $row['Last'];
		$first = $row['First'];
		$num = $row['Student Number'];
		echo "$num, $last <br>";
	}
?>
Meow
</body>
</html>