<?php

//location_rack.php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
    header('location:login.php');
} 

if(!$object->is_master_user())
{
    header('location:index.php');
}

$object->query = "
    SELECT * FROM item_ims 
    INNER JOIN category_msbs 
    ON category_msbs.category_id = item_ims.item_category 
    INNER JOIN  medicine_manufacuter_company_msbs 
    ON  medicine_manufacuter_company_msbs.medicine_manufacuter_company_id = item_ims.item_manufactured_by 
    INNER JOIN location_rack_msbs 
    ON location_rack_msbs.location_rack_id = item_ims.item_location_rack 
    ORDER BY item_ims.item_name ASC
";

$result = $object->get_result();

$message = '';

$error = '';

if(isset($_POST["add_product"]))
{
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    $formdata = array();

    if(empty($_POST["item_name"]))
    {
        $error .= '<li>Product Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["item_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['item_name'] = trim($_POST["item_name"]);
        }
    }

    /*if(empty($_POST["medicine_pack_type"]))
    {
        $error .= '<li>Product Pack Type is required</li>';
    }
    else
    {
        $formdata['medicine_pack_type'] = trim($_POST["medicine_pack_type"]);
    }*/

    if(empty($_POST["item_manufactured_by"]))
    {
        $error .= '<li>Manufacturing Company is required</li>';
    }
    else
    {
        $formdata['item_manufactured_by'] = trim($_POST["item_manufactured_by"]);
    }

    if(empty($_POST["item_category"]))
    {
        $error .= '<li>Category is required</li>';
    }
    else
    {
        $formdata['item_category'] = trim($_POST["item_category"]);
    }

    if(empty($_POST["item_location_rack"]))
    {
        $error .= '<li>Location Rack is required</li>';
    }
    else
    {
        $formdata['item_location_rack'] = trim($_POST["item_location_rack"]);
    }    

    if($error == '')
    {
        $object->query = "
        SELECT * FROM item_ims 
        WHERE item_name = '".$formdata['item_name']."' 
        AND item_manufactured_by = '".$formdata['item_manufactured_by']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Product Already Exists</li>';
        }
        else
        {
            $data = array(
                ':item_name'                =>  $formdata['item_name'],
                ':item_manufactured_by'     =>  $formdata['item_manufactured_by'],
                ':item_category'            =>  $formdata['item_category'],
                ':item_available_quantity'  =>  0,
                ':item_location_rack'       =>  $formdata['item_location_rack'],
                ':item_status'              =>  'Enable',
                ':item_add_datetime'        =>  $object->now,
                ':item_update_datetime'     =>  $object->now
            );

            $object->query = "
            INSERT INTO item_ims 
            (item_name, item_manufactured_by, item_category, item_available_quantity, item_location_rack, item_status, item_add_datetime, item_update_datetime) 
            VALUES (:item_name, :item_manufactured_by, :item_category, :item_available_quantity, :item_location_rack, :item_status, :item_add_datetime, :item_update_datetime)
            ";

            $object->execute($data);

            header('location:product.php?msg=add');
        }
    }
}

if(isset($_POST["edit_product"]))
{
    $formdata = array();

    if(empty($_POST["item_name"]))
    {
        $error .= '<li>Product Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["item_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['item_name'] = trim($_POST["item_name"]);
        }
    }

    if(empty($_POST["item_manufactured_by"]))
    {
        $error .= '<li>Manufacturing Company is required</li>';
    }
    else
    {
        $formdata['item_manufactured_by'] = trim($_POST["item_manufactured_by"]);
    }

    if(empty($_POST["item_category"]))
    {
        $error .= '<li>Category is required</li>';
    }
    else
    {
        $formdata['item_category'] = trim($_POST["item_category"]);
    }

    if(empty($_POST["item_location_rack"]))
    {
        $error .= '<li>Location Rack is required</li>';
    }
    else
    {
        $formdata['item_location_rack'] = trim($_POST["item_location_rack"]);
    }

    if($error == '')
    {
        $item_id = $object->convert_data(trim($_POST["item_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM item_ims 
        WHERE item_name = '".$formdata['item_name']."' 
        AND item_manufactured_by = '".$formdata['item_manufactured_by']."'
        AND item_id != '".$item_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Product Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':item_name'                =>  $formdata['item_name'],
                ':item_manufactured_by'     =>  $formdata['item_manufactured_by'],
                ':item_category'            =>  $formdata['item_category'],
                ':item_location_rack'       =>  $formdata['item_location_rack'],
                ':item_update_datetime'     =>  $object->now,
                ':item_id'                  =>  $item_id
            );

            print_r($data);

            $object->query = "
            UPDATE item_ims 
            SET item_name = :item_name, 
            item_manufactured_by = :item_manufactured_by, 
            item_category = :item_category, 
            item_location_rack = :item_location_rack, 
            item_update_datetime = :item_update_datetime 
            WHERE item_id = :item_id
            ";

            $object->execute($data);

            header('location:product.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $item_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':item_status'          =>  $status,
        ':item_update_datetime' =>  $object->now,
        ':item_id'              =>  $item_id
    );

    $object->query = "
    UPDATE item_ims 
    SET item_status = :item_status, 
    item_update_datetime = :item_update_datetime 
    WHERE item_id = :item_id
    ";

    $object->execute($data);

    header('location:product.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Product Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="product.php">Product Management</a></li>
                                <li class="breadcrumb-item active">Add Product</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Add Product
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_name" type="text" placeholder="Enter Product Name" name="item_name" value="<?php if(isset($_POST["item_name"])) echo $_POST["item_name"]; ?>" />
                                                    <label for="item_name">Product Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="item_manufactured_by" class="form-control" id="item_manufactured_by">
                                                        <?php echo $object->fill_company(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["item_manufactured_by"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("item_manufactured_by").value = "'.$_POST["item_manufactured_by"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="item_manufactured_by">Product Manufacture By</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="item_category" class="form-control" id="item_category">
                                                        <?php echo $object->fill_category(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["item_category"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("item_category").value = "'.$_POST["item_category"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="item_category">Category</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="item_location_rack" class="form-control" id="item_location_rack">
                                                        <?php echo $object->fill_location_rack(); ?>
                                                    </select>
                                                    <?php
                                                    if(isset($_POST["item_location_rack"]))
                                                    {
                                                        echo '
                                                        <script>
                                                        document.getElementById("item_location_rack").value = "'.$_POST["item_location_rack"].'"
                                                        </script>
                                                        ';
                                                    }
                                                    ?>
                                                    <label for="item_location_rack">Location Rack</label>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="tax_id" id="tax_id" class="form-control selectpicker"data-live-search="true" data-live-search-style="begins" title="Please select a lunch ...">
                                                    <?php //echo $object->fill_tax(); ?>
                                                    </select>
                                                    <label for="tax_id">Tax</label>
                                                </div>
                                            </div>
                                        </div>!-->
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_product" class="btn btn-success" value="Add" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $item_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($item_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM item_ims 
                                    WHERE item_id = '$item_id'
                                    ";

                                    $product_result = $object->get_result();

                                    foreach($product_result as $product_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="product.php">Product Management</a></li>
                                    <li class="breadcrumb-item active">Edit Product Data</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Edit Product
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_name" type="text" placeholder="Enter Product Name" name="item_name" value="<?php echo $product_row["item_name"]; ?>" />
                                                        <label for="item_name">Product Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="item_manufactured_by" class="form-control" id="item_manufactured_by">
                                                            <?php echo $object->fill_company(); ?>
                                                        </select>
                                                        <label for="item_manufactured_by">Product Manufacture By</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="item_category" class="form-control" id="item_category">
                                                            <?php echo $object->fill_category(); ?>
                                                        </select>
                                                        <label for="item_category">Category</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <select name="item_location_rack" class="form-control" id="item_location_rack">
                                                            <?php echo $object->fill_location_rack(); ?>
                                                        </select>
                                                        <label for="item_location_rack">Location Rack</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="item_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_product" class="btn btn-primary" value="Edit" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <script>
                                document.getElementById('item_manufactured_by').value = "<?php echo $product_row["item_manufactured_by"]; ?>";
                                document.getElementById('item_category').value = "<?php echo $product_row["item_category"]; ?>";
                                document.getElementById('item_location_rack').value = "<?php echo $product_row["item_location_rack"]; ?>";

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
                                <li class="breadcrumb-item active">Product Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Product Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Product Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Product Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="product.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="product_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Company</th>
                                                <th>Category</th>
                                                <th>Location Rack</th>
                                                <th>Available Quantity</th>                                                
                                                <th>Status</th>
                                                <th>Added On</th>
                                                <th>Updated On</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>

                            var dataTable = $('#product_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_product"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[8],
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
                                if(confirm("Are you sure you want to "+new_status+" this Product?"))
                                {
                                    window.location.href="product.php?action=delete&code="+code+"&status="+new_status+"";
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