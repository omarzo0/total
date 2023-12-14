<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1) {
// Fetch the data again after insertion
$query = "SELECT username,user_type, move , time, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM system_move WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

if ($result) {
    // Count the number of output rows
    $num_clients = mysqli_num_rows($result);
}

// Search by date
if (isset($_POST['search'])) {
    $search_date = $_POST['date'];
    
    // Convert the search date to the desired format "day-month-year"
    $formatted_date = date('Y-m-d', strtotime($search_date));

    $query = "SELECT username,user_type, move , time, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM system_move WHERE DATE(date) = '$formatted_date'";
    $result = mysqli_query($conn, $query);

    // Check if the query was executed successfully
    if (!$result) {
        echo "No matching records found in Term Clients.<br><br>";
    } else {
        // Count the number of output clients
        $num_clients = mysqli_num_rows($result);
    }
}


// Search by date
if (isset($_POST['search_Byname'])) {
    $search_name = $_POST['search_name'];
    $search_date = $_POST['date'];
    
    // Convert the search date to the desired format "day-month-year"
    $formatted_date = date('Y-m-d', strtotime($search_date));
    $query = "SELECT username,user_type, move , time, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM system_move WHERE (username LIKE '$search_name%' AND DATE(date) = '$formatted_date') OR (username LIKE '$search_name%' AND DATE(date) = CURDATE() )";
    $result = mysqli_query($conn, $query);

    // Check if the query was executed successfully
    if (!$result) {
    } else {
        // Count the number of output clients
        $num_clients = mysqli_num_rows($result);
    }
}

// Export data to Excel
if (isset($_POST['export_excel'])) {
    
    $querys = "SELECT username,user_type, move , time, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM system_move WHERE DATE(date) = CURDATE()";
    $results = mysqli_query($conn, $querys);

// Check if the query was executed successfully
if ($results) {
    // Count the number of output students
    $num_clients = mysqli_num_rows($results);

    function cleanData($str) {
        $str = str_replace("\t", "\\t", $str);
        $str = str_replace("\n", "\\n", $str);
        $str = '"' . $str . '"';
        return $str;
    }

    $filename = "تقرير حركة النظام.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    $output = fopen("php://output", "w");

    $headers_arabic = ['اسم المستخدم','النوع ', 'الحركة','الوقت','تاريخ العملية'];

    fputs($output, "\xEF\xBB\xBF");
    fputcsv($output, $headers_arabic);

    while ($row = mysqli_fetch_assoc($results)) {
        $clean_row = array_map('cleanData', $row);
        fputcsv($output, $clean_row);
    }

    fclose($output);
    exit(0);
}
}


}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.0/html2canvas.min.js"></script>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css"> 
    <link rel="shortcut icon" type="x-icon" href="">

    <title>حركه النظام</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>حركه النظام</h1>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>

            </div>
            
                <div class="form-row1">
                    
                    <input type="text"style="margin-left: 330px;
                    text-align:center"  id="search-name" name="search_name" placeholder="ادخل اسم المستخدم">

                </div>
                
                <br>
                    <button style="margin-bottom: 30px;
                    margin-top: 30px;
                    margin-left: 400px;"  id="myform" type="submit" class="save"  name="search_Byname" >بحث</button>
                    
                <button style="margin-bottom: 30px;
                margin-top: -70px;
                margin-left: 0px;"  id="myform" type="file" class="save" name="export_excel" > excel طباعه التقرير</button>
                </form>
                <button style="margin-bottom: 30px; margin-top: -70px; margin-left: 0px;" id="printReport" class="save">طباعة التقرير صورة</button>
            
                    <div class="container">
	
                        <div style="margin-left: 10px;" class="table" id="datatable">
                            <div class="table-header">
                                <div class="header__item"><a id="name" class="filter__link" href="#">الحركة</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">اسم المستخدم</a></div>
                                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">النوع</a></div>
                                <div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">الوقت</a></div>
                                <div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">التاريخ</a></div>
                            </div>
                            <div class="table-content">	
                              
                           
    <?php
    // Display data in the table
    if ($num_clients === 0) {
        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="table-row">';
            echo '<div class="table-data" style="white-space: nowrap;">' . $row['move'] . '</div>';

            echo '<div class="table-data">' . $row['username'] . '</div>';
            echo '<div class="table-data">' . $row['user_type'] . '</div>';
            echo '<div class="table-data">' . $row['time'] . '</div>';
            echo '<div class="table-data">' . $row['formatted_date'] . '</div>'; 
            echo '</div>';          
        }
    }
    ?>
                            </div>	
                        </div>
                    </div>
                  
        </div>
    </div>
    <!-- Vertical navbar section  -->
    <section id="navbar" class="nav-bar">
        <div class="menu-toggle">
            <div class="hamburger">
                <span></span>
            </div>
        </div>
        <div class="sidebar close">
            <div class="logo-details">
                <span class="logo_name">
                    <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">
                </span>
            </div>
        <ul class="nav-links">
            <li>
                <a href="adminhome.php" id="btn" >
                    <i class='bx bxs-home'></i>
                    <span class="link_name">نظره عامه</span>
                </a>
            </li>
            <li>
                <a href="dailytt.php">
                    <i class='bx bxs-book-add' ></i>
                    <span class="link_name">يوميه الورديه</span>
                </a>
            </li>
            <li>
                <a href="oil.php">
                    <i class='bx bx-folder-plus'></i>
                    <span class="link_name">الزيوت</span>
                </a>
            </li>
            <li>
                <a href="expensis.php">
                    <i class='bx bx-folder-plus'></i>
                    <span class="link_name">المصاريف</span>
                </a>
            </li>
            <li>
                <a href="bons.php">
                    <i class='bx bxs-file-plus'></i>
                    <span class="link_name">البونات</span>
                </a>
            </li>
            <li>
                <a href="client.php">
                    <i class='bx bx-comment-error'></i>
                    <span class="link_name">عملاء اجله</span>
                </a>
            </li>
            <li>
                <a href="daitre.php">
                    <i class='bx bx-comment-error'></i>
                    <span class="link_name">يوميه الخزينه </span>
                </a>
            </li>
            <li>
                <a href="tamwenpass.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">دفتر التموين</span>
                </a>
            </li>
            <li>
                <a href="tremove.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name"> حركه الخزينه</span>
                </a>
            </li>
            <li>
                <a href="dailyqed.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name"> قيد يومي</span>
                </a>
            </li>
            <li>
                <a href="employee.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">الموظفين </span>
                </a>
            </li>
            <li>
                <a href="systemmove.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">حركه النظام </span>
                </a>
            </li>
            <li>
            <a href="logout.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">تسجيل الخروح</span>
                </a>
            </li>
        </ul>
    </div>
</section>
<script src="script.js"></script>
<script>
    function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting-time').value = today; // Set the input field value to today's date
    }

    document.getElementById('printReport').addEventListener('click', function() {
        // Get the search_date and search_name values
        const searchDate = document.getElementById('meeting_time').value;
const searchName = document.getElementById('search-name').value;
const printWindow = window.open(`generate_report.php?search_date=${searchDate}&search_name=${searchName}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>

</body>

</html>