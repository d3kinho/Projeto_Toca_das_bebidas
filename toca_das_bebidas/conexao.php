<?php

$hostname = "localhost";
$database = "toca_das_bebidas";
$user = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($hostname, $user, $password, $database);
if ($conn->connect_errno) {
    echo "Falha ao conectar: (" . $conn->connect_errno . ") " . $conn->connect_error;
}