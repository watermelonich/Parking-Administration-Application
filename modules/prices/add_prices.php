<?php

require_once '../../config/config.php';

require_once '../../config/function.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_price']))
{
	$data = array();

	if(empty($_POST['category_id']))
	{
		$errors[] = 'Please Select Category';
	}
	else
	{
		$data[] = $_POST['category_id'];
	}

	if(empty($_POST['duration_id']))
	{
		$errors[] = 'Please Select Duration';
	}
	else
	{
		$data[] = $_POST['duration_id'];
	}

  	if (empty($_POST['price_value']) && $_POST['price_value'] > 0) 
  	{
	    $errors[] = 'Enter Price Data';
  	}
  	else
  	{
  		$data[] = trim($_POST['price_value']);
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$check_query = "
  		SELECT price_id FROM parking_price 
  		WHERE category_id = '".$data[0]."' AND duration_id = '".$data[1]."'
  		";

  		$statement = $connect->prepare($check_query);

  		$statement->execute();

  		if($statement->rowCount() > 0)
  		{
  			$errors[] = 'Price Data for Selected Category and Duration is already exists in database';
  		}
  		else
  		{
	  		$query = "
	  		INSERT INTO parking_price (category_id, duration_id, price_value, price_created_on) 
	  		VALUES (?, ?, ?, ?)";

	  		$statement = $connect->prepare($query);

	  		$statement->execute($data);

	  		if($statement->rowCount() > 0)
	  		{
		  		$_SESSION['success'] = 'New Price Data Added';

		  		header('location:prices.php?msg=success');
		  		exit();
		  	}
		}
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Parking Prices</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="prices.php">Parking Prices Management</a></li>
        <li class="breadcrumb-item active">Add Parking Prices</li>
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
				<h5 class="card-title">Add Parking Prices</h5>
			</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
				    	<label class="form-label">Category</label>
				    	<select name="category_id" class="form-control">
				    		<option value="">Select Category</option>
				    		<?php echo category_list($connect); ?>
				    	</select>
				  	</div>
				  	<div class="mb-3">
				    	<label class="form-label">Time Duration</label>
				    	<select name="duration_id" class="form-control">
				    		<option value="">Select Time Duration</option>
				    		<?php echo duration_list($connect); ?>
				    	</select>
				  	</div>
				  	<label class="form-label">Parking Prices</label>
				  	<div class="input-group mb-3">
				  		<span class="input-group-text"><?php echo CURRENCY ?></span>
				    	<input type="number" class="form-control" name="price_value">
				  	</div>
				  	<button type="submit" name="add_price" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>