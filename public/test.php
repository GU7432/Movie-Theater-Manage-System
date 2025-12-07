<?php
    include "../config/db_conn.php";

    echo "<table border='1'>
    <tr>
    <th>ID</th>
    <th>name</th>
    <th>street</th>
    <th>city</th>
    </tr>";

    $street = "Taipei";
    $query = ("select * from employee where city = ?");
    $stmt = $db -> prepare($query);
    $error = $stmt -> execute(array($street));
    $result = $stmt -> fetchAll();

    for($i = 0; $i<count($result); $i++){
        echo "<tr>";
        echo "<td>".$result [$i]['ID']."</td>";
        echo "<td>".$result [$i]['person_name']."</td>";
        echo "<td>".$result [$i]['street']."</td>";
        echo "<td>".$result [$i]['city']."</td>";
    }
    echo "</table>"
?>