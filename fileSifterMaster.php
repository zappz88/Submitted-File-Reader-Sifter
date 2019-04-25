<?php

class FileSift {

    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "mydb";

    //Convert node to indexed array
    public function nodeToIndexedArray($node) {
        $nodeArray = (array) $node;
        $nodeArrayIndexed = array_values($nodeArray);
        return $nodeArrayIndexed;
    }

    //Convert xml to object and grab root node
    public function getRootNode($xmlFile) {
        $xml = simplexml_load_file($xmlFile) or die("Error: Cannot create object");
        $xmlArrayIndexed = $this->nodeToIndexedArray($xml);
        $xmlRootNode = $xmlArrayIndexed[0];
        return $xmlRootNode;
    }

    public function downloadFile($fileExtension, $fileTmp) {
        if ($fileExtension == "xml") {
            $newFileName = uniqid('', true) . "." . $fileExtension;
            $fileDestination = 'uploads/' . $newFileName;
            if (move_uploaded_file($fileTmp, $fileDestination)) {
                echo "XML File Successfully Uploaded<br>";
            }
            return $fileDestination;
        } else if ($fileExtension == "txt") {
            $newFileName = uniqid('', true) . "." . $fileExtension;
            $fileDestination = 'uploads/' . $newFileName;
            if (move_uploaded_file($fileTmp, $fileDestination)) {
                echo "Text File Successfully Uploaded<br>";
            }
            return $fileDestination;
        }
    }
    
    public function createLink($string, $linkName){
        return "<a href=" . "$string" . ">" . "$linkName" . "</a>";
    }

    //Construct as needed data to upload
    public function insertXmlData($xmlFile) {
        $xmlRootNode = $this->getRootNode($xmlFile);
        if (sizeof($xmlRootNode) !== 0) {
            for ($i = 0; $i < sizeof($xmlRootNode); $i++) {
                $xmlCurrentNode = $xmlRootNode[$i];
                $xmlCurrentNodeArrayIndexed = $this->nodeToIndexedArray($xmlCurrentNode);

                $conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                } else {
                    $sql = "insert into uploads(line1, line2, line3) values('$xmlCurrentNodeArrayIndexed[0]', '$xmlCurrentNodeArrayIndexed[1]', '$xmlCurrentNodeArrayIndexed[2]');";
                    $conn->query($sql);
                    $conn->close();
                }
            }
        }
    }

    public function insertDelimitedData($delimiter, $delimitedFile) {
        $file = fopen($delimitedFile, "r") or die("Unable to open file!");
        if ($file) {
            $count = 0;
            while (($line = fgets($file)) !== false) {
                $count++;
                $exploded = explode($delimiter, $line);
                if (sizeOf($exploded) !== 3) {
                    echo 'Line ' . $count . ' missing data.<br>';
                } else {
                    $conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "insert into uploads(line1, line2, line3) values('$exploded[0]', '$exploded[1]', '$exploded[2]');";
                    $conn->query($sql);
                    $conn->close();
                }
            }
            fclose($file);
        }
    }

    public function showXMLasObjects($xmlFile) {
        $xml = simplexml_load_file($xmlFile) or die("Error: Cannot create object");
        $xmlRootNode = $this->getRootNode($xml);
        for ($i = 0; $i < sizeof($xmlRootNode); $i++) {
            $xmlCurrentNode = $xmlRootNode[$i];
            print_r($xmlCurrentNode);
            echo '<br><br>';
        }
    }

    public function showXMLasArray($xmlFile) {
        $xml = simplexml_load_file($xmlFile) or die("Error: Cannot create object");
        $xmlRootNode = $this->getRootNode($xml);
        for ($i = 0; $i < sizeof($xmlRootNode); $i++) {
            $xmlCurrentNode = $xmlRootNode[$i];
            $xmlCurrentNodeArray = $this->nodeToIndexedArray($xmlCurrentNode);
            print_r($xmlCurrentNodeArray);
            echo '<br><br>';
        }
    }

}

if (isset($_FILES['fileUpload']) && !empty($_FILES['fileUpload'])) {
    $file = $_FILES['fileUpload'];

    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExploded = explode('.', $fileName);
    $fileExtension = strtolower(end($fileExploded));
    $allowedFileTypes = array("txt", "xml");

    if (in_array($fileExtension, $allowedFileTypes) && $fileError === 0 && $fileSize <= 2097152 && !empty($fileTmp)) {
        switch ($fileExtension) {
            case 'xml':
                $xml = new FileSift();
                $xml->insertXmlData($fileTmp);
                $xml->downloadFile($fileExtension, $fileTmp);
                $xmlLink = $xml->downloadFile($fileExtension, $fileTmp);
                echo $xml->createLink($xmlLink, $xmlLink);
                break;
            case 'txt':
                $txt = new FileSift();
                $txt->insertDelimitedData('|', $fileTmp);
                $txt->downloadFile($fileExtension, $fileTmp);
                $txtLink = $txt->downloadFile($fileExtension, $fileTmp);
                echo $txt->createLink($txtLink, $txtLink);
                break;
        }
    } else {
        echo "Not an accepted file type.<br>";
    }
} else {
    echo "File not submitted.<br>";
}
?>
