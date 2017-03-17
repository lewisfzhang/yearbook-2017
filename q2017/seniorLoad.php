<html>
<head>
<title>Senior Load</title>
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
		//get vals from master table
		$last = $row['Last'];
		$first = $row['First'];
		$num = $row['Student Number'];
		
		//get email from seniorEmails table
		$statement2 = $dbq -> prepare('SELECT StudentEmail FROM seniorEmails WHERE FirstName = :first AND LastName = :last;');
		$statement2 -> bindValue(':first', $first);
		$statement2 -> bindValue(':last', $last);
		$result2 = $statement2 -> execute();
		while($row2 = $result2->fetchArray(SQLITE3_ASSOC)){
			$email = $row2['StudentEmail'];
		}
		
		//create new row in quotations for each
		$statement3 = $dbq -> prepare('INSERT INTO quotations (id, lastName, firstName, email) VALUES (:num, :last, :first, :email);');
		$statement3 -> bindValue(':num', $num);
		$statement3 -> bindValue(':last', $last);
		$statement3 -> bindValue(':first', $first);
		$statement3 -> bindValue(':email', $email);
		/*
		if($statement3 -> execute()){
			echo "$num, $first $last, $email <br>";
		}
		*/
	}
?>
Meow
</body>
</html>