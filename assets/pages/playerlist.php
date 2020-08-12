<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."playerrank";
$result = $conn->query("SELECT * FROM $database_call ORDER BY points LIMIT 500");
$row_cnt = ceil($result->num_rows / 25);

$page_start = ($_GET["p"] ? mysqli_real_escape_string($conn, htmlspecialchars($_GET["p"], ENT_QUOTES)) : '0');

if($page_start >=1){ $page_start = $page_start - 1; }
if($page_start <0){ $page_start = 0; }
$page_start = $page_start * 25;

$database_call = $db_prefix."playerrank";
$sql = "SELECT * FROM $database_call ORDER BY points DESC LIMIT $page_start,25";
$result = $conn->query($sql);
?>

<h2>Player List (Top 500)</h2>

<center>
	<nav aria-label="Page navigation">
	  <ul class="pagination">
		<li>
		  <a href="<?php echo "?view=players&p=1"; ?>" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
		  </a>
		</li>
		<?php
		$x = 1;
		while($x<=$row_cnt){
			echo "<li><a href=\"?view=players&p=$x\">$x</a></li>";
			$x++;
		}
		?>
		<li>
		  <a href="<?php echo "?view=players&p=$row_cnt"; ?>" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
		  </a>
		</li>
	  </ul>
	</nav>
</center>

<table class="table table-striped table-hover sortable">
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

<?php $conn->close(); } ?>