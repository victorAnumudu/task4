
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
        if(isset($_POST['post'])) {
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
                    $sql = "INSERT INTO product (user_id, product_name, product_price) ";
                    $sql .= "VALUES (?, ?, ?)";
                    $stmt = $connection->prepare($sql);
                    $stmt->bind_param("iss", $id, $product, $amount);
                    $stmt->execute();
                    $stmt->close();
                    header("location: index.php");
                }
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
            <form action="post.php" method="post">
                <h3>Post Product form</h3>
                <?php 
                    if($missing || $error) {
                        echo "<span class='error'>Attend to the issues below!</span>";
                    }
                ?>
               
                <div class="input">
                    <label for="product">Product: 
                        <?php 
                            if(in_array("product", $missing)) {
                                echo "<span class='error'>Enter product</span>";
                            } 
                        ?>
                    </label>
                    <textarea name="product" id="product"><?php 
                            if($missing || $error) {
                                echo htmlentities($product);
                            }
                        ?></textarea>
                    
                </div>

                <div class="input">
                    <label for="Amount">Amount: 
                        <?php 
                            if(in_array("amount", $missing)) {
                                echo "<span class='error'>Enter Amount</span>";
                            }
                        ?>
                    </label>
                    <input type="text" name="amount" placeholder="enter amount" id="amount"
                        <?php 
                            if($missing || $error) {
                                echo "value=".htmlentities($amount)."";
                            }
                        ?>
                    >
                </div>
               
                <div class="submit">
                    <input type="submit" name="post" value="post">
                </div>
                <p>click here <a href="index.php">Dashboard</a></p>
            </form>
        </div>
    </main>

    <?php require "footer.php" ?>

</body>
</html>