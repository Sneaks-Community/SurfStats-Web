<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mapname = mysqli_real_escape_string($conn, htmlspecialchars($_GET["name"], ENT_QUOTES));

$database_call = $db_prefix."playertimes";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname'")) { $run_cnt = $result->num_rows; $result->close(); }

$database_call = $db_prefix."maptier";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname' LIMIT 1")) { $row = $result->fetch_object(); $map_tier = $row->tier; $map_author = $row->mapper; $result->close(); }

$database_call = $db_prefix."zones";
if ($result = $conn->query("SELECT MAX(zonegroup) AS bonuses FROM $database_call where mapname = '$mapname'")) { $row = $result->fetch_object(); $bonus_num = $row->bonuses;  $result->close(); }

$database_call = $db_prefix."bonus";
if ($result = $conn->query("SELECT COUNT(*) AS bonuses FROM $database_call WHERE mapname = '$mapname'")) { $row = $result->fetch_object(); $bonus_comp = $row->bonuses;  $result->close(); }

$database_call = $db_prefix."playertimes";
if ($result = $conn->query("SELECT AVG(runtimepro) AS average FROM $database_call WHERE mapname='$mapname'")) { $row = $result->fetch_object(); $avg_times = $row->average;  $result->close(); }

$database_call = $db_prefix."playertimes";
$result = $conn->query("SELECT * FROM $database_call WHERE mapname = '$mapname'");
$row_cnt = ceil($result->num_rows / 50);

$page_start = ($_GET["p"] ? mysqli_real_escape_string($conn, htmlspecialchars($_GET["p"], ENT_QUOTES)) : '0');

if($page_start >=1){ $page_start = $page_start - 1; }
if($page_start <0){ $page_start = 0; }
$page_start = $page_start * 50;

$sql = "SELECT * FROM $database_call WHERE mapname = '$mapname' ORDER BY runtimepro ASC LIMIT $page_start,50";
$result = $conn->query($sql);

?>

<style>
	.subheader img {
		float: right;
		border-radius: 10px;
	}
	
	.subheader a:link {
		color: lightgrey;
	}
	
	.subheader a:visited {
		color: lightgrey;
	}
</style>

<div class="subheader">
	<img src="<?php echo "../bans/images/maps/$mapname.jpg"; ?>">
	<h2><?php echo $mapname; ?> <a href="<?php echo "https://fastdl.snksrv.com/maps/$mapname.bsp.bz2"; ?>"<i class='fa fa-download' aria-hidden='true'></i></a></h2>
	<h5>Map Author: <?php echo $map_author; ?></h5>
	<b>Completions: <?php echo $run_cnt; ?></b><br/>
	<b>Average Time: <?php echo "".processFloat($avg_times).""; ?></b><br/>
	<b>Map Tier: <?php echo $map_tier; ?></b><br/>
	<b>Bonuses: <?php echo $bonus_num; ?> (<?php echo $bonus_comp; ?> Completions)</b>
</div>

<h2>Record Times</h2>

<nav aria-label="Page navigation">
  <ul class="pagination">
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=1"; ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
	<?php
	$x = 1;
	while($x<=$row_cnt){
		echo "<li><a href=\"?view=map&name=$mapname&p=$x\">$x</a></li>";
		$x++;
	}
	?>
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=$row_cnt"; ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player</th>
			<th></th>
			<th>Best Time</th>
			<th>Date</th>
			<th>Start Speed</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($result->num_rows > 0) {
			// output data of each row
			$x=1;
			while($row = $result->fetch_assoc()) {
				if($x<=3) {
					if( isset($_GET['p']) && $_GET['p'] == 1 ) {
						echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='".$lang_rank[$x]."'><i class='fa fa-trophy' aria-hidden='true'></i></span></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td><td>".$row["date"]."</td><td>".$row["startspeed"]." u/s</td></tr>";
					} else {
						echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td><td>".$row["date"]."</td><td>".$row["startspeed"]." u/s</td></tr>";
					}
				}else{
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td><td>".$row["date"]."</td><td>".$row["startspeed"]." u/s</td></tr>";
				}
			$x++;
			}
		} 
		?>
	</tbody>
</table>

<?php $conn->close(); } ?>
