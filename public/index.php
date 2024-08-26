<?php

// Check if the system setup is complete

include '../config/config.php';
include '../config/function.php';

if (isset($_SESSION['user_id'])) 
{
    header('Location: dashboard.php');
    exit();
}

try{
    $query = "SELECT parking_id FROM parking_setting";

    $statement = $connect->prepare($query);

    if($statement->execute())
    {
        $page = 'login';

        if($statement->rowCount() == 0)
        {
            $page = 'register';
        }
    }
    else
    {
        echo 'I am here';
    }
}
catch (PDOException $e)
{
    $sql = file_get_contents('database.sql');
    $connect->exec($sql);
    header('location:index.php');
}

$errors = array();

if(isset($_POST["btn_register"]))
{
    $data = array();

    if(empty($_POST['parking_name']))
    {
        $errors[] = 'Please Enter your Parking Name';
    }
    else
    {
        $data['parking_name'] = trim($_POST['parking_name']);
    }

    if(empty($_POST['parking_contact_person']))
    {
        $errors[] = 'Please Enter Your Name';
    }
    else
    {
        $data['parking_contact_person'] = trim($_POST['parking_contact_person']);
    }

    if(empty($_POST['parking_contact_number']))
    {
        $errors[] = 'Please Enter Your Contact Number';
    }
    else
    {
        $data['parking_contact_number'] = trim($_POST['parking_contact_number']);
    }

    if(empty($_POST['parking_admin_login']))
    {
        $errors[] = 'Please Enter your Email Address';
    }
    else
    {
        if (!filter_var($_POST['parking_admin_login'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'Please Enter Valid Email Address';
        }
        else
        {
            $data['parking_admin_login'] = trim($_POST['parking_admin_login']);
        }
    }

    if(empty($_POST['parking_admin_password']))
    {
        $errors[] = 'Please Enter your Password Detail';
    }
    else
    {
        $data['parking_admin_password'] = trim($_POST['parking_admin_password']);
    }

    if(count($errors) == 0)
    {
        $setting_data = array(
            ':parking_name'             =>  $data['parking_name'],
            ':parking_contact_person'   =>  $data['parking_contact_person'],
            ':parking_contact_number'   =>  $data['parking_contact_number'],
            ':parking_created_on'       =>  time()
        );

        $query = "
        INSERT INTO parking_setting 
        (parking_name, parking_contact_person, parking_contact_number, parking_created_on) 
        VALUES (:parking_name, :parking_contact_person, :parking_contact_number, :parking_created_on)
        ";

        $statement = $connect->prepare($query);

        $statement->execute($setting_data);

        $user_data = array(
            ':user_email_address'       =>  $data['parking_admin_login'],
            ':user_password'            =>  $data['parking_admin_password'],
            ':user_type'                =>  'Master',
            'user_created_on'           =>  time()
        );

        $query = "
        INSERT INTO parking_user 
        (user_email_address, user_password, user_type, user_created_on) 
        VALUES (:user_email_address, :user_password, :user_type, :user_created_on)
        ";

        $statement = $connect->prepare($query);

        $statement->execute($user_data);

        $_SESSION['success'] = 'Registration Completed, Now you can login using ' . $_POST['parking_admin_login'] . '';

        header('location:index.php?msg=register');
    }
}

if(isset($_POST['btn_login']))
{
    $data = array();

    if(empty($_POST['email']))
    {
        $errors[] = 'Please Enter your Email Address';
    }
    else
    {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'Please Enter Valid Email Address';
        }
        else
        {
            $data['email'] = trim($_POST['email']);
        }
    }

    if(empty($_POST['password']))
    {
        $errors[] = 'Please Enter your Password Detail';
    }
    else
    {
        $data['password'] = trim($_POST['password']);
    }

    if(count($errors) == 0)
    {
        $check_data[] = $data['email'];
        
        $query = "
        SELECT * FROM parking_user 
        WHERE user_email_address = ?
        ";

        $statement = $connect->prepare($query);

        $statement->execute($check_data);

        $user_data = $statement->fetch();

        if($user_data)
        {
            if($user_data['user_password'] == $data['password'])
            {
                $query = "
                SELECT * FROM parking_setting 
                LIMIT 1
                ";

                $statement = $connect->prepare($query);

                $statement->execute();

                $setting_data = $statement->fetch();

                if(!empty($setting_data['parking_timezone']))
                {
                    $_SESSION['timezone'] = $setting_data['parking_timezone'];
                }

                if(!empty($setting_data['parking_currency']))
                {
                    $_SESSION['currency'] = Get_currency_symbol($setting_data['parking_currency']);
                }

                if(!empty($setting_data['parking_datetime_format']))
                {
                    $_SESSION['date_time_format'] = $setting_data['parking_datetime_format'];
                }

                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['user_type'] = $user_data['user_type'];
                header('location:dashboard.php');
            }
            else
            {
                $errors[] = 'Incorrect Password';
            }
        }
        else
        {
            $errors[] = 'Incorrect Email Address';
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Parking Management System in PHP</title>
        <!-- Load Bootstrap 5 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="mt-5">
                <h1 class="text-center">Parking Management System</h1>
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4 mt-5">
                        <?php
                            
                        if(count($errors) > 0)
                        {
                            echo '<div class="alert alert-danger"><ul class="list-unstyled">';
                            foreach ($errors as $error) 
                            {
                                echo '<li>' . $error . '</li>';
                            }

                            echo '</ul></div>';
                        }

                        

                        if($page == 'login')
                        {
                            if(isset($_SESSION['success']))
                            {
                                echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

                                unset($_SESSION['success']);
                            }
                        ?>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text-center">Login</h3>
                            </div>
                            <div class="card-body">                            
                                <!-- Login form -->
                                <form id="login-form" method="post">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <div class="invalid-feedback">Please enter a password.</div>
                                    </div>
                                    <div align="center">
                                        <button type="submit" name="btn_login" class="btn btn-primary">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br />
                        <h4>Sample Login</h4>
                        <p><b>eMail -</b> johnsmith@gmail.com<br /><b>Password - </b>password</p>
                        <?php
                        }
                        else
                        {
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text-center">Register</h3>
                            </div>
                            <div class="card-body"> 
                                <form method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Parking Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="parking_name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="parking_contact_person">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="parking_contact_number">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="parking_admin_login">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="parking_admin_password">
                                    </div>
                                    <div align="center">
                                        <button type="submit" name="btn_register" class="btn btn-primary">Register</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
    </body>
</html>