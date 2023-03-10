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

if(isset($_POST["add_user"]))
{
    $formdata = array();

    if(empty($_POST["user_name"]))
    {
        $error .= '<li>Nom d utilisateur est nécessaire</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["user_name"]))
        {
            $error .= '<li>Seules les lettres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['user_name'] = trim($_POST["user_name"]);
        }
    }

    if(empty($_POST["user_email"]))
    {
        $error .= '<li>Adresse e-mail est nécessaire</li>';
    }
    else
    {
        if(!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
        {
            $error .= '<li>Adresse e-mail invalide</li>';
        }
        else
        {
            $formdata['user_email'] = trim($_POST["user_email"]);
        }
    }

    if(empty($_POST["user_password"]))
    {
        $error .= '<li>Mot de passe requis</li>';
    }
    else
    {
        $formdata['user_password'] = trim($_POST["user_password"]);
    }

    if($error == '')
    {
        $object->query = "
        SELECT * FROM user_ims 
        WHERE user_email = '".$formdata['user_email']."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>L adresse mail existe déjà</li>';
        }
        else
        {
            $data = array(
                ':user_name'        =>  $formdata['user_name'],
                ':user_email'       =>  $formdata['user_email'],
                ':user_password'    =>  $formdata['user_password'],
                ':user_type'        =>  'User',
                ':user_status'      =>  'Enable',
                ':user_created_on'  =>  $object->now
            );

            $object->query = "
            INSERT INTO user_ims 
            (user_name, user_email, user_password, user_type, user_status, user_created_on) 
            VALUES (:user_name, :user_email, :user_password, :user_type, :user_status, :user_created_on)
            ";

            $object->execute($data);

            header('location:user.php?msg=add');
        }
    }
}

if(isset($_POST["edit_user"]))
{
    $formdata = array();

    if(empty($_POST["user_name"]))
    {
        $error .= '<li>Nom d utilisateur est nécessaire</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["user_name"]))
        {
            $error .= '<li>Seules les lettres et les espaces blancs sont autorisés</li>';
        }
        else
        {
            $formdata['user_name'] = trim($_POST["user_name"]);
        }
    }

    if(empty($_POST["user_email"]))
    {
        $error .= '<li>Adresse e-mail est nécessaire</li>';
    }
    else
    {
        if(!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
        {
            $error .= '<li>Adresse e-mail invalide</li>';
        }
        else
        {
            $formdata['user_email'] = trim($_POST["user_email"]);
        }
    }

    if(empty($_POST["user_password"]))
    {
        $error .= '<li>Mot de passe requis</li>';
    }
    else
    {
        $formdata['user_password'] = trim($_POST["user_password"]);
    }

    if($error == '')
    {
        $user_id = $object->convert_data(trim($_POST["user_id"]), 'decrypt');

        $object->query = "
        SELECT * FROM user_ims 
        WHERE user_email = '".$formdata['user_email']."' 
        AND user_id != '".$user_id."'
        ";

        $object->execute();

        if($object->row_count() > 0)
        {
            $error = '<li>L adresse mail existe déjà</li>';
        }
        else
        {
            $data = array(
                ':user_name'        =>  $formdata['user_name'],
                ':user_email'       =>  $formdata['user_email'],
                ':user_password'    =>  $formdata['user_password'],
                ':user_id'          =>  $user_id
            );

            $object->query = "
            UPDATE user_ims 
            SET user_name = :user_name, 
            user_email = :user_email,
            user_password = :user_password 
            WHERE user_id = :user_id
            ";

            $object->execute($data);

            header('location:user.php?msg=edit');
        }
    }
}


if(isset($_GET["action"], $_GET["code"], $_GET["status"]) && $_GET["action"] == 'delete')
{
    $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
    $status = trim($_GET["status"]);
    $data = array(
        ':user_status'      =>  $status,
        ':user_id'          =>  $user_id
    );

    $object->query = "
    UPDATE user_ims 
    SET user_status = :user_status 
    WHERE user_id = :user_id
    ";

    $object->execute($data);

    header('location:user.php?msg='.strtolower($status).'');

}


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Gestion des utilisateurs</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="user.php">Gestion des utilisateurs</a></li>
                                <li class="breadcrumb-item active">Ajouter un utilisateur</li>
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
                                            <i class="fas fa-user-plus"></i> Ajouter un nouvel utilisateur
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_name" type="text" placeholder="Enter User Name" name="user_name" value="<?php if(isset($_POST["user_name"])) echo $_POST["user_name"]; ?>" />
                                                    <label for="user_name">Nom d'utilisateur</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_email" type="text" placeholder="Enter User Email Address" name="user_email" value="<?php if(isset($_POST["user_email"])) echo $_POST["user_email"]; ?>" />
                                                    <label for="user_email">Adresse e-mail</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="user_password" type="password" placeholder="Enter User Password" name="user_password" value="<?php if(isset($_POST["user_password"])) echo $_POST["user_password"]; ?>" />
                                                    <label for="user_password">Mot de passe</label>
                                                </div>
                                                <div class="mt-4 mb-0">
                                                    <input type="submit" name="add_user" class="btn btn-success" value="Ajouter" />
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
                                $user_id = $object->convert_data(trim($_GET["code"]), 'decrypt');
                                
                                if($user_id > 0)
                                {
                                    $object->query = "
                                    SELECT * FROM user_ims 
                                    WHERE user_id = '$user_id'
                                    ";

                                    $user_result = $object->get_result();

                                    foreach($user_result as $user_row)
                                    {
                                ?>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                    <li class="breadcrumb-item"><a href="user.php">Gestion des utilisateurs</a></li>
                                    <li class="breadcrumb-item active">Modifier l'utilisateur</li>
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
                                                <i class="fas fa-user-edit"></i> Modifier les détails de l'utilisateur
                                            </div>
                                            <div class="card-body">
                                                <form method="post">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_name" type="text" placeholder="Enter User Name" name="user_name" value="<?php echo $user_row["user_name"]; ?>" />
                                                        <label for="user_name">Nom d'utilisateur</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_email" type="text" placeholder="Enter User Email Address" name="user_email" value="<?php echo $user_row["user_email"]; ?>" />
                                                        <label for="user_email">Adresse e-mail</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="user_password" type="password" placeholder="Enter User Password" name="user_password" value="<?php echo $user_row["user_password"]; ?>" />
                                                        <label for="user_password">Mot de passe</label>
                                                    </div>
                                                    <div class="mt-4 mb-0">
                                                        <input type="hidden" name="user_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                        <input type="submit" name="edit_user" class="btn btn-primary" value="Modifier" />
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
                                <li class="breadcrumb-item active">Gestion des utilisateurs</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouvel utilisateur ajouté<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Données utilisateur modifiées <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l état de l utilisateur sur Désactiver <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification de l état de l utilisateur sur Activer <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Gestion des utilisateurs
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="user.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="user_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom d'utilisateur</th>
                                                <th>Adresse e-mail de l'utilisateur</th>
                                                <th>Mot de passe</th>
                                                <th>Type d'utilisateur</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <script>
                            
                            var userdataTable = $('#user_data').DataTable({
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax":{
                                    url:"action.php",
                                    type:"POST",
                                    data:{action:"fetch_user"}
                                },
                                "columnDefs":[
                                    {
                                        "target":[4,5],
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
                                if(confirm("Are you sure you want to "+new_status+" this User?"))
                                {
                                    window.location.href="user.php?action=delete&code="+code+"&status="+new_status+"";
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