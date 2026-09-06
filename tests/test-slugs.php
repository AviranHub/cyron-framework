<?php
$mysqli = new mysqli('localhost', 'root', '', 'cyron');
$result = $mysqli->query('SELECT id, slug FROM books LIMIT 5');
while ($row = $result->fetch_assoc()) {
    echo $row['slug'] . "\n";
}
