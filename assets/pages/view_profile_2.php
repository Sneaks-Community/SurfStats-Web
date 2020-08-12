<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//First query is to get a list of maps this person has completed.

$steamid = mysqli_real_escape_string($conn, htmlspecialchars($_GET["id"], ENT_QUOTES));

//Grab STEAM Profile ID
$split = explode(":", $steamid); // STEAM_?:?:??????? format
$steam_profile = ($split[2] * 2) + 0x0110000100000000 + $split[1];

//Overall Rank
$database_call = $db_prefix."playerrank";
if ($result = $conn->query("SELECT (SELECT count(1)+1 FROM $database_call b WHERE a.points < b.points) AS rank FROM $database_call a WHERE steamid = '$steamid'")) { $row = $result->fetch_object(); $rank = $row->rank;  $result->close(); }

//Bonus Completions
$database_call = $db_prefix."bonus";
if ($result = $conn->query("SELECT COUNT(*) as bonuscomps FROM $database_call WHERE steamid = '$steamid'")) { $row = $result->fetch_object(); $bonuscomps = $row->bonuscomps;  $result->close(); }

//Stage Completions
$database_call = $db_prefix."stages";
if ($result = $conn->query("SELECT COUNT(*) as stagecomps FROM $database_call WHERE steamid = '$steamid'")) { $row = $result->fetch_object(); $stagecomps = $row->stagecomps;  $result->close(); }

//Stage Record Count
$database_call = $db_prefix."stages";
if ($result = $conn->query("SELECT (SELECT count(1)+1 FROM $database_call b WHERE a.map=b.map AND a.runtime > b.runtime AND a.stage = b.stage) AS rank FROM $database_call a WHERE steamid = '$steamid'"))
$srcount = 0;
while ($row = $result->fetch_assoc())
{
    if ($row['rank'] == 1)
    {
        $srcount += 1;
    }
}

//Bonus Record Count
$database_call = $db_prefix."bonus";
if ($result = $conn->query("SELECT mapname, (SELECT count(1)+1 FROM $database_call b WHERE a.mapname=b.mapname AND a.runtime > b.runtime AND a.zonegroup = b.zonegroup) AS rank FROM $database_call a WHERE steamid = '$steamid'"))
$brcount = 0;
while ($row = $result->fetch_assoc())
{
    if ($row['rank'] == 1)
    {
        $brcount += 1;
    }
}

//Get Map Times
$database_call = $db_prefix."playertimes";

$sql = "SELECT mapname, date, (SELECT count(1)+1 FROM $database_call b WHERE a.mapname=b.mapname AND a.runtimepro > b.runtimepro) AS rank, runtimepro FROM $database_call a WHERE steamid = '$steamid'";
$result = $conn->query($sql);
$map_array = array();
$recordStat = 0;

if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		array_push($map_array,$row["mapname"]);
		$map_times .= "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td>".$row["rank"]."</td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td><td>".$row["date"]."</td></tr>";
	}
}

//Get Bonus Times
$database_call = $db_prefix."bonus";

$sql = "SELECT mapname, date, (SELECT count(1)+1 FROM $database_call b WHERE a.mapname=b.mapname AND a.runtime > b.runtime AND a.zonegroup = b.zonegroup) AS rank, runtime, zonegroup FROM $database_call a WHERE steamid = '$steamid'";
$result = $conn->query($sql);
$bonus_array = array();
$recordStat = 0;

if ($result->num_rows > 0) {
	// output data of each row
	while($row = $result->fetch_assoc()) {
		array_push($bonus_array,$row["mapname"]);
		$bonus_times .= "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtime"])."</td><td>".$row["rank"]."</td><td>".$row["zonegroup"]."</td><td>".$row["date"]."</td></tr>";
	}
}

//Next, lookup all maps in the array and check to see if our player is in any of the top 3 rankings.

$database_call = $db_prefix."playertimes";

foreach ($map_array as $value){
//	$sql = "SELECT * FROM $database_call WHERE mapname = '$value' ORDER BY runtimepro ASC LIMIT 3"; // top 3 times
	$sql = "SELECT * FROM $database_call WHERE mapname = '$value' ORDER BY runtimepro ASC LIMIT 1"; // only top map records
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$x = 1;
		while($row = $result->fetch_assoc()) {
		if($row["steamid"] == $steamid){ $recordStat++; $record_times .= "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td><span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='".$lang_rank[$x]."'><i class='fa fa-trophy' aria-hidden='true'></i></span></td><td><span class='rank_$x'><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</span></td></tr>"; } $x++;
		}
	}
}

$database_call = $db_prefix."playerrank";
$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
		echo "<h2><a href='http://steamcommunity.com/profiles/".$steam_profile."'>".$row["name"]."</a></h2>Last seen on ". $row["lastseen"]."<br/>";
		?>
		<b>Rank: <?php echo $rank; ?></b><br/><br/>
		<table class="table table-striped table-hover ">
			<tbody>
				<?php
					echo"<tr><td><b>Points</b>: ".$row["points"]."</td><td><b>Country</b>: ".$row["country"]."</tr>";
					echo"<tr><td><b>Map Completions</b>: ".$row["finishedmaps"]."</td><td><b>Map Records</b>: ".$recordStat."</tr>";?>
					<tr><td><b><a href="#bonus">Bonus Completions</b></a>: <?php echo $bonuscomps; ?></td><td><b>Bonus Records</b>: <?php echo $brcount; ?></td></tr>
					<tr><td><b>Stage Completions</b>: <?php echo $stagecomps; ?></td><td><b>Stage Records</b>: <?php echo $srcount; ?></td></tr>
			</tbody>
		</table>
		<?php
    }
}
?>

<h2>Map Records</h2>

<table class="table table-striped table-hover sortable">
	<thead>
		<tr>
			<th>Map Name</th>
			<th>Rank</th>
			<th>Best Time</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $record_times; ?>
	</tbody>
</table>
	
<div id="map"><h2>Map Times</h2></div>

<table class="table table-striped table-hover sortable">
	<thead>
		<tr>
			<th>Map Name</th>
			<th>Rank</th>
			<th>Personal Best</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $map_times; ?>
	</tbody>
</table>

<div id="bonus"><h2>Bonus Times</h2></div>

<table class="table table-striped table-hover sortable">
	<thead>
		<tr>
			<th>Map Name</th>
			<th>Personal Best</th>
			<th>Rank</th>
			<th>Bonus</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $bonus_times; ?>
	</tbody>
</table>
<?php $conn->close(); } ?>