<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Player Count
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as players FROM $database_call WHERE `key` LIKE 'player_count'")) { $row = $result->fetch_object(); $players = $row->players;  $result->close(); }

//Map Completions
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as maptimes FROM $database_call WHERE `key` LIKE 'map_completions'")) { $row = $result->fetch_object(); $maptimes = $row->maptimes;  $result->close(); }

//Bonus Completions
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as bonustimes FROM $database_call WHERE `key` LIKE 'bonus_completions'")) { $row = $result->fetch_object(); $bonustimes = $row->bonustimes;  $result->close(); }

//Stage Completions
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as stagetimes FROM $database_call WHERE `key` LIKE 'stage_completions'")) { $row = $result->fetch_object(); $stagetimes = $row->stagetimes;  $result->close(); }

//Total Points
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as totalpoints FROM $database_call WHERE `key` LIKE 'total_points'")) { $row = $result->fetch_object(); $totalpoints = $row->totalpoints;  $result->close(); }

//Players last month
$database_call = $db_prefix."stats";
if ($result = $conn->query("SELECT value as recentplayers FROM $database_call WHERE `key` LIKE 'players_month'")) { $row = $result->fetch_object(); $recentplayers = $row->recentplayers;  $result->close(); }
?>


<h2>Statistics</h2>

<table class="table table-striped table-hover ">
	<tbody>
		<tr><td><b>Ranked Players</b>: <?php echo $players; ?></td><td><b>Players - Last 30 Days</b>: <?php echo $recentplayers; ?></td></tr>
		<tr><td><b>Total Points</b>: <?php echo $totalpoints; ?></td><td><b>Map Completions</b>: <?php echo $maptimes; ?></td></tr>
		<tr><td><b>Bonus Completions</b>: <?php echo $bonustimes; ?></td><td><b>Stage Completions</b>: <?php echo $stagetimes; ?></td></tr>
	</tbody>
</table>

<?php
$database_call = $db_prefix."playerrank";
$sql = "SELECT * FROM $database_call ORDER BY points DESC LIMIT 10";
$result = $conn->query($sql);
?>

<h2>Leaderboard</h2>

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>Player Name</th>
			<th>Country</th>
			<th>Points</th>
			<th>Maps Completed</th>
			<th>Last Played</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {

			echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td>".$row["country"]."</td><td>".$row["points"]."</td><td>".$row['finishedmaps']."<td>".$row['lastseen']."</td></tr>";
		}
	}
	?>
	</tbody>
</table>
<?php

$database_call = $db_prefix."latestrecords";
	
//$sql = "SELECT * FROM $database_call ORDER BY date DESC LIMIT 20";
$sql = "SELECT * FROM $database_call WHERE date in (SELECT max(date) FROM $database_call GROUP BY map) ORDER BY date DESC LIMIT 20";
$result = $conn->query($sql);

?>

<h2>Recent Records</h2>

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>Player Name</th>
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
			$timestamp = strtotime($row['date']);
			$dt = new DateTime("now", new DateTimeZone($local_timezone));
			$dt->setTimestamp($timestamp);
			$timestamp = $dt->format('M j, Y, g:i a T');
			
			echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtime"])."</td><td><a href='?view=map&name=".$row["map"]."'>".$row["map"]."</a></td><td>".$timestamp."</td></tr>";
		}
	}
	?>
	</tbody>
</table>
<footer>
	<center>
		Made with free, <a href="https://gitlab.com/Rowedahelicon/CkSurfStatsPage", target="_">open source</a> software.
	</center>
</footer>
<?php
$conn->close();
}
?>