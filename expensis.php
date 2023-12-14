<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1)
{  $id = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}
    // Function to search data by date
    function searchDataByDate($conn, $search_date) {
        $search_date_formatted = date('Y-m-d', strtotime($search_date));
        $query = "SELECT id , photo, sand, money, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM expenses WHERE DATE(date) = '$search_date_formatted'";
        $result = mysqli_query($conn, $query);
        return $result;
    }

    // Add employee
    if (isset($_POST['save_form'])) {
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
    

        $sql = "INSERT INTO expenses (money, sand , photo) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            die(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "iss", $money, $sand, $photo);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Unset the POST variables after successful insertion
            unset($sand);
            unset($money);
            header('Location: expensis.php');
            exit;
        } else {
            echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "expensis.php";</script>';
            exit;
        }
    }

    // Perform the database query
    $query = "SELECT id , photo, sand, money, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM expenses WHERE DATE(date) = CURDATE()";
    $result = mysqli_query($conn, $query);

    // Check if the query was executed successfully
    if ($result) {
        // Count the number of output rows
        $num_clients = mysqli_num_rows($result);
    }

    // Export data to Excel
if (isset($_POST['export_excel'])) {

     // Perform the database query
     $query = "SELECT photo, sand, money, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM expenses WHERE DATE(date) = CURDATE()";
     $result = mysqli_query($conn, $query);
 
     // Check if the query was executed successfully
     if ($result) {
         // Count the number of output rows
         $num_clients = mysqli_num_rows($result);
     
    // Function to clean data for Excel export
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
    $filename = "تقرير المصاريف.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    $output = fopen("php://output", "w");

    $headers_arabic = ['الصورة','السند', 'المبلغ', 'تاريخ العملية'];

    fputs($output, "\xEF\xBB\xBF"); 
    fputcsv($output, $headers_arabic);

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

        // Perform the database query for the searched date using the search function
        $result = searchDataByDate($conn, $search_date);

        // Count the number of output rows
        $num_clients = mysqli_num_rows($result);
    }


if (isset($_POST['delete'])) {
    $search_name = $_POST['delete'];
    $values = explode('|', $search_name);
    // Get the individual trumba_type and trumba_number
    $id = mysqli_real_escape_string($conn, $values[0]);
    $sand = mysqli_real_escape_string($conn, $values[1]);
    $money = mysqli_real_escape_string($conn, $values[2]);
    // Create a delete query based on the client_name
    $delete_query = "DELETE FROM expenses WHERE ( id = ?) OR (photo =? AND id = ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $delete_query)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sss", $id, $photo, $id);

    $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {
            // Insert into System_Move table for the start value
$action_description_start = $money . "  تم حذف المبلغ  " .$sand. " من المصاريف باسم سند  ";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);


            }
        }
    if (mysqli_stmt_execute($stmt)) {
        // Deletion successful, you can redirect or show a success message here
        // For example, redirect to the same page to refresh the table
        header('Location: expensis.php');
        exit;
    } else {
        // Deletion failed, show an error message or handle the error accordingly
        echo '<script>alert("حدث خطأ أثناء حذف الزيت"); window.location.href = "expensis.php";</script>';
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
    <link rel="shortcut icon" type="x-icon" href="">

    <title>المصاريف</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>المصاريف</h1>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
</form>
            </div>
               <div style="margin-left: 300px;" class="form-cont">
                <form method="post" enctype="multipart/form-data">
                <div class="form-row1">
                <label for="photo">ارفاق صورة</label>
                <br>
    <input type="file" id="photo" class="save" name="photo[]" multiple>
</div>
               <div class="form-row1">
                    <label for="oil-type">السند</label>
                    <br>
                    <input type="text" id="oil-type" class="bo" name="sand">
                </div>
                <div class="form-row1">
                    <label for="start-balance">ادخل المبلغ</label>
                    <br>
                    <input type="number" id="start-balance" class="bo" name="money">
                </div>
               </div>
                <br>
                <button style="margin-bottom: 30px;
                    margin-top: -50px;
                    margin-left: 0px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
                <button style="margin-bottom: 30px;
                margin-top: -70px;
                margin-left: 0px;"  id="myform" type="file" class="save" name="export_excel" > excel طباعه التقرير</button>
                </form>
                <button style="margin-bottom: 30px; margin-top: -70px; margin-left: 0px;" id="printReport" class="save">طباعة التقرير صورة</button>
            
                    <div  style="width: 80%;" class="table" id="datatable">
                    <div class="table-header">
                    <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#">التحكم</a></div>
                    <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>
                    <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>
                    <div class="header__item"><a id="name" class="filter__link" href="#">المبلغ</a></div>
                        <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">السند</a></div>
                    </div>
                    <div class="table-content">	
                        <form method="post">
                    <?php
                    // Display data in the table
                    if ($num_clients === 0) {
                        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
                    } else {
                        // Fetch and display data from the database
                        while ($row = mysqli_fetch_assoc($result)) {$nameValue = $row['id']. '|'. $row ['sand'] . '|' .$row['money'];
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

        if (empty($row['sand']) || $row['sand'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['sand'] . '</div>';
        }
        echo '</div>';       
    }
} 
                    ?>
                    </form>
                    </div>	
                </div>
            </div>
          
        </div>
    </div>

<!-- Vertical navbar section  -->
<?php if( $user_type == "عامل" )
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
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
            echo '</div>';
            echo '</section>';

        
    }elseif( $user_type == "محاسب")
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let arrow = document.querySelectorAll(".arrow");
for (var i = 0; i < arrow.length; i++) {
    arrow[i].addEventListener("click", (e) => {
        let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
        arrowParent.classList.toggle("showMenu");
    });
}
const menu_toggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');

menu_toggle.addEventListener('click', () => {
    menu_toggle.classList.toggle('is-active');
    sidebar.classList.toggle('is-active');
});

function showConfirmation() {
        Swal.fire({
            title: 'Sign out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.querySelector('form').submit();
            }
        })
    }
    function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting-time').value = today; // Set the input field value to today's date
    }

    document.getElementById('printReport').addEventListener('click', function() {
        // Get the search_date and search_name values
        const searchDate = document.getElementById('meeting_time').value;
const printWindow = window.open(`expensis_generate_report.php?search_date=${searchDate}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
</body>

</html>