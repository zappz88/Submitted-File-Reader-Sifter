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
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));
    $allowed = array("txt");

    /* Check to see if file type is allowed for upload via $allowed, and if so,
      break on requested character */
    if (in_array($file_ext, $allowed)) {
        if ($file_error === 0) {
            if ($file_size <= 2097152) {
                $myfile = fopen($file_tmp, "r") or die("Unable to open file!");
                if ($myfile) {
                    $count = 0;
                    while (($buffer = fgets($myfile)) !== false) {
                        $exploded = explode(".", $buffer);
                        if (count($exploded) != 2) {
                            $count += 1;
                            echo "Line error/Data missing $count" . "<br>";
                        } else {
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

                            $sql = "insert into line(sentence, sentence2) values('$exploded[0]', '$exploded[1]');";
                            mysqli_query($conn, $sql);
//                            }
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
    }
}
?>
