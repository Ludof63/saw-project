<?php
$pw = json_decode(file_get_contents("../config.json"), true)["db_password"];
$password = trim($pw);
$db = new mysqli("127.0.0.1", "S4943369", $password, "S4943369");

$queries = ["%s"];

foreach ($queries as $query) {
    $query_result = $db->query($query);
    if (is_bool($query_result)) echo $query_result ? "OK" : "Error";
    else {
        $first = true;
        $result = array();
        $max_lengths = array();
        while ($row = $query_result->fetch_assoc()) {
            if ($first) {
                $result[] = array();
                foreach ($row as $key => $_) {
                    $result[0][] = $key;
                    $max_lengths[] = strlen($key);
                }
                $first = false;
            }
            $new_line = array();
            $counter = 0;
            foreach ($row as $elem) {
                $elem = strval($elem);
                $new_line[] = $elem;
                $max_lengths[$counter] = max($max_lengths[$counter], strlen($elem));
                $counter++;
            }
            $result[] = $new_line;
        }
        $first = true;
        foreach ($result as $row) {
            foreach ($row as $key => $value) {
                echo $value;
                for ($i = 0; $i < $max_lengths[$key] - strlen($value); $i++)
                    echo " ";
                echo "|";
            }
            echo "\n";
            if ($first) {
                foreach ($max_lengths as $length) {
                    for ($i = 0; $i < $length; $i++) echo "-";
                    echo "|";
                }
                echo "\n";
                $first = false;
            }
        }
    }
    echo "\n";
}
