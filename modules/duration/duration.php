<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
	$statement = $connect->prepare("DELETE FROM parking_duration WHERE duration_id = ?");
  	
  	$statement->execute([$_GET['id']]);
  	
  	$_SESSION['success'] = 'Duration Data has been removed';
  	
  	header('location:duration.php?msg=success');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Parking Duration Management</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Parking Duration Management</li>
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
					<h5 class="card-title">Parking Duration Management</h5>
				</div>
				<div class="col col-6">
					<div class="float-end"><a href="add_duration.php" class="btn btn-success btn-sm">Add</a></div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-bordered" id="duration-table">
					<thead>
						<tr>
							<td>ID</td>
							<th>Duration</th>
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
    $('#duration-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
        	url: 'action.php',
        	method:"POST",
        	data: {action : 'fetch_duration'}
        },
        "columns": [
            { "data": "duration_id" },
            { "data": "duration_value" },
            { "data" : "duration_created_on"},
            { "data": "duration_updated_on"},
            {
        		"data": null,
        		"render": function(data, type, row) {
          			return '<a href="edit_duration.php?id='+row.duration_id+'" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'+row.duration_id+'">Delete</button>';
        		}
        	}
        ]
    });

    $(document).on('click', '.delete_btn', function(){
    	if(confirm("Are you sure you want to remove this Duration data?"))
    	{
    		window.location.href = 'duration.php?action=delete&id=' + $(this).data('id') + '';
    	}
    });
});

</script>

parking_price

price_id
category_id
duration_id
price_value
created_on
updated_on