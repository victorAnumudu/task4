
<?php 
    session_start();
    if(!isset($_SESSION['username'])){
        header("location: login.php");
    } else {
        $username = $_SESSION['username'];
        $id = $_SESSION['id'];
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];

        $product_id = $_GET['product_id'];

        $error = [];
        $missing = [];

        require "db_connection.php";
        //if connection to database failed
        if($connection->connect_error) {
            die("Connection Error ".$conn->connect_error);
            header("location: ../index.php?error=servererror");
            exit();
        }

        if(isset($_POST['update'])) {
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
                $sql = "UPDATE product SET product_name=?, product_price=? ";
                $sql .= "WHERE product_id=? AND user_id=? LIMIT 1";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ssii", $product, $amount, $product_id, $id);
                $stmt->execute();
                if($stmt->affected_rows > 0){
                    header("location: message.php?message=success");
                } else {
                    header("location: message.php?message=failed");
                }
                $stmt->close();         
            }
        }
        else {
            $sql = "SELECT * FROM product ";
            $sql .= "WHERE product_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            } else {
                $products = [];
            }
        } 
        
        $connection->close(); // end of db connection
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
            <form action="update.php?product_id=<?php echo $product_id ?>" method="post">
                <h3>Update Product form</h3>
                <?php 
                    if($missing || $error) {
                        echo "<span class='error'>Attend to the issues below!</span>";
                    }
                ?>
               
                <div class="input">
                    <label for="product">Update Product: 
                        <?php 
                            if(in_array("product", $missing)) {
                                echo "<span class='error'>Update product name</span>";
                            } 
                        ?>
                    </label>
                    <textarea name="product" id="product"><?php 
                        if($missing || $error || isset($_POST["update"])) {
                            echo htmlentities($product);
                        } else {
                            echo htmlentities($products[0]['product_name']);
                        }
                        ?></textarea>
                    
                </div>

                <div class="input">
                    <label for="Amount">Update Amount: 
                        <?php 
                            if(in_array("amount", $missing)) {
                                echo "<span class='error'>Update product Amount</span>";
                            }
                        ?>
                    </label>
                    <input type="text" name="amount" placeholder="enter amount" id="amount"
                        <?php 
                            if($missing || $error || isset($_POST["update"])) {
                                echo "value=".$amount."";
                            } else {
                                echo "value=".$products[0]['product_price']."";
                            }
                        ?>
                    >
                </div>
               
                <div class="submit">
                    <input type="submit" name="update" value="update">
                </div>
                <p>click here <a href="index.php">Dashboard</a></p>
            </form>
        </div>
    </main>

    <?php require "footer.php" ?>

</body>
</html>