<?php
/**
 * View Recent Page - Recent Records
 * 
 * Security features:
 * - Prepared statements for all database queries
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Connect to database
$conn = dbConnect();

$database_call = $db_prefix . "latestrecords";

// Get recent records using prepared statement
$stmt = $conn->prepare("SELECT * FROM $database_call ORDER BY date DESC LIMIT 100");
$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Recent Records</h2>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Player name</th>
            <th>Runtime</th>
            <th>Map</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='?view=profile&id=" . esc($row["steamid"]) . "'>" . esc($row["name"]) . "</a></td>";
                echo "<td><i class=\"fa fa-clock\" aria-hidden=\"true\"></i> " . processFloat($row["runtime"]) . "</td>";
                echo "<td><a href='?view=map&name=" . esc($row["mapname"]) . "'>" . esc($row["mapname"]) . "</a></td>";
                echo "<td>" . esc($row['date']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"4\">No recent records found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();

} // end secure check
?>
