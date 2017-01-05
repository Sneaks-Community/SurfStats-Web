<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$database_call = $db_prefix."playertimes";

$sql = "SELECT tbl.* FROM $database_call tbl INNER JOIN( SELECT mapname,MIN(runtimepro) MinPoint From $database_call Group By mapname)tbl1 On tbl1.mapname=tbl.mapname Where tbl1.Minpoint=tbl.runtimepro";
$result = $conn->query($sql);

?>
<table class="table table-striped table-hover ">
<thead>
	<tr>
		<th>Map name</th>
		<th>Best time</th>
		<th>Best time player</th>
	</tr>
</thead>
<tbody>
<?php

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td><a href='?view=map&name=".$row["mapname"]."'>".$row["mapname"]."</a></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td></tr>";
    }
} 

?>
</tbody>
</table>
<?php

$conn->close();

}

?>