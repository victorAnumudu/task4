<style>
    nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    background-color: rgb(0, 0, 70);
    padding-left: 5px;
}

nav p {
    color: white;
    font-weight: 900;
    font-size: 20px;
    text-transform: uppercase;
    font-family: cursive;
}

.nav_bar {
    list-style-type: none;
    color: #fff;
    display: flex;
    flex-wrap: wrap;
    position: relative;
}

.nav_bar > li{
    /* position: relative; */
    padding: 10px 5px;
}

.nav_bar ul {
    list-style-type: none;
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    background-color: #fff;
    width: 100%;
    padding: 5px;
}

.nav_bar > li:hover ul {
    display: block;
}

.nav_bar ul a{
    display: block;
    width: 100%;
    padding: 5px 0;
    text-decoration: none;
    color: rgb(0, 0, 70);
}
.nav_bar ul a:hover{
    text-decoration: underline;
}
</style>

<nav>
    <p>market vendor</p>
    <ul class="nav_bar">
        <li>welcome <?php echo($lastname." ".$firstname) ?></li>
        <li>menu &DownArrow;
            <ul>
                <li><a href="edit_password.php?id=<?php echo $id?>">Edit password</a></li>
                <li><a href="index.php?id=<?php echo $id?>">View myItems</a></li>
                <li><a href="post.php?id=<?php echo $id?>">post item</a></li>
                <li><a href="logout.php?id=<?php echo $id?>">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>