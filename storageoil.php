<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

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
$query = "SELECT photo, id, oil_name, start, end, Treasury_Ward,  DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM oil_storage WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) 
{
    // Count the number of output students
    $num_clients = mysqli_num_rows($result);
}
// add employee
if (isset($_POST['save_form'])) {
    $oil_name = $_POST['oil_type'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $Treasury_Ward = $_POST['Treasury_Ward'];

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
   
    if ($end > $start)
    {
        echo '<script>alert("الرجاء ادخال بيانات صحيحة"); window.location.href = "storageoil.php";</script>';
        exit;
    }

    $sql = "INSERT INTO oil_storage (oil_name, start, end, Treasury_Ward, photo)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "siiis",
        $oil_name,
        $start,
        $end,
        $Treasury_Ward,
        $photo
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Unset the POST variables after successful insertion
        unset($oil_name);
        unset($start);
        unset($end);
        unset($Treasury_Ward);
        header('Location: storageoil.php');
        exit;
    } else {
        echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "storageoil.php";</script>';
        exit;
    }
}


// Export data to Excel
if (isset($_POST['export_excel'])) {

       // Perform the database query
$query = "SELECT photo, oil_name, start, end, Treasury_Ward,  DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM oil_storage WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) 
{
    // Count the number of output students
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
    $filename = "تقرير مخزن الزيوت.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    $output = fopen("php://output", "w");

    $headers_arabic = ['الصورة', 'نوع الزيت', 'رصيد البدايه', 'رصيد النهايه', 'وارد مخزن','تاريخ العملية'];

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
    
    // Convert the search date to the desired format "day-month-year"
    $formatted_date = date('Y-m-d', strtotime($search_date));

    $query = "SELECT photo , id , oil_name, start, end, Treasury_Ward, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM oil_storage WHERE DATE(date) = '$formatted_date'";
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
    $oil_name = mysqli_real_escape_string($conn, $values[0]);
    $id = mysqli_real_escape_string($conn, $values[1]);
    $photo = mysqli_real_escape_string($conn, $values[2]);

    // Create a delete query based on the client_name
    $delete_query = "DELETE FROM oil_storage WHERE (oil_name = ? AND id = ?) OR (photo =? AND id = ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $delete_query)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $oil_name, $id, $photo, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Deletion successful, you can redirect or show a success message here
        // For example, redirect to the same page to refresh the table
        header('Location: storageoil.php');
        exit;
    } else {
        // Deletion failed, show an error message or handle the error accordingly
        echo '<script>alert("حدث خطأ أثناء حذف الزيت"); window.location.href = "storageoil.php";</script>';
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

    <title>المخزن</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>المخزن</h1>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

<button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
</form>
                <br><br>
                <a href="storageoil.php"><button style="margin-left: 90px;" class="save">المخزن</button></a>
                <a href="oil.php"><button class="save">الورديه</button></a>
                <br><br>
            </div>
            <div class="form-cont">
                <form method="post"  enctype="multipart/form-data">

                
                <div class="form-row1">
            <label for="photo">ارفاق صورة</label>
            <br>
    <input type="file" id="photo" class="save" name="photo[]" multiple>
</div>
                <div class="form-row1">
    <label for="oil_type">اختر نوع الزيت</label>
    <br>
    <select name="oil_type" id="oil_type" >
        <?php
        $conn = require __DIR__ . "/connect.php";
        // Prepare and execute the database query
        $query = "SELECT name FROM oil";
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
                <div class="form-row1">
                    <label for="start-balance">رصيد النهايه</label>
                    <br>
                    <input type="number" id="start-balance" class="bo" name="end" >
                </div>
                <div class="form-row1">
                    <label for="end-balance">رصيد البدايه</label>
                    <br>
                    <input type="number" id="end-balance" class="bo" name="start" >
                </div>
                <div class="form-row1">
                    <label for="inventory-in">وارد مخزن</label>
                    <br>
                    <input type="number" id="inventory-in" class="bo" name="Treasury_Ward" >
                </div>
                <br><br>
                <button  style="margin-bottom: 30px;
                margin-top: 20px;
                margin-left: -40px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
                <button style="margin-bottom: 30px;
                margin-top: -70px;
                margin-left: 0px;"  id="myform" type="file" class="save" name="export_excel" > excel طباعه التقرير</button>
                </form>
                <button style="margin-bottom: 30px; margin-top: -70px; margin-left: 0px;" id="printReport" class="save">طباعة التقرير صورة</button>
            
        </div>
                
                <div  style="width: 82%;
                margin-left:-20px" class="table">
                    <div class="table-header">
                        <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#">التحكم</a></div>
                        <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>
                        <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>
                        <div class="header__item"><a id="name" class="filter__link" href="#">نوع الزيت</a></div>
                        <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">رصيد النهايه</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">رصيد البدايه</a></div>
                        <div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">وارد مخزن</a></div>
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
                    ?>
                    </form>
                    <a href="addoil.php"><button  style="margin-bottom: 30px;
                margin-top: 30px;
                margin-left: 70px;" class="save">اضافه/حدف</button></a>
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
        document.getElementById('meeting_time').value = today; // Set the input field value to today's date
    }

    document.getElementById('printReport').addEventListener('click', function() {
        // Get the search_date and search_name values
        const searchDate = document.getElementById('meeting_time').value;
const printWindow = window.open(`oilstorage_generate_report.php?search_date=${searchDate}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
</body>

</html>