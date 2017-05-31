<?php
if($secure==1){
	
$conn = @new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."playerrank";
$database_call_2 = $db_prefix."maptier";

$sql = "SELECT * FROM $database_call ORDER BY points DESC LIMIT 10";
$result = $conn->query($sql);

?>

<h2>Our Servers</h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>IP</th>
			<th>Server Name</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$file = fopen("servers.txt","r");
		while(! feof($file))
		{
			$pieces = htmlspecialchars(fgets($file),ENT_QUOTES);
			$pieces = explode("|", $pieces);
			echo "<tr><td><a href='steam://connect/".$pieces[0]."'>".$pieces[0]."</a></td><td>".$pieces[1]."</td></tr>";
		}

		fclose($file);
	?>
	</tbody>
</table>

<h2>Leaderboard</h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player name</th>
			<th>Country</th>
			<th>Points</th>
			<th>Maps Played</th>
			<th>Last Played</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {

			echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td>".$row["country"]."</td><td>".$row["points"]."</td><td>".$row["finishedmaps"]."<td>".$row["lastseen"]."</td></tr>";

		}
	} 
	?>
	</tbody>
</table>
<?php

$database_call = $db_prefix."latestrecords";

$sql = "SELECT * FROM $database_call ORDER BY date DESC LIMIT 20";
$result = $conn->query($sql);

?>

<h2>Recent scores</h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player name</th>
			<th>Runtime</th>
			<th>Map</th>
			<th>Date</th>
			<th>Map Tier</th>
		</tr>
	</thead>
	<tbody>
	<?php

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$timestamp = strtotime($row["date"]);
			$dt = new DateTime("now", new DateTimeZone($local_timezone));
			$dt->setTimestamp($timestamp);
			$timestamp = $dt->format('M j, Y, g:i a T');
			$current_map = $row["map"];
			$result_tier = $conn->query("SELECT tier FROM $database_call_2 WHERE mapname='$current_map' limit 1");
			$value = mysqli_fetch_object($result_tier);
			$this_map_tier = $value->tier;
			echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtime"])."</td><td><a href='?view=map&name=".$row["map"]."'>".$row["map"]."</a></td><td>".$timestamp."</td><td>".$this_map_tier."</td></tr>";

		}
	} 

	?>
	</tbody>
</table>
<?php
$conn->close();
}
?>