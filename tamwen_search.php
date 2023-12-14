<?php
// Create connection
$conn = require __DIR__ . "/connect.php";



// Perform the search based on the selected value
if (isset($_POST['selectedValue'])) {
    $selectedValue = $_POST['selectedValue'];
   
    // Fetch the data from the database
    $query = "SELECT benz_type, start, ward, monsrf, tlomba, end, m3yar, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM daftr_tamwen WHERE benz_type = '$selectedValue'";
    $result = mysqli_query($conn, $query);

    // Prepare the HTML table rows for the search results
    $tableRows = '';
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tableRows .= '
            <div class="table-header">
            <div class="header__item"><a id="name" class="filter__link" href="#">' . $row['benz_type'] . '</a></div>
        </div>
            <div class="table-row">
                <div class="table-data">' . $row['benz_type'] . '</div>
                <div class="table-data">' . $row['end'] . '</div>
                <div class="table-data">' . $row['tlomba'] . '</div>
                <div class="table-data">' . $row['monsrf'] . '</div>
                <div class="table-data">' . $row['ward'] . '</div>
                <div class="table-data">' . $row['start'] . '</div>
                <div class="table-data">' . $row['formatted_date'] . '</div>
            </div>';
        }
    } else {
        $tableRows = '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
    }
   
    // Return the table rows as the response
    echo $tableRows;
}

mysqli_close($conn);
?>