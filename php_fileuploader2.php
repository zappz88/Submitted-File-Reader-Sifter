<?php

if (isset($_FILES['fileToUpload3'])) {
    $file = $_FILES['fileToUpload3'];
    $file;

    //file properties
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    //file extensions
    $file_exploded = explode('.', $file_name);
    $file_ext = strtolower(end($file_exploded));
    $allowed = array("txt", "xml");

    /* Check to see if file type is allowed for upload via $allowed, and if so,
      break on requested character */
    if (in_array($file_ext, $allowed)) {
        if ($file_ext == 'txt') {
            if ($file_error === 0) {
                if ($file_size <= 2097152) {
                    $myfile = fopen($file_tmp, "r") or die("Unable to open file!");
                    if ($myfile) {
                        $count = 0;
                        while (($buffer = fgets($myfile)) !== false) {
                            $exploded = explode("|", $buffer);

                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "mydb";
// Create connection
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            $sql = "insert into uploads(line1, line2, line3) values('$exploded[0]', '$exploded[1]', '$exploded[2]');";
                            mysqli_query($conn, $sql);
                            mysqli_close($conn);
                        }
                    }
                }
                if (!feof($myfile)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($myfile);
            }
        }
        if ($file_ext == 'xml') {
            if ($file_error === 0) {
                if ($file_size <= 2097152) {
                    if ($file_tmp) {
                        $count = 0;
                        $xml = simplexml_load_file($file_tmp) or die("Error: Cannot create object");
                        for ($i = 0; $i < sizeof($xml->person); $i++) {
                            $person = $xml->person[$i];
                            $person = (array) $person;
                            $firstName = $person['firstname'];
                            $middleName = $person['middlename'];
                            $lastName = $person['lastname'];

                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "mydb";
// Create connection
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            $sql = "insert into uploads(line1, line2, line3) values('$firstName', '$middleName', '$lastName');";
                            mysqli_query($conn, $sql);
                            mysqli_close($conn);
                        }
                    }
                }
            }
        }
    }
}
?>
