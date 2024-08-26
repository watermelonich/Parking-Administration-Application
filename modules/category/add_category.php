<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_category']))
{
	$data = array();

  	if (empty($_POST['category_name'])) 
  	{
	    $errors[] = 'Enter Category Name';
  	}
  	else
  	{
  		$data[] = trim($_POST['category_name']);
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$query = "

  		INSERT INTO parking_category (category_name, category_added_on)
		SELECT * FROM (SELECT ?, ?) AS tmp
		WHERE NOT EXISTS (
		    SELECT category_name FROM parking_category WHERE category_name = '".$data[0]."'
		) LIMIT 1;

  		";

  		/*$query = "
  		INSERT INTO category (category_name, category_added_on) VALUES (?, ?)";*/

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		if($statement->rowCount() > 0)
  		{
	  		$_SESSION['success'] = 'New Category Data Added';

	  		header('location:category.php');
	  		exit();
	  	}
	  	else
	  	{
	  		$errors[] = 'Category Data already exists in database';
	  	}
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Vehicle Category</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="category.php">Vehicle Category Management</a></li>
        <li class="breadcrumb-item active">Add Vehicle Category</li>
    </ol>
	<div class="col-md-4">
		<?php

		if(isset($errors))
        {
            foreach ($errors as $error) 
            {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }

		?>
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Add Vehicle Category</h5>
			</div>
			<div class="card-body">
				<form method="POST">
				  	<div class="mb-3">
				    	<label class="form-label">Vehicle Category Name</label>
				    	<input type="text" class="form-control" name="category_name">
				  	</div>
				  	<button type="submit" name="add_category" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>