<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."playerrank";

$mapname = mysqli_real_escape_string($conn, htmlspecialchars($_GET[name], ENT_QUOTES));


$database_call = $db_prefix."playertimes";

$sql = "SELECT * FROM $database_call WHERE mapname = '$mapname' ORDER BY runtimepro ASC";
$result = $conn->query($sql);

?>

<h2>Map stats : <?php echo $mapname; ?></h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player name</th>
			<th></th>
			<th>Best time</th>
		</tr>
	</thead>
	<tbody>
		<?php

		if ($result->num_rows > 0) {
			// output data of each row
			$x=1;
			while($row = $result->fetch_assoc()) {
				if($x<=3) {
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='".$lang_rank[$x]."'><i class='fa fa-trophy' aria-hidden='true'></i></span></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
				}else{
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
				}
			$x++;
			}
		} 

		?>
	</tbody>
</table>
<?php $conn->close(); } ?>