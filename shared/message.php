
<?php 
    session_start();
    if(!isset($_SESSION['username'])){
        header("location: login.php");
    } else {
        $username = $_SESSION['username'];
        $id = $_SESSION['id'];
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];

        if(isset($_GET['message'])){
            if($_GET['message'] == "success"){
                $message = "Successful";
            } else {
                $message = "Operation Failed";
            }
        }

    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashborad</title>
</head>
<style>
    *{
        padding: 0;
        margin: 0;
        box-sizing: border-box;
    }
    main{
        min-height: 600px;
    }
    .post{
        background-color: #e3e3e3;
        padding: 5px;
        margin: 10px 0;
    }
</style>
<body>
    
    <?php require "header.php" ?>

    <main>
        <div class="post">
            <p><?php echo $message ?></p>
            <?php echo "<a href='index.php'>goto Dashboard</a>" ?>
        </div>
    </main>

    <?php require "footer.php" ?>

</body>
</html>