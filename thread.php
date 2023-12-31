<?php 
    session_start();
    include 'includes/head.php';
?>

<?php
    $query = mysqli_query($conn, "SELECT * FROM `posts` WHERE `id` = '" . $_GET["id"] . "'") or die(mysqli_error());
    if($fetch = mysqli_fetch_array($query)){
        $postTitle = $fetch['title'];
        $postBody = $fetch['body'];
        $poster = $fetch['user'];
        $postID = $fetch['id'];

    }

    else{
        echo "404: This post does not exist.";
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $postTitle ?> </title>
</head>
<body>

<div style="background-color:gray;">
    <h1><?php echo $postTitle ?></h1>
    <p><?php echo $postBody ?></p>
    <p>by <?php echo $poster ?></p>
</div>


<?php  

    $thisPostLikes = 0;

    $query = mysqli_query($conn, "SELECT * FROM `likes` WHERE `postid` =  '$postID'") or die(mysqli_error());
    $liked = false;

    while($fetch = mysqli_fetch_array($query)){
        $thisPostLikes++;
        if($fetch['user'] == $_SESSION['user']){
            $liked = true;
        }
    }
?>

<?php

    if ($liked) { ?>
        <form method="POST">
            <button type="submit" name="unlike-btn">Unlike <?php echo $thisPostLikes ?></button>
        </form>
<?php 
    } 
    else{
?>
        <form method="POST">
            <button type="submit" name="like-btn">Like <?php echo $thisPostLikes ?></button>
        </form>
<?php 
    } 
?>



<?php 
    if (isset($_POST['like-btn'])){
        $user = $_SESSION['user'];
        $insertQuery = mysqli_query($conn, "INSERT INTO `likes` VALUES('', '$user', '$postID', 'post')") or die(mysqli_error());
        header("Location: thread.php?id=" . $postID);
    }

    if (isset($_POST['unlike-btn'])){
        $user = $_SESSION['user'];
        $deleteQuery = mysqli_query($conn, "DELETE FROM `likes` WHERE `user` = '$user' AND `postid` = '$postID'") or die(mysqli_error());
        header("Location: thread.php?id=" . $postID);

    }
  
        
?>

<p>Reply to post:</p>

<form method="POST">
    <textarea name="replyBody" id="replyBody" cols="30" rows="10"></textarea>
    <button type="submit" name="reply-btn">Reply</button>
</form>

<?php 
    if (isset($_POST['reply-btn'])){
        $replyBody = mysqli_real_escape_string($conn, $_POST['replyBody']);
        $user = mysqli_real_escape_string($conn, $_SESSION['user']);
        mysqli_query($conn, "INSERT INTO `replys` VALUES('', '$replyBody', '$user', UTC_TIMESTAMP(), '$postID', '$postID')") or die(mysqli_error());
    }
?>

<p>Comments:</p>

<?php
    $query = mysqli_query($conn, "SELECT * FROM `replys` WHERE `postid` = '" . $_GET["id"] . "'") or die(mysqli_error());
    while($fetch = mysqli_fetch_array($query)){
?>

<?php
                    $postDate = $fetch['date'];
                    $currentTime = time();
                    $postTime = strtotime($postDate);
                    $timeDiff = $currentTime - $postTime;
                    $relativeTime = '';

                    if ($timeDiff < 60) {
                        $relativeTime = $timeDiff . ' seconds ago';
                    } elseif ($timeDiff < 3600) {
                        $relativeTime = floor($timeDiff / 60) . ' minutes ago';
                    } elseif ($timeDiff < 86400) {
                        $relativeTime = floor($timeDiff / 3600) . ' hours ago';
                    } else {
                        $relativeTime = floor($timeDiff / 86400) . ' days ago';
                    }
                ?>

<div style="background-color:gray;">
    <p>by <?php echo $fetch['user'] ?> at <?php echo $relativeTime?></p>
    <p><?php echo $fetch['body'] ?></p>
    
</div>

<?php } ?> 
    
</body>
</html>