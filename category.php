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

if(isset($_POST["add_category"]))
{
    $formdata = array();

    if(empty($_POST["category_name"]))
    {
        $error .= '<li>Category Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["category_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['category_name'] = trim($_POST["category_name"]);
        }
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM category_ims 
        WHERE category_name = '".$formdata['category_name']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Category Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':category_name'        =>  $formdata['category_name'],
                ':category_status'      =>  'Enable',
                ':category_datetime'    =>  $object->now
            );

            $object->query = "
            INSERT INTO category_ims 
            (category_name, category_status, category_datetime) 
            VALUES (:category_name, :category_status, :category_datetime)
            ";

            $object->execute($data);

            header('location:category.php?msg=add');
        }
    }
}

if(isset($_POST["edit_category"]))
{
    $formdata = array();

    if(empty($_POST["category_name"]))
    {
        $error .= '<li>Category Name is required</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["category_name"]))
        {
            $error .= '<li>Only letters, Numbers and white space allowed</li>';
        }
        else
        {
            $formdata['category_name'] = trim($_POST["category_name"]);
        }
    }

    if($error == '')
    {
        $category_id = $object->convert_data(trim($_POST["category_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM category_ims 
        WHERE category_name = '".$formdata['category_name']."' 
        AND category_id != '".$category_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>Category Name Already Exists</li>';
        }
        else
        {
            $data = array(
                ':category_name'    =>  $formdata['category_name'],
                ':category_id'      =>  $category_id
            );

            $object->query = "
            UPDATE category_ims 
            SET category_name = :category_name 
            WHERE category_id = :category_id
            ";

            $object->execute($data);

            header('location:category.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $category_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':category_status'      =>  $status,
        ':category_id'          =>  $category_id
    );

    $object->query = "
    UPDATE category_ims 
    SET category_status = :category_status 
    WHERE category_id = :category_id
    ";

    $object->execute($data);

    header('location:category.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Gestion des catégories</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="category.php">Gestion des catégories</a></li>
                                <li class="breadcrumb-item active">Ajouter une catégorie</li>
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
                                            <i class="fas fa-user-plus"></i> Ajouter une nouvelle catégorie
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="category_name" type="text" placeholder="Entrez le nom de la catégorie" name="category_name" value="<?php if(isset($_POST["category_name"])) echo $_POST["category_name"]; ?>" />
                                                    <label for="category_name">Nom de catégorie</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_category" class="btn btn-success" value="Ajouter" />
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
                                $category_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($category_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM category_ims 
                                    WHERE category_id = '$category_id'
                                    ";

                                    $category_result = $object->get_result();

                                    foreach($category_result as $category_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="category.php">Gestion des catégories</a></li>
                                    <li class="breadcrumb-item active">Modifier la catégorie</li>
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
                                                <i class="fas fa-user-edit"></i> Modifier les détails de la catégorie
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="category_name" type="text" placeholder="Entrez le nom de la catégorie" name="category_name" value="<?php echo $category_row["category_name"]; ?>" />
                                                        <label for="category_name">Nom de catégorie</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="category_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_category" class="btn btn-primary" value="Edit" />
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
                                <li class="breadcrumb-item active">Gestion des catégories</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouvelle catégorie ajoutée<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Données de catégorie modifiées <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l état de la catégorie à Désactiver <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l état de la catégorie à Activer <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Gestion des catégories
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="category.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="category_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom de catégorie</th>
                                                <th>Statut</th>
                                                <th>Date et heure</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>
                            
                            var categorydataTable = $('#category_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_category"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[3],
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
                                if(confirm("Are you sure you want to "+new_status+" this Category?"))
                                {
                                    window.location.href="category.php?action=delete&code="+code+"&status="+new_status+"";
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