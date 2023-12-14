<?php
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1) {
    $whereClause = ''; // Initialize an empty WHERE clause

    // Check if search_date is provided in the URL
    if (isset($_GET['search_date']) && !empty($_GET['search_date'])) {
        $searchDate = mysqli_real_escape_string($conn, $_GET['search_date']);
        $whereClause .= " AND DATE(date) = '$searchDate' AND Type = 'مدفوعات'";
    }
 
 $query = "SELECT id , photo, Type, Statement, sand, money,  DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date
              FROM treasury_movement 
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

    echo '<title>حركه الخزينه</title>';
echo '<body>';
echo '<div class="main-body1">';
echo'<div class="nav1">';
echo '<h1>حركه الخزينه</h1>';
echo '<h1>مدفوعات</h1>';
echo '</div>';
echo '<div style="margin-left: 10px;" class="table" id="datatable">';
echo '<div class="table-header">';
echo '<div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>';
echo ' <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>';
echo '<div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> المبلغ</a></div>';
echo '<div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> النوع</a></div>';
echo '<div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> السند</a></div>';
echo ' <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البيان</a></div>';
echo '</div>';       
echo '<div class="table-content">';	
// Display data in the table
if ($num_clients === 0) {
    echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
} else {
    // Fetch and display data from the database
    while ($row = mysqli_fetch_assoc($result)) {
       
        echo '<div class="table-row">';
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

if (empty($row['money']) || $row['money'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['money'] . '</div>';
}

if (empty($row['Type']) || $row['Type'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['Type'] . '</div>';
}




if (empty($row['sand']) || $row['sand'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['sand'] . '</div>';
}

if (empty($row['Statement']) || $row['Statement'] === NULL) {
echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
} else {
echo '<div class="table-data">' . $row['Statement'] . '</div>';
}
echo '</div>';       
}
}

echo '</div>';
echo '</div>';
echo '</div';
echo '</body>';
echo '</html>';
?>
