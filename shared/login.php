
<?php 
    $error = [];
    $missing = [];
    if(isset($_POST['login'])) {
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
            require "db_connection.php";
            //if connection to database failed
            if($connection->connect_error) {
                die("Connection Error ".$conn->connect_error);
                header("location: ../index.php?error=servererror");
                exit();
            }
            else {
                $sql = "SELECT user_id, first_name, last_name, user_email, user_password FROM users WHERE user_email=? OR user_name=?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ss", $username, $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
            
                if($result->num_rows <= 0) {
                    $error[] = "invalid email or username";
                } 
                else {
                    $record = $result->fetch_assoc();
                    $new_password = password_verify($password, $record['user_password']);
                    
                    if(!$new_password) { // password is wrong
                        $error[] = "password incorrect";
                    } else {
                        // password is correct
                        session_start();
                        $_SESSION['username'] = $username;
                        $_SESSION['id'] = $record['user_id'];
                        $_SESSION['firstname'] = $record['first_name'];
                        $_SESSION['lastname'] = $record['last_name'];
                        header("location: index.php");
                    }
                }
            }
            $connection->close();
        }// end of db connection
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="index.css">
</head>
<style>
    body {
        background-image: url("images/background.jpg");
        background-size: 100% 100%;
        background-repeat: no-repeat;
        width: 100%;
        height: 100%;
    }
    .cover_body {
        position: absolute;
        height: inherit;
        width: inherit;
        background-color: rgba(255, 255, 255, 0.5);
        z-index: -1;
    }
</style>
<body>
    <div class="cover_body"></div>
    <nav>
        <p>market vendor</p>
        <ul class="nav_bar">
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Signup</a></li>
        </ul>
    </nav>
    <main>
        <div class="form_wrapper">
            <form action="login.php" method="post">
                <h3>Login form</h3>
                <?php 
                    if($missing || $error) {
                        echo "<span class='error'>Attend to the issues below!</span>";
                    }
                ?>
               
                <div class="input">
                    <label for="username">Username: 
                        <?php 
                            if(in_array("username", $missing)) {
                                echo "<span class='error'>Enter username or Email</span>";
                            } else if($error && in_array("invalid email or username", $error)) {
                                echo "<span class='error'>invalid username or email</span>";
                            }
                        ?>
                    </label>
                    <input type="text" name="username" placeholder="enter email or username" id="username"
                        <?php 
                            if($missing || $error) {
                                echo "value=".htmlentities($username)."";
                            }
                        ?>
                    >
                </div>

                <div class="input">
                    <label for="password">Password: 
                        <?php 
                            if(in_array("password", $missing)) {
                                echo "<span class='error'>Enter password</span>";
                            } else if($error && in_array("password incorrect", $error)) {
                                echo "<span class='error'>password incorrect</span>";
                            }
                        ?> 
                    </label>
                    <input type="password" name="password" id="password">
                </div>
               
                <div class="submit">
                    <input type="submit" name="login" value="login">
                </div>
                <p>click here <a href="../index.php">home</a> to go back to home or <a href="register.php">register</a> to register</p>
            </form>
        </div>
    </main>
    
    <?php require "footer.php"; ?>

</body>
</html>