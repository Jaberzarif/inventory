<?php

//action.php

include('class/db.php');

$object = new db();

if(isset($_POST["action"]))
{
	if($_POST['action'] == 'fetch_user')
	{
		$object->query = "
		SELECT * FROM user_ims 
		WHERE user_type = 'User' AND 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= '(user_email LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR user_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR user_status LIKE "%'.$_POST["search"]["value"].'%") ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY user_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["user_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["user_id"]).'`, `'.$row["user_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["user_id"]).'`, `'.$row["user_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['user_name'];
			$sub_array[] = $row['user_email'];
			$sub_array[] = $row['user_password'];
			$sub_array[] = $row['user_status'];
			$sub_array[] = $status;
			$sub_array[] = '<a href="user.php?action=edit&code='.$object->convert_data($row["user_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_user_all_records($object->connect),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
		echo json_encode($output);

		
	}

	if($_POST['action'] == 'fetch_category')
	{
		$object->query = "
		SELECT * FROM category_ims 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR category_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY category_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["category_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["category_id"]).'`, `'.$row["category_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["category_id"]).'`, `'.$row["category_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['category_name'];			
			$sub_array[] = $status;
			$sub_array[] = $row['category_datetime'];
			$sub_array[] = '<a href="category.php?action=edit&code='.$object->convert_data($row["category_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_category_all_records($object->connect),
			"recordsFiltered" 	=> 	get_total_category_all_records($object->connect),
			"data"    			=> 	$data
		);
		echo json_encode($output);

		
	}

	if($_POST['action'] == 'fetch_location_rack')
	{
		$object->query = "
		SELECT * FROM location_rack_ims 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE location_rack_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR location_rack_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY location_rack_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["location_rack_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["location_rack_id"]).'`, `'.$row["location_rack_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["location_rack_id"]).'`, `'.$row["location_rack_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['location_rack_name'];			
			$sub_array[] = $status;
			$sub_array[] = $row['location_rack_datetime'];
			$sub_array[] = '<a href="location_rack.php?action=edit&code='.$object->convert_data($row["location_rack_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_location_rack_all_records($object->connect),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'fetch_company')
	{
		$object->query = "
		SELECT * FROM item_manufacuter_company_ims 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR company_short_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR company_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY item_manufacuter_company_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["company_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_manufacuter_company_id"]).'`, `'.$row["company_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_manufacuter_company_id"]).'`, `'.$row["company_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['company_name'];			
			$sub_array[] = $row['company_short_name'];
			$sub_array[] = $status;
			$sub_array[] = $row['company_added_datetime'];
			$sub_array[] = $row['company_updated_datetime'];
			$sub_array[] = '<a href="company.php?action=edit&code='.$object->convert_data($row["item_manufacuter_company_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_company_all_records($object->connect),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'fetch_supplier')
	{
		$object->query = "
		SELECT * FROM supplier_ims 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE supplier_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_contact_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_email LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_status LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY supplier_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["supplier_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["supplier_id"]).'`, `'.$row["supplier_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["supplier_id"]).'`, `'.$row["supplier_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['supplier_name'];			
			$sub_array[] = $row['supplier_address'];
			$sub_array[] = $row['supplier_contact_no'];
			$sub_array[] = $row['supplier_email'];
			$sub_array[] = $status;
			$sub_array[] = $row['supplier_datetime'];
			$sub_array[] = '<a href="supplier.php?action=edit&code='.$object->convert_data($row["supplier_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_supplier_all_records($object->connect),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'fetch_product')
	{
		$object->query = "
		SELECT * FROM item_ims 
	    INNER JOIN category_ims 
	    ON category_ims.category_id = item_ims.item_category 
	    INNER JOIN  item_manufacuter_company_ims 
	    ON  item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
	    INNER JOIN location_rack_ims 
	    ON location_rack_ims.location_rack_id = item_ims.item_location_rack 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE item_ims.item_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_manufacuter_company_ims.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR category_ims.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR location_rack_ims.location_rack_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_ims.item_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY item_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["item_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_id"]).'`, `'.$row["item_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_id"]).'`, `'.$row["item_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['item_name'];			
			$sub_array[] = $row['company_name'];
			$sub_array[] = $row['category_name'];
			$sub_array[] = $row['location_rack_name'];
			$sub_array[] = $row['item_available_quantity'];
			$sub_array[] = $status;
			$sub_array[] = $row['item_add_datetime'];
			$sub_array[] = $row['item_update_datetime'];
			$sub_array[] = '<a href="product.php?action=edit&code='.$object->convert_data($row["item_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_item_all_records($object->connect),
			"recordsFiltered" 	=> 	get_item_all_records($object->connect),
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'fetch_tax')
	{
		$object->query = "
		SELECT * FROM tax_ims 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'WHERE tax_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR tax_percentage LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR tax_status LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR tax_added_on LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR tax_updated_on LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY tax_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["tax_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["tax_id"]).'`, `'.$row["tax_status"].'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["tax_id"]).'`, `'.$row["tax_status"].'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row['tax_name'];	
			$sub_array[] = $row['tax_percentage'] . '%';
			$sub_array[] = $status;
			$sub_array[] = $row['tax_added_on'];
			$sub_array[] = $row['tax_updated_on'];
			$sub_array[] = '<a href="tax.php?action=edit&code='.$object->convert_data($row["tax_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_tax_records($object->connect),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
		echo json_encode($output);

		
	}

	if($_POST['action'] == 'fetch_purchase')
	{
		$object->query = "
		SELECT * FROM item_purchase_ims 
	    INNER JOIN item_ims 
	    ON item_ims.item_id = item_purchase_ims.item_id 
	    INNER JOIN  supplier_ims 
	    ON  supplier_ims.supplier_id = item_purchase_ims.supplier_id 
		";

		$where = 'WHERE ';

		if(!$object->is_master_user())
		{
		    $where = "item_purchase_ims.item_purchase_enter_by = '".$_SESSION["user_id"]."' AND ";
		}

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= $where . '(item_ims.item_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_batch_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR supplier_ims.supplier_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_purchase_qty LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.available_quantity LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_purchase_price_per_unit LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_purchase_total_cost LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_sale_price_per_unit LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_purchase_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_purchase_ims.item_purchase_status LIKE "%'.$_POST["search"]["value"].'%" ) ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY item_purchase_ims.item_purchase_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["item_purchase_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_purchase_id"]).'`, `'.$row["item_purchase_status"].'`, `'.$object->convert_data($row["item_id"]).'`); "><i class="fa fa-toggle-off" aria-hidden="true"></i> Disable</button>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
				$delete_button = '<button type="button" class="btn btn-success btn-sm" onclick="delete_data(`'.$object->convert_data($row["item_purchase_id"]).'`, `'.$row["item_purchase_status"].'`, `'.$object->convert_data($row["item_id"]).'`); "><i class="fa fa-toggle-on" aria-hidden="true"></i> Enable</button>';
			}
			$sub_array = array();
			$sub_array[] = $row["item_name"];	
			$sub_array[] = $row["item_batch_no"];
			$sub_array[] = $row["supplier_name"];
			$sub_array[] = $row["item_purchase_qty"];
			$sub_array[] = $row["available_quantity"];
			$sub_array[] = $object->cur_sym . $row["item_purchase_price_per_unit"];
			$sub_array[] = $object->cur_sym . $row["item_purchase_total_cost"];
			$sub_array[] = $row["item_manufacture_month"].'/'.$row["item_manufacture_year"];
			$sub_array[] = $row["item_expired_month"].'/'.$row["item_expired_year"];
			$sub_array[] = $object->cur_sym . $row["item_sale_price_per_unit"];
			$sub_array[] = $row["item_purchase_datetime"];
			$sub_array[] = $status;
			$sub_array[] = '<a href="product_purchase.php?action=edit&code='.$object->convert_data($row["item_purchase_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;'.$delete_button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_item_purchse_records($object->connect),
			"recordsFiltered" 	=> 	get_total_item_purchse_records($object->connect),
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'fetch_order')
	{
		$object->query = "
		SELECT * FROM order_ims 
		";

		$other_query = '';

		if(!$object->is_master_user())
		{
		    $other_query = "
		    INNER JOIN user_ims 
    		ON user_ims.user_id = order_ims.order_created_by 
		    WHERE order_ims.order_created_by = '".$_SESSION["user_id"]."' AND 
		    ";
		}
		else
		{
			$other_query = "WHERE ";
		}

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= $other_query . '(order_ims.order_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR order_ims.buyer_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR order_ims.order_total_amount LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR order_ims.order_added_on LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR order_ims.order_updated_on LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR order_ims.order_status LIKE "%'.$_POST["search"]["value"].'%") ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY order_ims.order_id DESC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			if($row["order_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
			}
			$sub_array = array();
			$sub_array[] = $row["order_id"];	
			$sub_array[] = $row["buyer_name"];
			$sub_array[] = $object->cur_sym . $row["order_total_amount"];
			if(isset($row["user_name"]))
			{
				$sub_array[] = $row["user_name"];
			}
			else
			{
				$sub_array[] = 'Master';
			}
			
			$sub_array[] = $status;
			$sub_array[] = $row['order_added_on'];
			$sub_array[] = $row['order_updated_on'];
			$sub_array[] = '<a href="print_order.php?action=pdf&code='.$object->convert_data($row["order_id"]).'" class="btn-warning btn btn-sm" target="_blank">Print</a>&nbsp;<a href="order.php?action=edit&code='.$object->convert_data($row["order_id"]).'" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($row["order_id"]).'`, `'.$row["order_status"].'`); "><i class="fas fa-times"></i></button>';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_order_records($object->connect),
			"recordsFiltered" 	=> 	get_total_order_records($object->connect),
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}

	if($_POST["action"] == 'fetch_product_data')
	{
		$data = array(
			':item_purchase_id'	=>	$_POST["item_purchase_id"]
		);

		$object->query = "
		SELECT * FROM item_purchase_ims 
		INNER JOIN item_ims 
		ON item_ims.item_id =  item_purchase_ims.item_id 
		WHERE item_purchase_ims.item_purchase_id = :item_purchase_id
		";

		$object->execute($data);

		$result = $object->statement_result();

		$data = array();

		foreach($result as $row)
		{
			$data['item_id']					=	$row["item_id"];
			$data['item_name']					=	$row["item_name"];
			$data['item_batch_no']				=	$row["item_batch_no"];
			$data['item_available_quantity']	=	$row["item_available_quantity"];
			$data['item_expiry_date']			=	$row["item_expired_month"] . '/' . $row["item_expired_year"];
			$data['item_sale_price_per_unit']	=	$row["item_sale_price_per_unit"];
			$data['product_company']			=	$object->Get_Product_company_code($row["item_manufactured_by"]);
			$data['item_purchase_id']			=	$row["item_purchase_id"];
		}

		echo json_encode($data);

	}

	if($_POST["action"] == 'fetch_chart_data')
	{
		$data = array();
		$object->query = "
		SELECT SUM(order_total_amount) AS Total, DATE(order_added_on) AS Order_date FROM order_ims 
		WHERE order_status = 'Enable' 
		AND DATE(order_added_on) >= '".$_POST["start_date"]."' 
		AND DATE(order_added_on) <= '".$_POST["end_date"]."' 
		GROUP BY Order_date
		";

		$object->execute();

		if($object->row_count() > 0)
		{
			foreach($object->statement_result() as $row)
			{
				$data[] = array(
					'date'		=>	$row['Order_date'],
					'sale'		=>	$row['Total']
				);
			}
		}
		echo json_encode($data);
	}

	if($_POST["action"] == 'fetch_out_stock_product')
	{
		$object->query = "
		    SELECT * FROM item_ims 
		    INNER JOIN category_ims 
		    ON category_ims.category_id = item_ims.item_category 
		    INNER JOIN  item_manufacuter_company_ims 
		    ON  item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
		    INNER JOIN location_rack_ims 
		    ON location_rack_ims.location_rack_id = item_ims.item_location_rack 
		    WHERE item_ims.item_status = 'Enable' 
		    AND item_ims.item_available_quantity < 1 
		";

		if(isset($_POST["search"]["value"]))
		{
			$object->query .= 'AND (item_ims.item_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_manufacuter_company_ims.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_ims.item_available_quantity LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR location_rack_ims.location_rack_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_ims.item_status LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_ims.item_add_datetime LIKE "%'.$_POST["search"]["value"].'%" ';
			$object->query .= 'OR item_ims.item_update_datetime LIKE "%'.$_POST["search"]["value"].'%") ';
		}

		if(isset($_POST["order"]))
		{
			$object->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$object->query .= 'ORDER BY item_ims.item_name ASC ';
		}

		if($_POST["length"] != -1)
		{
			$object->query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->execute();

		$result = $object->statement_result();

		$data = array();

		$filtered_rows = $object->row_count();

		foreach($result as $row)
		{
			$status = '';
			$delete_button = '';
			if($row["item_status"] == 'Enable')
			{
				$status = '<div class="badge bg-success">Enable</div>';
			}
			else
			{
				$status = '<div class="badge bg-danger">Disable</div>';
			}
			$sub_array = array();
			$sub_array[] = $row['item_name'];			
			$sub_array[] = $row['company_name'];
			$sub_array[] = $row['item_available_quantity'];
			$sub_array[] = $row['location_rack_name'];
			$sub_array[] = $status;
			$sub_array[] = $row['item_add_datetime'];
			$sub_array[] = $row['item_update_datetime'];
			$sub_array[] = '<a href="product_purchase.php?action=add&code='.$object->convert_data("add").'&product='.$object->convert_data($row["item_id"]).'" class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i> Purchase</a>';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  get_total_inventory_out_of_stock($object->connect),
			"recordsFiltered" 	=> 	get_total_inventory_out_of_stock($object->connect),
			"data"    			=> 	$data
		);
		echo json_encode($output);
	}
}

function get_total_user_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM user_ims WHERE user_type='user'");
	$statement->execute();
	return $statement->rowCount();
}

function get_total_category_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM category_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_location_rack_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM location_rack_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_company_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM item_manufacuter_company_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_supplier_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM supplier_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_item_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM item_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_total_tax_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM tax_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_total_item_purchse_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM item_purchase_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_total_order_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM order_ims");
	$statement->execute();
	return $statement->rowCount();
}

function get_total_inventory_out_of_stock($connect)
{
	$statement = $connect->prepare("SELECT * FROM item_ims WHERE item_available_quantity < 1 AND item_status = 'Enable'");
	$statement->execute();
	return $statement->rowCount();
}


?>