<?php

//user.php

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

$message = '';

$error = '';

if(isset($_POST["add_company"]))
{
    $formdata = array();

    if(empty($_POST["company_name"]))
    {
        $error .= '<li>Company Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["company_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['company_name'] = trim($_POST["company_name"]);
        }
    }

    if(empty($_POST["company_short_name"]))
    {
        $error .= '<li>Company Short Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[A-Za-z']*$/", $_POST["company_short_name"]))
        {
            $error .= '<li>Only letters allowed</li>';
        }
        else
        {
            if(strlen($_POST["company_short_name"]) > 3 && strlen($_POST["company_short_name"]) < 3)
            {
                $error .= '<li>Company Short Name must be only 3 characters</li>';
            }
            else
            {
                $formdata['company_short_name'] = strtoupper(trim($_POST["company_short_name"]));
            }
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM item_manufacuter_company_ims 
        WHERE company_name = '".$formdata['company_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Company Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':company_name'             =>  $formdata['company_name'],
                ':company_short_name'       =>  $formdata['company_short_name'],
                ':company_status'           =>  'Enable',
                ':company_added_datetime'   =>  $object->now,
                ':company_updated_datetime' =>  $object->now
            );

            $object->query = "
            INSERT INTO item_manufacuter_company_ims 
            (company_name, company_short_name, company_status, company_added_datetime, company_updated_datetime) 
            VALUES (:company_name, :company_short_name, :company_status, :company_added_datetime, :company_updated_datetime)
            ";

            $object->execute($data);

            header('location:company.php?msg=add');
        }
    }
}

if(isset($_POST["edit_company"]))
{
    $formdata = array();

    if(empty($_POST["company_name"]))
    {
        $error .= '<li>Company Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["company_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['company_name'] = trim($_POST["company_name"]);
        }
    }

    if(empty($_POST["company_short_name"]))
    {
        $error .= '<li>Company Short Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[A-Za-z']*$/", $_POST["company_short_name"]))
        {
            $error .= '<li>Only letters allowed</li>';
        }
        else
        {
            if(strlen($_POST["company_short_name"]) > 3 && strlen($_POST["company_short_name"]) < 3)
            {
                $error .= '<li>Company Short Name must be only 3 characters</li>';
            }
            else
            {
                $formdata['company_short_name'] = strtoupper(trim($_POST["company_short_name"]));
            }
        }
    }

    if($error == '')
    {
        $item_manufacuter_company_id = $object->convert_data(trim($_POST["item_manufacuter_company_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM item_manufacuter_company_ims 
        WHERE company_name = '".$formdata['company_name']."' 
        AND item_manufacuter_company_id != '".$item_manufacuter_company_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Company Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':company_name'                     =>  $formdata['company_name'],
                ':company_short_name'               =>  $formdata['company_short_name'],
                ':company_updated_datetime'         =>  $object->now,
                ':item_manufacuter_company_id'  =>  $item_manufacuter_company_id
            );

            $object->query = "
            UPDATE item_manufacuter_company_ims 
            SET company_name = :company_name, 
            company_short_name = :company_short_name, 
            company_updated_datetime = :company_updated_datetime  
            WHERE item_manufacuter_company_id = :item_manufacuter_company_id
            ";

            $object->execute($data);

            header('location:company.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $item_manufacuter_company_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':company_status'                   =>  $status,
        ':item_manufacuter_company_id'  =>  $item_manufacuter_company_id
    );

    $object->query = "
    UPDATE item_manufacuter_company_ims 
    SET company_status = :company_status 
    WHERE item_manufacuter_company_id = :item_manufacuter_company_id
    ";

    $object->execute($data);

    header('location:company.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Company Management</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="company.php">Medicine Company Management</a></li>
                                <li class="breadcrumb-item active">Add Company</li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    if(isset($error) && $error != '')
                                    {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                    }
                                    ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-plus"></i> Add Company
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="company_name" type="text" placeholder="Enter Company Name" name="company_name" value="<?php if(isset($_POST["company_name"])) echo $_POST["company_name"]; ?>" />
                                                    <label for="company_name">Company Name</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="company_short_name" type="text" placeholder="Enter Company Short Name" name="company_short_name" value="<?php if(isset($_POST["company_short_name"])) echo $_POST["company_short_name"]; ?>" maxlength="3" style="text-transform:uppercase" />
                                                    <label for="company_short_name">Company Short Name</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_company" class="btn btn-success" value="Add" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                            else if($_GET["action"] == 'edit')
                            {
                                $item_manufacuter_company_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($item_manufacuter_company_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM item_manufacuter_company_ims 
                                    WHERE item_manufacuter_company_id = '$item_manufacuter_company_id'
                                    ";

                                    $company_result = $object->get_result();

                                    foreach($company_result as $company_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="company.php">Medicine Company Management</a></li>
                                    <li class="breadcrumb-item active">Edit Company</li>
                                </ol>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        if(isset($error) && $error != '')
                                        {
                                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                        }
                                        ?>
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <i class="fas fa-user-edit"></i> Edit Company
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="company_name" type="text" placeholder="Enter Company Name" name="company_name" value="<?php echo $company_row["company_name"]; ?>" />
                                                        <label for="company_name">Company Name</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="company_short_name" type="text" placeholder="Enter Company Short Name" name="company_short_name" value="<?php echo $company_row["company_short_name"]; ?>" maxlength="3" style="text-transform:uppercase" />
                                                        <label for="company_short_name">Company Short Name</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="item_manufacuter_company_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_company" class="btn btn-primary" value="Edit" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                <li class="breadcrumb-item active">Company Management</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Company Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Company Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Company Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Company Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Company Management
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="company.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Add</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="company_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Short Name</th>
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
                            
                            var dataTable = $('#company_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_company"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[4],
                                        "orderable":false
                                    }
                                ],
                                "pageLength": 25
                            });

                            function delete_data(code, status)
                            {
                                var new_status = 'Enable';
                                if(status == 'Enable')
                                {
                                    new_status = 'Disable';
                                }
                                if(confirm("Are you sure you want to "+new_status+" this Company Name?"))
                                {
                                    window.location.href="company.php?action=delete&code="+code+"&status="+new_status+"";
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