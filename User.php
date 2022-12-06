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
    $name = "";
    $data="";
    $pfp = "";
    $bna = "";
    $flws = 0;
    $subStyle = "";
    $subText = "Follow";
    $btns = "<style>.owner{display:none;}</style>";
    $uID = $_GET['u'];
    $sql = "SELECT * FROM Users WHERE UserID='$uID'";
    $ret = $conn->query($sql);
    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $flws = $i['Followers'];
        }
    }
    if(isset($_POST['FLW']) && isset($_SESSION['UserID']) && isset($_POST['channel'])){
        if($_SESSION['UserID'] != $_POST['channel']){
            $ufID = $_POST['channel'];
        $sql = "SELECT * FROM Users WHERE UserID='". $_SESSION['UserID']."'";
        $ret = $conn->query($sql);
        $followsSRL = "";
        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $followsSRL = $i['Follows'];
            }
        }
        $follows = unserialize($followsSRL);
        if($followsSRL == ""){
            $follows = array();
        }
        if(!in_array($ufID, $follows)){
            array_push($follows, $ufID);
            $flws++;
            $sql = "UPDATE Users
            SET Followers = '$flws'
            WHERE UserID = '$ufID';";
            $ret = $conn->query($sql);

            $liuID = mysqli_real_escape_string($conn, $_SESSION['UserID']);
            $sql = "SELECT * FROM Users WHERE UserID = '$liuID'";
            $ret = $conn->query($sql);

            $uinfo = $ret->fetch_assoc();
            $usr = $uinfo['Name'];

            $nID = generateRandomString(64);
            $hdln = "New Follower!";
            $usr = htmlspecialchars($usr, ENT_QUOTES, 'UTF-8');
            $txt = "<b>$usr</b> is now your Follower!";
            $txt = mysqli_real_escape_string($conn, $txt);
            $date = date("Y-m-d H:i:s");
            $cID = $uID;
            $link = "User.php?u=$liuID";
            $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Link, Date) VALUES ('$nID','$hdln','$txt','$cID','$link','$date')";
            $ret = $conn->query($sql);
        }
        else{
            $flws--;
            if (($key = array_search($ufID, $follows)) !== false) {
                unset($follows[$key]);
            }
            $sql = "UPDATE Users
            SET Followers = '$flws'
            WHERE UserID = '$ufID';";
            $ret = $conn->query($sql);
        }
        $followsSRL = serialize($follows);
        $sql = "UPDATE Users
            SET Follows = '$followsSRL'
            WHERE UserID = '". $_SESSION['UserID']."'";
        $ret = $conn->query($sql);
        }
        echo "<script>window.location=User.php/u=$uID</script>";
    }
    $sql = "SELECT * FROM Users WHERE UserID='$uID'";
    $ret = $conn->query($sql);

    $badgeIMG = "";

    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $name = htmlspecialchars($i['Name'], ENT_QUOTES, 'UTF-8');
            $data = $i['Followers'] . " Followers  ●  ". $i['Coins'] . " Coins";
            $pfp = $i['ProfilePic'];
            $bna = $i['Banner'];
            $flws = $i['Followers'];
            $badges = array($i['Verified'], $i['BetaTester'], $i['KoFi']);
            if($badges[0] == TRUE){
                $badgeIMG .= "<img src='Icons/AuxzilVerified.png' alt='badge' title='Verified'>";
            }
            if($badges[1] == TRUE){
                $badgeIMG .= "<img src='Icons/AuxzilBeta.png' alt='badge' title='Beta Tester'>";
            }
            if($badges[2] == TRUE){
                $badgeIMG .= "<img src='Icons/AuxzilKoFi.png' alt='badge' title='KoFi Supporter'>";
            }
        }
    }
    if(isset($_SESSION['UserID'])){
    $sql = "SELECT * FROM Users WHERE UserID='". $_SESSION['UserID']."'";
    $ret = $conn->query($sql);
    $followsSRL = "";
        if($ret->num_rows > 0){
            while($i = $ret->fetch_assoc()){
                $followsSRL = $i['Follows'];
            }
        }
    $follows = unserialize($followsSRL);
    if(isset($uID)){
    if(in_array($uID, $follows)){
        $subStyle = "<style>#sub{background-color: #c45e5e;}</style>";
        $subText = "UnFollow";
    }
    }
        if($uID == $_SESSION['UserID']){
            $btns = "";
        }
    }

    $StyleShow = "";

    if(isset($_GET['av'])){
        $StyleShow = "<style>.frontpage{display:none;}.about{display:none;}</style>";
    }
    else if(isset($_GET['ab'])){
        $StyleShow = "<style>.frontpage{display:none;}.allvids{display:none;}</style>";
    }
    else{
        $StyleShow = "<style>.allvids{display:none;}.about{display:none;}</style>";
    }


    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
?>
<?php
    $newVideos = "";
    $sql = "SELECT * FROM Videos WHERE UserID='$uID' ORDER BY Date DESC";
    $ret = $conn->query($sql);
    $count = 0;
    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $vidname = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $thmb = $i['ThumbnailURL'];
            $viID = $i['VideoID'];
            $newVideos .= "<a href='watch.php?v=$viID'><div class = 'NewV'><div class='pimg' style='background-image: url(". $thmb .");'><div class='nV'><p class = 'nVt'>". $vidname ."</p></div></div></div></a>";
            $count++;
            if($count == 5){
                break;
            }
        }
    }
    $popVideos = "";
    $sql = "SELECT * FROM Videos WHERE UserID='$uID' ORDER BY Views DESC";
    $ret = $conn->query($sql);
    $count = 0;
    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $vidname = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $thmb = $i['ThumbnailURL'];
            $viID = $i['VideoID'];
            $popVideos .= "<a href='watch.php?v=$viID'><div class = 'NewV'><div class='pimg' style='background-image: url(". $thmb .");'><div class='nV'><p class = 'nVt'>". $vidname ."</p></div></div></div></a>";
            $count++;
            if($count == 5){
                break;
            }
        }
    }
?>
<?php
    $sql = "SELECT * FROM Users WHERE UserID='$uID'";
    $ret = $conn->query($sql);

    $aboutStuff = "";

    if($ret->num_rows > 0){
        $i = $ret->fetch_assoc();
        $aboutStuff = htmlspecialchars($i['About'], ENT_QUOTES, 'UTF-8');
    }

    if($aboutStuff == ""){
        $aboutStuff = "<p style='color:gray;' class='aboutText'>This user doesn't have a Description.</p>";
    }else{
        $aboutStuff = "<p class='aboutText'>". $aboutStuff ."</p>";
    }

    $aboutStuff = str_replace("\r\n", "<br>", $aboutStuff);
    $aboutStuff = make_links_clickable($aboutStuff);

    function make_links_clickable($text){
        return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" style="color:#c94949;">$1</a>', $text);
    }

    $sql = "SELECT * FROM Videos WHERE UserID='$uID' ORDER BY Date DESC";
    $ret = $conn->query($sql);

    $allVideos = "";

    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $vidname = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $thmb = $i['ThumbnailURL'];
            $viID = $i['VideoID'];
            $allVideos .= "<a href='watch.php?v=$viID'><div class = 'NewV'><div class='pimg' style='background-image: url(". $thmb .");'><div class='nV'><p class = 'nVt'>". $vidname ."</p></div></div></div></a>";
        }
    }

    if($allVideos == ""){
        $allVideos = "<p style='color:gray;'>This user doesn't have any Videos.</p>";
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
    <?php echo($btns); echo($subStyle); echo $StyleShow;?>
    <div class = "mainpage">
        <section class="pbuttons">
            <form action="" method="get">
                <input type="hidden" value="<?php echo $uID ?>" name="u">
            <ul>
                <li><input type="submit" value="Home" class="UserButton"></li>
                <li><input type="submit" value="All Videos" name="av" class="UserButton"></a></li>
                <li><input type="submit" value="About" name="ab" class="UserButton"></li>
            </ul>
            </form>
        </section>
        <section class="banner" style="background-image: url(<?php echo($bna); ?>);"></section>
        <div class="pfp" style="background-image: url(<?php echo($pfp); ?>);"></div>
        <section class="uinfo">
            <div id="i">
                <section class="umi"><h1><?php echo $name ?></h1><?php echo $badgeIMG;?></section>
                <h2><?php echo $data ?></h2>
            </div>
            <div class="uButtons">
                <form action="" id="subFrom">
                    <section>
                        <a href="index.php?l=1"><button class="owner" type="button">Logout</button></a>
                        <a href="manage.php"><button class="owner" type="button">Manage Account</button></a>
                        <a href="UploadVideo.php"><button class="owner" type="button">Upload</button></a>
                        <button name="FLW" type="submit" formmethod="post" id="sub"><?php echo $subText?></button>
                        <input type="hidden" name="channel" value="<?php echo($_GET['u']);?>"/>
                    </section>
                </form>
            </div>
        </section>
        <!--Front page stuff-->
        <section class="frontpage">
            <section class="uNew fP">
                <h1>Newest Videos</h1>
                <section class = "unewest">
                    <?php echo $newVideos;?>
                </section>
            </section>
            <section class="uNew fP">
                <h1>Most popular Videos</h1>
                <section class = "unewest">
                    <?php echo $popVideos;?>
                </section>
            </section>
        </section>
        <!--All Videos-->
        <section class="allvids">
            <section class="videoGrid aroundbox">
                <?php echo $allVideos; ?>
            </section>
        </section>
        <!--About page-->
        <section class="about">
            <section class="aroundbox" id="aboutbox">
                <?php echo $aboutStuff; ?>
            </section>
        </section>
        <p class="userInfo"></p>
    </div>
</body>
</html>
