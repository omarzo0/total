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
$query = "SELECT id, photo, oil_name, first_term_balance, end_term_balance, saled, price, total,  DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM oil_ward WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) {
    // Count the number of output students
    $num_clients = mysqli_num_rows($result);
}
// add employee
if (isset($_POST['save_form'])) {
    $oil_name = $_POST['oil_type'];
    $query_oil = "SELECT name, price FROM oil WHERE name = ?";
    $stmt = mysqli_prepare($conn, $query_oil);
    mysqli_stmt_bind_param($stmt, "s", $oil_name);
    mysqli_stmt_execute($stmt);
    $results = mysqli_stmt_get_result($stmt);

    if (!$results) {
        die(mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($results);
    if ($row) {
        $first_term_balance = $_POST['first_term_balance'];
        $end_term_balance = $_POST['end_term_balance'];
        $photo = $_FILES['photo']['name'];
        $saled = $first_term_balance - $end_term_balance;
        $price = $row['price'];
        $total = $saled * $price;

   
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

        if ($end_term_balance > $first_term_balance) {
            echo '<script>alert("الرجاء ادخال بيانات صحيحة"); window.location.href = "oil.php";</script>';
            exit;
        }

        $sql = "INSERT INTO oil_ward (oil_name, first_term_balance, end_term_balance, saled, price, total, photo)
                VALUES (?, ?, ?, ?, ?, ?,?)";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            die(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "siiisss",
            $oil_name,
            $first_term_balance,
            $end_term_balance,
            $saled,
            $price,
            $total,
            $photo
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            // Unset the POST variables after successful insertion
            unset($oil_name);
            unset($first_term_balance);
            unset($end_term_balance);
            unset($saled);
            unset($price);
            unset($total);
            header('Location: oil.php');
            exit;
        } else {
            echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "oil.php";</script>';
            exit;
        }
    }
}



// Export data to Excel
if (isset($_POST['export_excel'])) {
    $query = "SELECT photo, oil_name, first_term_balance, end_term_balance, saled, price, total,  DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM oil_ward WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) {
    // Count the number of output students
    $num_clients = mysqli_num_rows($result);

    function cleanData($str) {
        $str = str_replace("\t", "\\t", $str);
        $str = str_replace("\n", "\\n", $str);
        $str = '"' . $str . '"';
        return $str;
    }

    $filename = "تقرير الزيوت.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    $output = fopen("php://output", "w");

    $headers_arabic = ['الصورة','نوع الزيت', 'اول المده', 'اخر المده', 'مباع ' ,'سعر الواحدة',' الاجمالى', 'تاريخ العملية'];

    fputs($output, "\xEF\xBB\xBF");
    fputcsv($output, $headers_arabic);

    while ($row = mysqli_fetch_assoc($result)) {
        $clean_row = array_map('cleanData', $row);
        fputcsv($output, $clean_row);
    }

    fclose($output);
    exit(0);
}
}

// Search by date
if (isset($_POST['search'])) {
    $search_date = $_POST['date'];
    
    // Convert the search date to the desired format "day-month-year"
    $formatted_date = date('Y-m-d', strtotime($search_date));

    $query = "SELECT photo , id , oil_name, first_term_balance, end_term_balance, saled, price, total, DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM oil_ward WHERE DATE(date) = '$formatted_date'";
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
    // Get the button's value (trumba_type and trumba_number)
    $buttonValue = $_POST['delete'];
    // Split the value into an array
    $values = explode('|', $buttonValue);
   // Get the individual trumba_type and trumba_number
   $oil_name = mysqli_real_escape_string($conn, $values[0]);
   $id = mysqli_real_escape_string($conn, $values[1]);

    // Use the trumba_type and trumba_number in the SQL query
    $query = "SELECT id, oil_name, first_term_balance, end_term_balance, saled, price, total, photo,  DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM oil_ward  WHERE oil_name = '$oil_name' AND id = '$id'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['oil_name'] = $row['oil_name'];
        $_SESSION['first_term_balance'] = $row['first_term_balance'];
        $_SESSION['end_term_balance'] = $row['end_term_balance'];
        $_SESSION['saled'] = $row['saled'];
        $_SESSION['price'] = $row['price'];
        $_SESSION['total'] = $row ['total'];
        $_SESSION['date'] = $row['formatted_date'];
        $_SESSION['photo'] = $row['photo'];
        $_SESSION['id_oil'] = $row['id'];
    }
    header('Location: oil_update.php');
    exit();
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

    <title>الورديه</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>الزيوت</h1>
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
            <form method="post"  enctype="multipart/form-data">
                <div class="form-row1">
                <label for="oil_type">اختر نوع الزيت</label>
    <br>
    <select name="oil_type" id="oil_type" >
        <?php
        $conn = require __DIR__ . "/connect.php";
        // Prepare and execute the database query
        $query = "SELECT name , price FROM oil";
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
                    <label style="  margin-left: -60px;" for="start-balance">رصيد اخر المده</label>
                    <br>
                    <input type="text" id="start-balance" class="bo" name="end_term_balance">
                </div>
                <div class="form-row1">
                    <label style="  margin-left: -65px;" for="end-balance">رصيد اول المده</label>
                    <br>
                    <input type="text" id="end-balance" class="bo" name="first_term_balance">
                </div>
                <div class = "form-row1">
            <label for="photo">ارفاق صورة</label>
            <br>
    <input type="file" id="photo" class="save" name="photo[]" multiple></div>
                <br><br>
                <button style="margin-bottom: 30px;
                margin-top: -70px;
                margin-left: 0px;"  id="myform" type="file" class="save" name="export_excel" > excel طباعه التقرير</button>
   
                <button style="margin-bottom: 30px;
                    margin-top: -50px;
                    margin-left: 0px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
            </form>
            <button style="margin-bottom: 30px; margin-top: -70px; margin-left: 0px;" id="printReport" class="save">طباعة التقرير صورة</button>
            
                    <div class="container">
	
                        <div style="margin-left: 10px;" class="table" id="datatable">
                            <div class="table-header">
                                <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#">التحكم</a></div>
                                <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> تاريخ العملية</a></div>
                                <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> صورة</a></div>
                                <div class="header__item"><a id="name" class="filter__link" href="#">نوع الزيت</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">الاجمالى</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">سعر الواحدة</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">اخر المده</a></div>
                                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">اول المده</a></div>
                                <div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">مباع</a></div>
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
                            $nameValue = $row['oil_name']. '|' .$row['id'].'|'. $row['formatted_date'];
                            echo '<div class="table-row">';
                            echo '<div class="table-data"><button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;" id="myform" type="submit" class="save" name="delete" value="' . $nameValue. '">تعديل</button></div>';
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


        if (empty($row['total']) || $row['total'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['total'] . '</div>';
        }

        if (empty($row['price']) || $row['price'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['price'] . '</div>';
        }

        if (empty($row['end_term_balance']) || $row['end_term_balance'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['end_term_balance'] . '</div>';
        }




        if (empty($row['first_term_balance']) || $row['first_term_balance'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['first_term_balance'] . '</div>';
        }

        if (empty($row['saled']) || $row['saled'] === NULL) {
            echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
        } else {
            echo '<div class="table-data">' . $row['saled'] . '</div>';
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
const printWindow = window.open(`oil_generate_report.php?search_date=${searchDate}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
<script src="script.js"></script>
</body>

</html>