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

    // Check if search_name is provided in the URL
    if (isset($_GET['search_name']) && !empty($_GET['search_name'])) {
        $searchName = mysqli_real_escape_string($conn, $_GET['search_name']);
        $whereClause .= " AND username = '$searchName'";
    }

    // Fetch the data based on the WHERE clause
    $query = "SELECT username,user_type, move , time, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date 
              FROM system_move 
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

    echo '<title>حركه النظام</title>';
echo '<body>';
echo '<div class="main-body1">';
echo'<div class="nav1">';
echo ' <h1>حركه النظام</h1> ';
echo '</div>';
echo '<div style="margin-left: 10px;" class="table" id="datatable">';
echo '<div class="table-header">';
echo '<div class="header__item"><a id="name" class="filter__link" href="#">الحركة</a></div>';
echo '<div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">اسم المستخدم</a></div>';
echo '<div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">النوع</a></div>';
echo '<div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">الوقت</a></div>';
echo '<div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">التاريخ</a></div>';
echo '</div>';       
echo '<div class="table-content">';	
// Display data in the table
if ($num_clients === 0) {
    echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="table-row">';
        echo '<div class="table-data">' . $row['move'] . '</div>';
        echo '<div class="table-data">' . $row['username'] . '</div>';
        echo '<div class="table-data">' . $row['user_type'] . '</div>';
        echo '<div class="table-data">' . $row['time'] . '</div>';
        echo '<div class="table-data">' . $row['formatted_date'] . '</div>'; 
        echo '</div>';          
    }
}
echo '</div>';
echo '</div';
echo '</body>';
echo '</html>';
?>
