<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."latestrecords";

$sql = "SELECT * FROM $database_call ORDER BY date DESC";
$result = $conn->query($sql);

?>

<h2>Recent Records</h2>

<table class="table table-striped table-hover ">
<thead>
	<tr>
		<th>Player name</th>
		<th>Runtime</th>
		<th>Map</th>
		<th>Date</tH>
	</tr>
</thead>
<tbody>
<?php

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

		echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".foo($row["runtime"])."</td><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td>".$row[date]."</td></tr>";

	}
} 

?>
</tbody>
</table>
<?php

$conn->close();

}

?>