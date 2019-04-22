<?php

class FileSift {

    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "mydb";

    public function xmlSift($xmlFile) {
        $xml = simplexml_load_file($xmlFile) or die("Error: Cannot create object");

        if (!is_array($xml)) {
            $xmlFileToArray = (array) $xml;
            $xmlFileArrayIndexed = array_values($xmlFileToArray);
            if (is_array($xmlFileArrayIndexed)) {
                $xmlRootNode = $xmlFileArrayIndexed[0];
                for ($i = 0; $i < sizeof($xmlRootNode); $i++) {
                    $xmlCurrentNode = $xmlRootNode[$i];
                    $xmlCurrentNodeToArray = (array) $xmlCurrentNode;
                    $xmlCurrentNodeArrayIndexed = array_values($xmlCurrentNodeToArray);
                    {
                        $conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "insert into uploads(line1, line2, line3) values('$xmlCurrentNodeArrayIndexed[0]', '$xmlCurrentNodeArrayIndexed[1]', '$xmlCurrentNodeArrayIndexed[2]');";
                        $conn->query($sql);
                        $conn->close();
                    }
                }
            }
        }
    }

    public function delimitedSift($delimitedFile) {
        $file = fopen($delimitedFile, "r") or die("Unable to open file!");
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $exploded = explode("|", $line);

                $conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "insert into uploads(line1, line2, line3) values('$exploded[0]', '$exploded[1]', '$exploded[2]');";
                $conn->query($sql);
                $conn->close();
            }
            fclose($file);
        }
    }

}

if (isset($_FILES['fileToUpload3'])) {
    $file = $_FILES['fileToUpload3'];

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
                    if ($file_tmp) {
                        $delimitedSift = new FileSift();
                        $delimitedSift->delimitedSift($file_tmp);
                    }
                }
            }
        }
    }
    if ($file_ext == 'xml') {
        if ($file_error === 0) {
            if ($file_size <= 2097152) {
                if ($file_tmp) {
                    $count = 0;
                    $xmlSift = new FileSift();
                    $xmlSift->xmlSift($file_tmp);
                }
            }
        }
    }
}
?>