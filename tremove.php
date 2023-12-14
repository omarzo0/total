<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');
function getTotalAmount() {
    // Get current date

    $conn = require __DIR__ . "/connect.php";
    // Query to get the sum of money for the given conditions
    $sql = "SELECT SUM(money) AS totalAmount FROM treasury_movement WHERE DATE(date) = CURDATE() AND Type = 'مقبوضات'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalAmount = $row["totalAmount"];
    } else {
        $totalAmount = 0;
    }

    $conn->close();

    return $totalAmount;
}
if (is_logged_in() || $_SESSION['id'] == 1)
{
    $id = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}
    // Perform the database query
$query = "SELECT id , photo, Type, Statement, sand, money, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM treasury_movement WHERE DATE(date) = CURDATE() AND Type = 'مقبوضات'";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) {
    // Count the number of output students
    $num_clients = mysqli_num_rows($result);
}
// add employee
if (isset($_POST['save_form'])) {
    $statement = $_POST['statement'];
    $sand = $_POST['sand'];
    $money = $_POST['money'];
    
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/";
    $target_files = array();
    
    $target_dir = "uploads/";
    $target_files = array();
    for ($i = 0; $i < count($photo); $i++) {
        $target_file = $target_dir . basename($photo[$i]);
        move_uploaded_file($_FILES["photo"]["tmp_name"][$i], $target_file);
        array_push($target_files, $target_file);
    }
    // Convert the array of target files to a comma-separated string
    $photo = implode(",", $target_files);


        $sql = "INSERT INTO treasury_movement (Type, Statement, sand, money, photo)
                VALUES ('مقبوضات', ?, ?, ?, ?)";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            die(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssis",
            $statement,
            $sand,
            $money,
            $photo
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Unset the POST variables after successful insertion
            unset($statement);
            unset($sand);
            unset($money);
            header('Location: tremove.php');
            exit;
        } else {
            echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "tremove.php";</script>';
            exit;
        }
    }



// Export data to Excel
if (isset($_POST['export_excel'])) {
    $query = "SELECT photo, Type, Statement, sand, money, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM treasury_movement WHERE DATE(date) = CURDATE() AND Type = 'مقبوضات' ";
    $result = mysqli_query($conn, $query);
    
    // Check if the query was executed successfully
    if ($result) {
        // Count the number of output students
        $num_clients = mysqli_num_rows($result);
    
    function cleanData($str) {
        // Escape tab characters
        $str = str_replace("\t", "\\t", $str);
        // Escape newline characters
        $str = str_replace("\n", "\\n", $str);
        // Enclose data in double quotes
        $str = '"' . $str . '"';
        return $str;
    }

    // Set headers for Excel file download
    $filename = "تقرير حركه الخزينه مقبوضات.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    // Create a file pointer connected to the output stream
    $output = fopen("php://output", "w");

    // Write the column headers to the CSV file
    $headers_arabic = [' الصورة',' النوع','البيان', 'السند', 'المبلغ', 'تاريخ العملية'];

    // Convert the headers to UTF-8 and write to the CSV file
    fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM (Byte Order Mark)
    fputcsv($output, $headers_arabic);

    // Fetch and clean each row of data
    while ($row = mysqli_fetch_assoc($result)) {
        $clean_row = array_map('cleanData', $row);
        // Write the cleaned data to the CSV file
        fputcsv($output, $clean_row);
    }

    // Close the file pointer
    fclose($output);
    exit(0);
}

}


// Search by date
if (isset($_POST['search'])) {
    $search_date = $_POST['date'];
    
    // Convert the search date to the desired format "day-month-year"
    $formatted_date = date('Y-m-d', strtotime($search_date));

    $query = "SELECT id , photo,  Type, Statement, sand, money, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM treasury_movement WHERE DATE(date) = '$formatted_date' AND Type = 'مقبوضات' ";
    $result = mysqli_query($conn, $query);

    // Check if the query was executed successfully
    if (!$result) {
        echo "No matching records found in Term Clients.<br><br>";
    } else {
        // Count the number of output clients
        $num_clients = mysqli_num_rows($result);
    }
}



if (isset($_POST['delete'])) {
    $search_name = $_POST['delete'];
    $values = explode('|', $search_name);
    // Get the individual trumba_type and trumba_number
    $Statement = mysqli_real_escape_string($conn, $values[0]);
    $id = mysqli_real_escape_string($conn, $values[1]);
    $photo = mysqli_real_escape_string($conn, $values[2]);

    // Create a delete query based on the client_name
    $delete_query = "DELETE FROM treasury_movement WHERE (Statement = ? AND id = ?) OR (photo =? AND id = ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $delete_query)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $Statement, $id, $photo, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Deletion successful, you can redirect or show a success message here
        // For example, redirect to the same page to refresh the table
        header('Location: tremove.php');
        exit;
    } else {
        // Deletion failed, show an error message or handle the error accordingly
        echo '<script>alert("حدث خطأ أثناء حذف الزيت"); window.location.href = "tremove.php";</script>';
        exit;
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

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">   
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>حركه الخزينه</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>حركه الخزينه</h1>
                <h1>مقبوضات</h1>
                <hr>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
                    </form>
                <a href="tremove.php"><button style="margin-left: 90px;" class="save">حركه الخزينه</button></a>
                <a href="equations.php"><button class="save">الحسابات</button></a>
                <a href="tremove.php"><button style="margin-left: 90px;" class="save">المقبوضات</button></a>
                <a href="back.php"><button class="save">المدفوعات</button></a>
            </div>
            <div style="margin-left: 200px;" class="form-cont">
            
            <form method="post" enctype="multipart/form-data">
            <div class="form-row1">
            <label for="photo">ارفاق صورة</label>
            <br>
    <input type="file" id="photo" class="save" name="photo[]" multiple>
</div>
                <div class="form-row1">
                    <label for="start-balance">المبلغ</label>
                    <br>
                    <input type="number" id="start-balance" class="bo" name="money" >
                </div>
                    <div class="form-row1">
                        <label for="oil-type">السند</label>
                        <br>
                        <input type="text" id="oil-type" class="bo" name="sand" >
                    </div>
                    
                    <div style="margin-left: -10px;" class="form-row1">
                        <label style="margin-left: 45px;" for="start-balance"> البيان</label>
                        <br>
                         <select style="margin-left: 10px;" name="statement" id="oil_type">
        <?php
        $conn = require __DIR__ . "/connect.php";
        // Prepare and execute the database query
        $query = "SELECT name FROM statement";
        $results = mysqli_query($conn, $query);

        if ($results && mysqli_num_rows($results) > 0) {
            // Loop through the rows to generate options
            while ($row = mysqli_fetch_assoc($results)) {
                $name = $row['name'];
                echo "<option value=\"$name\">$name</option>";
            }
        } else {
            echo "<option value=\"\">لا يوجد بيانات</option>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </select>
                    </div>
            </div>
            <br>
                <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
                <button style="margin-bottom: 30px;
                margin-top: -70px;
                margin-left: 0px;"  id="myform" type="file" class="save" name="export_excel" > excel طباعه التقرير</button>
                </form>
                <button style="margin-bottom: 30px; margin-top: -70px; margin-left: 0px;" id="printReport" class="save">طباعة التقرير صورة</button>
            
                    <div  style="width: 80%;
                    margin-left:-20px" class="table" id="datatable">
                        <div class="table-header">
                        <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#">التحكم</a></div>
                    <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>                            
                    <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>
                    <div class="header__item"><a id="name" class="filter__link" href="#">المبلغ</a></div>
                            <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">النوع</a></div>
                            <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">السند</a></div>
                            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البيان</a></div>
                        </div>
                        <div class="table-content">	
                            <form method="post">
                    <?php
                    // Display data in the table
                    if ($num_clients === 0) {
                        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
                    } else {
                        // Fetch and display data from the database
                        while ($row = mysqli_fetch_assoc($result)) {
                            $nameValue = $row['Statement']. '|' .$row['id'].'|'. $row['photo'];
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
                    ?>
                    </form>
                        </div>	
                    </div>
                </div>
              <a href="addtremove.php"><button  style="margin-bottom: 30px;
                margin-top: 30px;
                margin-left: 70px;" class="save">اضافه/حدف</button></a>
                 <label style="margin-left: 650px;" for="total"> الاجمالي</label>
</div>
<input style="margin-left:830px; margin-top:-400px" type="number" id="meeting-time" value="<?php echo getTotalAmount(); ?>" readonly>

                  
        </div>
    </div>
    <!-- Vertical navbar section  -->
    <?php if( $user_type == "محاسب")
    {
        echo '  <section id="navbar" class="nav-bar"> ';
        echo '  <div class="menu-toggle">';
        echo '  <div class="hamburger">';
        echo '     <span></span>';
            echo '  </div>';
        echo '  </div>';
          echo ' <div class="sidebar close">';
          echo '  <div class="logo-details">';
          echo '   <span class="logo_name">';
          echo '   <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">';
          echo '   </span>';
          echo '   </div>';
          echo '<ul class="nav-links">';
            echo ' <li>';
            echo '<a href="dailytt.php">';
            echo " <i class='bx bxs-book-add' ></i>";
                        echo '  <span class="link_name">يوميه الورديه</span>';
                        echo '  </a>';
                    echo ' </li>';
                    echo ' <li>';
                    echo '   <a href="oil.php">';
                    echo " <i class='bx bx-folder-plus'></i>";
                            echo '   <span class="link_name">الزيوت</span>';
                            echo '  </a>';
                        echo ' </li>';
                    echo ' <li>';
                    echo ' <a href="expensis.php">';
                          echo "  <i class='bx bx-folder-plus'></i>";
                            echo ' <span class="link_name">المصاريف</span>';
                            echo ' </a>';
                            echo '  </li>';
                    echo '  <li>';
                    echo '  <a href="bons.php">';
                        echo " <i class='bx bxs-file-plus'></i>";
                            echo '<span class="link_name">البونات</span>';
                            echo ' </a>';
                        echo '</li>';


                        echo '  <li>';
                        echo ' <a href="client.php">';
                echo "   <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">عملاء اجله</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="daitre.php">';
                echo "  <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">يوميه الخزينه </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="tamwenpass.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '    <span class="link_name">دفتر التموين</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="tremove.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name"> حركه الخزينه</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo ' <a href="dailyqed.php">';
                echo "<i class='bx bx-cog'></i>";
                    echo '  <span class="link_name"> قيد يومي</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
                echo '  </ul>';
                echo '  </div>';
                echo ' </section>'; 
    }
    else {
        echo '  <section id="navbar" class="nav-bar"> ';
        echo '  <div class="menu-toggle">';
        echo '  <div class="hamburger">';
        echo '     <span></span>';
            echo '  </div>';
        echo '  </div>';
          echo ' <div class="sidebar close">';
          echo '  <div class="logo-details">';
          echo '   <span class="logo_name">';
          echo '   <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">';
          echo '   </span>';
          echo '   </div>';
          echo '<ul class="nav-links">';
          echo ' <li>';
          echo '   <a href="adminhome.php" id="btn" >';
                   echo " <i class='bx bxs-home'></i>";
                    echo '  <span class="link_name">نظره عامه</span>';
                    echo '</a>';
                    echo '</li>';
            echo ' <li>';
            echo '<a href="dailytt.php">';
            echo " <i class='bx bxs-book-add' ></i>";
                        echo '  <span class="link_name">يوميه الورديه</span>';
                        echo '  </a>';
                    echo ' </li>';
                    echo ' <li>';
                    echo '   <a href="oil.php">';
                    echo " <i class='bx bx-folder-plus'></i>";
                            echo '   <span class="link_name">الزيوت</span>';
                            echo '  </a>';
                        echo ' </li>';
                    echo ' <li>';
                    echo ' <a href="expensis.php">';
                          echo "  <i class='bx bx-folder-plus'></i>";
                            echo ' <span class="link_name">المصاريف</span>';
                            echo ' </a>';
                            echo '  </li>';
                    echo '  <li>';
                    echo '  <a href="bons.php">';
                        echo " <i class='bx bxs-file-plus'></i>";
                            echo '<span class="link_name">البونات</span>';
                            echo ' </a>';
                        echo '</li>';


                        echo '  <li>';
                        echo ' <a href="client.php">';
                echo "   <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">عملاء اجله</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="daitre.php">';
                echo "  <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">يوميه الخزينه </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="tamwenpass.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '    <span class="link_name">دفتر التموين</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="tremove.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name"> حركه الخزينه</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo ' <a href="dailyqed.php">';
                echo "<i class='bx bx-cog'></i>";
                    echo '  <span class="link_name"> قيد يومي</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="employee.php">';
                echo "  <i class='bx bx-cog'></i>";
                    echo '  <span class="link_name">الموظفين </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="systemmove.php">';
                  echo "  <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name">حركه النظام </span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
                echo '  </ul>';
                echo '  </div>';
                echo ' </section>'; }?>
<!-- Javascript section -->
<script>
    function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting-time').value = today; // Set the input field value to today's date
    }

    
    document.getElementById('printReport').addEventListener('click', function() {
        // Get the search_date and search_name values
        const searchDate = document.getElementById('meeting_time').value;
const printWindow = window.open(`tremove_generate_report.php?search_date=${searchDate}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
</body>

</html>