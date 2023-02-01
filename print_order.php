<?php

//print_order.php

if(isset($_GET["action"], $_GET["code"]) && $_GET["action"] == 'pdf' && $_GET['code'] != '')
{
	include('class/db.php');

	$object = new db();

	$order_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

	$object->query = "
	SELECT * FROM store_ims 
	LIMIT 1
	";

	$store_result = $object->get_result();

	$store_name = '';
	$store_address = '';
	$store_contact_no = '';
	$store_email = '';

	foreach($store_result as $store_row)
	{
		$store_name = $store_row['store_name'];
		$store_address = $store_row['store_address'];
		$store_contact_no = $store_row['store_contact_no'];
		$store_email = $store_row['store_email_address'];
	}

	$html = '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<h2 align="center" style="margin-bottom:15px;">'.$store_name.'</h2>
				<div align="center" style="margin-bottom:6px">'.$store_address.'</div><div align="center"><b>Phone No. : </b>'.$store_contact_no.' &nbsp;&nbsp;&nbsp;<b>Email : </b>'.$store_email.'</div>
			</td>
		</tr>
		<tr>
			<td>
	';

	$object->query = "
	SELECT * FROM order_ims 
	WHERE order_id = '$order_id'
	";

	$total_amount = 0;
	$created_by = '';
	$order_date = '';
	$order_result = $object->get_result();
	$html .= '
				<table border="1" width="100%" cellpadding="5" cellspacing="0">

	';

	$tax_name = '';
	$tax_per = '';
	foreach($order_result as $order_row)
	{
		$html .= '
					<tr>
						<td width="50%">
							<div style="margin-bottom:8px;"><b>Order No : </b>'.$order_row["order_id"].'</div>
							<div style="margin-bottom:8px;"><b>Customer Name : </b>'.$order_row["buyer_name"].'</div>
														    <b>Date         : </b>'.$order_row["order_added_on"].'   
						</td>
						<td width="50%" align="center"><b>CASH MEMO</b></td>
					</tr>
		';

		$total_amount = $order_row["order_total_amount"];
		$created_by = $object->Get_user_name_from_id($order_row["order_created_by"]);

		$tax_name = $order_row["order_tax_name"];
		$tax_per = $order_row["order_tax_percentage"];
	}

	$object->query = "
	SELECT * FROM order_item_ims 
	WHERE order_id = '$order_id'
	";

	$order_item_result = $object->get_result();

	$html .= '
				</table>
				<br />
				<table width="100%" border="1" cellpadding="5" cellspacing="0">
					<tr>
						<td width="5%"><b>Sr.</b></td>
						<td width="32%"><b>Particular</b></td>
						<td width="6%"><b>Mfg.</b></td>
						<td width="11%"><b>Batch No.</b></td>
						<td width="10%"><b>Expiry Dt.</b></td>
						<td width="13%"><b>MRP</b></td>
						<td width="9%"><b>Qty.</b></td>
						<td width="16%"><b>Sale Price</b></td>
					</tr>
	';

	$count_product = 0;

	$temp_total = 0;

	foreach($order_item_result as $order_item_row)
	{
		$count_product++;

		$m_data = $object->Get_product_name($order_item_row['item_id'], $order_item_row["item_purchase_id"]);

		$html .= '
					<tr>
						<td>'.$count_product.'</td>
						<td>'.$m_data["item_name"].'</td>
						<td>'.$m_data["company_short_name"].'</td>
						<td>'.$m_data["item_batch_no"].'</td>
						<td>'.$m_data["expiry_date"].'</td>
						<td>'.$object->cur_sym . $m_data["item_sale_price_per_unit"].'</td>
						<td>'.$order_item_row["item_quantity"].'</td>
						<td>'.$object->cur_sym . number_format(floatval($order_item_row["item_price"] * $order_item_row["item_quantity"]), 2, '.', ',').'</td>
					</tr>
		';

		$temp_total = floatval($temp_total) + floatval($order_item_row["item_price"] * $order_item_row["item_quantity"]);
	}

	if($tax_name != '' && $tax_per != '')
	{
		$tax_name_arr = explode(", ", $tax_name);

		$tax_per_arr = explode(", ", $tax_per);

		for($i = 0; $i < count($tax_name_arr); $i++)
		{
			$html .= '
					<tr>
						<td colspan="7" align="right">
							'.$tax_name_arr[$i].' @ '.$tax_per_arr[$i].'%
						</td>
						<td>
							'. $object->cur_sym . number_format($temp_total * $tax_per_arr[$i] / 100, 2) .'
						</td>
					</tr>
			';
		}
	}


	$html .= '
					<tr>
						<td colspan="7" align="right"><b>Total</b></td>
						<td>'.$object->cur_sym . number_format(floatval($total_amount), 2, '.', ' ').'</td>
					</tr>
	';

	$html .= '
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Created By '.$created_by.'</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>

	';

	//echo $html;

	require_once('class/pdf.php');

	$pdf = new Pdf();

	$pdf->set_paper('letter', 'landscape');

	$file_name = ''.$order_id .'.pdf';

	$pdf->loadHtml($html);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
	exit(0);
}
else
{
	header('location:order.php');
}

?>