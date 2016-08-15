<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."playerrank";

$steamid = mysqli_real_escape_string($conn, htmlspecialchars($_GET[id], ENT_QUOTES));

$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        
		echo "<h2>".$row[name]." (".$row[steamid].")</h2>Last seen on ". $row[lastseen]."<br/><br/>";
		
		?>
		<table class="table table-striped table-hover ">
			<tbody>
				<?php
					echo"<tr><td><b>Points</b> : ".$row[points]."</td><td><b>Country</b> : ".$row[country]."</tr>";
					echo"<tr><td><b>Win Ratio</b> : ".$row[winratio]."</td><td><b>Points Ratio</b> : ".$row[pointsratio]."</tr>";
					echo"<tr><td><b>Finished Maps</b> : ".$row[finishedmaps]."</td><td><b>Finished Maps Pro</b> : ".$row[finishedmapspro]."</tr>";
				?>
			</tbody>
		</table>
		<?php
    }
} 

$database_call = $db_prefix."playertimes";

$sql = "SELECT * FROM $database_call WHERE steamid = '$steamid'";
$result = $conn->query($sql);

?>

<h2>Map stats</h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Map name</th>
			<th>Best time</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				echo "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
			}
		}
		?>
	</tbody>
</table>
<?php $conn->close(); } ?>