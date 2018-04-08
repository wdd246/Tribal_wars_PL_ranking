<style>
    th,td{
        border:1px solid black;
    }
</style>
   <?php
    include("ini.php");
    $con = mysqli_connect($host, $user, $pass ,$db);
    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    echo "<table><tr><th><a href='id.php'>Player ID</a></th><th><a href='name.php'>Name</a></th><th><a href='points.php'>Total points</a></th><th><a href='villages.php'>Total villages</a></th><th><a href='wins.php'>Total wins</a></th></tr>";

    $sql = "SELECT player_id, name, sum(points) as total_points, sum(villages) as total_villages, sum(wins) as total_wins FROM player GROUP BY player_id ORDER BY total_points DESC";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["player_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["total_points"]. "</td><td>" . $row["total_villages"]. "</td><td>" . $row["total_wins"]. "</td>";
            echo "</tr>";
        }
    } else {
        echo "0 results";
    }
    echo "</table>";
?>
