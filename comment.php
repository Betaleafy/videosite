<?php
    session_start();
    $sendBackID = "";
    if(isset($_SESSION['UserID']))
    {
      echo "uID confirmed";

        $server = "localhost";
	    $user = "auxzilco_ServerRegister";
	    $pass = "7g(c_vRa#i?M";
	    $db = "auxzilco_CentralDB";

        $conn = new mysqli($server, $user, $pass, $db);
        $conn->set_charset('utf8mb4');

        echo "1";

        if($conn->connect_error)
        {
            die("Couldnt connect to SQL Server! (big oof)<br>");
        }

      if(isset($_POST['postcomment']) && isset($_POST['v']))
      {
        $text = mysqli_real_escape_string($conn, $_POST['comment']);
        $userID = $_SESSION['UserID'];
        $videoID = mysqli_real_escape_string($conn, $_POST['v']);
        $commentID = generateRandomString(64);
        $commentDate = date("Y-m-d H:i:s");

        $sendBackID = $videoID;

        echo "2";

        $sql = "INSERT INTO Comments (CommentID, UserID, VideoID, PostDate, Text) VALUES ('$commentID', '$userID', '$videoID', '$commentDate', '$text')";
        $ret = $conn->query($sql);

        $sql = "SELECT * FROM Videos WHERE VideoID = '$videoID'";
        $ret = $conn-> query($sql);

        $vinfo = $ret->fetch_assoc();
        $cID = $vinfo['UserID'];
        $vTitle = $vinfo['Title'];

        $sql = "SELECT * FROM Users WHERE UserID = '$userID'";
        $ret = $conn-> query($sql);

        $uinfo = $ret->fetch_assoc();
        $commentName = $uinfo['Name'];

        $nID = generateRandomString(64);
        $hdln = "New Comment!";
        $usr = $commentName;
        $usr = htmlspecialchars($usr, ENT_QUOTES, 'UTF-8');
        $vT = $vTitle;
        $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
        $txt = "The User <b>$usr</b> has made a Comment on your Video <b>$vT</b>";
        $date = date("Y-m-d H:i:s");
        $link = "watch.php?v=$videoID";
        $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Link, Date) VALUES ('$nID','$hdln','$txt','$cID','$link','$date')";
        $ret = $conn->query($sql);
        echo "3";

        $contents = explode(" ", $text);
        for($i = 0; $i < count($contents); $i++){
          echo $contents[$i] . "    -    ";
          $pos = strpos($contents[$i], "@");
          if($pos !== FALSE){
            echo " found! ";
            $content = str_replace("@", "", $contents[$i]);
            $content = mysqli_real_escape_string($conn, $content);
            $sql = "SELECT * FROM Users WHERE Name = '$content'";
            $ret = $conn->query($sql);
            if($ret->num_rows > 0){
              $info = $ret->fetch_assoc();
              $cID = $info['UserID'];
              $nID = generateRandomString(64);
              $hdln = "Someone pinged you in a Comment!";
              $usr = $commentName;
              $usr = htmlspecialchars($usr, ENT_QUOTES, 'UTF-8');
              $vT = $vTitle;
              $vT = htmlspecialchars($vT, ENT_QUOTES, 'UTF-8');
              $txt = "The User <b>$usr</b> has made a Comment and pinged you on the Video <b>$vT</b>";
              $date = date("Y-m-d H:i:s");
              $sql = "INSERT INTO Notifications (NotificationID, Headline, Text, UserID, Link, Date) VALUES ('$nID','$hdln','$txt','$cID','$link','$date')";
              $ret = $conn->query($sql);
            }
          }
        }
      }

      if(isset($_POST['delcomment']))
      {
          echo "1";
        if($_SESSION['UserID'] == $_POST['PostUserID'])
        {
            echo "2";
          $commentID = mysqli_real_escape_string($conn, $_POST['CommentID']);
          $sendBackID = $_POST['v'];
          echo "2.5 lol";
          $sql = "DELETE FROM Comments WHERE CommentID = '$commentID';";
          $ret = $conn->query($sql);
          echo "3";
        }
      }
    }

    echo "<script>window.location='watch.php?v=$sendBackID'</script>";

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
