<?php
// fetch_data.php
require 'config/init.php';

// Initialize the query
$query = "SELECT * FROM residents WHERE 1=1";

// Check if any filters are set and modify the query accordingly
if (!empty($_POST['sector'])) {
    $sector = $_POST['sector'];
    $query .= " AND sector_code = :sector";
}

if (!empty($_POST['ethnicity'])) {
    $ethnicity = $_POST['ethnicity'];
    $query .= " AND ethnicity = :ethnicity";
}

if (!empty($_POST['employment_status'])) {
    $employment_status = $_POST['employment_status'];
    $query .= " AND employment_status = :employment_status";
}

// Age filter logic
if (!empty($_POST['age'])) {
    $age = $_POST['age'];

    // Handle specific age ranges
    if ($age == 'infants_toddlers') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 0 AND 3";
    } elseif ($age == 'children') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 4 AND 12";
    } elseif ($age == 'teens') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 18";
    } elseif ($age == 'youth') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 30";
    } elseif ($age == 'middle_age') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 31 AND 45";
    } elseif ($age == 'adults') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 46 AND 59";
    } elseif ($age == 'seniors') {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60";
    } elseif (is_numeric($age)) {
        $query .= " AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) = :age";
    }
}

// Prepare the query
$stmt = $db->prepare($query);

// Bind parameters
if (!empty($sector)) {
    $stmt->bindParam(':sector', $sector, PDO::PARAM_STR);
}

if (!empty($ethnicity)) {
    $stmt->bindParam(':ethnicity', $ethnicity, PDO::PARAM_STR);
}

if (!empty($employment_status)) {
    $stmt->bindParam(':employment_status', $employment_status, PDO::PARAM_STR);
}

if (!empty($age) && is_numeric($age)) {
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
}

// Execute the query
$stmt->execute();

// Fetch and return the data as JSON
$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode([
    "data" => $data
]);
?>
