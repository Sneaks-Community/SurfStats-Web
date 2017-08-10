<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
	header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
	die( header( 'location: /index.php' ) );
}

$conn = @new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Servers list
$file = fopen("servers.txt","r");
while(! feof($file))
{
	$pieces = explode("|", fgets($file));
	$servers_table .="<tr><td><a href='steam://connect/".htmlspecialchars($pieces[0],ENT_QUOTES)."'>".htmlspecialchars($pieces[0],ENT_QUOTES)."</a></td><td>".htmlspecialchars($pieces[1],ENT_QUOTES)."</td></tr>";
}
fclose($file);

//Leaderboard list
$database_call = $db_prefix."playerrank";
($stmt = $conn->prepare("SELECT steamid,name,country,points,finishedmaps,lastseen FROM $database_call ORDER BY points DESC LIMIT 10")) or trigger_error($conn->error, E_USER_ERROR);
$stmt->execute() or trigger_error($stmt->error, E_USER_ERROR);
($stmt_result = $stmt->get_result()) or trigger_error($stmt->error, E_USER_ERROR);
if ($stmt_result->num_rows > 0) {
	while($row = $stmt_result->fetch_assoc()) {
		$leaderboard_table .="<tr><td><a href='?view=profile&id=".htmlentities($row["steamid"])."'>".htmlentities($row["name"])."</a></td><td>".htmlentities($row["country"])."</td><td>".htmlentities($row["points"])."</td><td>".htmlentities($row["finishedmaps"])."<td>".htmlentities($row["lastseen"])."</td></tr>";
	}
}

//Recent Score List
$database_call = $db_prefix."latestrecords";
$database_call_2 = $db_prefix."maptier";

($stmt = $conn->prepare("SELECT steamid,name,runtime,map,date FROM $database_call ORDER BY date DESC LIMIT 20")) or trigger_error($conn->error, E_USER_ERROR);
$stmt->execute() or trigger_error($stmt->error, E_USER_ERROR);
($stmt_result = $stmt->get_result()) or trigger_error($stmt->error, E_USER_ERROR);
if ($stmt_result->num_rows > 0) {
	while($row = $stmt_result->fetch_assoc()) {
		
		$timestamp = strtotime($row["date"]);
		$dt = new DateTime("now", new DateTimeZone($local_timezone));
		$dt->setTimestamp($timestamp);
		$timestamp = $dt->format('M j, Y, g:i a T');
		
		$current_map = htmlentities($row["map"]);
		$result_tier = $conn->query("SELECT tier FROM $database_call_2 WHERE mapname='$current_map' limit 1");
		$value = mysqli_fetch_object($result_tier);
		$this_map_tier = $value->tier;
		
		$scores_table .= "<tr><td><a href='?view=profile&id=".htmlentities($row["steamid"])."'>".htmlentities($row["name"])."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtime"])."</td><td><a href='?view=map&name=".htmlentities($row["map"])."'>".htmlentities($row["map"])."</a></td><td>".htmlentities($timestamp)."</td><td>".htmlentities($this_map_tier)."</td></tr>";
	}
}

$conn->close();
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
		<?php echo $servers_table; ?>
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
		<?php echo $leaderboard_table; ?>
	</tbody>
</table>


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
		<?php echo $scores_table; ?>
	</tbody>
</table>