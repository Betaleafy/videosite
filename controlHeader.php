<?php
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }
    session_start();
    if(isset($_GET['l'])){
        session_destroy();
        echo '<script>window.location="index.php"</script>';
        exit;
    }
    $server = "localhost";
    $user = "auxzilco_ServerRegister";
    $pass = "7g(c_vRa#i?M";
    $db = "auxzilco_CentralDB";
    $conn = new mysqli($server, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
    if($conn->connect_error)
    {
        die("Couldnt connect to SQL Server! (big oof)<br>");
    }
    function SendSQL($sql, $params, $types, $conn) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $ret = $stmt->get_result();
        $stmt->close();
        return $ret;  
    }
?>