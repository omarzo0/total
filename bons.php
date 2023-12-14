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
$query = "SELECT photo, id, benz_type, price, category, total, bon_serial, Side, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM bons WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);

// Check if the query was executed successfully
if ($result) {
    // Count the number of output students
    $num_clients = mysqli_num_rows($result);
}



// add employee
if (isset($_POST['save_form'])) {
   
    $bon_serial = $_POST['bon_serial'];
    $category = $_POST['category'];
    $Side = $_POST['Side'];
    $benz_type = $_POST['benz_type'];
    $query_bon = "SELECT id,number, category, Side, benz_type, total, DATE_FORMAT(date, '%Y-%m-%d') AS today FROM matching_bons WHERE DATE(date) = CURDATE() AND category = '$category' AND benz_type = '$benz_type' AND Side = '$Side' ";
    $result_bon = mysqli_query($conn, $query_bon);
    if ($result_bon)
    {
        while($row_bon = mysqli_fetch_assoc($result_bon))
        {
            $category_pre =$row_bon ['category'];
            $number_pre = $row_bon['number'];
            $dates = $row_bon['today'];
            $id = $row_bon['id'];
            $old_total = $row_bon ['total'];
            if ($category == $category_pre) {
                $date = date("Y-m-d");
                $number = $number_pre + 1;
                $query1 = "UPDATE matching_bons
                           SET number = ?, category = ?, benz_type = ?, side = ?, total = ?
                           WHERE category = '$category_pre' AND benz_type = '$benz_type' AND side = '$Side' AND date = '$date' AND id = '$id'";
            }
          
        }
    }
       
        $query = "SELECT solar_price, ben80_price, ben92_price, ben95_price FROM benzene_price ORDER BY date DESC LIMIT 1";
    
        $result_booking = mysqli_query($conn, $query);
    
        // Check if the query was executed successfully
        if ($result_booking) {
            $row = mysqli_fetch_assoc($result_booking);
            // Count the number of output students
            $num_clients = mysqli_num_rows($result_booking);
    
            if ($benz_type == "سولار")
            {
                $solar_price = $row['solar_price'];
                $total_solar = $solar_price * $category ;
            }
            elseif ($benz_type == "بنزين 80")
            {
                $solar_price = $row['ben80_price'];
                $total_solar = $solar_price * $category ;
            } elseif ($benz_type == "بنزين 92")
            {
                $solar_price = $row['ben92_price'];
                $total_solar = $solar_price * $category ;
            } elseif ($benz_type == "بنزين 95")
            {
                $solar_price = $row['ben95_price'];
                $total_solar = $solar_price * $category ;
            }
    
            
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
        $sql = "INSERT INTO bons (bon_serial, category, price,  benz_type, Side, total, photo)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                
               
               if ($old_total == NULL)
               {
                $old_total2 = $total_solar;
                $number = 1;
    
                $query1 = "INSERT INTO matching_bons (number, category, benz_type, Side, total)
                VALUES (?, ?, ?, ?, ?)";
               }else {
    $old_total2 = $old_total + $total_solar;}
       // Initialize and prepare the statements
    $stmt = mysqli_stmt_init($conn);
    $stmt2 = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql) || !mysqli_stmt_prepare($stmt2, $query1)) {
        die(mysqli_error($conn));
    }
    
    // Bind parameters and execute first statement
    mysqli_stmt_bind_param($stmt, "iisssss",
        $bon_serial,
        $category,
        $solar_price,
        $benz_type,
        $Side,
        $total_solar,
        $photo
    );
        // Bind parameters and execute second statement
        mysqli_stmt_bind_param($stmt2, "iisss",
            $number,
            $category,
            $benz_type,
            $Side,
            $old_total2
        );
    
        if (mysqli_stmt_execute($stmt2)) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_stmt_close($stmt2);
    
            // Unset the POST variables after successful insertion
            unset($bon_serial);
            unset($category);
            unset($Side);
            unset($benz_type);
            unset($solar_price);
            unset($total_solar);
    
            header('Location: bons.php');
            exit;
        }
    
        else {
            echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "bons.php";</script>';
            exit;
        }
    
    }else {
        echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "bons.php";</script>';
        exit;
    }
    }

// Export data to Excel
if (isset($_POST['export_excel'])) {
    // Function to clean data for Excel export
    $query = "SELECT photo, benz_type, price, category, total, bon_serial, Side, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM bons WHERE DATE(date) = CURDATE()";
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
    $filename = "تقرير البونات.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=UTF-8");

    // Create a file pointer connected to the output stream
    $output = fopen("php://output", "w");

    // Write the column headers to the CSV file
    $headers_arabic = ['الصورة','نوع البنزين', 'السعر', 'الفئه', 'الاجمالي', 'سريال البونات', 'الجهه', 'تاريخ العملية'];

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


if (isset($_POST['search'])) {
    $date = isset($_POST['date']) ? $_POST['date'] : '';

    // Convert the input date to the format 'month/day/year'
    $search_date_formatted = date('m/d/Y', strtotime($date));

    // Query the term_clients table with the formatted date
    $query = "SELECT photo, id ,benz_type, price, category, total, bon_serial, Side, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date
              FROM bons
              WHERE DATE_FORMAT(date, '%m/%d/%Y') = '$search_date_formatted'";

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
    // Split the value into an array
    $values = explode('|', $search_name);
    // Get the individual trumba_type and trumba_number
    $bon_serial = mysqli_real_escape_string($conn, $values[0]);
    $id = mysqli_real_escape_string($conn, $values[1]);
    $photo = mysqli_real_escape_string($conn, $values[2]);
    $category = mysqli_real_escape_string($conn, $values[3]);
    $Side = mysqli_real_escape_string($conn, $values[4]);
    $benz_type = mysqli_real_escape_string($conn, $values[5]);

    // Use prepared statement to avoid SQL injection
    $sql = "DELETE FROM bons WHERE (bon_serial = ? AND id = ?) OR (photo =? AND id = ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "isss", $bon_serial, $id, $photo, $id);
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {
            // Insert into System_Move table for the start value
$action_description_start = "ببون سيريال: " . $bon_serial . "، الجهة: " . $Side . "، الفئة: " . $category . "، نوع البنزين: " . $benz_type;
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);


            }
        }
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Deletion successful
            echo '<script>alert("تم حذف البيانات بنجاح"); window.location.href = "bons.php";</script>';
        } else {
            echo '<script>alert("لم يتم العثور على بيانات للحذف"); window.location.href = "bons.php";</script>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("حدث خطأ أثناء حذف البيانات"); window.location.href = "bons.php";</script>';
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

    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>البونات</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">

                <h1 style="margin-left: 900px;" >البونات</h1>
                
                    
          <a href="bons_search.php"><button style="margin-bottom: 30px;
                        margin-top: -50px;
                        margin-left: 70px;" class="save">بحث بنوع البنزين </button></a>
            <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
                    </form>
                    <br><br>
                <a href="bonsequal.php"><button style="margin-left: 90px;" class="save">مطابقه البونات</button></a>
                <a href="bons.php"><button class="save">البونات</button></a>
                


            </div>
            <div  style="margin-left: 140px;" class="form-cont">

                <form method="post"  enctype="multipart/form-data">
                <div class="form-row1">
                <label for="photo">ارفاق صورة</label>
                <br>
    <input type="file" id="photo" class="save" name="photo[]" multiple>
</div>
                <div class="form-row1">
                    <label for="oil-type">ادخل الفئه</label>
                    <br>
                    <input type="text" id="oil-type" class="bo" name="category" >
                </div>
                <div class="form-row1">
                    <label style="margin-left: -60px;" for="start-balance">ادخل سريال البون</label>
                    <br>
                    <input type="number" id="start-balance" class="bo" name="bon_serial" >
                </div>
                <div class="form-row1">
                    <label style="margin-left: -22px;" for="start-balance">الجهه</label>
                    <br>
                    <select name="Side" >
                        <option value="شرطة">شرطة</option>
                        <option value="جمعية">جمعية</option>
                      </select>         
                   </div>
                <div class="form-row1">
                    <label  style="margin-left: -60px;" for="start-balance">اختر نوع البنزين</label>
                    <br>
                    <select name="benz_type" >
                        <option value="سولار">سولار</option>
                        <option value="بنزين 95">بنزين 95</option>
                        <option value="بنزين 92">بنزين 92</option>
                        <option value="بنزين 80">بنزين 80</option>
                      </select>          
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
                        <div class="header__item"><a id="name" class="filter__link" href="#">الجهه</a></div>
                        <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> سريال البونات</a></div>
                        <div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> الاجمالي</a></div>
                        <div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> الفئه</a></div>
                        <div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> السعر</a></div>
                        <div class="header__item"> <a id="draws" class="filter__link filter__link--number" href="#"> نوع البنزين</a></div>
                    </div>
                    <div class="table-content">	
                        <form method="post">
                            <?php
                            // Display data in the table
                            if ($num_clients === 0) {
                                echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
                            } else {

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $nameValue = $row['bon_serial']. '|' .$row['id'].'|'. $row['photo'].'|'. $row['category'].'|'. $row['Side'] . '|' . $row['benz_type'] ;
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
        

                if (empty($row['Side']) || $row['Side'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['Side'] . '</div>';
                }
        
                if (empty($row['bon_serial']) || $row['bon_serial'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['bon_serial'] . '</div>';
                }
        
                if (empty($row['total']) || $row['total'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['total'] . '</div>';
                }
        
                if (empty($row['category']) || $row['category'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['category'] . '</div>';
                }
        


                if (empty($row['price']) || $row['price'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['price'] . '</div>';
                }
        
                if (empty($row['benz_type']) || $row['benz_type'] === NULL) {
                    echo '<div style="color: #FF0000;" class="table-data">لا توجد بيانات</div>';
                } else {
                    echo '<div class="table-data">' . $row['benz_type'] . '</div>';
                }
                echo '</div>';       
            }
                            }
                            ?>
                            </form>
                    </div>	
                </div>
            </div>
          <a href="/adminhome.html"><button  style="margin-bottom: 30px;
            margin-top: 30px;
            margin-left: 70px;" class="save">اضافه/حدف</button></a>
                  
        </div>
    </div>
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
const printWindow = window.open(`bons_generate_report.php?search_date=${searchDate}`, '_blank', 'width=800,height=600');


        // Wait for the window to load, then trigger the print dialog
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
</body>

</html>