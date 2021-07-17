
<?php 
    session_start();
    if(!isset($_SESSION['username'])){
        header("location: login.php");
    } else {
        $username = $_SESSION['username'];
        $id = $_SESSION['id'];
        $firstname = $_SESSION['firstname'];
        $lastname = $_SESSION['lastname'];

        require_once("db_connection.php");
        //if connection to database failed
        if($connection->connect_error) {
            die("Connection Error ".$conn->connect_error);
            header("location: ../index.php?error=servererror");
            exit();
        }
        if(isset($_GET['id'])){
            $sql = "SELECT product.product_id, product.user_id, product.product_name, ";
            $sql .= "product.product_price, users.first_name, users.last_name FROM product ";
            $sql .= "JOIN users ON users.user_id = product.user_id WHERE product.user_id =?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            } else {
                $products = [];
            }
        } 
        else {
            $sql = "SELECT product.product_id, product.user_id, product.product_name, ";
            $sql .= "product.product_price, users.first_name, users.last_name FROM product ";
            $sql .= "JOIN users ON users.user_id = product.user_id";
            $stmt = $connection->prepare($sql);
            // $stmt->bind_param("s", $firstName);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            } else {
                $products = [];
            }
        }

        $stmt->close();
        $connection->close();
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
        <?php for($i = 0; $i < count($products); $i++) {?>
            <div class="post">
                <p>Posted by: <?php echo "{$products[$i]['first_name']} {$products[$i]['last_name']}" ?></p>
                <p>Product: <?php echo $products[$i]['product_name'] ?></p>
                <p>Price: $<?php echo $products[$i]['product_price'] ?></p>
                <?php 
                    if(isset($_GET['id'])){
                        $product_id = $products[$i]['product_id'];
                        echo "<a href='update.php?product_id=$product_id'>Update Post</a><br>";
                        echo "<a href='delete.php?product_id=$product_id'>Delete Post</a>";
                    }
                ?>
            </div>
        <?php } ?>
        <?php 
            if(isset($_GET['id'])){
                echo "<a href='index.php'>View all Post</a>";
            }
        ?>
    </main>

    <?php require "footer.php" ?>

</body>
</html>