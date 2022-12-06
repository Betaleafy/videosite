<?php session_start();?>
<?php
    if(!isset($_GET['v'])){
        echo '<script>window.location="index.php"</script>';
    }
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

    $vID = $_GET['v'];
    $sql = "SELECT * FROM Videos WHERE VideoID='$vID'";
    $ret = $conn->query($sql);
    $vTitle = "";
    $vDesc = "";
    $vThumb = "";
    $clicks = "";
    $coinsearned = "";
    $Url = "";
    $cName = "";
    $cDesc = "";
    $cID = "";
    $cPfp = "";

    if($ret->num_rows > 0){
        $i = $ret->fetch_assoc();
        $vTitle = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
        $vDesc = htmlspecialchars($i['Description'], ENT_QUOTES, 'UTF-8');
        $vThumb = $i['ThumbnailURL'];
        $clicks = $i['Views'];
        $coinsearned = $i['Coins'];
        $Url = $i['URL'];
        $cID = $i['UserID'];
        $sql2 = "SELECT * FROM Users WHERE UserID='$cID'";
        $usrret = $conn->query($sql2);
        $j = $usrret->fetch_assoc();
        $cName = htmlspecialchars($j['Name'], ENT_QUOTES, 'UTF-8');
        $cDesc = htmlspecialchars($j['About'], ENT_QUOTES, 'UTF-8');
        $cPfp = htmlspecialchars($j['ProfilePic'], ENT_QUOTES, 'UTF-8');
    }
    else{
        echo '<script>window.location="index.php"</script>';
    }

    $cDesc = str_replace("\r\n", "<br>", $cDesc);

    $heheheheStoleYaIP = $_SERVER['REMOTE_ADDR'];
    $sql = "SELECT * FROM ViewCooldown WHERE IPAddress = '$heheheheStoleYaIP'";
    $ret = $conn->query($sql);

    $wuID = "nouser";

    if(isset($_SESSION['UserID'])){
        $wuID = $_SESSION['UserID'];
    }

    $viewInfo = array("address" => $heheheheStoleYaIP, "userid" => $wuID, "videoid" => $vID, "timestamp" => date("Y-m-d H:i:s"));

    if($ret->num_rows > 0){
        $infoObj = $ret->fetch_assoc();
        $dbTimeStamp = $infoObj['TimeStamp'];
        $tmsp = $viewInfo['timestamp'];
        $minutes = (strtotime($dbTimeStamp) - time()) / 60;

        echo "<script>console.log('$minutes')</script>";

        if($minutes < -5){
            $sql = "SELECT * FROM Videos WHERE VideoID = '$vID';";
            $ret = $conn->query($sql);
            $infoObj = $ret->fetch_assoc();
            $clicks = $infoObj['Views'];
            $clicks++;
            $sql = "UPDATE Videos SET Views = '$clicks' WHERE VideoID = '$vID';";
            $ret = $conn->query($sql);
            $tmsp = $viewInfo['timestamp'];
            $sql = "UPDATE ViewCooldown SET TimeStamp = '$tmsp' WHERE IPAddress = '$heheheheStoleYaIP';";
            $ret = $conn->query($sql);

            checkMarks($clicks, $vTitle, $conn, $cID);
        }
    }
    else{
        $tmsp = $viewInfo['timestamp'];
        $sql = "INSERT INTO ViewCooldown (IPAddress, UserID, VideoID, TimeStamp) VALUES ('$heheheheStoleYaIP', '$wuID','$vID','$tmsp');";
        $ret = $conn->query($sql);
        $sql = "SELECT * FROM Videos WHERE VideoID = '$vID';";
        $ret = $conn->query($sql);
        $infoObj = $ret->fetch_assoc();
        $clicks = $infoObj['Views'];
        $clicks++;
        $sql = "UPDATE Videos SET Views = '$clicks' WHERE VideoID = '$vID';";
        $ret = $conn->query($sql);

        checkMarks($clicks, $vTitle, $conn, $cID);
    }

    function checkMarks($c, $vT, $con, $upID){
        if($c == 100){
            $nID = generateRandomString(64);
            $hdln = "Woah! A Video of yours got 100 views!";
            $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
            $txt = "Your Video <b>$vT</b> has just hit 100 Views!<br> Congrats! :P";
            $txt = mysqli_real_escape_string($con, $txt);
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Date) VALUES ('$nID','$hdln','$txt','$upID','$date')";
            $ret = $con->query($sql);
        }
        else if($c == 1000){
            $nID = generateRandomString(64);
            $hdln = "Woah! A Video of yours got 1000 views!";
            $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
            $txt = "Your Video <b>$vT</b> has just hit 1000 Views!<br> Congrats! :P";
            $txt = mysqli_real_escape_string($con, $txt);
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Date) VALUES ('$nID','$hdln','$txt','$upID','$date')";
            $ret = $con->query($sql);
        }
        else if($c == 10000){
            $nID = generateRandomString(64);
            $hdln = "Woah! A Video of yours got 10000 views!";
            $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
            $txt = "Your Video <b>$vT</b> has just hit 10000 Views!<br> Congrats! :P";
            $txt = mysqli_real_escape_string($con, $txt);
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Date) VALUES ('$nID','$hdln','$txt','$upID','$date')";
            $ret = $con->query($sql);
        }
    }

    $RawDesc = $vDesc;
    $vDesc = str_replace("\r\n", "<br>", $vDesc);
    $vDesc = make_links_clickable($vDesc);
    $newVideos = "";
    $sql = "SELECT * FROM Videos ORDER BY Date DESC LIMIT 5";
    $ret = $conn->query($sql);
    $count = 0;

    if($ret->num_rows > 0){
        while($i = $ret->fetch_assoc()){
            $vidname = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $thmb = $i['ThumbnailURL'];
            $userID = $i['UserID'];
            $views = $i['Views'];
            $coins = $i['Coins'];
            $viID = $i['VideoID'];
            $sql2 = "SELECT * FROM Users WHERE UserID='$userID'";
            $usrret = $conn->query($sql2);
            $j = $usrret->fetch_assoc();
            $username = htmlspecialchars($j['Name'], ENT_QUOTES, 'UTF-8');
            $newVideos .= "<a href='watch.php?v=$viID' class='nocss'><div class='FvS'><div class='pimg' style='background-image:url($thmb);'></div><div><p class = 'FvStitle'>$vidname</p><p class = 'FvUser'>by <a href='User.php?u=$userID'>$username</a></p><section class = 'FvDataC'><p class = 'FvData'>$views Views  $coins Coins</p></section></div></div></a>";
            $count++;
            if($count == 6){
                break;
            }
        }
    }

    function make_links_clickable($text){
        return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" style="color:#c94949;">$1</a>', $text);
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
    $styleEditButton = "<style>#EditButton{display:none}</style>";
    if($cID == $_SESSION['UserID']){
        $styleEditButton = "";
    }
?>

<?php

$sql = "SELECT * FROM Comments WHERE VideoID = '$vID' ORDER BY PostDate DESC";
$ret = $conn->query($sql);

$comment = "";
$commentUserID = "";
$commentPFP = "";

if($ret->num_rows > 0)
{
    while($i = $ret->fetch_assoc())
    {
        $commentUserID = $i['UserID'];
        $commentText = htmlspecialchars($i['Text'], ENT_QUOTES, 'UTF-8');

        $commentText = str_replace("\r\n", "<br>", $commentText);
        $commentText = make_links_clickable($commentText);

        $sql = "SELECT * FROM Users WHERE UserID = '$commentUserID'";
        $ret2 = $conn->query($sql);
        $uinfo = $ret2->fetch_assoc();
        $commentID = $i['CommentID'];
        $commentOptions = "";

        if(isset($_SESSION['UserID'])){
            if($commentUserID == $_SESSION['UserID']){
                $commentOptions = "<form action='comment.php' methog='post' class='deleteButton' method='post'> <input type='hidden' name='CommentID' value='$commentID'> <input type='hidden' name='PostUserID' value='$commentUserID'> <input type='hidden' name='v' value='$vID'> <input type='submit' name='delcomment' value='Delete'> </form>";
            }
        }

        $commentPFP = $uinfo['ProfilePic'];
        $commenUsername = htmlspecialchars($uinfo['Name'], ENT_QUOTES, 'UTF-8');

        $comment .= "<div class='aroundbox commentbox' id='smallbox'> <div class='pimg' style='background-image: url($commentPFP);'></div> <div class='commentstuff'> <h3>$commenUsername</h3> <p>$commentText</p> </div> $commentOptions </div>";
    }
}

    if (strlen($RawDesc) > 100)
        $sDesc = substr($RawDesc, 0, 97) . '...';
    else
        $sDesc = $RawDesc;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxzil</title>
    <link rel="stylesheet" href="Design.css">
    <meta content="<?php echo $vTitle;?>" name="og:title">
    <meta content="<?php echo $sDesc;?>" property="og:description">
    <meta content="Auxzil" property="og:site_name">
    <meta content='https://auxzil.com/gentleman.jpg' property='og:image'>
    <script src="https://cdn.plyr.io/3.5.6/plyr.js"></script>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.5.6/plyr.css">
    <link rel="shortcut icon" href="auxzilco.ico" type="image/x-icon">
    <style>
      .videostuff
      {
        --plyr-color-main: var(--acclr) !important;
        --plyr-video-control-color: var(--acclr) !important;
        --plyr-video-control-color-hover: var(--acclr) !important;
        --plyr-badge-text-color: var(--acclr) !important;
      }
    </style>
</head>
<body>
<?php include 'header.php';?>
<?php echo $styleEditButton;?>
    <div class = "mainv">
        <section class="topSection">
            <div class="videostuff">
            <div class="vidcont">
                    <video autoplay id="plyr-video" width="320", height="240" controls style="--plyr-color-main: #1ac266; !important">
                        <source src="<?php echo $Url; ?>" type="video/mp4">
                        <!--<source src="<?php echo $Url; ?>" type="video/mp4" size="480"> compression code-->
                    </video>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                    // Controls (as seen below) works in such a way that as soon as you explicitly define (add) one control
                    // to the settings, ALL default controls are removed and you have to add them back in by defining those below.
                    // For example, let's say you just simply wanted to add 'restart' to the control bar in addition to the default.
                    // Once you specify *just* the 'restart' property below, ALL of the controls (progress bar, play, speed, etc) will be removed,
                    // meaning that you MUST specify 'play', 'progress', 'speed' and the other default controls to see them again.
                        const controls = [
                        'play-large', // The large play button in the center
                        'restart', // Restart playback
                        'rewind', // Rewind by the seek time (default 10 seconds)
                        'play', // Play/pause playback
                        'fast-forward', // Fast forward by the seek time (default 10 seconds)
                        'progress', // The progress bar and scrubber for playback and buffering
                        'current-time', // The current time of playback
                        'duration', // The full duration of the media
                        'mute', // Toggle mute
                        'volume', // Volume control
                        'captions', // Toggle captions
                        'settings',
                        'fullscreen' // Toggle fullscreen
                    ];
                    const player = Plyr.setup('#plyr-video', { controls });});
                    </script>
                <form action="manage.php" method="post">
                    <div class="vidinfo">
                        <div><h1><?php echo $vTitle; ?></h1></div>
                        <input type="hidden" value="<?php echo $vID?>" name="VideoToEdit">
                        <input type="hidden" value="<?php echo $cID?>" name="Creator">
                        <div><input type="submit" value="Edit" class="UserButton" name="EditVideo" id="EditButton"><div id="vc"><p><?php echo $clicks;?> Views • <?php echo $coinsearned; ?> Coins</p></div></div>
                    </div>
                </form>
                <div class="aroundbox"><p id="desc"><?php echo $vDesc; ?></p></div>
                <div>
                    <form action="comment.php" class="aroundbox comment" id="smallbox" method="post">
                        <p class="headinfo">Comments</p>
                        <textarea name="comment" cols="30" rows="5"></textarea>
                        <input type="hidden" name="v" value="<?php echo $vID;?>">
                        <button name="postcomment" type="submit">Post</button>
                    </form>
                    <?php echo $comment;?>
                </div>
            </div>
            <div class="sideSection">
                <section>
                    <?php echo $newVideos;?>
                </section>
                <section>
                    <a href="User.php?u=<?php echo $cID;?>">
                        <div class="creatorBox">
                            <div class="cpfp" style="background-image: url(<?php echo $cPfp; ?>);"></div>
                            <div>
                                <p><?php echo $cName;?></p>
                                <h4><?php echo $cDesc;?></h4>
                            </div>
                        </div>
                    </a>
                </section>
            </div>
        </section>
    </div>
</body>
<script>document.getElementsByTagName('video')[0].volume = 0.5;</script>
</html>
