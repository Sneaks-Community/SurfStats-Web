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
            <?php
            // Get current page from URL, default to 1
            $current_page = isset($_GET["p"]) ? intval($_GET["p"]) : 1;
            if ($current_page < 1) $current_page = 1;
            if ($current_page > $row_cnt) $current_page = $row_cnt;
            
            // Calculate visible page range
            $max_visible = 5; // Maximum number of page links to show
            $show_pages = array();
            
            if ($row_cnt <= $max_visible + 1) {
                // If total pages is small, show all
                for ($x = 1; $x <= $row_cnt; $x++) {
                    $show_pages[] = $x;
                }
            } else {
                // Always show first page
                $show_pages[] = 1;
                
                // Calculate range around current page
                $start = max(2, $current_page - 2);
                $end = min($row_cnt - 1, $current_page + 2);
                
                // If near the start, show pages 2 through 8 (or max_visible)
                if ($current_page <= 4) {
                    $start = 2;
                    $end = $max_visible;
                }
                
                // If near the end, show more pages towards the end
                if ($current_page >= $row_cnt - 3) {
                    $start = max(2, $row_cnt - $max_visible + 1);
                    $end = $row_cnt - 1;
                }
                
                // Add ellipsis if there's a gap after first page
                if ($start > 2) {
                    $show_pages[] = '...';
                }
                
                // Add the page range
                for ($x = $start; $x <= $end; $x++) {
                    $show_pages[] = $x;
                }
                
                // Add ellipsis if there's a gap before last page
                if ($end < $row_cnt - 1) {
                    $show_pages[] = '...';
                }
                
                // Always show last page (if more than 1 page total)
                if ($row_cnt > 1) {
                    $show_pages[] = $row_cnt;
                }
            }
            
            // Previous button
            $prev_page = ($current_page > 1) ? $current_page - 1 : 1;
            echo '<li><a href="?view=players&p=' . $prev_page . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
            
            // Display page numbers
            foreach ($show_pages as $x) {
                if ($x === '...') {
                    echo '<li class="disabled"><a href="#">...</a></li>';
                } else {
                    $active = ($x == $current_page) ? ' class="active"' : '';
                    echo '<li' . $active . '><a href="?view=players&p=' . $x . '">' . $x . '</a></li>';
                }
            }
            
            // Next button
            $next_page = ($current_page < $row_cnt) ? $current_page + 1 : $row_cnt;
            echo '<li><a href="?view=players&p=' . $next_page . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
            ?>
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
