<?php
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1) {
    $whereClause = ''; // Initialize an empty WHERE clause

    // Check if search_date is provided in the URL
    if (isset($_GET['search_date']) && !empty($_GET['search_date'])) {
        $searchDate = mysqli_real_escape_string($conn, $_GET['search_date']);
        $whereClause .= " AND DATE(date) = '$searchDate'";
    }

    // Fetch the data based on the WHERE clause
    $query = "SELECT photo, id, oil_name, start, end, Treasury_Ward,  DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date
              FROM oil_storage 
              WHERE 1 $whereClause";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        $num_clients = mysqli_num_rows($result);
    }
}

mysqli_close($conn);

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.0/html2canvas.min.js"></script>';

    echo "<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>";
    echo '<link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">';
    echo '<link rel="stylesheet" href="css/style.css">';
    echo '<link rel="stylesheet" href="css/style2.css">';
    echo '<link rel="stylesheet" href="css/all.min.css">';
    echo '<link rel="stylesheet" href="css/normalize.css"> ';
    echo '<link rel="shortcut icon" type="x-icon" href="">';

    echo '<title>مخزن الزيوت</title>';
echo '<body>';
echo '<div class="main-body1">';
echo'<div class="nav1">';
echo '  <h1> مخزن الزيوت</h1>';
echo '</div>';
echo '<div style="margin-left: 10px;" class="table" id="datatable">';
echo '<div class="table-header">';
echo '<div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>';
echo ' <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>';
echo '<div class="header__item"><a id="name" class="filter__link" href="#">نوع الزيت</a></div>';
echo '<div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">رصيد النهايه</a></div>';
echo ' <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">رصيد البدايه</a></div>';
echo ' <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">وارد مخزن</a></div>';
echo '</div>';       
echo '<div class="table-content">';	
 // Display data in the table
 if ($num_clients === 0) {
    echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد عملاء</div></div>';
} else {
    // Fetch and display data from the database
    while ($row = mysqli_fetch_assoc($result)) {
        $nameValue = $row['oil_name']. '|' .$row['id'].'|'. $row['photo'];
        echo '<div class="table-row">';
        echo '<div class="table-data"><button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;" id="myform" type="submit" class="save" name="delete" value="' . $nameValue. '">حذف</button></div>';
echo '<div class="table-data">' . $row['formatted_date'] . '</div>';
echo '<div class="table-data">';

if (empty($row['photo']) || $row['photo'] === 'uploads/') {
echo '<span style="color: #FF0000;text-align:center;">لا توجد صورة</span>';
} else {
$photo = explode(",", $row['photo']);
foreach ($photo as $image) {
echo "<a href='" . $image . "' target='_blank'><img src='" . $image . "' width='50' height='50' /></a>";
}
}
echo '</div>';

if (empty($row['oil_name']) || $row['oil_name'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['oil_name'] . '</div>';
}

if (empty($row['end']) || $row['end'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['end'] . '</div>';
}




if (empty($row['start']) || $row['start'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['start'] . '</div>';
}

if (empty($row['Treasury_Ward']) || $row['Treasury_Ward'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['Treasury_Ward'] . '</div>';
}
echo '</div>';       
}
}
echo '</div>';
echo '</div';
echo '</body>';
echo '</html>';
?>
