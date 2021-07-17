<?php 
    $error = [];
    $missing = [];
    if(isset($_POST['register'])) {
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
            if($password != $c_password) {
                $error[] = "passwordmismatched";
            } 
            else if (strlen($password) <= 7) {
                $error[] = "passwordtoosmall";
            }
            else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $error[] = "invalid email";
            }
            else{
                require "db_connection.php";
                //if connection to database failed
                if($connection->connect_error) {
                    die("Connection Error ".$conn->connect_error);
                    header("location: ../index.php?error=servererror");
                    exit();
                }
                else {
                    $stmt = $connection->prepare("SELECT user_email FROM users WHERE user_email=?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                
                    if($result->num_rows > 0) {
                        $error[] = "email taken";
                    } 

                    else {
                        $stmt = $connection->prepare("SELECT user_name FROM users WHERE user_name=?");
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->close();

                        if($result->num_rows > 0) {
                            $error[] = "username taken";
                        } 
                        else{
                            $new_password = password_hash($password, PASSWORD_DEFAULT);
                            $sql = "INSERT INTO users ";
                            $sql .= "(first_name, last_name, user_email, user_name, user_password) ";
                            $sql .= "VALUES (?, ?, ?, ?, ?)";
                            $stmt = $connection->prepare($sql);
                            $stmt->bind_param("sssss", $f_name, $l_name, $email, $username, $new_password);
                            $stmt->execute();
                            $stmt->close();
                            header("location: login.php");
                        }
                        
                    }
                }
                // $stmt->close();
                $connection->close();
            }// end of db connection
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            <form action="register.php" method="post">
                <h3>Register form</h3>
                <?php 
                    if($missing || $error) {
                        echo "<span class='error'>Attend to the issues below!</span>";
                    }
                ?>
                <div class="input">
                    <label for="f_name">Firstname: 
                    <?php 
                        if(in_array("f_name", $missing)) {
                            echo "<span class='error'>Enter first name</span>";
                        }
                    ?>
                    <input type="text" name="f_name" id="f_name" 
                        <?php 
                            if($missing || $error) {
                                echo "value=".htmlentities($f_name)."";
                            }
                        ?>                    
                    >
                </div>
                <div class="input">
                    <label for="l_name">Lastname: 
                    <?php 
                        if(in_array("l_name", $missing)) {
                            echo "<span class='error'>Enter last name</span>";
                        }
                    ?>
                    <input type="text" name="l_name" id="l_name"
                        <?php 
                            if($missing || $error) {
                                echo "value=".htmlentities($l_name)."";
                            }
                        ?>
                    >
                </div>
                <div class="input">
                    <label for="email">Email: 
                    <?php 
                        if(in_array("email", $missing)) {
                            echo "<span class='error'>Enter email</span>";
                        } else if($error && in_array("invalid email", $error)) {
                            echo "<span class='error'>invalid email</span>";
                        } else if($error && in_array("email taken", $error)) {
                            echo "<span class='error'>user exist</span>";
                        }
                    ?>   
                    <input type="" name="email" id="email"
                        <?php 
                            if($missing || $error) {
                                echo "value=".htmlentities($email)."";
                            }
                        ?>
                    >
                </div>
                <div class="input">
                    <label for="username">Username: 
                    <?php 
                        if(in_array("username", $missing)) {
                            echo "<span class='error'>Enter username</span>";
                        } else if($error && in_array("username taken", $error)) {
                            echo "<span class='error'>username alreay taken</span>";
                        }
                    ?>  
                    <input type="text" name="username" id="username"
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
                        if(in_array("password", $missing) || in_array("c_password", $missing)) {
                            echo "<span class='error'>Enter password</span>";
                        } else if($error && in_array("passwordmismatched", $error)) {
                            echo "<span class='error'>password mismatched</span>";
                        } else if($error && in_array("passwordtoosmall", $error)) {
                            echo "<span class='error'>password must be up to 8 characters</span>";
                        }
                    ?>  
                    <input type="password" name="password" id="password">
                </div>
                <div class="input">
                    <label for="c_password">Confirm password: 
                    <?php 
                        if(in_array("password", $missing) || in_array("c_password", $missing)) {
                            echo "<span class='error'>Enter confirm password</span>";
                        } else if($error && in_array("passwordmismatched", $error)) {
                            echo "<span class='error'>password mismatched</span>";
                        } else if($error && in_array("passwordtoosmall", $error)) {
                            echo "<span class='error'>password must be up to 8 characters</span>";
                        }
                    ?>   
                    <input type="password" name="c_password" id="c_password">
                </div>
                <div class="submit">
                    <input type="submit" name="register" value="Register">
                </div>
                <p>click here <a href="../index.php">home</a> to go back to home or <a href="login.php">login</a> to login</p>
            </form>
        </div>
    </main>
    
    <?php require "footer.php"; ?>

</body>
</html>