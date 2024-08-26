<?php

require_once '../../config/config.php';

require_once '../../config/function.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_price']))
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

  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$data[] = $_POST['price_id'];

  		$query = "
  		UPDATE parking_price 
  		SET category_id = ?, duration_id = ?, price_value = ?, price_updated_on = ?  
  		WHERE price_id = ?
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		$_SESSION['success'] = 'Price Data has been Changed';

  		header('location:prices.php?msg=success');
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the vehical parking_price's details
  	$statement = $connect->prepare("SELECT * FROM parking_price WHERE price_id = ?");

  	$statement->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$price = $statement->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Parking Prices</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="prices.php">Parking Prices Management</a></li>
        <li class="breadcrumb-item active">Edit Parking Prices</li>
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
				<h5 class="card-title">Edit Parking Prices</h5>
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
				    	<input type="number" class="form-control" name="price_value" value="<?php echo $price['price_value']; ?>">
				  	</div>
				  	<input type="hidden" name="price_id" value="<?php echo $price['price_id']; ?>" />
				  	<button type="submit" name="edit_price" class="btn btn-primary">Edit Price</button>
				</form>
				<script>

					$("select[name='category_id']").val("<?php echo $price['category_id']; ?>");

					$("select[name='duration_id']").val("<?php echo $price['duration_id']; ?>");

				</script>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>