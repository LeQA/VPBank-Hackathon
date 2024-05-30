<?php
session_start();
include("libs/db.php");

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "login":
            try {
                $database = create_database_connection();
                $username = isset($_POST["username"]) ? $_POST["username"] : ''; 
                $password = isset($_POST["password"]) ? $_POST["password"] : ''; 
                // mysqli_real_escape_string($database, $_POST["username"]) : ''; 
                // $password = isset($_POST["password"]) ? mysqli_real_escape_string($database, $_POST["password"]) : '';
                $sql = "SELECT user_id, username, password FROM users WHERE username='$username' AND password='$password' ORDER BY user_id DESC LIMIT 1";
                $db_result = $database->query($sql);
                if ($db_result && $db_result->num_rows > 0) {
                    $row = $db_result->fetch_assoc();
                    $_SESSION['user_id'] = $row['user_id'];
                    header("Location: /wall.php");
                    exit;
                } else {
                    echo "Incorrect username or password";
                }
            } catch (mysqli_sql_exception $e) {
                echo "An error occurred: " . $e->getMessage();
            }
            die();
            // ' OR '1'='1
        case "register":
            $res = select_one(
                "SELECT username FROM users WHERE username = ?",
                $_POST['username']
            );
            if ($res) {
                header('Refresh:2; url=index.php'); 
                echo "Sorry this username already registered";
            } else {
                // $password = md5($_POST['password']);
                exec_query(
                    "INSERT INTO users (user_id, username, password) VALUES (?, ?, ?)",
                    md5($_POST['username']),
                    $_POST['username'],
                    // $password
                    $_POST['password']
                );
                header('Refresh:2; url=index.php');
                echo "Registered successfully";
            }
            die();
        case "logout":
            unset($_SESSION["user_id"]);
            header("Location: /index.php");
            die();
    }
}

include("static/html/header.html");
?>


<div class="container mt-5">
    <div class="row justify-content-md-center">
        <div class="col-md-5 mt-5 my-auto">
            <h1 class="text-primary font-weight-bolder">Message Board</h1>
        </div>
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <!-- login form -->
                    <h4>Login</h4>
                    <form action="/index.php?action=login" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-primary">Sign in</button>
                    </form>
                    <hr>
                    <!-- register form -->
                    <h4>Register</h4>
                    <form action="/index.php?action=register" method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-success">Sign up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>