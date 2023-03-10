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

$message = '';

$error = '';

if(isset($_POST["add_location_rack"]))
{
    $formdata = array();

    if(empty($_POST["location_rack_name"]))
    {
        $error .= '<li>Location Rack Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["location_rack_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM location_rack_ims 
        WHERE location_rack_name = '".$formdata['location_rack_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Location Rack Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':location_rack_name'        =>  $formdata['location_rack_name'],
                ':location_rack_status'      =>  'Enable',
                ':location_rack_datetime'    =>  $object->now
            );

            $object->query = "
            INSERT INTO location_rack_ims 
            (location_rack_name, location_rack_status, location_rack_datetime) 
            VALUES (:location_rack_name, :location_rack_status, :location_rack_datetime)
            ";

            $object->execute($data);

            header('location:location_rack.php?msg=add');
        }
    }
}

if(isset($_POST["edit_location_rack"]))
{
    $formdata = array();

    if(empty($_POST["location_rack_name"]))
    {
        $error .= '<li>Location Rack Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["location_rack_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['location_rack_name'] = trim($_POST["location_rack_name"]);
        }
    }

    if($error == '')
    {
        $location_rack_id = $object->convert_data(trim($_POST["location_rack_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM location_rack_ims 
        WHERE location_rack_name = '".$formdata['location_rack_name']."' 
        AND location_rack_id != '".$location_rack_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Location Rack Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':location_rack_name'    =>  $formdata['location_rack_name'],
                ':location_rack_id'      =>  $location_rack_id
            );

            $object->query = "
            UPDATE location_rack_ims 
            SET location_rack_name = :location_rack_name 
            WHERE location_rack_id = :location_rack_id
            ";

            $object->execute($data);

            header('location:location_rack.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $location_rack_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':location_rack_status'      =>  $status,
        ':location_rack_id'          =>  $location_rack_id
    );

    $object->query = "
    UPDATE location_rack_ims 
    SET location_rack_status = :location_rack_status 
    WHERE location_rack_id = :location_rack_id
    ";

    $object->execute($data);

    header('location:location_rack.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Gestion des racks d'emplacement</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="location_rack.php">Gestion des racks d'emplacement</a></li>
                                <li class="breadcrumb-item active">Ajouter un rack d'emplacement</li>
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
                                            <i class="fas fa-user-plus"></i> Ajouter un nouveau rack d'emplacement
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="location_rack_name" type="text" placeholder="Entrez le nom du rack d'emplacement" name="location_rack_name" value="<?php if(isset($_POST["location_rack_name"])) echo $_POST["location_rack_name"]; ?>" />
                                                    <label for="location_rack_name">Emplacement Nom du rack</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_location_rack" class="btn btn-success" value="Ajouter" />
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
                                $location_rack_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($location_rack_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM location_rack_ims 
                                    WHERE location_rack_id = '$location_rack_id'
                                    ";

                                    $location_rack_result = $object->get_result();

                                    foreach($location_rack_result as $location_rack_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="location_rack.php">Gestion des racks d'emplacement</a></li>
                                    <li class="breadcrumb-item active">Modifier le rack d'emplacement</li>
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
                                                <i class="fas fa-user-edit"></i> Modifier les d??tails du rack d'emplacement
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="location_rack_name" type="text" placeholder="Entrez le nom du rack d'emplacement" name="location_rack_name" value="<?php echo $location_rack_row["location_rack_name"]; ?>" />
                                                        <label for="location_rack_name">Emplacement Nom du rack</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="location_rack_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_location_rack" class="btn btn-primary" value="Edit" />
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
                                <li class="breadcrumb-item active">Gestion des racks d'emplacement</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouveau rack d emplacement ajout??<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Donn??es de rack d emplacement modifi??es <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l ??tat du suivi de l emplacement ?? D??sactiv?? <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l ??tat du rack d emplacement sur Activer <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Gestion des racks d'emplacement
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="location_rack.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="location_rack_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Emplacement Nom du rack</th>
                                                <th>Statut</th>
                                                <th>Date et heure</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>
                            
                            var dataTable = $('#location_rack_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_location_rack"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[3],
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
                                if(confirm("Are you sure you want to "+new_status+" this Location Rack?"))
                                {
                                    window.location.href="location_rack.php?action=delete&code="+code+"&status="+new_status+"";
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