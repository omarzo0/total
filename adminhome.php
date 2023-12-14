<?php
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');

require_once ('start2.php');

// Function to fetch the newest benzene prices from the database
function getNewestPrices($conn) {
    $query_newest_prices = "SELECT * FROM benzene_price ORDER BY date DESC LIMIT 1";
    $result_newest_prices = mysqli_query($conn, $query_newest_prices);
    $newest_prices = mysqli_fetch_assoc($result_newest_prices);
    return $newest_prices;
}

// Function to update the newest benzene prices in the database
function updateNewestPrices($conn, $solar_price, $ben80_price, $ben92_price, $ben95_price) {
    $query_update_prices = "UPDATE benzene_price SET solar_price = ?, ben80_price = ?, ben92_price = ?, ben95_price = ? ORDER BY date DESC LIMIT 1";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $query_update_prices)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssss",
        $solar_price,
        $ben80_price,
        $ben92_price,
        $ben95_price
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        return false;
    }
}

if (is_logged_in() || $_SESSION['id'] == 1) {
    $id = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}

    // Get the newest date in the benzene_price table
    $query_login = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_login_h'";
    $result_login = mysqli_query($conn, $query_login);
    if ($result_login) {
        $row_login = mysqli_fetch_assoc($result_login);
        $currentValue = $row_login['value'];
    }

    // Get the newest date in the benzene_price table
    $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
    $result_move = mysqli_query($conn, $query_move);
    if ($result_move) {
        $row_move = mysqli_fetch_assoc($result_move);
        $currentValue_move = $row_move['value'];
    }


    $query_latest_date = "SELECT MAX(DATE(date)) AS newest_date FROM benzene_price";
    $result_latest_date = mysqli_query($conn, $query_latest_date);
    $newest_date_row = mysqli_fetch_assoc($result_latest_date);
    $newest_date = $newest_date_row['newest_date'];

    // Get today's date in the format "year-month-day"
    $today_date = date('Y-m-d');

    // Check if the newest date and today's date are equal
    if ($newest_date === $today_date) {
        // If the newest date and today's date are equal, get the latest prices from the database
        $newest_prices = getNewestPrices($conn);

        if ($newest_prices) {
            $solar_price = $newest_prices['solar_price'];
            $ben80_price = $newest_prices['ben80_price'];
            $ben92_price = $newest_prices['ben92_price'];
            $ben95_price = $newest_prices['ben95_price'];
        } else {
            // If no values are returned from the latest date, insert all prices with 0
            $solar_price = 0;
            $ben80_price = 0;
            $ben92_price = 0;
            $ben95_price = 0;
        }

        // Get the logged-in history
        $query = "SELECT name, sys_type, time, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM login_h WHERE DATE(date) = CURDATE() ORDER BY time DESC";

        $result = mysqli_query($conn, $query);
        if ($result) {
            // Count the number of output rows
            $num_clients = mysqli_num_rows($result);
        }

       // Calculate the total amount of each type of benzene in stock
       $current_date = date("Y-m-d");

       // Calculate the previous day's date
       $previous_date = date("Y-m-d", strtotime("-1 day", strtotime($current_date)));
       
       // SQL query to select all the values from the previous day
       $query2 = "SELECT trumba_type, SUM(total) AS trumba_total FROM benzene 
                  WHERE DATE(date) = '$previous_date'
                  GROUP BY trumba_type";
       
       $result2 = mysqli_query($conn, $query2);

if ($result2) {
$solar = 0;
$ben_80 = 0;
$ben_92 = 0;
$ben_95 = 0;

while ($row = mysqli_fetch_assoc($result2)) {
if ($row['trumba_type'] == 'سولار') {
 $solar = $row['trumba_total'];
} elseif ($row['trumba_type'] == 'بنزين 80') {
 $ben_80 = $row['trumba_total'];
} elseif ($row['trumba_type'] == 'بنزين 92') {
 $ben_92 = $row['trumba_total'];
} elseif ($row['trumba_type'] == 'بنزين 95') {
 $ben_95 = $row['trumba_total'];
}
}
}


        // Add new employee entry
        if (isset($_POST['save_form'])) {
            $solar_price = $_POST['solar_price'];
            $ben80_price = $_POST['ben80_price'];
            $ben92_price = $_POST['ben92_price'];
            $ben95_price = $_POST['ben95_price'];

            // Update the newest prices with the new values
            $update_result = updateNewestPrices($conn, $solar_price, $ben80_price, $ben92_price, $ben95_price);

            if ($update_result) {
                // Unset the POST variables after successful update
                unset($solar_price);
                unset($ben80_price);
                unset($ben92_price);
                unset($ben95_price);
                header('Location: adminhome.php');
                exit;
            } else {
                echo '<script>alert("حدث خطأ أثناء تحديث الأسعار"); window.location.href = "adminhome.php";</script>';
                exit;
            }
        }

        // Handle search form submission
        if (isset($_POST['search'])) {
            $search_date = $_POST['date'];

            // Convert the search date to the database format "year-month-day"
            $search_date_formatted = date('Y-m-d', strtotime($search_date));

            $query2 = "SELECT trumba_type, SUM(total) AS trumba_total FROM benzene 
            WHERE DATE(date) = '$search_date_formatted'
            GROUP BY trumba_type";
 
            $result2 = mysqli_query($conn, $query2);

            if ($result2)
            {
                
while ($row = mysqli_fetch_assoc($result2)) {
    if ($row['trumba_type'] == 'سولار') {
     $solar = $row['trumba_total'];
    } elseif ($row['trumba_type'] == 'بنزين 80') {
     $ben_80 = $row['trumba_total'];
    } elseif ($row['trumba_type'] == 'بنزين 92') {
     $ben_92 = $row['trumba_total'];
    } elseif ($row['trumba_type'] == 'بنزين 95') {
     $ben_95 = $row['trumba_total'];
    }
    }
            }

            // Perform the database query for the searched date
            $query = "SELECT name, sys_type, time, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM login_h WHERE DATE(date) = '$search_date_formatted'";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Count the number of output rows
                $num_clients = mysqli_num_rows($result);
            }
        }
    } else {
        // If the newest date and today's date are different, reinsert all values from the newest date into today's date
        $query_copy_values = "INSERT INTO benzene_price (solar_price, ben80_price, ben92_price, ben95_price, date)
                              SELECT solar_price, ben80_price, ben92_price, ben95_price, '$today_date' FROM benzene_price WHERE DATE(date) = '$newest_date'";

        $result_copy_values = mysqli_query($conn, $query_copy_values);

        if ($result_copy_values) {
            header('Location: adminhome.php');
            exit;
        } else {
            echo '<script>alert("حدث خطأ أثناء نسخ القيم"); window.location.href = "adminhome.php";</script>';
            exit;
        }
    }

    // Fetch the newest prices again to display them in the form
    $newest_prices = getNewestPrices($conn);
    $solar_price = $newest_prices['solar_price'];
    $ben80_price = $newest_prices['ben80_price'];
    $ben92_price = $newest_prices['ben92_price'];
    $ben95_price = $newest_prices['ben95_price'];

    if (isset($_POST['login'])) {
        $id = $_SESSION['id'];
        // Fetch the data again after insertion
$query = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result) {
   $row = mysqli_fetch_assoc($result);
   $name = $row['username'];
   $user_type = $row['user_type'];
}

        // Prepare the SQL query to select the value column where sys_type is 'web'
        $query_login = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_login_h'";
            
        $result_login = mysqli_query($conn, $query_login);
        
        if ($result_login) {
            $row_login = mysqli_fetch_assoc($result_login);
            $currentValue = $row_login['value'];
    
            if ($currentValue === "1") {
                  // Insert into System_Move table for the start value
$action_description_start = "تم قفل تسجيل الدخول ";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
                // Prepare the SQL query to update the value to 0
                $query_update = "UPDATE setting SET value = '0' WHERE sys_type = 'web' AND option = 'sys_login_h'";
            
                // Initialize a statement object
                $stmt_update = mysqli_stmt_init($conn);
            
                // Check if the statement initialization was successful
                if (!mysqli_stmt_prepare($stmt_update, $query_update)) {
                    die("Statement preparation failed: " . mysqli_error($conn));
                }
            
                // Execute the UPDATE statement
                if (mysqli_stmt_execute($stmt_update)) {
                    header('Location: adminhome.php');
                    exit;
                } else {
                    echo "Update failed: " . mysqli_stmt_error($stmt_update);
                }
            
                // Close the UPDATE statement
                mysqli_stmt_close($stmt_update);
            } else {
                 // Prepare the SQL query to update the value to 0
                   // Insert into System_Move table for the start value
$action_description_start = "تم فتح تسجيل الدخول ";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
                 $query_update = "UPDATE setting SET value = '1' WHERE sys_type = 'web' AND option = 'sys_login_h'";
            
                 // Initialize a statement object
                 $stmt_update = mysqli_stmt_init($conn);
             
                 // Check if the statement initialization was successful
                 if (!mysqli_stmt_prepare($stmt_update, $query_update)) {
                     die("Statement preparation failed: " . mysqli_error($conn));
                 }
             
                 // Execute the UPDATE statement
                 if (mysqli_stmt_execute($stmt_update)) {
                     header('Location: adminhome.php');
                     exit;
                 } else {
                     echo "Update failed: " . mysqli_stmt_error($stmt_update);
                 }
            }
        } else {
            die("SELECT query failed: " . mysqli_error($conn));
        }
        
        // Close the SELECT result
        mysqli_free_result($result_select);
    }


    if (isset($_POST['move'])) {
        $id = $_SESSION['id'];
        // Fetch the data again after insertion
$query = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result) {
   $row = mysqli_fetch_assoc($result);
   $name = $row['username'];
   $user_type = $row['user_type'];
}
        // Prepare the SQL query to select the value column where sys_type is 'web'
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {
                           // Insert into System_Move table for the start value
$action_description_start = "تم قفل حركة النظام ";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
                // Prepare the SQL query to update the value to 0
                $query_update = "UPDATE setting SET value = '0' WHERE sys_type = 'web' AND option = 'sys_move'";
            
                // Initialize a statement object
                $stmt_update = mysqli_stmt_init($conn);
            
                // Check if the statement initialization was successful
                if (!mysqli_stmt_prepare($stmt_update, $query_update)) {
                    die("Statement preparation failed: " . mysqli_error($conn));
                }
            
                // Execute the UPDATE statement
                if (mysqli_stmt_execute($stmt_update)) {
                    header('Location: adminhome.php');
                    exit;
                } else {
                    echo "Update failed: " . mysqli_stmt_error($stmt_update);
                }
            
                // Close the UPDATE statement
                mysqli_stmt_close($stmt_update);
            } else {
                  // Insert into System_Move table for the start value
$action_description_start = "تم فتح حركة النظام ";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
                 // Prepare the SQL query to update the value to 0
                 $query_update = "UPDATE setting SET value = '1' WHERE sys_type = 'web' AND option = 'sys_move'";
            
                 // Initialize a statement object
                 $stmt_update = mysqli_stmt_init($conn);
             
                 // Check if the statement initialization was successful
                 if (!mysqli_stmt_prepare($stmt_update, $query_update)) {
                     die("Statement preparation failed: " . mysqli_error($conn));
                 }
             
                 // Execute the UPDATE statement
                 if (mysqli_stmt_execute($stmt_update)) {
                     header('Location: adminhome.php');
                     exit;
                 } else {
                     echo "Update failed: " . mysqli_stmt_error($stmt_update);
                 }
            }
        } else {
            die("SELECT query failed: " . mysqli_error($conn));
        }
        
        // Close the SELECT result
        mysqli_free_result($result_select);
    }
    mysqli_close($conn);
}
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

    <title>نظره عامه</title>
</head>



<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>نظره عامه</h1>
               
                <div class="cal-wrapper">
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

<button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
<br>
<button style="margin-bottom: 30px;
                    margin-top: 20px;
                    margin-left: 400px;
                    <?php if ($currentValue === "0") echo "background-color: red;"; ?>"
            type="submit"
            class="save"
            name="login">
        <?php if ($currentValue === "0") echo "غير مفعل تسجيل الدخول"; else echo "مفعل تسجيل الدخول"; ?>
    </button>

    <button style="margin-bottom: 30px;
                    margin-top: 20px;
                    margin-left: 0px;
                    <?php if ($currentValue_move === "0") echo "background-color: red;"; ?>"
            type="submit"
            class="save"
            name="move">
        <?php if ($currentValue_move === "0") echo "غير مفعل تسجيل حركة النظام"; else echo "مفعل تسجيل حركة النظام"; ?>
    </button>
</form>
                 </div>
                 <div style="margin-left: 100px;" class="form-cont">
                 <form method="post">
                    <div class="form-row1">
                        <label for="start-balance">بنزين 95</label>
                        <br>
                        <input type="text" id="start-balance" class="bo" value="<?php echo isset($ben_95) ? $ben_95 : ''; ?>" readonly>
                    </div>
                        <div class="form-row1">
                            <label for="oil-type">بنزين 92</label>
                            <br>
                            <input type="text" id="start-balance" class="bo" value="<?php echo isset($ben_92) ? $ben_92 : ''; ?>" readonly>
                        </div>
                        <div class="form-row1">
                            <label for="start-balance"> بنزين 80</label>
                            <br>
                            <input type="text" id="start-balance" class="bo" value="<?php echo isset($ben_80) ? $ben_80 : ''; ?>" readonly>
                        </div>
                        <div class="form-row1">
                            <label for="start-balance"> سولار</label>
                            <br>
                            <input type="text" id="start-balance" class="bo" value="<?php echo isset($solar) ? $solar : ''; ?>" readonly>
                        </div>
                 </form>
                        <div  style="width: 72%; margin-top:80px; margin-left:-130px" class="table" id="datatable">
                            <div class="table-header">
                                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">التاريخ</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">الوقت</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">النوع</a></div>
                                <div class="header__item"><a id="name" class="filter__link" href="#">اسم المستخدم</a></div>
                            </div>
                            <div class="table-content">	
                              <?php
                                 // Display data in the table
                                 if ($num_clients === 0) {
                                    echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
                                    } else {
                                         while ($row = mysqli_fetch_assoc($result)) {
                                             echo '<div class="table-row">';
                                             echo '<div class="table-data">' .  $row['formatted_date']  . '</div>';
                                             echo '<div class="table-data">' .  $row['time']  . '</div>';
                                             echo '<div class="table-data">' .  $row['sys_type']  . '</div>';
                                             echo '<div class="table-data">' . $row['name'] . '</div>';
                                             echo '</div>';
                                            }
                                        }
                                ?>
                            </div>	
                        </div>
                        <form method="post">
                        <div style="margin-left: 700px;
                        margin-top:-695px" class="form-cont">
                            <div class="form-row1">
    <label for="start-balance">بنزين 95</label>
    <br>
    <input type="text" id="start-balance" class="bo" name="ben95_price"  value="<?php echo isset($ben95_price) ? $ben95_price : ''; ?>">
</div>
<br> 

                                <div class="form-row1">
    <label for="start-balance">بنزين 92</label>
    <br>
    <input type="text" id="start-balance" class="bo" name="ben92_price"  value="<?php echo isset($ben92_price) ? $ben92_price : ''; ?>">
</div>
<br>
                                <div class="form-row1">
    <label for="start-balance">بنزين 80</label>
    <br>
    <input type="text" id="start-balance" class="bo" name="ben80_price"  value="<?php echo isset($ben80_price) ? $ben80_price : ''; ?>">
</div>
<br>
                                <div class="form-row1">
    <label for="start-balance">سولار</label>
    <br>
    <input type="text" id="start-balance" class="bo" name="solar_price"  value="<?php echo isset($solar_price) ? $solar_price : ''; ?>">
</div>
<br>

                                <button style="margin-bottom: 30px;
                    margin-top: 20px;
                    margin-left: 90px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
                        </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
  <!-- Vertical navbar section  -->
  <?php 
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
                echo ' </section>'; ?>
<script src="script.js"></script>
<script>
    function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting_time').value = today; // Set the input field value to today's date
    }
</script>
</body>

</html>