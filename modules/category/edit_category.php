<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_category']))
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

  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$data[] = $_POST['category_id'];

  		$query = "
  		UPDATE parking_category 
  		SET category_name = ?, category_updated_on = ? 
  		WHERE category_id = ?
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		$_SESSION['success'] = 'Category Data has been Changed';

  		header('location:category.php');
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the vehical category's details
  	$statement = $connect->prepare("SELECT * FROM parking_category WHERE category_id = ?");

  	$statement->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$category = $statement->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Vehicle Category</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="category.php">Category Management</a></li>
        <li class="breadcrumb-item active">Edit Vehicle Category</li>
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
				<h5 class="card-title">Edit Vehicle Category</h5>
			</div>
			<div class="card-body">
				<form method="POST">
				  	<div class="mb-3">
				    	<label class="form-label">Vehicle Category Name</label>
				    	<input type="text" class="form-control" name="category_name" value="<?php echo $category['category_name']; ?>">
				  	</div>
				  	<input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>" />
				  	<button type="submit" name="edit_category" class="btn btn-primary">Edit Category</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>