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
  //fat noises

  if(isset($_GET['search']))
  {
    $search = mysqli_real_escape_string($conn,$_GET['search']);
    $sql = "SELECT * FROM Videos WHERE Title LIKE '%$search%'";
    $return = $conn->query($sql);
    $allVids = "";

    while($i = $return->fetch_assoc())
    {
        $viID = $i['VideoID'];
        $vidname = $i['Title'];
        $tnail = $i['ThumbnailURL'];
        $allVids .=  "<a href='watch.php?v=$viID'><div class = 'Discvr'><div class='pimg' style='background-image: url($tnail);'><div class='nV'><p class = 'nVt'>$vidname</p></div></div></div></a>";
    }
  }
  else if(isset($_GET['follows']) && isset($_SESSION['UserID']))
{
  $usID = $_SESSION['UserID'];
  $sql = "SELECT * FROM Users WHERE UserID = '$usID'";
  $return = $conn->query($sql);

  if($return->num_rows > 0){
    //fat noises
    $info = $return->fetch_assoc();
    $followsSRL = $info['Follows'];
    $follows = unserialize($followsSRL);

    $sql = "SELECT * FROM Videos WHERE UserID = '$follows[0]'";
    for	($index = 1; $index < count($follows); $index++){
      $creator = mysqli_real_escape_string($conn,$follows[$index]);
      $sql .= " OR UserID = '$creator'";
    }
    $sql .= " ORDER BY Date DESC";

    $return = $conn->query($sql);
    $allVids = "";

    if($return->num_rows > 0){
      while($i = $return->fetch_assoc())
      {
          $viID = $i['VideoID'];
          $vidname = $i['Title'];
          $tnail = $i['ThumbnailURL'];
          $allVids .=  "<a href='watch.php?v=$viID'><div class = 'Discvr'><div class='pimg' style='background-image: url($tnail);'><div class='nV'><p class = 'nVt'>$vidname</p></div></div></div></a>";
      }
    }
  }
}
  else
  {
    $sql = "SELECT * FROM Videos ORDER BY Date DESC";
    $return = $conn->query($sql);
    $allVids = "";

    while($i = $return->fetch_assoc())
    {
        $viID = $i['VideoID'];
        $vidname = $i['Title'];
        $tnail = $i['ThumbnailURL'];
        $allVids .=  "<a href='watch.php?v=$viID'><div class = 'Discvr'><div class='pimg' style='background-image: url($tnail);'><div class='nV'><p class = 'nVt'>$vidname</p></div></div></div></a>";
    }
  }

?>

<?php
if(isset($_GET['search']))
{
  $search = mysqli_real_escape_string($conn,$_GET['search']);
  $sql = "SELECT * FROM Users WHERE Name LIKE '%$search%'";
  $return = $conn->query($sql);
  $rUsers = "";

  while($i = $return->fetch_assoc())
  {
      $uID = $i['UserID'];
      $uName = $i['Name'];
      $uDesc = $i['About'];
      $uPFP = $i['ProfilePic'];
      $rUsers .=  "<a href='User.php?u=$uID'><div class='creatorLinkBox'><div class='clpfp' style='background-image: url($uPFP);'></div><div><p>$uName</p><h4>$uDesc</h4></div></div></a>";
  }
}
else if(isset($_GET['follows']) && isset($_SESSION['UserID']))
{
  $usID = $_SESSION['UserID'];
  $sql = "SELECT * FROM Users WHERE UserID = '$usID'";
  $return = $conn->query($sql);

  if($return->num_rows > 0){
    //fat noises
    $info = $return->fetch_assoc();
    $followsSRL = $info['Follows'];
    $follows = unserialize($followsSRL);

    $sql = "SELECT * FROM Users WHERE UserID = '$follows[0]'";
    for	($index = 1; $index < count($follows); $index++){
      $creator = mysqli_real_escape_string($conn,$follows[$index]);
      $sql .= " OR UserID = '$creator'";
    }

    $return = $conn->query($sql);
    $rUsers = "";

    if($return->num_rows > 0){
      while($i = $return->fetch_assoc())
      {
          $uID = $i['UserID'];
          $uName = $i['Name'];
          $uDesc = $i['About'];
          $uPFP = $i['ProfilePic'];
          $rUsers .=  "<a href='User.php?u=$uID'><div class='creatorLinkBox'><div class='clpfp' style='background-image: url($uPFP);'></div><div><p>$uName</p><h4>$uDesc</h4></div></div></a>";
      }
    }
  }
}
else
{
  $sql = "SELECT * FROM Users ORDER BY UserID DESC";
  $return = $conn->query($sql);
  $rUsers = "";

  while($i = $return->fetch_assoc())
  {
      $uID = $i['UserID'];
      $uName = $i['Name'];
      $uDesc = $i['About'];
      $uPFP = $i['ProfilePic'];
      $rUsers .=  "<a href='User.php?u=$uID'><div class='creatorLinkBox'><div class='clpfp' style='background-image: url($uPFP);'></div><div><p>$uName</p><h4>$uDesc</h4></div></div></a>";
  }
}

$searchBar = "";

if(isset($_GET['search']))
{
  $searchBar = mysqli_real_escape_string($conn,$_GET['search']);
}

  //fat noises

  $stylegtfpb = "";

  if(isset($_GET['follows'])){
    $stylegtfpb = "<style>.gtfpb{display:none;}</style>";
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
    <?php echo($btns); echo($subStyle); echo $StyleShow; echo $stylegtfpb;?>
    <div class = "maindis">
        <form action="" method="get" class="dcform"><p id="dcP">Discover</p><input type="submit" name="follows" value="Go to follow page" class="gtfpb"></form>
        <form action="" class="search aroundbox" id="smallaround" method="get">
            <p>Search</p>
            <input type="text" name="search" value="<?php echo $searchBar;?>">
            <button type="submit">Search</button>
        </form>
        <section class="discoverPage">
            <section class="allvids">
                <section class="videoGrid aroundbox">
                    <?php echo $allVids;?>
                </section>
            </section>
            <section class="allchannels">
                <section class="cHolder">
                     <?php echo $rUsers;?>
                </section>
            </section>
        </section>
    </div>
</body>
</html>
