<?php
/**
 * Player List Page - Top 500 Players
 * 
 * Security features:
 * - Prepared statements for pagination
 * - Input validation for page number
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Connect to database
$conn = dbConnect();

$database_call = $db_prefix . "playerrank";

// Get total count using prepared statement
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM $database_call");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$row_cnt = ceil($row['cnt'] / 25);
$stmt->close();

// Handle pagination with validation
$page = isset($_GET["p"]) ? $_GET["p"] : '0';
if (!validatePageNumber($page)) {
    $page = 0;
}
$page_start = (intval($page) >= 1) ? (intval($page) - 1) * 25 : 0;
if ($page_start < 0) $page_start = 0;

// Get players with prepared statement for pagination
$stmt = $conn->prepare("SELECT * FROM $database_call ORDER BY points DESC LIMIT ?, 25");
$stmt->bind_param("i", $page_start);
$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Player List (Top 500)</h2>

<center>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li>
                <a href="?view=players&p=1" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
            $x = 1;
            while($x <= $row_cnt) {
                echo "<li><a href=\"?view=players&p=$x\">$x</a></li>";
                $x++;
            }
            ?>
            <li>
                <a href="<?php echo "?view=players&p=" . esc($row_cnt); ?>" aria-label="Next">
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
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='?view=profile&id=" . esc($row["steamid"]) . "'>" . esc($row["name"]) . "</a></td>";
                echo "<td>" . esc($row["country"]) . "</td>";
                echo "<td>" . esc($row["points"]) . "</td>";
                echo "<td>" . esc($row['finishedmaps']) . "</td>";
                echo "<td>" . esc($row['lastseen']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"5\">No players found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();

} // end secure check
?>
