<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
	$statement = $connect->prepare("DELETE FROM parking_price WHERE price_id = ?");
  	
  	$statement->execute([$_GET['id']]);
  	
  	$_SESSION['success'] = 'Prices Data has been removed';
  	
  	header('location:prices.php?msg=success');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Parking Prices Management</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Parking Prices Management</li>
    </ol>
    <?php
    if(isset($_GET['msg']))
    {
	    if(isset($_SESSION['success']))
		{
			echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

			unset($_SESSION['success']);
		}
	}

    ?>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-6">
					<h5 class="card-title">Parking Prices Management</h5>
				</div>
				<div class="col col-6">
					<div class="float-end"><a href="add_prices.php" class="btn btn-success btn-sm">Add</a></div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-bordered" id="prices-table">
					<thead>
						<tr>
							<td>ID</td>
							<th>Category</th>
							<th>Duration</th>
							<th>Price</th>
							<th>Created At</th>
							<th>Updated At</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">


<?php

include('footer.php');

?>

<script>

$(document).ready(function() {
    $('#prices-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
        	url: 'action.php',
        	method:"POST",
        	data: {action : 'fetch_price'}
        },
        "columns": [
            { "data": "price_id" },
            { "data": "category_name" },
            { "data" : "duration_value" },
            { "data" : "price_value" },
            { "data" : "price_created_on"},
            { "data": "price_updated_on"},
            {
        		"data": null,
        		"render": function(data, type, row) {
          			return '<a href="edit_price.php?id='+row.price_id+'" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'+row.price_id+'">Delete</button>';
        		}
        	}
        ]
    });

    $(document).on('click', '.delete_btn', function(){
    	if(confirm("Are you sure you want to remove this Price data?"))
    	{
    		window.location.href = 'prices.php?action=delete&id=' + $(this).data('id') + '';
    	}
    });
});

</script>