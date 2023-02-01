<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

$where_condition = '';

if(!$object->is_master_user())
{
    $where_condition = "
    WHERE order_ims.order_created_by = '".$_SESSION["user_id"]."' 
    ";
}

$object->query = "
    SELECT * FROM order_ims 
    INNER JOIN user_msbs 
    ON user_msbs.user_id = order_ims.order_created_by 
    ".$where_condition."
    ORDER BY order_id DESC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_order"]))
{
    $formdata = array();

    if(empty($_POST["buyer_name"]))
    {
        $error .= '<li>Le nom du client est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["buyer_name"]))
        {
            $error .= '<li>Seuls les lettres, les chiffres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['buyer_name'] = trim($_POST["buyer_name"]);
        }
    }

    if($error == '')
    {
        $order_tax_name = '';
        $order_tax_percentage = '';

        if(isset($_POST['order_tax_name']))
        {
            $order_tax_name = implode(", ", $_POST['order_tax_name']);
        }

        if(isset($_POST['order_tax_percentage']))
        {
            $order_tax_percentage = implode(", ", $_POST['order_tax_percentage']);
        }

        $data = array(
            ':buyer_name'                => $formdata['buyer_name'],
            ':order_total_amount'        => $_POST['order_total_amount'],
            ':order_created_by'          => $_SESSION['user_id'],
            ':order_status'              => 'Enable',
            ':order_added_on'            => $object->now,
            ':order_updated_on'          => $object->now,
            ':order_tax_name'            => $order_tax_name,
            ':order_tax_percentage'      => $order_tax_percentage
        );

        $object->query = "
        INSERT INTO order_ims 
        (buyer_name, order_total_amount, order_created_by, order_status, order_added_on, order_updated_on, order_tax_name, order_tax_percentage) 
        VALUES (:buyer_name, :order_total_amount, :order_created_by, :order_status, :order_added_on, :order_updated_on, :order_tax_name, :order_tax_percentage)
        ";

        $object->execute($data);

        $order_id = $object->connect->lastInsertId();

        if($order_id > 0)
        {

            $item_id = $_POST["item_id"];

            $item_purchase_id = $_POST["item_purchase_id"];

            $item_quantity = $_POST["item_quantity"];

            $item_price = $_POST["item_price"];

            if(count($item_id) > 0)
            {
                for($i = 0; $i < count($item_id); $i++)
                {
                    $sub_data = array(
                        ':order_id'             =>  $order_id,
                        ':item_id'          =>  $item_id[$i],
                        ':item_purchase_id' =>  $item_purchase_id[$i],
                        ':item_quantity'    =>  $item_quantity[$i],
                        ':item_price'       =>  $item_price[$i]
                    );

                    $object->query = "
                    INSERT INTO order_item_ims 
                    (order_id, item_id, item_purchase_id, item_quantity, item_price) 
                    VALUES(:order_id, :item_id, :item_purchase_id, :item_quantity, :item_price)
                    ";

                    $object->execute($sub_data);

                    $object->query = "
                    UPDATE item_purchase_ims 
                    SET available_quantity = available_quantity - ".$item_quantity[$i]." 
                    WHERE item_purchase_id = '".$item_purchase_id[$i]."'
                    ";

                    $object->get_result();

                    $object->query = "
                    UPDATE item_ims 
                    SET item_available_quantity = item_available_quantity - ".$item_quantity[$i]." 
                    WHERE item_id = '".$item_id[$i]."'
                    ";

                    $object->get_result();

                }
            }

            header('location:order.php?msg=add');
        }
        else
        {
            $error = '<li>Something Went Wrong</li>';
        }
    }
}

if(isset($_GET["action"], $_GET["item_code"], $_GET["order_code"]) && $_GET["action"] == 'remove_item')
{
    $order_item_id = $object->convert_data(trim($_GET["item_code"]), 'decrypt');

    $object->query = "
    SELECT * FROM order_item_ims 
    WHERE order_item_id = '".$order_item_id."'
    ";

    $item_result = $object->get_result();

    foreach($item_result as $item_row)
    {
        $item_id = $item_row["item_id"];
        $item_purchase_id = $item_row["item_purchase_id"];
        $item_quantity = $item_row["item_quantity"];
        $item_price = $item_row["item_price"];
        $object->query = "
        DELETE FROM order_item_ims 
        WHERE order_item_id = '".$order_item_id."'
        ";

        $object->get_result();

        $tax_per_arr = $object->Get_order_tax_percentage($item_row["order_id"]);

        $item_amt_without_tax = $item_quantity * $item_price;

        $tax_amt = 0;

        for($i = 0; $i < count($tax_per_arr); $i++)
        {
            $tax_amt = floatval($tax_amt) + floatval($item_amt_without_tax * $tax_per_arr[$i] / 100 );
        }

        $item_amt_with_tax = floatval($item_amt_without_tax) + floatval($tax_amt);

        $object->query = "
        UPDATE order_ims 
        SET order_total_amount = order_total_amount - ".$item_amt_with_tax ." 
        WHERE order_id = '".$item_row["order_id"]."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE item_purchase_ims 
        SET available_quantity = available_quantity + ".$item_quantity." 
        WHERE item_purchase_id = '".$item_purchase_id."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE item_ims 
        SET item_available_quantity = item_available_quantity + ".$item_quantity." 
        WHERE item_id = '".$item_id."'
        ";

        $object->get_result();
    }

    header('location:order.php?action=edit&code='.$_GET["order_code"].'');

}

if(isset($_POST["edit_order"]))
{
    $formdata = array();

    if(empty($_POST["buyer_name"]))
    {
        $error .= '<li>Le nom du client est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["buyer_name"]))
        {
            $error .= '<li>Seuls les lettres, les chiffres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['buyer_name'] = trim($_POST["buyer_name"]);
        }
    }

    if($error == '')
    {
        $order_id = $object->convert_data(trim($_POST["order_id"]), 'decrypt');

        $order_tax_name = '';
        $order_tax_percentage = '';

        if(isset($_POST['order_tax_name']))
        {
            $order_tax_name = implode(", ", $_POST['order_tax_name']);
        }

        if(isset($_POST['order_tax_percentage']))
        {
            $order_tax_percentage = implode(", ", $_POST['order_tax_percentage']);
        }

        $data = array(
            ':buyer_name'           =>  $formdata['buyer_name'],
            ':order_total_amount'   =>  $_POST["order_total_amount"],
            ':order_updated_on'     =>  $object->now,
            ':order_tax_name'       => $order_tax_name,
            ':order_tax_percentage' => $order_tax_percentage, 
            ':order_id'             =>  $order_id
        );

        $object->query = "
        UPDATE order_ims 
        SET buyer_name = :buyer_name, 
        order_total_amount = :order_total_amount, 
        order_updated_on = :order_updated_on, 
        order_tax_name = :order_tax_name, 
        order_tax_percentage = :order_tax_percentage 
        WHERE order_id = :order_id
        ";

        $object->execute($data);

        $item_id = $_POST["item_id"];
        $item_purchase_id = $_POST["item_purchase_id"];
        $item_quantity = $_POST["item_quantity"];
        $item_price = $_POST["item_price"];

        for($i = 0; $i < count($item_id); $i++)
        {
            $object->query = "
            SELECT * FROM order_item_ims 
            WHERE order_id = '".$order_id."' 
            AND item_id = '".$item_id[$i]."' 
            AND item_purchase_id = '".$item_purchase_id[$i]."'
            ";

            $order_item_result = $object->get_result();

            foreach($order_item_result as $order_item_row)
            {
                $itemid = $order_item_row["item_id"];
                $itempurchaseid = $order_item_row["item_purchase_id"];
                $itemquantity = $order_item_row["item_quantity"];
                $medicineprice = $order_item_row["item_price"];

                if($itemquantity != $item_quantity[$i])
                {
                    $data = array(
                        ':item_quantity'    =>  $item_quantity[$i],
                        ':order_item_id'        =>  $order_item_row['order_item_id']
                    );
                    $object->query = "
                    UPDATE order_item_ims 
                    SET item_quantity = :item_quantity 
                    WHERE order_item_id = :order_item_id
                    ";

                    $object->execute($data);

                    $final_update_qty = 0;
                    if($itemquantity > $item_quantity[$i])
                    {
                        $final_update_qty = $itemquantity - $item_quantity[$i];

                        $object->query = "
                        UPDATE item_purchase_ims 
                        SET available_quantity = available_quantity + ".$final_update_qty." 
                        WHERE item_purchase_id = '".$item_purchase_id[$i]."'
                        ";

                        $object->execute();

                        $object->query = "
                        UPDATE item_ims 
                        SET item_available_quantity = item_available_quantity + ".$final_update_qty." 
                        WHERE item_id = '".$item_id[$i]."'
                        ";

                        $object->execute();
                        
                    }
                    else
                    {
                        $final_update_qty = $item_quantity[$i] - $itemquantity;

                        $object->query = "
                        UPDATE item_purchase_ims 
                        SET available_quantity = available_quantity - ".$final_update_qty." 
                        WHERE item_purchase_id = '".$item_purchase_id[$i]."'
                        ";

                        $object->execute();

                        $object->query = "
                        UPDATE item_ims 
                        SET item_available_quantity = item_available_quantity - ".$final_update_qty." 
                        WHERE item_id = '".$item_id[$i]."'
                        ";

                        $object->execute();
                    }
                }
            }
        }

        header('location:order.php?msg=edit');

    }
}




if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $order_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

    $object->query = "
    SELECT * FROM order_item_ims 
    WHERE order_id = '".$order_id."'
    ";

    $item_result = $object->get_result();

    foreach($item_result as $item_row)
    {
        $object->query = "
        UPDATE item_purchase_ims 
        SET available_quantity = available_quantity + ".$item_row['item_quantity']." 
        WHERE item_purchase_id = '".$item_row['item_purchase_id']."'
        ";

        $object->get_result();

        $object->query = "
        UPDATE item_ims 
        SET item_available_quantity = item_available_quantity + ".$item_row['item_quantity']." 
        WHERE item_id = '".$item_row['item_id']."'
        ";

        $object->get_result();
    }

    $object->query = "
    DELETE FROM order_item_ims 
    WHERE order_id = '".$order_id."'
    ";

    $object->execute();

    $object->query = "
    DELETE FROM order_ims 
    WHERE order_id = '".$order_id."'
    ";

    $object->execute();

    header('location:order.php?msg=delete');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">La gestion des commandes</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="order.php">La gestion des commandes</a></li>
                                <li class="breadcrumb-item active">Ajouter une commande</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>
                            <span id="msg_area"></span>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Ajouter une commande
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="buyer_name" type="text" placeholder="Enter Nom du client" name="buyer_name" value="<?php if(isset($_POST["buyer_name"])) echo $_POST["buyer_name"]; ?>" />
                                                    <label for="buyer_name">Nom du client</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <select class="form-control selectpicker" id="add_product_id"  data-live-search="true" data-size="5" data-allow-clear="true" data-noneSelectedText="Select Product">
                                                    <?php echo $object->get_product_array(); ?>
                                                </select>
                                                
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" name="add_medicine" id="add_medicine" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Ajouter un produit</button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="34%">Nom du produit</th>
                                                        <th width="6%">Fabricant</th>
                                                        <th width="11%">N ° de lot.</th>
                                                        <th width="11%">Date d'expiration</th>
                                                        <th width="11%">Quantité</th>
                                                        <th width="11%">Prix ​​unitaire</th>
                                                        <th width="11%">Prix ​​total</th>
                                                        <th width="5%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order_item_area">
                                                <?php
                                                $temp_total = 0;
                                                if(isset($_POST["item_purchase_id"]))
                                                {
                                                    if(count($_POST["item_purchase_id"]) > 0)
                                                    {
                                                        $item_purchase_id = $_POST["item_purchase_id"];
                                                        
                                                        for($i = 0; $i < count($item_purchase_id); $i++)
                                                        {
                                                            $data = array(
                                                                ':item_purchase_id' =>  $item_purchase_id[$i]
                                                            );

                                                            $object->query = "
                                                            SELECT * FROM item_purchase_ims 
                                                            INNER JOIN item_ims 
                                                            ON item_ims.item_id =  item_purchase_ims.item_id 
                                                            WHERE item_purchase_ims.item_purchase_id = :item_purchase_id
                                                            ";

                                                            $object->execute($data);

                                                            $order_result = $object->statement_result();

                                                            $data = array();

                                                            foreach($order_result as $order_row)
                                                            {
                                                                echo '
                                                                <tr>
                                                                    <td>'.$order_row['item_name'].'<input type="hidden" name="item_id[]" value="'.$order_row['item_id'].'" /><input type="hidden" name="item_purchase_id[]" value="'.$order_row['item_purchase_id'].'" /></td>
                                                                    <td>'.$object->Get_Product_company_code($order_row["item_manufactured_by"]).'</td>
                                                                    <td>'.$order_row['item_batch_no'].'</td>
                                                                    <td>'.$order_row["item_expired_month"] . '/' . $order_row["item_expired_year"].'</td>
                                                                    <td><input type="number" name="item_quantity[]" class="form-control item_quantity" placeholder="Quantity" value="'.$_POST["item_quantity"][$i].'" min="1" onblur="calculate_total()" /></td>
                                                                    <td><span class="item_unit_price">'.number_format($order_row['item_sale_price_per_unit'], 2).'</span><input type="hidden" name="item_price[]" value="'.$order_row['item_sale_price_per_unit'].'" /></td>
                                                                    <td><span class="item_total_price">'.number_format($order_row['item_sale_price_per_unit'] * $_POST["item_quantity"][$i], 2) .'</span></td>
                                                                    <td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fas fa-minus"></i></button></td>
                                                                </tr>
                                                                ';

                                                                $temp_total = floatval($temp_total) + ($order_row['item_sale_price_per_unit'] * $_POST["item_quantity"][$i]);
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                <?php 

                                                if(isset($_POST["item_purchase_id"]))
                                                {
                                                    if(count($_POST["item_purchase_id"]) > 0)
                                                    {
                                                        $object->query = "
                                                        SELECT * FROM tax_ims 
                                                        WHERE tax_status = 'Enable' 
                                                        ORDER BY tax_name ASC
                                                        ";

                                                        $result = $object->get_result();

                                                        foreach($result as $row)
                                                        {
                                                            echo '
                                                            <tr>
                                                                <td colspan="6" align="right" class="store_tax_per" data-taxper="'.$row["tax_percentage"].'" data-taxid="'.$row["tax_id"].'">
                                                                    '.$row["tax_name"].' @ '.$row["tax_percentage"].'%
                                                                    <input type="hidden" name="order_tax_name[]" value="'.$row["tax_name"].'" />
                                                                    <input type="hidden" name="order_tax_percentage[]" value="'.$row["tax_percentage"].'" />
                                                                </td>
                                                                <td colspan="2" id="tax'.$row["tax_id"].'">
                                                                    '.number_format($temp_total * $row["tax_percentage"] / 100, 2) .'
                                                                </td>
                                                            </tr>
                                                            ';
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    echo $object->Get_tax_field();
                                                }
                                                ?>
                                                    <tr>
                                                        <td colspan="6" align="right"><b>Totale</b></td>
                                                        <td colspan="2" id="order_total_amount"><?php if(isset($_POST["order_total_amount"])) echo number_format($_POST["order_total_amount"], 2); ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="order_total_amount" id="hidden_order_total_amount" value="<?php if(isset($_POST["order_total_amount"])) echo $_POST["order_total_amount"]; ?>" />
                                            <input type="submit" name="add_order" class="btn btn-success" value="Ajouter" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <script>

                            function _(element)
                            {
                                return document.getElementById(element);
                            }

                            _('add_medicine').onclick = function()
                            {
                                var item_purchase_id = _('add_product_id').value;
                                //console.log(med_id);
                                if(item_purchase_id == '')
                                {
                                    _('msg_area').innerHTML = '<div class="alert alert-danger">Veuillez sélectionner le produit</div>';
                                    setTimeout(function(){
                                        _('msg_area').innerHTML = '';
                                    }, 5000);
                                }
                                else
                                {
                                    var form_data = new FormData();
                                    form_data.append('item_purchase_id', item_purchase_id);
                                    form_data.append('action', 'fetch_product_data');
                                    fetch('action.php', {

                                        method:"POST",

                                        body:form_data

                                    }).then(function(response){

                                        return response.json();

                                    }).then(function(responseData){
                                        console.log(responseData);
                                        $('#add_product_id').selectpicker('val', '');
                                        if(responseData.item_available_quantity == 0)
                                        {
                                            _('msg_area').innerHTML = '<div class="alert alert-danger">Cette quantité de produit n est pas disponible</div>';
                                            setTimeout(function(){
                                                _('msg_area').innerHTML = '';
                                            }, 5000);
                                        }
                                        else
                                        {
                                            var no = random_number(100, 999);
                                            var html = '<tr id="'+no+'">';
                                            html += '<td>'+responseData.item_name+'<input type="hidden" name="item_id[]" value="'+responseData.item_id+'" /><input type="hidden" name="item_purchase_id[]" value="'+responseData.item_purchase_id+'" /></td>';
                                            html += '<td>'+responseData.product_company+'</td>';
                                            html += '<td>'+responseData.item_batch_no+'</td>';
                                            html += '<td>'+responseData.item_expiry_date+'</td>';
                                            html += '<td><input type="number" name="item_quantity[]" class="form-control item_quantity" placeholder="Quantity" value="1" min="1" onblur="check_qty(this); calculate_total();" data-number="'+no+'" /></td>';
                                            html += '<td><span class="item_unit_price">'+responseData.item_sale_price_per_unit+'</span><input type="hidden" name="item_price[]" value="'+responseData.item_sale_price_per_unit+'" /></td>';
                                            html += '<td><span class="item_total_price" id="item_total_price_'+no+'">'+responseData.item_sale_price_per_unit+'</span></td>';
                                            html += '<td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fas fa-minus"></i></button></td>';
                                            html += '</tr>';

                                            var data = document.getElementById('order_item_area');

                                            data.insertRow().innerHTML = html;

                                            calculate_total();
                                        }

                                    }); 
                                }                                

                            }

                            function deleteRow(btn) {
                                var row = btn.parentNode.parentNode;
                                row.parentNode.removeChild(row);
                                calculate_total();
                            }

                            function random_number(min, max) {
                                min = Math.ceil(min);
                                max = Math.floor(max);
                                return Math.floor(Math.random() * (max - min + 1)) + min;
                            }

                            function calculate_total()
                            {
                                var qty = document.getElementsByClassName('item_quantity');
                                var unit_price = document.getElementsByClassName('item_unit_price');
                                var total_price = document.getElementsByClassName('item_total_price');

                                var total = 0;

                                if(qty.length > 0)
                                {
                                    for(var i = 0; i < qty.length; i++)
                                    {
                                        console.log('Qty - ' + qty[i].value);
                                        console.log('Unit Price - ' + unit_price[i].innerHTML);
                                        var temp_total_price = parseFloat(qty[i].value) * parseFloat(unit_price[i].innerHTML);
                                        total_price[i].innerHTML = temp_total_price.toFixed(2);
                                        total = parseFloat(total) + parseFloat(temp_total_price);
                                    }
                                }

                                var tax_class = document.getElementsByClassName('store_tax_per');
                                var total_tax_amt = 0;

                                if(tax_class.length > 0)
                                {
                                    for(var j = 0; j < tax_class.length; j++)
                                    {
                                        var taxper = tax_class[j].getAttribute('data-taxper');
                                        var taxid = tax_class[j].getAttribute('data-taxid');
                                        var tax_amt = parseFloat(total) * parseFloat(taxper) / 100;
                                        _('tax'+taxid).innerHTML = tax_amt.toFixed(2);
                                        total_tax_amt = parseFloat(total_tax_amt) + parseFloat(tax_amt);
                                    }
                                }

                                total = parseFloat(total) + parseFloat(total_tax_amt);

                                _('hidden_order_total_amount').value = total;
                                _('order_total_amount').innerHTML = total.toFixed(2);
                            }

                            function check_qty(element)
                            {
                                var min = element.min;
                                if(parseInt(element.value) < min)
                                {
                                    element.value = min;
                                }
                                if(element.value == '')
                                {
                                    element.value = min;
                                }
                            }

                            

                            </script>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $order_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($order_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM order_ims 
                                    WHERE order_id = '$order_id'
                                    ";

                                    $order_result = $object->get_result();

                                    foreach($order_result as $order_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="order.php">La gestion des commandes</a></li>
                                    <li class="breadcrumb-item active">Modifier les données de commande</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Modifier les données de commande
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="buyer_name" type="text" placeholder="Entrez le nom du client" name="buyer_name" value="<?php echo $order_row["buyer_name"]; ?>" />
                                                        <label for="buyer_name">Nom du client</label>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th width="34%">Nom du produit</th>
                                                            <th width="6%">Fabricant</th>
                                                            <th width="11%">N ° de lot.</th>
                                                            <th width="11%">Date d'expiration</th>
                                                            <th width="11%">Quantité</th>
                                                            <th width="11%">Prix ​​unitaire</th>
                                                            <th width="11%">Prix ​​total</th>
                                                            <th width="5%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="order_item_area">
                                                    <?php

                                                    $object->query = "
                                                    SELECT * FROM order_item_ims 
                                                    WHERE order_id = '".$order_row["order_id"]."'
                                                    ";

                                                    $order_item_result = $object->get_result();

                                                    $temp_total = 0;

                                                    foreach($order_item_result as $order_item_row)
                                                    {
                                                        $object->query = "
                                                            SELECT * FROM item_purchase_ims 
                                                            INNER JOIN item_ims 
                                                            ON item_ims.item_id =  item_purchase_ims.item_id 
                                                            WHERE item_purchase_ims.item_purchase_id = '".$order_item_row['item_purchase_id']."'
                                                        ";

                                                        $product_pur_result = $object->get_result();

                                                        foreach($product_pur_result as $product_pur_row)
                                                        {
                                                            echo '
                                                            <tr>
                                                                <td>'.$product_pur_row['item_name'].'<input type="hidden" name="item_id[]" value="'.$order_item_row['item_id'].'" /><input type="hidden" name="item_purchase_id[]" value="'.$order_item_row['item_purchase_id'].'" /></td>
                                                                <td>'.$object->Get_Product_company_code($product_pur_row["item_manufactured_by"]).'</td>
                                                                <td>'.$product_pur_row['item_batch_no'].'</td>
                                                                <td>'.$product_pur_row["item_expired_month"] . '/' . $product_pur_row["item_expired_year"].'</td>
                                                                <td><input type="number" name="item_quantity[]" class="form-control item_quantity" placeholder="Quantity" value="'.$order_item_row["item_quantity"].'" min="1" onblur="check_qty(this); calculate_total();" /></td>
                                                                <td><span class="item_unit_price">'.$product_pur_row['item_sale_price_per_unit'].'</span><input type="hidden" name="item_price[]" value="'.$order_item_row['item_price'].'" /></td>
                                                                <td><span class="item_total_price">'.number_format($product_pur_row['item_sale_price_per_unit'] * $order_item_row["item_quantity"], 2).'</span></td>
                                                                <td><button type="button" name="remove_item" class="btn btn-danger btn-sm" onclick="delete_data(`'.$object->convert_data($order_item_row["order_item_id"]).'`, `'.$_GET["code"].'`);"><i class="fas fa-minus"></i></button></td>
                                                            </tr>
                                                            ';

                                                            $temp_total = floatval($temp_total) + floatval($product_pur_row['item_sale_price_per_unit'] * $order_item_row["item_quantity"]);
                                                        }
                                                    }
                                                    ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <?php
                                                        $tax_name_arr = explode(", ", $order_row['order_tax_name']);
                                                        $tax_per_arr = explode(", ", $order_row['order_tax_percentage']);

                                                        for($i = 0; $i < count($tax_name_arr); $i++)
                                                        {
                                                            echo '
                                                            <tr>
                                                                <td colspan="6" align="right" class="store_tax_per" data-taxper="'.$tax_per_arr[$i].'" data-taxid="'.$i.'">
                                                                    '.$tax_name_arr[$i].' @ '.$tax_per_arr[$i].'%
                                                                    <input type="hidden" name="order_tax_name[]" value="'.$tax_name_arr[$i].'" />
                                                                    <input type="hidden" name="order_tax_percentage[]" value="'.$tax_per_arr[$i].'" />
                                                                </td>
                                                                <td colspan="2" id="tax'.$i.'">
                                                                    '.number_format($temp_total * $tax_per_arr[$i] / 100, 2) .'
                                                                </td>
                                                            </tr>
                                                            ';
                                                        }

                                                        ?>
                                                        <tr>
                                                            <td colspan="6" align="right"><b>Totale</b></td>
                                                            <td colspan="2" id="order_total_amount"><?php echo $order_row["order_total_amount"]; ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="order_total_amount" id="hidden_order_total_amount" value="<?php echo $order_row["order_total_amount"]; ?>" />
                                                <input type="hidden" name="order_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_order" class="btn btn-primary" value="Edit" />
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <script>
                                    function _(element)
                                    {
                                        return document.getElementById(element);
                                    }

                                    function calculate_total()
                                    {
                                        var qty = document.getElementsByClassName('item_quantity');
                                        var unit_price = document.getElementsByClassName('item_unit_price');
                                        var total_price = document.getElementsByClassName('item_total_price');

                                        var total = 0;

                                        if(qty.length > 0)
                                        {
                                            for(var i = 0; i < qty.length; i++)
                                            {
                                                console.log('Qty - ' + qty[i].value);
                                                console.log('Unit Price - ' + unit_price[i].innerHTML);
                                                var temp_total_price = parseFloat(qty[i].value) * parseFloat(unit_price[i].innerHTML);
                                                total_price[i].innerHTML = temp_total_price.toFixed(2);
                                                total = parseFloat(total) + parseFloat(temp_total_price);
                                            }
                                        }

                                        var tax_class = document.getElementsByClassName('store_tax_per');
                                        var total_tax_amt = 0;

                                        if(tax_class.length > 0)
                                        {
                                            for(var j = 0; j < tax_class.length; j++)
                                            {
                                                var taxper = tax_class[j].getAttribute('data-taxper');
                                                var taxid = tax_class[j].getAttribute('data-taxid');
                                                var tax_amt = parseFloat(total) * parseFloat(taxper) / 100;
                                                _('tax'+taxid).innerHTML = tax_amt.toFixed(2);
                                                total_tax_amt = parseFloat(total_tax_amt) + parseFloat(tax_amt);
                                            }
                                        }

                                        total = parseFloat(total) + parseFloat(total_tax_amt);

                                        _('hidden_order_total_amount').value = total;
                                        _('order_total_amount').innerHTML = total.toFixed(2);
                                    }

                                    function check_qty(element)
                                    {
                                        var min = element.min;
                                        if(parseInt(element.value) < min)
                                        {
                                            element.value = min;
                                        }
                                        if(element.value == '')
                                        {
                                            element.value = min;
                                        }
                                    }

                                    function delete_data(item_code, order_code)
                                    {
                                        if(confirm("Are you sure you want this medicine from Order?"))
                                        {
                                            window.location.href="order.php?action=remove_item&item_code="+item_code+"&order_code="+order_code+"";
                                        }
                                    }
                                </script>
                                
                                <?php
                                    }
                                }
                                else
                                {
                                    echo '<div class="alert alert-info">Something Went Wrong</div>';
                                }                                
                            }
                            else
                            {
                                echo '<div class="alert alert-info">Something Went Wrong</div>';
                            }
                        ?>

                        <?php
                        }
                        else
                        {
                        ?>
                        
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item active">La gestion des commandes</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouvelle commande ajoutée<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Données de commande modifiées <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'delete')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">La commande a été supprimée <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> La gestion des commandes
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="order.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="order_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>N ° de commande.</th>
                                                <th>Nom du client</th>
                                                <th>Montant de la commande</th>
                                                <th>Créé par</th>
                                                <th>Statut</th>
                                                <th>Ajouté le</th>
                                                <th>Mis à jour le</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>
                            
                            var dataTable = $('#order_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_order"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[7],
                                        "orderable":false
                                    }
                                ],
                                "pageLength": 10
                            });

                            function delete_data(code, status)
                            {
                                var new_status = 'Enable';
                                if(status == 'Enable')
                                {
                                    new_status = 'Disable';
                                }
                                if(confirm("Are you sure you want to "+new_status+" this Order?"))
                                {
                                    window.location.href="order.php?action=delete&code="+code+"&status="+new_status+"";
                                }
                            }

                            </script>
                        <?php
                        }
                        ?>

                        </div>

<?php

include('footer.php');

?>