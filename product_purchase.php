<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
}

$where = '';

if(!$object->is_master_user())
{
    $where = "WHERE item_purchase_ims.medicine_purchase_enter_by = '".$_SESSION["user_id"]."' ";
}

$message = '';

$error = '';

if(isset($_POST["add_purchase"]))
{
    $formdata = array();

    if(empty($_POST["item_id"]))
    {
        $error .= '<li>Product Name is required</li>';
    }
    else
    {
        $formdata['item_id'] = trim($_POST["item_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["item_batch_no"]))
    {
        $error .= '<li>Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9 ']*$/", $_POST["item_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['item_batch_no'] = trim($_POST["item_batch_no"]);
        }
    }

    if(empty($_POST["item_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.']*$/", $_POST["item_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['item_purchase_qty'] = trim($_POST["item_purchase_qty"]);
        }
    }

    if(empty($_POST["item_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['item_purchase_price_per_unit'] = trim($_POST["item_purchase_price_per_unit"]);
    }

    if(empty($_POST["item_manufacture_month"]))
    {
        $error .= '<li>Manufacturing Month is required</li>';
    }
    else
    {
        $formdata['item_manufacture_month'] = trim($_POST["item_manufacture_month"]);
    }

    if(empty($_POST["item_manufacture_year"]))
    {
        $error .= '<li>Manufacturing Year is required</li>';
    }
    else
    {
        $formdata['item_manufacture_year'] = trim($_POST["item_manufacture_year"]);
    }

    if(empty($_POST["item_expired_month"]))
    {
        $error .= '<li>Expired Month is required</li>';
    }
    else
    {
        $formdata['item_expired_month'] = trim($_POST["item_expired_month"]);
    }

    if(empty($_POST["item_expired_year"]))
    {
        $error .= '<li>Expired Year is required</li>';
    }
    else
    {
        $formdata['item_expired_year'] = trim($_POST["item_expired_year"]);
    }

    if(empty($_POST["item_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['item_sale_price_per_unit'] = trim($_POST["item_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $total_cost = floatval($formdata['item_purchase_qty']) * floatval($formdata['item_purchase_price_per_unit']);

        $data = array(
            ':item_id'                      =>  $formdata['item_id'],
            ':supplier_id'                  =>  $formdata['supplier_id'],
            ':item_batch_no'                =>  $formdata['item_batch_no'],
            ':item_purchase_qty'            =>  $formdata['item_purchase_qty'], 
            ':available_quantity'           =>  $formdata['item_purchase_qty'], 
            ':item_purchase_price_per_unit' =>  $formdata['item_purchase_price_per_unit'],
            ':item_purchase_total_cost'     =>  $total_cost,
            ':item_manufacture_month'       =>  $formdata['item_manufacture_month'],
            ':item_manufacture_year'        =>  $formdata['item_manufacture_year'],
            ':item_expired_month'           =>  $formdata['item_expired_month'],
            ':item_expired_year'            =>  $formdata['item_expired_year'],
            ':item_sale_price_per_unit'     =>  $formdata['item_sale_price_per_unit'],
            ':item_purchase_datetime'       =>  $object->now,
            ':item_purchase_status'         =>  'Enable',
            ':item_purchase_enter_by'       =>  $_SESSION['user_id']
        );

        $object->query = "
        INSERT INTO item_purchase_ims 
        (item_id, supplier_id, item_batch_no, item_purchase_qty, available_quantity, item_purchase_price_per_unit, item_purchase_total_cost, item_manufacture_month, item_manufacture_year, item_expired_month, item_expired_year, item_sale_price_per_unit, item_purchase_datetime, item_purchase_status, item_purchase_enter_by) 
        VALUES (:item_id, :supplier_id, :item_batch_no, :item_purchase_qty, :available_quantity, :item_purchase_price_per_unit, :item_purchase_total_cost, :item_manufacture_month, :item_manufacture_year, :item_expired_month, :item_expired_year, :item_sale_price_per_unit, :item_purchase_datetime, :item_purchase_status, :item_purchase_enter_by)
        ";

        $object->execute($data);

        $object->query = "
        UPDATE item_ims 
        SET item_available_quantity = item_available_quantity + ".$formdata['item_purchase_qty']." 
        WHERE item_id = '".$formdata['item_id']."'
        ";

        $object->execute();

        header('location:product_purchase.php?msg=add');
    }
}

if(isset($_POST["edit_purchase"]))
{
    $formdata = array();

    if(empty($_POST["item_id"]))
    {
        $error .= '<li>Product Name is required</li>';
    }
    else
    {
        $formdata['item_id'] = trim($_POST["item_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Supplier is required</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["item_batch_no"]))
    {
        $error .= '<li>Batch No. is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9 ']*$/", $_POST["item_batch_no"]))
        {
            $error .= '<li>Only letters and Numbers allowed</li>';
        }
        else
        {
            $formdata['item_batch_no'] = trim($_POST["item_batch_no"]);
        }
    }

    if(empty($_POST["item_purchase_qty"]))
    {
        $error .= '<li>Purchase Quantity is required</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.']*$/", $_POST["item_purchase_qty"]))
        {
            $error .= '<li>Only Numbers allowed</li>';
        }
        else
        {
            $formdata['item_purchase_qty'] = trim($_POST["item_purchase_qty"]);
        }
    }

    if(empty($_POST["item_purchase_price_per_unit"]))
    {
        $error .= '<li>Purchase Price per unit is required</li>';
    }
    else
    {
        $formdata['item_purchase_price_per_unit'] = trim($_POST["item_purchase_price_per_unit"]);
    }

    if(empty($_POST["item_manufacture_month"]))
    {
        $error .= '<li>Manufacturing Month is required</li>';
    }
    else
    {
        $formdata['item_manufacture_month'] = trim($_POST["item_manufacture_month"]);
    }

    if(empty($_POST["item_manufacture_year"]))
    {
        $error .= '<li>Manufacturing Year is required</li>';
    }
    else
    {
        $formdata['item_manufacture_year'] = trim($_POST["item_manufacture_year"]);
    }

    if(empty($_POST["item_expired_month"]))
    {
        $error .= '<li>Expired Month is required</li>';
    }
    else
    {
        $formdata['item_expired_month'] = trim($_POST["item_expired_month"]);
    }

    if(empty($_POST["item_expired_year"]))
    {
        $error .= '<li>Expired Year is required</li>';
    }
    else
    {
        $formdata['item_expired_year'] = trim($_POST["item_expired_year"]);
    }

    if(empty($_POST["item_sale_price_per_unit"]))
    {
        $error .= '<li>Sale Price per unit is required</li>';
    }
    else
    {
        $formdata['item_sale_price_per_unit'] = trim($_POST["item_sale_price_per_unit"]);
    }

    if($error == '')
    {
        $item_purchase_id = $object->convert_data(trim($_POST["item_purchase_id"]), 'decrypt');

        $object->query = "
        SELECT item_purchase_qty FROM item_purchase_ims 
        WHERE item_purchase_id = '".$item_purchase_id."'
        ";

        $temp_result = $object->get_result();

        $item_purchase_qty = 0;

        foreach($temp_result as $temp_row)
        {
            $item_purchase_qty = $temp_row["item_purchase_qty"];
        }

        $total_cost = floatval($formdata['item_purchase_qty']) * floatval($formdata['item_purchase_price_per_unit']);

        $data = array(
            ':item_id'                      =>  $formdata['item_id'],
            ':supplier_id'                  =>  $formdata['supplier_id'],
            ':item_batch_no'                =>  $formdata['item_batch_no'],
            ':item_purchase_qty'            =>  $formdata['item_purchase_qty'],
            ':available_quantity'           =>  $formdata['item_purchase_qty'], 
            ':item_purchase_price_per_unit' =>  $formdata['item_purchase_price_per_unit'],
            ':item_purchase_total_cost'     =>  $total_cost,
            ':item_manufacture_month'       =>  $formdata['item_manufacture_month'],
            ':item_manufacture_year'        =>  $formdata['item_manufacture_year'],
            ':item_expired_month'           =>  $formdata['item_expired_month'],
            ':item_expired_year'            =>  $formdata['item_expired_year'], 
            ':item_sale_price_per_unit'     =>  $formdata['item_sale_price_per_unit'],
            ':item_purchase_id'             =>  $item_purchase_id
        );

        $object->query = "
            UPDATE item_purchase_ims 
            SET item_id = :item_id, 
            supplier_id = :supplier_id,
            item_batch_no = :item_batch_no, 
            item_purchase_qty = :item_purchase_qty, 
            available_quantity = :available_quantity, 
            item_purchase_price_per_unit = :item_purchase_price_per_unit, 
            item_purchase_total_cost = :item_purchase_total_cost, 
            item_manufacture_month = :item_manufacture_month, 
            item_manufacture_year = :item_manufacture_year, 
            item_expired_month = :item_expired_month, 
            item_expired_year = :item_expired_year, 
            item_sale_price_per_unit = :item_sale_price_per_unit  
            WHERE item_purchase_id = :item_purchase_id
            ";

        $object->execute($data);

        if($item_purchase_qty != $formdata['item_purchase_qty'])
        {
            $final_update_qty = 0;
            if($item_purchase_qty > $formdata['item_purchase_qty'])
            {
                $final_update_qty = $item_purchase_qty - $formdata['item_purchase_qty'];

                $object->query = "
                UPDATE item_ims 
                SET item_available_quantity = item_available_quantity - ".$final_update_qty." 
                WHERE item_id = '".$formdata['item_id']."'
                ";
            }
            else
            {
                $final_update_qty = $formdata['item_purchase_qty'] - $item_purchase_qty;

                $object->query = "
                UPDATE item_ims 
                SET item_available_quantity = item_available_quantity + ".$final_update_qty." 
                WHERE item_id = '".$formdata['item_id']."'
                ";
            }

            $object->execute();
        }

        header('location:product_purchase.php?msg=edit');
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $item_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');

    $item_id = $object->convert_data(trim($_GET["id"]), 'decrypt');

    $object->query = "
    SELECT item_purchase_qty FROM item_purchase_ims 
    WHERE item_purchase_id = '".$item_purchase_id."'
    ";

    $temp_result = $object->get_result();

    $item_purchase_qty = 0;

    foreach($temp_result as $temp_row)
    {
        $item_purchase_qty = $temp_row["item_purchase_qty"];
    }

    $status = trim($_GET["status"]);
    $data = array(
        ':item_purchase_status'      =>  $status,
        ':item_purchase_id'          =>  $item_purchase_id
    );

    $object->query = "
    UPDATE item_purchase_ims 
    SET item_purchase_status = :item_purchase_status 
    WHERE item_purchase_id = :item_purchase_id
    ";

    $object->execute($data);

    if($status == 'Disable')
    {
        $object->query = "
        UPDATE item_ims 
        SET item_available_quantity = item_available_quantity - ".$item_purchase_qty." 
        WHERE item_id = '".$item_id."'
        ";
    }
    else
    {
        $object->query = "
        UPDATE item_ims 
        SET item_available_quantity = item_available_quantity + ".$item_purchase_qty." 
        WHERE item_id = '".$item_id."'
        ";
    }

    $object->execute();

    header('location:product_purchase.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Purchase Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="product_purchase.php">Purchase Management</a></li>
                                <li class="breadcrumb-item active">Add Purchase</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Add Purchase
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Select Product</label>
                                                    <select name="item_id" class="form-control input-lg selectpicker" id="item_id" data-live-search="true" data-size="5" data-allow-clear="true" data-noneSelectedText="Select Product">
                                                        <?php echo $object->fill_item(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["item_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("item_id").value = "'.$_POST["item_id"].'"
                                                        </script>
                                                        ';
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Select Supplier</label>
                                                    <select name="supplier_id" class="form-control selectpicker" id="supplier_id" data-live-search="true" data-size="5" data-allow-clear="true">
                                                        <?php echo $object->fill_supplier(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["supplier_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("supplier_id").value = "'.$_POST["supplier_id"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_purchase_qty" type="number" placeholder="Enter Quantity" name="item_purchase_qty" value="<?php if(isset($_POST["item_purchase_qty"])) echo $_POST["item_purchase_qty"]; ?>" />
                                                    <label for="item_purchase_qty">Quantity</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="item_purchase_price_per_unit" step=".01" value="<?php if(isset($_POST["item_purchase_price_per_unit"])) echo $_POST["item_purchase_price_per_unit"]; ?>" />
                                                    <label for="item_purchase_price_per_unit">Purchase Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_manufacture_month" class="form-control" id="item_manufacture_month">
                                                                <option value="">Select</option>
                                                                <option value="01">January</option>
                                                                <option value="02">February</option>
                                                                <option value="03">March</option>
                                                                <option value="04">April</option>
                                                                <option value="05">May</option>
                                                                <option value="06">June</option>
                                                                <option value="07">July</option>
                                                                <option value="08">August</option>
                                                                <option value="09">September</option>
                                                                <option value="10">October</option>
                                                                <option value="11">November</option>
                                                                <option value="12">December</option>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["item_manufacture_month"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("item_manufacture_month").value = "'.$_POST["item_manufacture_month"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="item_manufacture_month">Mfg. Month</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_manufacture_year" class="form-control" id="item_manufacture_year">
                                                                <option value="">Select</option>
                                                                <?php 
                                                                for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                {
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["item_manufacture_year"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("item_manufacture_year").value = "'.$_POST["item_manufacture_year"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="item_manufacture_year">Mfg. Year</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_expired_month" class="form-control" id="item_expired_month">
                                                                <option value="">Select</option>
                                                                <option value="01">January</option>
                                                                <option value="02">February</option>
                                                                <option value="03">March</option>
                                                                <option value="04">April</option>
                                                                <option value="05">May</option>
                                                                <option value="06">June</option>
                                                                <option value="07">July</option>
                                                                <option value="08">August</option>
                                                                <option value="09">September</option>
                                                                <option value="10">October</option>
                                                                <option value="11">November</option>
                                                                <option value="12">December</option>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["item_expired_month"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("item_expired_month").value = "'.$_POST["item_expired_month"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="item_expired_month">Expiry Month</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_expired_year" class="form-control" id="item_expired_year">
                                                                <option value="">Select</option>
                                                                <?php 
                                                                for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                {
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <?php
                                                            if(isset($_POST["item_expired_year"]))
                                                            {
                                                                echo '
                                                                <script>
                                                                document.getElementById("item_expired_year").value = "'.$_POST["item_expired_year"].'"
                                                                </script>
                                                                ';
                                                            }
                                                            ?>
                                                            <label for="item_expired_year">Expiry Year</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_batch_no" type="text" placeholder="Enter Batch Number" name="item_batch_no" value="<?php if(isset($_POST["item_batch_no"])) echo $_POST["item_batch_no"]; ?>" />
                                                    <label for="item_batch_no">Batch No.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="item_sale_price_per_unit" step=".01" value="<?php if(isset($_POST["item_sale_price_per_unit"])) echo $_POST["item_sale_price_per_unit"]; ?>" />
                                                    <label for="item_sale_price_per_unit">Sale Price per Unit</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_purchase" class="btn btn-success" value="Add" />
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>

                            $('.selectpicker').selectpicker();

                            </script>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $item_purchase_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($item_purchase_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM item_purchase_ims 
                                    WHERE item_purchase_id = '$item_purchase_id'
                                    ";

                                    $purchase_result = $object->get_result();

                                    foreach($purchase_result as $purchase_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="product_purchase.php">Purchase Management</a></li>
                                    <li class="breadcrumb-item active">Edit Purchase Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Purchase Data
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Select Product</label>
                                                        <select name="item_id" class="form-control input-lg selectpicker" id="item_id" data-live-search="true" data-size="5" data-allow-clear="true" data-noneSelectedText="Select Product">
                                                            <?php echo $object->fill_item(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Select Supplier</label>
                                                        <select name="supplier_id" class="form-control selectpicker" id="supplier_id" data-live-search="true" data-size="5" data-allow-clear="true">
                                                            <?php echo $object->fill_supplier(); ?>
                                                        </select>
                                                    <?php
                                                    if(isset($_POST["supplier_id"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("supplier_id").value = "'.$_POST["supplier_id"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <div class="form-floating mb-3">
                                                            <input class="form-control" id="item_purchase_qty" type="number" placeholder="Enter Quantity" name="item_purchase_qty" value="<?php echo $purchase_row["item_purchase_qty"]; ?>" />
                                                            <label for="item_purchase_qty">Medicine Quantity</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_purchase_price_per_unit" type="number" placeholder="Enter Purchase Price per Unit" name="item_purchase_price_per_unit" step=".01" value="<?php echo $purchase_row["item_purchase_price_per_unit"]; ?>" />
                                                        <label for="item_purchase_price_per_unit">Purchase Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_manufacture_month" class="form-control" id="item_manufacture_month">
                                                                    <option value="">Select</option>
                                                                    <option value="01">January</option>
                                                                    <option value="02">February</option>
                                                                    <option value="03">March</option>
                                                                    <option value="04">April</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">June</option>
                                                                    <option value="07">July</option>
                                                                    <option value="08">August</option>
                                                                    <option value="09">September</option>
                                                                    <option value="10">October</option>
                                                                    <option value="11">November</option>
                                                                    <option value="12">December</option>
                                                                </select>
                                                                <label for="item_manufacture_month">Mfg. Month</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_manufacture_year" class="form-control" id="item_manufacture_year">
                                                                    <option value="">Select</option>
                                                                    <?php 
                                                                    for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                    {
                                                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="item_manufacture_year">Mfg. Year</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_expired_month" class="form-control" id="item_expired_month">
                                                                    <option value="">Select</option>
                                                                    <option value="01">January</option>
                                                                    <option value="02">February</option>
                                                                    <option value="03">March</option>
                                                                    <option value="04">April</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">June</option>
                                                                    <option value="07">July</option>
                                                                    <option value="08">August</option>
                                                                    <option value="09">September</option>
                                                                    <option value="10">October</option>
                                                                    <option value="11">November</option>
                                                                    <option value="12">December</option>
                                                                </select>
                                                                <label for="item_expired_month">Expiry Month</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_expired_year" class="form-control" id="item_expired_year">
                                                                    <option value="">Select</option>
                                                                    <?php 
                                                                    for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                    {
                                                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="item_expired_year">Expiry Year</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_batch_no" type="text" placeholder="Enter Batch Number" name="item_batch_no" value="<?php echo $purchase_row["item_batch_no"]; ?>" />
                                                        <label for="item_batch_no">Medicine Batch No.</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_sale_price_per_unit" type="number" placeholder="Enter Sale Price per Unit" name="item_sale_price_per_unit" step=".01" value="<?php echo $purchase_row["item_sale_price_per_unit"]; ?>" />
                                                        <label for="item_sale_price_per_unit">Sale Price per Unit</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="item_purchase_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_purchase" class="btn btn-primary" value="Edit" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <script>
                                $('#item_id').selectpicker('val','<?php echo $purchase_row["item_id"]; ?>');
                                $('#supplier_id').selectpicker('val','<?php echo $purchase_row["supplier_id"]; ?>');
                                document.getElementById('item_manufacture_month').value = "<?php echo $purchase_row["item_manufacture_month"]; ?>";
                                document.getElementById('item_manufacture_year').value = "<?php echo $purchase_row["item_manufacture_year"]; ?>";
                                document.getElementById('item_manufacture_month').value = "<?php echo $purchase_row["item_manufacture_month"]; ?>";
                                document.getElementById('item_expired_month').value = "<?php echo $purchase_row["item_expired_month"]; ?>";
                                document.getElementById('item_expired_year').value = "<?php echo $purchase_row["item_expired_year"]; ?>";
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
                                <li class="breadcrumb-item active">Purchase Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Product Purchase Detail Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Purchase Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Purchase Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Purchase Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Purchase Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="product_purchase.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="purchase_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Batch No.</th>
                                                <th>Supplier</th>
                                                <th>Quantity</th>
                                                <th>Available Qty.</th>
                                                <th>Price per Unit</th>
                                                <th>Total Cost</th>
                                                <th>Mfg. Date</th>
                                                <th>Expiry Date</th>
                                                <th>Sale Price</th>
                                                <th>Purchase Date</th>
                                                <th>Status</th>
                                                <!--<th>Added On</th>
                                                <th>Updated On</th>!-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>
                            
                            var dataTable = $('#purchase_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_purchase"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[12],
                                        "orderable":false
                                    }
                                ],
                                "pageLength": 10
                            });

                            function delete_data(code, status, id)
                            {
                                var new_status = 'Enable';
                                if(status == 'Enable')
                                {
                                    new_status = 'Disable';
                                }
                                if(confirm("Are you sure you want to "+new_status+" this Medicine Purchase Details?"))
                                {
                                    window.location.href="product_purchase.php?action=delete&code="+code+"&status="+new_status+"&id="+id+"";
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