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

if(isset($_POST["add_tax"]))
{
    $formdata = array();

    if(empty($_POST["tax_name"]))
    {
        $error .= '<li>Le nom de la taxe est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["tax_name"]))
        {
            $error .= '<li>les lettres, les chiffres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['tax_name'] = trim($_POST["tax_name"]);
        }
    }

    if(empty($_POST["tax_percentage"]))
    {
        $error .= '<li>Le pourcentage de taxe est requis</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.' ]*$/", $_POST["tax_percentage"]))
        {
            $error .= '<li>les chiffres sont autorisés</li>';
        }
        else
        {
            $formdata['tax_percentage'] = trim($_POST["tax_percentage"]);
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM tax_ims 
        WHERE tax_name = '".$formdata['tax_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Le nom de taxe existe déjà</li>';
        }
        else
        {
            $data = array(
                ':tax_name'         =>  $formdata['tax_name'],
                ':tax_percentage'   =>  $formdata['tax_percentage'],
                ':tax_status'       =>  'Enable',
                ':tax_added_on'     =>  $object->now,
                ':tax_updated_on'   =>  $object->now
            );

            $object->query = "
            INSERT INTO tax_ims 
            (tax_name, tax_percentage, tax_status, tax_added_on, tax_updated_on) 
            VALUES (:tax_name, :tax_percentage, :tax_status, :tax_added_on, :tax_updated_on)
            ";

            $object->execute($data);

            header('location:tax.php?msg=add');
        }
    }
}

if(isset($_POST["edit_tax"]))
{
    $formdata = array();

    if(empty($_POST["tax_name"]))
    {
        $error .= '<li>Le nom de la taxe est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["tax_name"]))
        {
            $error .= '<li>les lettres, les chiffres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['tax_name'] = trim($_POST["tax_name"]);
        }
    }

    if(empty($_POST["tax_percentage"]))
    {
        $error .= '<li>Le pourcentage de taxe est requis</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.' ]*$/", $_POST["tax_percentage"]))
        {
            $error .= '<li>les chiffres sont autorisés</li>';
        }
        else
        {
            $formdata['tax_percentage'] = trim($_POST["tax_percentage"]);
        }
    }

    if($error == '')
    {
        $tax_id = $object->convert_data(trim($_POST["tax_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM tax_ims 
        WHERE tax_name = '".$formdata['tax_name']."' 
        AND tax_id != '".$tax_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Le nom de taxe existe déjà</li>';
        }
        else
        {
            $data = array(
                ':tax_name'         =>  $formdata['tax_name'],
                ':tax_percentage'   =>  $formdata['tax_percentage'],
                ':tax_updated_on'   =>  $object->now,
                ':tax_id'           =>  $tax_id
            );

            $object->query = "
            UPDATE tax_ims 
            SET tax_name = :tax_name, 
            tax_percentage = :tax_percentage, 
            tax_updated_on = :tax_updated_on 
            WHERE tax_id = :tax_id
            ";

            $object->execute($data);

            header('location:tax.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $tax_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':tax_status'      =>  $status,
        ':tax_id'          =>  $tax_id
    );

    $object->query = "
    UPDATE tax_ims 
    SET tax_status = :tax_status 
    WHERE tax_id = :tax_id
    ";

    $object->execute($data);

    header('location:tax.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Gestion fiscale</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="tax.php">Gestion fiscale</a></li>
                                <li class="breadcrumb-item active">Gestion fiscale</li>
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
                                            <i class="fas fa-user-plus"></i> Ajouter un nouveau détail fiscal
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="tax_name" type="text" placeholder="Entrez le nom de la taxe" name="tax_name" value="<?php if(isset($_POST["tax_name"])) echo $_POST["tax_name"]; ?>" />
                                                    <label for="tax_name">Nom fiscal</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="tax_percentage" type="number" placeholder="Entrez le pourcentage de taxe" name="tax_percentage" value="<?php if(isset($_POST["tax_percentage"])) echo $_POST["tax_percentage"]; ?>" />
                                                    <label for="tax_percentage">Pourcentage de taxe</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_tax" class="btn btn-success" value="Ajouter" />
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
                                $tax_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($tax_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM tax_ims 
                                    WHERE tax_id = '$tax_id'
                                    ";

                                    $tax_result = $object->get_result();

                                    foreach($tax_result as $tax_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="tax.php">Gestion fiscale</a></li>
                                    <li class="breadcrumb-item active">Modifier la taxe</li>
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
                                                <i class="fas fa-user-edit"></i>Modifier les détails fiscaux
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="tax_name" type="text" placeholder="Entrez le nom de la taxe" name="tax_name" value="<?php echo $tax_row["tax_name"]; ?>" />
                                                        <label for="tax_name">Nom fiscal</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="tax_percentage" type="number" placeholder="Entrez le pourcentage de taxe" name="tax_percentage" value="<?php echo $tax_row["tax_percentage"]; ?>" />
                                                        <label for="tax_percentage">Pourcentage de taxe</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="tax_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_tax" class="btn btn-primary" value="Modifier" />
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
                                <li class="breadcrumb-item active">Gestion fiscale</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouvelle taxe ajoutée<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Données fiscales modifiées <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification du statut fiscal à Désactiver <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification du statut fiscal à Activer <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Gestion fiscale
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="tax.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="tax_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom fiscal</th>
                                                <th>Pourcentage</th>
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
                            
                            var dataTable = $('#tax_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_tax"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[5],
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
                                if(confirm("Are you sure you want to "+new_status+" this Tax Detail?"))
                                {
                                    window.location.href="tax.php?action=delete&code="+code+"&status="+new_status+"";
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