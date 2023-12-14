<?php
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');
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

$selectedMonth = isset($_POST['selectedMonth']) ? $_POST['selectedMonth'] : date('m');
       //سولار
       $query_m3yar_solar = "SELECT SUM(m3yar) AS total_m3yar_solar FROM daftr_tamwen WHERE (MONTH(date) = '$selectedMonth') AND benz_type = 'سولار'";
       $result_m3yar_solar = mysqli_query($conn, $query_m3yar_solar);
       if (!$result_m3yar_solar) {
           die('Query Error: ' . mysqli_error($conn));
       }
       $m3yar_solarRow = mysqli_fetch_assoc($result_m3yar_solar);
       $total_m3yar_solar = $m3yar_solarRow['total_m3yar_solar'];
   
        // بنزين 80
        $query_m3yar_ben80 = "SELECT SUM(m3yar) AS total_m3yar_ben80 FROM daftr_tamwen WHERE (MONTH(date) = '$selectedMonth') AND benz_type = 'بنزين 80'";
        $result_m3yar_ben80 = mysqli_query($conn, $query_m3yar_ben80);
        if (!$result_m3yar_ben80) {
            die('Query Error: ' . mysqli_error($conn));
        }
        $m3yar_ben80Row = mysqli_fetch_assoc($result_m3yar_ben80);
        $total_m3yar_ben80 = $m3yar_ben80Row['total_m3yar_ben80'];
   
         // بنزين 92
       $query_m3yar_ben92 = "SELECT SUM(m3yar) AS total_m3yar_ben92 FROM daftr_tamwen WHERE (MONTH(date) = '$selectedMonth') AND benz_type = 'بنزين 92'";
       $result_m3yar_ben92 = mysqli_query($conn, $query_m3yar_ben92);
       if (!$result_m3yar_ben92) {
           die('Query Error: ' . mysqli_error($conn));
       }
       $m3yar_ben92Row = mysqli_fetch_assoc($result_m3yar_ben92);
       $total_m3yar_ben92 = $m3yar_ben92Row['total_m3yar_ben92'];
   
        // بنزين 95
        $query_m3yar_ben95 = "SELECT SUM(m3yar) AS total_m3yar_ben95 FROM daftr_tamwen WHERE (MONTH(date) = '$selectedMonth') AND benz_type = 'بنزين 95'";
        $result_m3yar_ben95 = mysqli_query($conn, $query_m3yar_ben95);
        if (!$result_m3yar_ben95) {
            die('Query Error: ' . mysqli_error($conn));
        }
        $m3yar_ben95Row = mysqli_fetch_assoc($result_m3yar_ben95);
        $total_m3yar_ben95 = $m3yar_ben95Row['total_m3yar_ben95'];
    
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
    <link rel="shortcut icon" type="x-icon" href="">

    <title>مطابقه الارصده</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1 style="margin-left: 850px;" >مطابقه الارصده</h1>
                <a href="tamwenpass.php"><button style="margin-left: 90px;" class="save">دفتر التموين</button></a>
                <a href="eqfa.php"><button class="save">مطابقه الارصده</button></a>
               
<div style="margin-left: 30px;" class="form-row1">
<form method="post">
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

            foreach ($options as $label => $value) {
                $selected = $selectedMonth == $value ? "selected" : "";
                echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
            }
            ?>
        </select>
</form>
        <br><br><br><br>
</div>
            </div>
            </div>                
               <br><br>
                <div  style="width: 80%;
                margin-left:-10px" class="table" id="datatable">
                    <div class="table-header">
                        <div class="header__item"><a id="name" class="filter__link" href="#">بنزين 95</a></div>
                        <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">بنزين 92</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">بنزين 80</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">سولار</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البيان</a></div>
                    </div>
                    <div class="table-content" id="tableContent">
                    <?php
        if (!empty($selectedMonth)) {
            $query = "SELECT benz_type, SUM(start) AS total_start, SUM(ward) AS total_ward, DATE_FORMAT(date, '%m/%d/%Y') AS formatted_date FROM daftr_tamwen
                      WHERE DATE_FORMAT(date, '%m') = '$selectedMonth'
                      GROUP BY benz_type";
    
            $result = mysqli_query($conn, $query);
    
            if ($result) {
                $totals = array(
                    'سولار' => 0,
                    'بنزين 80' => 0,
                    'بنزين 92' => 0,
                    'بنزين 95' => 0
                );
                
                $wards = array(
                    'سولار' => 0,
                    'بنزين 80' => 0,
                    'بنزين 92' => 0,
                    'بنزين 95' => 0
                );
            
                while ($row = mysqli_fetch_assoc($result)) {
                    $nameValue = $row['benz_type'];
                    $total_start = $row['total_start'];
                    $ward = $row['total_ward']; 
                    $totals[$nameValue] += $total_start;
                    $wards[$nameValue] += $ward;
                }

            }

    //سولار
    $query_mosrf_solar = "SELECT SUM(monsrf) AS total_monsrf FROM daftr_tamwen WHERE DATE_FORMAT(date, '%m') = '$selectedMonth' AND benz_type = 'سولار'";
    $result_monsrf_solar = mysqli_query($conn, $query_mosrf_solar);
    if (!$result_monsrf_solar) {
        die('Query Error: ' . mysqli_error($conn));
    }
    $monsrf_solarRow = mysqli_fetch_assoc($result_monsrf_solar);
    $total_monsrf_solar = $monsrf_solarRow['total_monsrf'];

     // بنزين 80
     $query_mosrf_ben80 = "SELECT SUM(monsrf) AS total_monsrf FROM daftr_tamwen WHERE DATE_FORMAT(date, '%m') = '$selectedMonth' AND benz_type = 'بنزين 80'";
     $result_monsrf_ben80 = mysqli_query($conn, $query_mosrf_ben80);
     if (!$result_monsrf_ben80) {
         die('Query Error: ' . mysqli_error($conn));
     }
     $monsrf_ben80Row = mysqli_fetch_assoc($result_monsrf_ben80);
     $total_monsrf_ben80 = $monsrf_ben80Row['total_monsrf'];

      // بنزين 92
    $query_mosrf_ben92 = "SELECT SUM(monsrf) AS total_monsrf FROM daftr_tamwen WHERE (DATE_FORMAT(date, '%m') = '$selectedMonth' AND benz_type = 'بنزين 92')";
    $result_monsrf_ben92 = mysqli_query($conn, $query_mosrf_ben92);
    if (!$result_monsrf_ben92) {
        die('Query Error: ' . mysqli_error($conn));
    }
    $monsrf_ben92Row = mysqli_fetch_assoc($result_monsrf_ben92);
    $total_monsrf_ben92 = $monsrf_ben92Row['total_monsrf'];

     // بنزين 95
     $query_mosrf_ben95 = "SELECT SUM(monsrf) AS total_monsrf FROM daftr_tamwen WHERE DATE_FORMAT(date, '%m') = '$selectedMonth' AND benz_type = 'بنزين 95'";
     $result_monsrf_ben95 = mysqli_query($conn, $query_mosrf_ben95);
     if (!$result_monsrf_ben95) {
         die('Query Error: ' . mysqli_error($conn));
     }
     $monsrf_ben95Row = mysqli_fetch_assoc($result_monsrf_ben95);
     $total_monsrf_ben95 = $monsrf_ben95Row['total_monsrf'];

     
                echo '<div class="table-row">';
                echo '<div class="table-data">' . $totals['بنزين 95'] . '</div>';
                echo '<div class="table-data">' . $totals['بنزين 92'] . '</div>';
                echo '<div class="table-data">' . $totals['بنزين 80'] . '</div>';
                echo '<div class="table-data">' . $totals['سولار'] . '</div>';
                echo'<div class="table-data"  style = " background-color: gray; color: white;">رصيد البدايه</div>';
                echo '</div>';
            
                echo '<div class="table-row">';
                echo '<div class="table-data">' . $wards['بنزين 95'] . '</div>';
                echo '<div class="table-data">' . $wards['بنزين 92'] . '</div>';
                echo '<div class="table-data">' . $wards['بنزين 80'] . '</div>';
                echo '<div class="table-data">' . $wards['سولار'] . '</div>';
                echo'<div class="table-data" style = " background-color: gray; color: white;">الوارد</div>';
                echo '</div>';

                echo '<div class="table-row">';
                echo '<div class="table-data">' . ($totals['بنزين 95'] + $wards['بنزين 95']) . '</div>';
                echo '<div class="table-data">' . ($totals['بنزين 92'] + $wards['بنزين 92']) . '</div>';
                echo '<div class="table-data">' . ($totals['بنزين 80'] + $wards['بنزين 80']) . '</div>';
                echo '<div class="table-data">' . ($totals['سولار'] + $wards['سولار']) . '</div>';
                echo '<div class="table-data" style="background-color: gray; color: white;">الاجمالى</div>';
                echo '</div>';

                echo '<div class="table-row">';
                echo '<div class="table-data">' . $total_monsrf_ben95 . '</div>';
                echo '<div class="table-data">' .$total_monsrf_ben92 . '</div>';
                echo '<div class="table-data">' . $total_monsrf_ben80 . '</div>';
                echo '<div class="table-data">' . $total_monsrf_solar. '</div>';
                echo '<div class="table-data" style="background-color: gray; color: white;">المنصرف</div>';
                echo '</div>';

                echo '<div class="table-row">';
                echo '<div class="table-data">' . (($totals['بنزين 95'] + $wards['بنزين 95']) - $total_monsrf_ben95) . '</div>';
                echo '<div class="table-data">' . (($totals['بنزين 92'] + $wards['بنزين 92']) - $total_monsrf_ben92) . '</div>';
                echo '<div class="table-data">' . (($totals['بنزين 80'] + $wards['بنزين 80']) - $total_monsrf_ben80). '</div>';
                echo '<div class="table-data">' . (($totals['سولار'] + $wards['سولار']) - $total_monsrf_solar). '</div>';
                echo '<div class="table-data" style="background-color: gray; color: white;">الرصيد المتبقي</div>';
                echo '</div>';
            
        } else {
            echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
        }
    
?>
                    </div>	
                </div>
                <h1 class="aear" >عيارات الطلمبات</h1>
                <div  style="width: 80%;
                margin-left:-10px" class="table" id="datatable">
                    <div class="table-header">
                        <div class="header__item"><a id="name" class="filter__link" href="#">بنزين 95</a></div>
                        <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">بنزين 92</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">بنزين 80</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">سولار</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">البيان</a></div>
                    </div>
                    <div class="table-content">
                        <div class="table-row">
                            <div class="table-data"><?php echo isset($total_m3yar_ben95) ? $total_m3yar_ben95 : ''; ?></div>
                            <div class="table-data"><?php echo isset($total_m3yar_ben92) ? $total_m3yar_ben92 : ''; ?></div>
                            <div class="table-data"><?php echo isset($total_m3yar_ben80) ? $total_m3yar_ben80 : ''; ?></div>
                            <div class="table-data"><?php echo isset($total_m3yar_solar) ? $total_m3yar_solar : ''; ?></div>
                            <div class="table-data" style="background-color: gray; color: white;">المعيار</div>
                        </div>                     
                    </div>	
                </div>

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
</body>
</html>
<?php
mysqli_close($conn);
?>