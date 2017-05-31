<?php
if($secure==1){

$error = 0;
$error_array = array();

$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mapname = mysqli_real_escape_string($conn, htmlspecialchars($_GET["name"], ENT_QUOTES));
if(empty($mapname)){ $error++; array_push($error_array,"You didn't specify a map name!"); }

$database_call = $db_prefix."playertimes";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname'")) { $run_cnt = $result->num_rows; $result->close(); }

$database_call = $db_prefix."maptier";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname' LIMIT 1")) { $row = $result->fetch_object(); $map_tier = $row->tier;  $result->close(); }

$database_call = $db_prefix."checkpoints";

$result = $conn->query("SELECT * FROM $database_call WHERE mapname = '$mapname'");
$row_cnt = ceil($result->num_rows / 30);

$page_start = ($_GET["p"] ? mysqli_real_escape_string($conn, htmlspecialchars($_GET["p"], ENT_QUOTES)) : '0');

if($page_start >=1){ $page_start = $page_start - 1; }
if($page_start <0){ $page_start = 0; }
$page_start = $page_start * 30;

$sql = "SELECT * FROM $database_call WHERE mapname = '$mapname' LIMIT $page_start,30";
$result = $conn->query($sql);

if($error>=1){
	echo"<div class='alert alert-warning' role='alert'><h2>The following errors have occured...</h2><br/><br/>"; 
	foreach($error_array as $error_det){ echo $error_det."<br/>"; } 
	echo"</div>";
}else{

?>

<h2><?php echo $mapname; ?></h2>
<b>Number of completed runs: <?php echo $run_cnt; ?></b><br/>
<b>Map tier: <?php echo $map_tier; ?></b>

<h2>Record times</h2>

<?php
if($use_marco_cksurf == '1'){
	echo"<div class=\"btn-group\" role=\"group\" aria-label=\"...\">
		<a class=\"btn btn-default\" href=\"?view=map&name=$mapname&p=1\">Records</a>
		<button type=\"button\" class=\"btn btn-primary\">Stage Records</button>
	</div>";
}

$database_call = $db_prefix."stages";

$query = "select a.* from $database_call a WHERE ( select count(*) from $database_call as b where a.stage = b.stage and a.id >= b.id) <= 5 AND map = '$mapname'";
if (!$conn->query($query)) {
	printf("Errormessage: %s\n", $conn->error);
}else{
	$result = $conn->query($query);
}
?>

<h2>Stage stats : <?php echo $mapname; ?></h2>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player name</th>
			<th>Map</th>
			<th>Stage</th>
			<th>Runtime</th>
			<th>Start speed</th>
		</tr>
	</thead>
	<tbody>
		<?php

		if ($result->num_rows > 0) {
			// output data of each row
			$x=1;
			while($row = $result->fetch_assoc()) {
				echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["steamid"]."</a></td><td>".$row["map"]."</td><td>".$row["stage"]."</td><td></i> ".processFloat($row["runtime"])."</td><td>".processFloat($row["startspeed"])."</td></tr>";
			}
		} 

		?>
	</tbody>
</table>

<?php $conn->close(); } } ?>