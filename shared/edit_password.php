
<?php 
    session_start();
    if(!isset($_SESSION['username'])){
        header("location: login.php");
    } else {
        $username = $_SESSION['username'];
        $id = $_SESSION['id'];
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];

        $error = [];
        $missing = [];
        if(isset($_POST['update_password'])) {
            foreach($_POST as $field => $value) {
                $value = is_array($field)? $field : trim($value);
                if (empty($value)) {
                    $missing[] = $field;
                    $$field = "";
                } else {
                    $$field = $value;
                }
            }

            // proceed execution if no missing field is found
            if(!$missing) {
                if($new_password != $c_new_password) {
                    $error[] = "passwordmismatched";
                } 
                else if (strlen($new_password) <= 7) {
                    $error[] = "passwordtoosmall";
                }
                else {
                    //connect to database
                    $updated_password = password_hash($new_password, PASSWORD_DEFAULT);

                    require "db_connection.php";
                    //if connection to database failed
                    if($connection->connect_error) {
                        die("Connection Error ".$conn->connect_error);
                        header("location: ../index.php?error=servererror");
                        exit();
                    }

                    $sql = "UPDATE users SET user_password=? ";
                    $sql .= "WHERE user_id=? LIMIT 1";
                    $stmt = $connection->prepare($sql);
                    $stmt->bind_param("si", $updated_password, $id);
                    $stmt->execute();
                    if($stmt->affected_rows > 0){
                        header("location: message.php?message=success");
                    } else {
                        header("location: message.php?message=failed");
                    }
                    $stmt->close();
                    $connection->close();
                }// close database
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
    <link rel="stylesheet" href="index.css">
    <!-- <link rel="stylesheet" href="../index.css"> -->
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
</style>
<body>
    
    <?php require "header.php" ?>

    <main>
        <div class="form_wrapper">
            <form action="edit_password.php" method="post">
                <h3>Edit password</h3>
                <?php 
                    if($missing || $error) {
                        echo "<span class='error'>Attend to the issues below!</span>";
                    }
                ?>

                <div class="input">
                    <label for="new password">Enter new password: 
                        <?php 
                            if(in_array("new_password", $missing)) {
                                echo "<span class='error'>Enter new password</span>";
                            } else if($error && in_array("passwordmismatched", $error)) {
                                echo "<span class='error'>password mismatched</span>";
                            } else if($error && in_array("passwordtoosmall", $error)) {
                                echo "<span class='error'>password must be up to 8 characters</span>";
                            }
                        ?>
                    </label>
                    <input type="password" name="new_password" placeholder="enter new password" id="new_password">
                </div>

                <div class="input">
                    <label for="c_new_password">Confirm new password: 
                        <?php 
                            if(in_array("c_new_password", $missing)) {
                                echo "<span class='error'>Enter confirm new password</span>";
                            } else if($error && in_array("passwordmismatched", $error)) {
                                echo "<span class='error'>password mismatched</span>";
                            } else if($error && in_array("passwordtoosmall", $error)) {
                                echo "<span class='error'>password must be up to 8 characters</span>";
                            }
                        ?>
                    </label>
                    <input type="password" name="c_new_password" placeholder="enter confirm new password" id="c_new_password">
                </div>
               
                <div class="submit">
                    <input type="submit" name="update_password" value="Update Password">
                </div>
                <p>click here <a href="index.php">Dashboard</a></p>
            </form>
        </div>
    </main>

    <?php require "footer.php" ?>

</body>
</html>