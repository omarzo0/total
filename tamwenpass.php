<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

require_once ('start2.php');

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
   $num_ben = 0;
   // Fetch the data for the current month
   $currentYear = date('Y');

// Get today's date in the format 'Y-m-d'
$currentDate = date("Y-m-d");

// Calculate the sum of the values in the 'total' column of the 'benzene' table for today's date, grouped by 'trumba_type'
$query_uppp = "SELECT trumba_type, SUM(total) AS total_sum FROM benzene WHERE DATE(date) = '$currentDate' GROUP BY trumba_type";
$result_upp = mysqli_query($conn, $query_uppp);

if ($result_upp) {
    // Loop through the results and update the 'monsrf' column in the 'daftr_tamwen' table for each 'trumba_type'
    while ($row = mysqli_fetch_assoc($result_upp)) {
        $trumbaType = $row['trumba_type'];
        $totalSum = $row['total_sum'];
        $query = "SELECT start, ward, tlomba, end, m3yar, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date 
        FROM daftr_tamwen 
        WHERE benz_type = '$trumbaType' AND DATE(date) = '$currentDate'";
 $result = mysqli_query($conn, $query);
 if ($result) {
    $row = mysqli_fetch_assoc($result);
    $ward = $row['ward'];
    $start_balance = $row['start'];
    $m3yar =  ($start_balance + $ward) - $totalSum;
        // Update the 'monsrf' column in the 'daftr_tamwen' table with the calculated sum for the specific 'trumba_type'
        $updateQuery = "UPDATE daftr_tamwen SET monsrf = $totalSum, end = '$m3yar' WHERE benz_type = '$trumbaType' AND DATE(date) = '$currentDate'";
        $updateResult = mysqli_query($conn, $updateQuery);

        if (!$updateResult) {
            echo "Error updating 'monsrf' column for trumba_type: $trumbaType - " . mysqli_error($conn) . "<br>";
        }
    }
    }
} else {
    echo "Error calculating sum: " . mysqli_error($conn);
}

$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : date('m');

$selectedValue = isset($_POST['selectedValue']) ? $_POST['selectedValue'] : 'سولار';
   $query = "SELECT benz_type, start, ward, monsrf, tlomba, end, m3yar, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date 
             FROM daftr_tamwen 
             WHERE YEAR(date) = '$currentYear' AND  (MONTH(date) = '$selectedMonth' AND benz_type = '$selectedValue')";
   
   $result = mysqli_query($conn, $query);
   if ($result) {
       // Count the number of output rows
       $num_clients = mysqli_num_rows($result);
       $tableRows = '';
       if ($num_clients === 0) {
           $tableRows .= '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
       } else {
           while ($row = mysqli_fetch_assoc($result)) {
            $buttonvalues = $row['benz_type']. '|' .$currentDate;
               $nameValue = $row['m3yar'];
               $startValue = $row['start'];
               $wardya = $row['ward'];
               $date_nw = $row['formatted_date'];
               $monsrf = $row['monsrf'];
               $tlomba = $row['tlomba'];
               $end = $row['end'];
               $tableRows .= '
                   <div class="table-row">
                       <div class="table-data">' . $nameValue . '</div>
                       <div class="table-data">' . $end . '</div>
                       <div class="table-data"><button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;" id="myform" type="submit" class="save" name="delete" value="' . $buttonvalues . '">الطلمبة</button></div>
                       <div class="table-data">' . $monsrf . '</div>
                       <div class="table-data">' . $wardya . '</div>
                       <div class="table-data">' . $startValue . '</div>
                       <div class="table-data">' . $date_nw . '</div>
                   </div>';
           }
       }
   }



/////date and benz_type
if (isset($_POST['search'])) {
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $selectedValue = isset($_POST['selectedValue']) ? $_POST['selectedValue'] : '';
    // Convert the input date to the format 'month/day/year'
    $search_date_formatted = date('m/d/Y', strtotime($date));

    // Query the term_clients table with the formatted date
    $query = "SELECT benz_type, start, ward, monsrf, tlomba, end, m3yar, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM daftr_tamwen
              WHERE DATE_FORMAT(date, '%m/%d/%Y') = '$search_date_formatted' AND benz_type = '$selectedValue'";

    $result = mysqli_query($conn, $query);
   if ($result) {
       // Count the number of output rows
       $num_clients = mysqli_num_rows($result);
       $tableRows = '';
       if ($num_clients === 0) {
           $tableRows .= '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
       } else {
           while ($row = mysqli_fetch_assoc($result)) {
            $buttonvalues = $row['benz_type']. '|' .$row['formatted_date'];
               $m3yarValue = $row['m3yar'];
               $start_Value = $row['start'];
               $wardyaValue = $row['ward'];
               $date_nw = $row['formatted_date'];
               $monsrf = $row['monsrf'];
               $tlomba = $row['tlomba'];
               $end = $row['end'];
               $tableRows .= '
                   <div class="table-row">
                       <div class="table-data">' . $m3yarValue . '</div>
                       <div class="table-data">' . $end . '</div>
                       <div class="table-data"><button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;" id="myform" type="submit" class="save" name="delete" value="' . $buttonvalues . '">الطلمبة</button></div>
                       <div class="table-data">' . $monsrf . '</div>
                       <div class="table-data">' . $wardyaValue . '</div>
                       <div class="table-data">' . $start_Value . '</div>
                       <div class="table-data">' . $date_nw . '</div>
                   </div>';
           }
       }
   }
}

//// zorar el torombat

if (isset($_POST['delete'])) {
    $search_name = $_POST['delete'];
    $currentDate = date("Y-m-d");
    // Split the value into an array
    $values = explode('|', $search_name);
    // Get the individual trumba_type and trumba_number
    $benz_type = mysqli_real_escape_string($conn, $values[0]);
    $searchdate = mysqli_real_escape_string($conn, $values[1]);
   if ($currentDate == $searchdate)
   {
    // Fetch the data again after deletion
    $query_ben = "SELECT trumba_number, trumba_type, start, end, total,DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM benzene 
                  WHERE trumba_type = '$benz_type' AND DATE(date) = '$searchdate'";
    $result_ben = mysqli_query($conn, $query_ben);

    if ($result_ben) {
        // Count the number of output rows
        $num_ben = mysqli_num_rows($result_ben);
    } else {
        echo "Error fetching data: " . mysqli_error($conn);
    }
}
else {
    // Fetch the data again after deletion
    $query_ben = "SELECT trumba_number, trumba_type, start, end, total,DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM benzene 
                  WHERE trumba_type = '$benz_type' AND DATE_FORMAT(date, '%m/%d/%Y') = '$searchdate'";
    $result_ben = mysqli_query($conn, $query_ben);

    if ($result_ben) {
        // Count the number of output rows
        $num_ben = mysqli_num_rows($result_ben);
    } else {
        echo "Error fetching data: " . mysqli_error($conn);
    }
}
}


if (isset($_POST['update_form'])) {
    // Get the selected benz_type from the POST data
    $selectedValue = isset($_POST['selectedValue']) ? $_POST['selectedValue'] : "";

    // Check if benz_type is selected
    if (!empty($selectedValue)) {
        // Get the other values from the POST data
        $ward = isset($_POST['wardyaValue']) ? $_POST['wardyaValue'] : '';
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $start_balance = isset($_POST['start_balance']) ? $_POST['start_balance'] : '';
        $nameValue = isset($_POST['nameValue']) ? $_POST['nameValue'] : '';

        $query_monsrf = "SELECT monsrf FROM daftr_tamwen WHERE benz_type = '$selectedValue' AND date = '$date'";
        $result_monsrf = mysqli_query($conn, $query_monsrf);

        if ($result_monsrf) {
            $row_monsrf = mysqli_fetch_assoc($result_monsrf);
            $monsrf = $row_monsrf['monsrf'];
            $end = ($start_balance + $ward) - $monsrf;

            $query = "UPDATE daftr_tamwen
                      SET start = '$start_balance', ward = '$ward', end = '$end', date = '$date', m3yar = '$nameValue'
                      WHERE benz_type = '$selectedValue' AND date = '$date'";

            // Execute the update query
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "tamwenpass.php";</script>';
            } else {
                // Update failed
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "Error fetching monsrf value: " . mysqli_error($conn);
        }
    }
}


if (isset($_POST['arsda']))
{
    header('Location: eqfa.php');
    exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <link rel="stylesheet" href="css/responsivetampass.css">
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>دفتر التموين</title>
</head>

<body>
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1 style="margin-left: 900px;" >دفتر التموين</h1>
                <hr>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting-time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
<br><br>
                <a href="tamwenpass.php"><button style="margin-left: 90px;" class="save">دفتر التموين</button></a>
                <a href="eqfa.php"><button class="save" name="arsda" >مطابقه الارصده</button></a>
            </div>
                <div style="margin-left: 200px;" class="form-cont2">
                <div style="margin-left: -20px;"class="form-row1">
                    <label for="oil-type">المعيار</label>
                    <br>
                    <input type="text" placeholder="ادخل المعيار" id="name3" name="nameValue" value="<?php echo isset($m3yarValue) ? $m3yarValue : ''; ?>" <?php if (!isset($m3yarValue) || $m3yarValue === '') echo 'readonly'; ?>>
                </div>
                <div style="margin-right: 100px;"class="form-row1">
                    <label for="oil-type">ادخل الوارد</label>
                    <br>
                    <input type="text" placeholder="ادخل الوارد" id="name3" name="wardyaValue" value="<?php echo isset($wardyaValue) ? $wardyaValue : ''; ?>" <?php if (!isset($wardyaValue) || $wardyaValue === '') echo 'readonly'; ?>>
                </div>
                <div style="margin-left: 20px;" class="form-row1">
                    <label style="margin-left: -47px;" for="start-balance">ادخل البدايه</label>
                    <br>
                    <input type="text" placeholder="ادخل البدايه" id="name3" name="start_balance" value="<?php echo isset($start_Value) ? $start_Value : ''; ?>" <?php if (!isset($start_Value) || $start_Value === '') echo 'readonly'; ?>>
                </div>
                <div style="margin-left: 30px;" class="form-row1">
    <label style="margin-left: 20px;" for="start-balance">اختر نوع البنزين</label>
    <br>
    <select style="margin-left: -12px;" id="searchDropdown" name="selectedValue" onchange="this.form.submit()">
    <?php
    $options = array(
        "اختر نوع البنزين" => "",
        "سولار" => "سولار",
        "بنزين 80" => "بنزين 80",
        "بنزين 92" => "بنزين 92",
        "بنزين 95" => "بنزين 95",
    );

    // Get the selected value from the POST data
    $selectedValue = isset($_POST['selectedValue']) ? $_POST['selectedValue'] : "";
    if (empty($selectedValue)) {
        $selectedValue = "سولار";
    }

    foreach ($options as $label => $value) {
        $selected = $selectedValue == $value ? "selected" : "";
        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
    }
    ?>
</select>

</div>

<div style="margin-left: 30px;" class="form-row1">
    <label style="margin-left: 20px;" for="start-balance">اختر الشهر</label>
    <br>
    <select style="margin-left: -12px;" name="selectedMonth" id="search_month" onchange="this.form.submit()">
            <?php
            $options = array(
                "اختر الشهر" => "",
                "1" => "01",
                "2" => "02",
                "3" => "03",
                "4" => "04",
                "5" => "05",
                "6" => "06",
                "7" => "07",
                "8" => "08",
                "9" => "09",
                "10" => "10",
                "11" => "11",
                "12" => "12"
            );

            $selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : "";
            if (empty($selectedMonth)) {
                $selectedMonth = date('m');
            }

            foreach ($options as $label => $value) {
                $selected = $selectedMonth == $value ? "selected" : "";
                echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
            }
            ?>
        </select>
</div>

        <br><br>
        <button style="margin-bottom: 30px; margin-top: -200px; margin-left: 40px;" type="submit" class="save" name="update_form" >حفظ</button>
      
                </div>
    </form>

    <div style="width: 80%; margin-left: -10px" class="table" id="datatable">
    <div class="table-header">
            <div class="header__item"><a id="name" class="filter__link" href="#">المعيار</a></div>
            <div class="header__item"><a id="name" class="filter__link" href="#">النهاية</a></div>
            <div class="header__item"><a id="name" class="filter__link" href="#">الطلمبات</a></div>
            <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">المنصرف</a></div>
            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">الوارد</a></div>
            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البداية</a></div>
            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">التاريخ</a></div>
        </div>
        <div class="table-content" id="tableContent">
            <form method="post">
            <?php
           
            echo $tableRows;
       ?>
            </form>
        </div>
    </div>

                  
        </div>
    </div>
    <h1 style="margin-left: 470px;" >الطرمبات</h1>
        <div  style="width: 74%;
        margin-bottom: 50px;
        margin-top: 50px;
        margin-left:-10px" class="table" id="datatable">
            <div class="table-header">
                <div class="header__item"><a id="name" class="filter__link" href="#">الاجمالي</a></div>
                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">النهايه</a></div>
                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البدايه</a></div>
                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">النوع</a></div>
                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">رقم الطلمبه</a></div>
            </div>
            <div class="table-content">	
            <?php
    // Display data in the table
    if ($num_ben === 0) {
        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
    } else {
        while ($row = mysqli_fetch_assoc($result_ben)) {
            echo '<div class="table-row">';
            echo '<div class="table-data">' . $row['total'] . '</div>';
            echo '<div class="table-data">' . $row['end'] . '</div>';
            echo '<div class="table-data">' . $row['start'] . '</div>';
            echo '<div class="table-data">' . $row['trumba_type'] . '</div>';
            echo '<div class="table-data">' . $row['trumba_number'] . '</div>'; 
            echo '</div>';          
        }
    }
    ?>
            </div>	
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
<script src="script.js"></script>
<script>  function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting-time').value = today; // Set the input field value to today's date
    }
    </script>
</body>
</html>