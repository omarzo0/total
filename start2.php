<?php
 // Define the "benz_type" values
 
// Function to check if data exists for today's date and a specific trumba_type
function checkDataForTodayByType($conn, $trumba_type) {
    $query = "SELECT COUNT(*) AS count FROM benzene WHERE DATE(date) = CURDATE() AND trumba_type = '$trumba_type'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];
    return $count;
}


if (is_logged_in() || $_SESSION['id'] == 1) {
    // Check if the search form is submitted
        // Perform the database query for today's date if search form is not submitted

        // Check if data exists for today and "سولار"
        $data_exists_solar = checkDataForTodayByType($conn, "سولار");
        $data_exists_ben80 = checkDataForTodayByType($conn, "بنزين 80");
        $data_exists_ben92 = checkDataForTodayByType($conn, "بنزين 92");
        $data_exists_ben95 = checkDataForTodayByType($conn, "بنزين 95");

        if ($data_exists_solar && $data_exists_ben80 && $data_exists_ben92 && $data_exists_ben95) {
            // Data exists for all types and today, perform the database query (same as before)
            $query = "SELECT id, trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE DATE(date) = CURDATE()";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Count the number of output rows
                $num_clients = mysqli_num_rows($result);
            }
        } else {
            // Data does not exist for today, create new rows for each trumba_type

            // For "سولار" with trumba_number from 1 to 6
            $trumba_type_solar = "سولار";
            $start_solar = 0;
            $end_solar = 0;
            $total_solar = 0;

            for ($i = 1; $i <= 6; $i++) {
                $trumba_number_solar = $i;

                $sql_solar = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_solar = mysqli_prepare($conn, $sql_solar);
                mysqli_stmt_bind_param($stmt_solar, "sssss", $trumba_number_solar, $trumba_type_solar, $start_solar, $end_solar, $total_solar);

                if (mysqli_stmt_execute($stmt_solar)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_solar);
            }

            // For "بنزين 80" with one row and trumba_number 1
            $trumba_type_ben80 = "بنزين 80";
            $start_ben80 = 0;
            $end_ben80 = 0;
            $total_ben80 = 0;

            $trumba_number_ben80 = 1;

            $sql_ben80 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
            $stmt_ben80 = mysqli_prepare($conn, $sql_ben80);
            mysqli_stmt_bind_param($stmt_ben80, "sssss", $trumba_number_ben80, $trumba_type_ben80, $start_ben80, $end_ben80, $total_ben80);

            if (mysqli_stmt_execute($stmt_ben80)) {
                // Row insertion successful
            } else {
                // Error occurred while inserting row
                echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                exit;
            }

            mysqli_stmt_close($stmt_ben80);

            // For "بنزين 92" with two rows and trumba_number from 1 to 2
            $trumba_type_ben92 = "بنزين 92";
            $start_ben92 = 0;
            $end_ben92 = 0;
            $total_ben92 = 0;

            for ($i = 1; $i <= 2; $i++) {
                $trumba_number_ben92 = $i;

                $sql_ben92 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_ben92 = mysqli_prepare($conn, $sql_ben92);
                mysqli_stmt_bind_param($stmt_ben92, "sssss", $trumba_number_ben92, $trumba_type_ben92, $start_ben92, $end_ben92, $total_ben92);

                if (mysqli_stmt_execute($stmt_ben92)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_ben92);
            }

            // For "بنزين 95" with two rows and trumba_number from 1 to 2
            $trumba_type_ben95 = "بنزين 95";
            $start_ben95 = 0;
            $end_ben95 = 0;
            $total_ben95 = 0;

            for ($i = 1; $i <= 2; $i++) {
                $trumba_number_ben95 = $i;

                $sql_ben95 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_ben95 = mysqli_prepare($conn, $sql_ben95);
                mysqli_stmt_bind_param($stmt_ben95, "sssss", $trumba_number_ben95, $trumba_type_ben95, $start_ben95, $end_ben95, $total_ben95);

                if (mysqli_stmt_execute($stmt_ben95)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_ben95);
            }
        }

        
 $benz_types = array("سولار", "بنزين 80", "بنزين 92", "بنزين 95");

 // Check if data exists for today and each "benz_type"
 $data_exists = true;
 foreach ($benz_types as $benz_type) {
     $query = "SELECT * FROM daftr_tamwen WHERE benz_type = '$benz_type' AND DATE(date) = CURDATE()";
     $result = mysqli_query($conn, $query);

     if (!$result || mysqli_num_rows($result) === 0) {
         $data_exists = false;
         break;
     }
 }

 if (!$data_exists) {
     // Data does not exist for today, create new rows for each "benz_type"

     // Prepare the insert query
     $sql = "INSERT INTO daftr_tamwen (benz_type, start, ward, monsrf, tlomba, end, m3yar, date) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
     $stmt = mysqli_prepare($conn, $sql);

     if (!$stmt) {
         echo "Error preparing statement: " . mysqli_error($conn);
         exit;
     }

     // Define initial values for each row
     $start = 0;
     $ward = 0;
     $monsrf = 0;
     $tlomba = 0;
     $end = 0;
     $m3yar = 0;

     // Insert rows for each "benz_type"
     foreach ($benz_types as $benz_type) {
         mysqli_stmt_bind_param($stmt, "sssssss", $benz_type, $start, $ward, $monsrf, $tlomba, $end, $m3yar);

         if (!mysqli_stmt_execute($stmt)) {
             echo "Error executing statement: " . mysqli_stmt_error($stmt);
             exit;
         }
     }

     mysqli_stmt_close($stmt);
 }


 $currentDate = date("Y-m-d");
 $query_tamwen = "SELECT * FROM ked WHERE DATE(date) = '$currentDate'";
 $results = mysqli_query($conn, $query_tamwen);
 $row = mysqli_fetch_assoc($results);
 
 if (!$row) {
     $currentDay = date('d');
     $currentMonth = date('m');
 
     if ($currentMonth === '01' && $currentDay === '01') {
         $number = 2;
     } else {
         $currentDate = date("Y-m-d");
         $previousDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
 
         $sql_2 = "SELECT number FROM ked WHERE DATE(date) = '$previousDate'";
         $results_2 = mysqli_query($conn, $sql_2);
         $row_2 = mysqli_fetch_assoc($results_2);
 
         if (!$row_2) {
             $number = 2;
         } else {
             $number_yes = $row_2['number'];
             $number = $number_yes + 1;
         }
     }
 
     // Insert the record with the calculated number
     $sql_insert = "INSERT INTO ked (number, date) VALUES (?, CURDATE())";
     $stmt_insert = mysqli_prepare($conn, $sql_insert);
     mysqli_stmt_bind_param($stmt_insert, "s", $number);
 
     if (mysqli_stmt_execute($stmt_insert)) {
         // Row insertion successful
     } else {
         // Error occurred while inserting row
         echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailyqed.php";</script>';
         exit;
     }
 }
 
 $sql = "SELECT * FROM na2l_fr2_ked WHERE date = '$currentDate'";
 $result = mysqli_query($conn, $sql);
 $rd = mysqli_fetch_assoc($result);
 if ($rd == null) {
     $sql = "SELECT name FROM statement";
     $result = mysqli_query($conn, $sql);
     $statement_names = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
     foreach ($statement_names as $name) {
         $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مقبوضات', '{$name['name']}', 0, 0, '$currentDate')";
         mysqli_query($conn, $sql);
     }
 
     foreach ($statement_names as $name) {
         $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مدفوعات', '{$name['name']}', 0, 0, '$currentDate')";
         mysqli_query($conn, $sql);
     }
 } else {
     $existing_statement_names = array_column($rd, 'Statement');
 $sql = "SELECT name FROM statement WHERE name NOT IN ('" . implode("','", $existing_statement_names) . "') AND NOT EXISTS (SELECT 1 FROM na2l_fr2_ked WHERE na2l_fr2_ked.Statement = statement.name AND na2l_fr2_ked.date = '$currentDate')";
 $result = mysqli_query($conn, $sql);
 $new_statement_names = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
 foreach ($new_statement_names as $name) {
     $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مقبوضات', '{$name['name']}', 0, 0, '$currentDate')";
     mysqli_query($conn, $sql);
 
     $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مدفوعات', '{$name['name']}', 0, 0, '$currentDate')";
     mysqli_query($conn, $sql);
 }
 }
 
}
 ?>