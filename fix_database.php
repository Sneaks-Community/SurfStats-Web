<?php

$secure=1;

include("config.php");
//This file is to change the character set of the playerranks table from latin1 to UTF-8

$player_database = $db_prefix."playerrank";

if($_POST){

	if($_POST[mysql_setting]=="1"){
		$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		if (!$conn->query("ALTER DATABASE $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
			printf("Errormessage: %s\n", $conn->error);
		}else{
			echo"Database charset changed!<br/><br/>";
		}

		if (!$conn->query("ALTER TABLE $player_database CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
			printf("Errormessage: %s\n", $conn->error);
		}else{
			echo"<h1>Database charset changed! You may now delete this script!</h1><br/><br/>";
		}

		mysqli_close($conn);
	}else{
		$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		if (!$conn->query("ALTER DATABASE $db_name CHARACTER SET utf8 COLLATE utf8_unicode_ci")) {
			printf("Errormessage: %s\n", $conn->error);
		}else{
			echo"Database charset changed!<br/><br/>";
		}

		if (!$conn->query("ALTER TABLE $player_database CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")) {
			printf("Errormessage: %s\n", $conn->error);
		}else{
			echo"<h1>Database charset changed! You may now delete this script!</h1><br/><br/>";
		}

		mysqli_close($conn);
	}

}

?>

<html>
<head>
</head>
<body>

<h1>Warning!</h1><br/>
<h4>Please copy your database before running this script and delete it when you're done!</h4><br/><br/>

This script is meant to fix a unicode oversight with the database structure of CKSurf, some player names with special characters will get saved in the database as question marks. This script will set the charset from <b>Latin-1</b> to <b>UTF-8</b>.<br/><br/>
Select your MySQL setting below and hit enter. If you don't know what it is, contact your webmaster or maybe hold off on running this script.<br/><br/>

<form action="?" method="post">
<input type="radio" name="mysql_setting[]" value="1" />MySQL 5.5.3 and above<br/>
<input type="radio" name="mysql_setting[]" value="0" />MySQL 5.5.2 and below<br/>
<input type="submit">

</body>
</html>

