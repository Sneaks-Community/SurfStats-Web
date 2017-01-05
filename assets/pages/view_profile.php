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

//End

$database_call = $db_prefix."playertimes";

$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result_save = $conn->query($sql);
$map_array = array();
$recordStat = 0;
$record_times = "";
$map_times = "";

if ($result_save->num_rows > 0) {
	// output data of each row
	while($row = $result_save->fetch_assoc()) {
		array_push($map_array,$row["mapname"]);
	}
}

//Next, lookup all maps in the array and check to see if our player is in any of the top 3 rankings.

$database_call = $db_prefix."playertimes";

foreach ($map_array as $value){
	$sql = "SELECT * FROM $database_call WHERE mapname = '$value' ORDER BY runtimepro ASC LIMIT 3";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$x = 1;
		while($row = $result->fetch_assoc()) {
		if($row["steamid"] == $steamid){ $recordStat++; $record_times[$row["mapname"]] = "<span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='".$lang_rank[$x]."'><i class='fa fa-trophy' aria-hidden='true'></i></span><ck-o\" aria-hidden=\"true\"></i>"; } $x++;
		}
	}
}

$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result_save = $conn->query($sql);

while($row = $result_save->fetch_assoc()) {
	$map_times .= "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td>".$record_times[$row["mapname"]]."</td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
}


		
$database_call = $db_prefix."playerrank";


$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
		echo "<h2><a href='http://steamcommunity.com/profiles/".$steam_profile."'>".$row["name"]."</a></h2>Last seen on ". $row["lastseen"]."<br/><br/>";
		
		?>
		<table class="table table-striped table-hover ">
			<tbody>
				<?php
					echo"<tr><td><b>Points</b> : ".$row["points"]."</td><td><b>Country</b> : ".$row["country"]."</tr>";
					echo"<tr><td><b>Win Ratio</b> : ".$row["winratio"]."</td><td><b>Points Ratio</b> : ".$row["pointsratio"]."</tr>";
					echo"<tr><td><b>Finished Maps</b> : ".$row["finishedmaps"]."</td><td><b>Records</b> : ".$recordStat."</tr>";
				?>
			</tbody>
		</table>
		<?php
    }
} 

?>

<h2>Map stats</h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Map name</th>
			<th>Rank</th>
			<th>Best time</th>
		</tr>
	</thead>
	<tbody>
		<?php echo $map_times; ?>
	</tbody>
</table>
<?php $conn->close(); } ?>