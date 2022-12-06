<?php include "controlHeader.php";?>
<?php
    $WelcomeText = "Welcome!";
    $topVids = "";
    $vidCount = 0;

    //Show newest uploads from subs
    if(isset($_SESSION['UserID'])){
        $name = htmlspecialchars($_SESSION['Username'], ENT_QUOTES, 'UTF-8');
        $id = $_SESSION['UserID'];
        $WelcomeText = "Welcome back, <b>$name</b>!";

        //Get User Data
        $return = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$id), "s", $conn);


        if($return->num_rows > 0){
            $info = $return->fetch_assoc();
            $followsSRL = $info['Follows'];
            $follows = unserialize($followsSRL);

            $sql = "SELECT * FROM Videos WHERE UserID = '$follows[0]'";
            for	($index = 1; $index < count($follows); $index++){
                $creator = mysqli_real_escape_string($conn,$follows[$index]);
                $sql .= " OR UserID = '$creator'";
            }
            $sql .= " ORDER BY Date DESC LIMIT 5";

            $return = $conn->query($sql);

            $sql = str_replace("'", "|", $sql);

            if($return->num_rows > 0){
                while($i = $return->fetch_assoc())
                {
                    $Title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
                    $Thumbnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
                    $UserID = $i['UserID'];
                    $videoID = $i['VideoID'];

                    $ret = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$UserID),"s", $conn);
                    $info = $ret->fetch_assoc();

                    $Username = $info['Name'];

                    $topVids .= "<a href='watch.php?v=$videoID'><section><div class='pimage' style='background-image: url($Thumbnail);'></div><p class='lineclamp' title='$Title'>$Title</p><h5>$Username</h5></section></a>";
                    $vidCount++;
                }
            }
        }
    }
    if($vidCount < 5){
        $noR = 5 - $vidCount;
        $sql = "SELECT * FROM Videos WHERE Visible = 1 ORDER BY RAND() LIMIT $noR";
        $ret = $conn->query($sql);
        while($i = $ret->fetch_assoc()){
            $Title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $Thumbnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
            $UserID = $i['UserID'];
            $videoID = $i['VideoID'];

            $return = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$UserID), "s", $conn);
            $info = $return->fetch_assoc();

            $Username = htmlspecialchars($info['Name'], ENT_QUOTES, 'UTF-8');

            $topVids .= "<a href='watch.php?v=$videoID'><section><div class='pimage' style='background-image: url($Thumbnail);'></div><p class='lineclamp' title='$Title'>$Title</p><h5>$Username</h5></section></a>";
        }
    }
    //featured Creators
    $FeaturedChannels = "";
    $ret = SendSQL("SELECT * FROM Users WHERE Featured = 1 ORDER BY RAND() LIMIT 7", array(), "", $conn);

    while($i = $ret->fetch_assoc()){
        $name = htmlspecialchars($i['Name'], ENT_QUOTES, 'UTF-8');
        $pfp = htmlspecialchars($i['ProfilePic'], ENT_QUOTES, 'UTF-8');
        $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
        $FeaturedChannels .= "<a href='User.php?u=$userID'><div class='channelBlock'><div class='pimage channel shadow' style='background-image: url($pfp);'></div><p>$name</p></div></a>";
    }

    $FeaturedVideos = "";
    $ret = SendSQL("SELECT * FROM Videos WHERE Visible = 1 ORDER BY featureID DESC LIMIT 3", array(), "", $conn);

    while($i = $ret->fetch_assoc()){
        $title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
        $tnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
        $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
        $videoID = htmlspecialchars($i['VideoID'], ENT_QUOTES, 'UTF-8');
        $ret2 = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$userID), "s", $conn);
        $salami = $ret2->fetch_assoc();
        $uName = htmlspecialchars($salami['Name'], ENT_QUOTES, 'UTF-8');
        $FeaturedVideos .= "<div class='videoElement'><a href='watch.php?v=$videoID'><div class='pimage shadow' style='background-image:url($tnail)'></div></a><div class='elementInfo'><a href='watch.php?v=$videoID'><h3>$title</h3></a><p>by <a href='User.php?u=$userID'><span class='red'>$uName</span></a></p></div></div>";
    }

    $FYVideos = "";
    $ret = SendSQL("SELECT * FROM Videos WHERE Visible = 1 ORDER BY RAND() LIMIT 3", array(), "", $conn);

    while($i = $ret->fetch_assoc()){
        $title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
        $tnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
        $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
        $videoID = htmlspecialchars($i['VideoID'], ENT_QUOTES, 'UTF-8');
        $ret2 = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$userID), "s", $conn);
        $salami = $ret2->fetch_assoc();
        $uName = htmlspecialchars($salami['Name'], ENT_QUOTES, 'UTF-8');
        $FYVideos .= "<div class='videoElement'><a href='watch.php?v=$videoID'><div class='pimage shadow' style='background-image:url($tnail)'></div></a><div class='elementInfo'><a href='watch.php?v=$videoID'><h3>$title</h3></a><p>by <a href='User.php?u=$userID'><span class='red'>$uName</span></a></p></div></div>";
    }

    $newvids = "";
    $ret = SendSQL("SELECT * FROM Videos WHERE Visible = 1 ORDER BY Date DESC LIMIT 6", array(), "", $conn);

    while($i = $ret->fetch_assoc()){
        $title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
        $tnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
        $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
        $videoID = htmlspecialchars($i['VideoID'], ENT_QUOTES, 'UTF-8');
        $ret2 = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$userID), "s", $conn);
        $salami = $ret2->fetch_assoc();
        $uName = htmlspecialchars($salami['Name'], ENT_QUOTES, 'UTF-8');
        $newvids .= "<div class='videoElement'><a href='watch.php?v=$videoID'><div class='pimage shadow' style='background-image:url($tnail)'></div></a><div class='elementInfo'><a href='watch.php?v=$videoID'><h3>$title</h3></a><p>by <a href='User.php?u=$userID'><span class='red'>$uName</span></a></p></div></div>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Redesign.css">
    <title>Auxzil</title>
</head>
<body>
    <?php include "SiteHeader.php";?>
    <section class="drop">
        <h2><?php echo $WelcomeText;?></h2>
        <div class="topVids">
            <?php echo $topVids;?>
        </div>
    </section>
    <section class="featuredChannels">
        <h2>Featured Creators</h2>
        <div class="channelLayout">
            <?php echo $FeaturedChannels;?>
        </div>
    </section>
    <div class="hL"></div>
    <section class="customized">
        <section class="featured">
            <h2>Featured</h2>
            <section class="VideoElements">
                <?php echo $FeaturedVideos; ?>
            </section>
        </section>
        <div class="vL"></div>
        <section class="forYou">
            <h2>For you</h2>
            <section class="VideoElements">
                <?php echo $FYVideos; ?>
            </section>
        </section>
    </section>
    <div class="hL"></div>
    <section class="brandNew">
        <h2>Brand New!</h2>
        <section class="newVids">
            <?php echo $newvids; ?>
        </section>
    </section>
    <?php include "footer.html";?>
</body>
</html>