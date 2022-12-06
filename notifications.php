<?php
    session_start();
    $server = "localhost";
	  $user = "auxzilco_ServerRegister";
	  $pass = "7g(c_vRa#i?M";
	  $db = "auxzilco_CentralDB";
    $table = "Videos";
    $conn = new mysqli($server, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
    if($conn->connect_error)
    {
        die("Couldnt connect to SQL Server! (big oof)<br>");
    }
?>
<?php
    $notis = "";
    if(isset($_SESSION['UserID'])){
        $uID = $_SESSION['UserID'];
        $sql = "SELECT * FROM Notifications WHERE UserID ='$uID'";
        $ret = $conn->query($sql);

        while($i = $ret->fetch_assoc()){
            $header = $i['Headline'];
            $msg = $i['Text'];
            $date = $i['Date'];
            $link = $i['Link'];
            $notis .= "<a href='$link'><div class='aroundbox noti'><section><h2>$header</h2><h4>$date</h4></section><p>$msg</p></div></a>";
        }

        $sql = "DELETE FROM Notifications WHERE UserID = '$uID'";
        $ret = $conn->query($sql);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxzil</title>
    <link rel="stylesheet" href="Design.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
</head>
<body>
    <?php include 'header.php';?>
    <p id="dcP">Notifications</p>
    <section>
        <?php echo $notis;?>
    </section>
</body>
</html>
