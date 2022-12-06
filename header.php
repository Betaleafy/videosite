<?php
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }
    if(isset($_GET['l'])){
        session_destroy();
        echo '<script>window.location="index.php"</script>';
    }
    $Acc = "";
    if(isset($_SESSION['UserID'])){
        $Acc = "<li><a href='/User.php?u=". $_SESSION['UserID'] ."'><b>". htmlspecialchars($_SESSION['Username'], ENT_QUOTES, 'UTF-8') ."</b></a></li>";

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

        $headuID = $_SESSION['UserID'];

        $sql = "SELECT * FROM Notifications WHERE UserID = '$headuID'";
        $ret = $conn->query($sql);

        $notification = "";
        if($ret->num_rows > 0){
            $notification = "<li><a href='notifications.php'><button class='notBtn'><p>$ret->num_rows</p></button></a></li>";
        }
    }
    else{
        $Acc = "<li><a href='/Login.php'>Login</a></li>";
    }

    $lm = "<style>*{--background1: #ededed;--top-bar: white;--background2: white;--font-color: black;--display-color:#d6d6d6;--shadow-color:#bababa;}</style>";

    if(!isset($_COOKIE['lightmode'])){
        $lm = "";
    }
?>
<?php echo $lm;?>
<div class="header">
    <div class = "logoholder">
        <a href="index.php"><img src="Auxzil.png" alt="auxzil" class="logo"></a>
    </div>
    <div>
        <div class = "links">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="discover.php">Discover</a></li>
                <?php echo $Acc?>
                <?php echo $notification;?>
                <li><p>0 <img src="coin.svg" alt="coin" class = "coin"></p></li>
            </ul>
        </div>
    </div>
</div>