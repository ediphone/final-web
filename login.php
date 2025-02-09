<?php 
    session_start();
    require_once('db.php');
    $error = "";
    if(isset($_POST['enter'])){
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        if($username == "admin"){
            $result = login_admin($username, $pwd);
            if($result['code']==0){
                $data = $result['data'];
                $_SESSION['username'] = $data['username'];
                header('Location: admin.php');
                exit();
            }else{
                $error = $result['message'];
            }
        }else{
            $result = login($username, $pwd);
            // print_r($result);
            if($result['code']==0){
                $data = $result['data'];
                $_SESSION['username'] = $data['username'];
                header('Location: index.php');
                exit();
            }else{
                $error = $result['message'];
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
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .container{
            width: 500px;
            box-shadow: 10px 10px 30px rgba(0,0,0,0.2);
        }
        .container h2{
            text-align: center;
            font-style: italic;
            font-weight: bold;
        }
        body{
            background: #ccc;
        }
    </style>
</head>
<body>
    <div class="container p-5 mt-5">
        <h2>Đăng nhập</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" placeholder="Enter username" name="username">
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd">
            </div>
            <div class="form-group">
                    <?php 
                        if(!empty($error)){
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                    ?>
            </div>
            <button type="submit" name="enter" class="btn btn-primary">Submit</button>
        </form>
    </div>
    
</body>
</html>