<?php

if (isset($_FILES['fileToUpload2'])) {
    $file = $_FILES['fileToUpload2'];
    $file;

    //file properties
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    //file extensions
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));
    $allowed = array("txt", 'jpg');

    /* Check to see if file type is allowed for upload via $allowed, and if so,
      break on requested character */
    if (in_array($file_ext, $allowed)) {
        if ($file_error === 0) {
            if ($file_size <= 2097152) {
//                 $file_name_new = uniqid('', true) . "." . $file_ext;
//                 $file_destination = 'uploads/' . $file_name_new;
//                 
//                 if(move_uploaded_file($file_tmp, $file_destination)){
//                     $file_destination;
//                 }
                $myfile = fopen($file_tmp, "r") or die("Unable to open file!");
                $readfile = fread($myfile, filesize($file_tmp));
                $exploded = explode('.', $readfile);
//                 print_r($exploded);
                fclose($myfile);
//                 print_r($exploded[0]);

                $servername = "";
                $username = "";
                $password = "";
                $dbname = "test";
// Create connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                foreach ($exploded as $value) {
                    $sql = "insert into line(sentence) values('$value');";
                    mysqli_query($conn, $sql);
                }
                mysqli_close($conn);
            }
        }
    }
}    
