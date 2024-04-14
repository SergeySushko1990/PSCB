<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bins";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function processXML($file) {
    global $conn;
    $xml = simplexml_load_file($file);
    $prevName = "";
    $diffData = [];

    foreach ($xml->record as $record) {
        $bin = substr($record->LO_RANGE, 0, 6);
        $name = (string)$record->BANK_NAME;
        $code = (string)$record->BILCURRENCY_CODE;

        if ($name === "DIFFERENT") {
            $name = $prevName;
        }

        if ($name === "MIX") {
            $name = $prevName . " MIXED WITH " . $name;
        }

        $sql = "SELECT * FROM bins WHERE bin = '$bin'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $sql = "UPDATE bins SET name = '$name', code = '$code' WHERE bin = '$bin'";
            $conn->query($sql);
        } else {
            $sql = "INSERT INTO bins (bin, name, code) VALUES ('$bin', '$name', '$code')";
            $conn->query($sql);
        }

        $prevName = $name;

        if ($bin !== "" && $name !== "" && $code !== "") {
            $diffData[] = [$record->LO_RANGE, $record->HI_RANGE, $name, $code];
        }
    }

    usort($diffData, function($a, $b) {
        return strcmp($a[0], $b[0]);
    });

    $file = fopen("diff.txt", "w");
    foreach ($diffData as $data) {
        fputcsv($file, $data);
    }
    fclose($file);
}

processXML("some_bins.xml");

processXML("another_bins.xml");

$conn->close();
