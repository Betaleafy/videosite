<?php include "controlHeader.php";?>
<?php
    $videoListings = "";
    $userListings = "";
    $searchTerm = "";
    $showMore = "load more";
    $showAmount = array(12,5);

    if(isset($_POST['loadMore'])){
        if($_POST['mode'] == "videos"){
            $showAmount[0] = $_POST['amountOfContent'];
            $showAmount[0] += 12;
        }
        else if($_POST['mode'] == "users"){
            $showAmount[1] = $_POST['amountOfContent'];
            $showAmount[1] += 5;
        }
    }

    if(isset($_GET['search'])){
        $showMore = "";
        $search = $_GET['search'];

        $searchTerm = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');

        $value = "%$search%";
        $ret = SendSQL("SELECT * FROM Videos WHERE Title LIKE ? AND Visible = 1", array(&$value), "s", $conn);

        while($i = $ret->fetch_assoc()){
            $Title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $Thumbnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
            $VideoID = htmlspecialchars($i['VideoID'], ENT_QUOTES, 'UTF-8');
            $UserID = $i['UserID'];

            $ret = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$UserID), "s", $conn);

            $info = $ret2->fetch_assoc();
            $Username = htmlspecialchars($info['Name'], ENT_QUOTES, 'UTF-8');

            $videoListings .= "<a href='watch.php?v=$VideoID'><section><div class='pimage' style='background-image: url($Thumbnail);'></div><p class='lineclamp' title='$Title'>$Title</p><h5>$Username</h5></section></a>";
        }

        $ret = SendSQL("SELECT * FROM Users WHERE Name LIKE ?", array(&$value), "s", $conn);

        while($i = $ret->fetch_assoc()){
            $pfp = htmlspecialchars($i['ProfilePic'], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($i['Name'], ENT_QUOTES, 'UTF-8');
            $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
            $userListings .= "<a href='User.php?u=$userID'><div class='pimage shadow' style='background-image: url($pfp);'></div><p>$name</p></a>";
        }
    }
    else
    {
        $ret = SendSQL("SELECT * FROM Videos WHERE Visible = 1 ORDER BY Date DESC LIMIT ?", array(&$showAmount[0]), "i", $conn);

        while($i = $ret->fetch_assoc()){
            $Title = htmlspecialchars($i['Title'], ENT_QUOTES, 'UTF-8');
            $Thumbnail = htmlspecialchars($i['ThumbnailURL'], ENT_QUOTES, 'UTF-8');
            $VideoID = htmlspecialchars($i['VideoID'], ENT_QUOTES, 'UTF-8');
            $UserID = $i['UserID'];

            $ret2 = SendSQL("SELECT * FROM Users WHERE UserID = ?", array(&$UserID), "s", $conn);
            $info = $ret2->fetch_assoc();
            $Username = htmlspecialchars($info['Name'], ENT_QUOTES, 'UTF-8');
            
            $videoListings .= "<a href='watch.php?v=$VideoID'><section><div class='pimage' style='background-image: url($Thumbnail);'></div><p class='lineclamp' title='$Title'>$Title</p><h5>$Username</h5></section></a>";
        }

        $ret = SendSQL("SELECT * FROM Users ORDER BY JoinDate DESC LIMIT ?", array(&$showAmount[1]), "i", $conn);

        while($i = $ret->fetch_assoc()){
            $pfp = htmlspecialchars($i['ProfilePic'], ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($i['Name'], ENT_QUOTES, 'UTF-8');
            $userID = htmlspecialchars($i['UserID'], ENT_QUOTES, 'UTF-8');
            $userListings .= "<a href='User.php?u=$userID'><div class='pimage shadow' style='background-image: url($pfp);'></div><p>$name</p></a>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxzil - Discover</title>
    <link rel="stylesheet" href="Redesign.css">
    <script src="https://kit.fontawesome.com/f3ebd80316.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include "SiteHeader.php";?>
    <section class="searchbar">
        <div>
            <i class="fas fa-search"></i>
        </div>
        <div>
            <form action="">
            <input type="text" name='search' placeholder="Search for anything, both videos and users, with whatever search term you like..." value="<?php echo $searchTerm;?>">
            </form>
        </div>
    </section>
    <section class="discover">
        <section class="videos">
            <h2>Discover</h2>
            <section class="vidgrid">
                <?php echo $videoListings;?>
            </section>
            <form action="" method='post'>
                <input type="hidden" name='amountOfContent' value="<?php echo $showAmount[0];?>">
                <input type="hidden" name='mode' value='videos'>
                <input type="submit" name='loadMore' value='<?php echo $showMore;?>' class='noskin palink'>
            </form>
        </section>
        <section class="users">
            <h2>Users</h2>
            <?php echo $userListings;?>
            <form action="" method='post'>
                <input type="hidden" name='amountOfContent' value="<?php echo $showAmount[1];?>">
                <input type="hidden" name='mode' value='users'>
                <input type="submit" name='loadMore' value='<?php echo $showMore;?>' class='noskin palink'>
            </form>
        </section>
    </section>
    <?php include "footer.html";?>
</body>
</html>