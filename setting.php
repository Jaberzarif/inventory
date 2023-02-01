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

if(isset($_POST["submit"]))
{
    $formdata = array();

    if(empty($_POST["store_name"]))
    {
        $error .= '<li>Le nom du magasin est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["store_name"]))
        {
            $error .= '<li>les lettres, les chiffres et les espaces sont autorisés</li>';
        }
        else
        {
            $formdata['store_name'] = trim($_POST["store_name"]);
        }
    }

    if(empty($_POST["store_address"]))
    {
        $error .= '<li>Adresse est obligatoire</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9' ]*$/", $_POST["store_address"]))
        {
            $error .= '<li>les lettres, les chiffres et les espaces sont autorisés</li>';
        }
        else
        {
            $formdata['store_address'] = trim($_POST["store_address"]);
        }
    }

    if(empty($_POST["store_contact_no"]))
    {
        $error .= '<li>Le numéro de contact est requis</li>';
    }
    else
    {
        if (!preg_match("/^[0-9']*$/", $_POST["store_contact_no"]))
        {
            $error .= '<li>Le numéro de contact est requis</li>';
        }
        else
        {
            $formdata['store_contact_no'] = trim($_POST["store_contact_no"]);
        }
    }

    if(empty($_POST["store_email_address"]))
    {
        $error .= '<li>Adresse e-mail est nécessaire</li>';
    }
    else
    {
        if (!filter_var($_POST["store_email_address"], FILTER_VALIDATE_EMAIL))
        {
            $error .= '<li>Invalid Email Address</li>';
        }
        else
        {
            $formdata['store_email_address'] = trim($_POST["store_email_address"]);
        }
    }

    if(empty($_POST["store_timezone"]))
    {
        $error .= '<li>Timezone is required</li>';
    }
    else
    {
        $formdata['store_timezone'] = trim($_POST["store_timezone"]);
    }

    if(empty($_POST["store_currency"]))
    {
        $error .= '<li>La devise est requise</li>';
    }
    else
    {
        $formdata['store_currency'] = trim($_POST["store_currency"]);
    }

    if($error == '')
    {
        $data = array(
            ':store_name'           =>  $formdata["store_name"],
            ':store_address'        =>  $formdata["store_address"],
            ':store_contact_no'     =>  $formdata["store_contact_no"],
            ':store_email_address'  =>  $formdata["store_email_address"],
            ':store_timezone'       =>  $formdata["store_timezone"],
            ':store_currency'       =>  $formdata["store_currency"],
            ':store_updated_on'     =>  date('Y-m-d H:i:s'),
            ':store_id'             =>  $_POST["store_id"]
        );

        $object->query = "
        UPDATE store_ims 
        SET store_name = :store_name, 
        store_address = :store_address, 
        store_contact_no = :store_contact_no, 
        store_email_address = :store_email_address, 
        store_timezone = :store_timezone, 
        store_currency = :store_currency, 
        store_updated_on = :store_updated_on 
        WHERE store_id = :store_id
        ";

        $object->execute($data);

        $message = '<div class="alert alert-success">Les données ont été modifiées avec succès</div>';
    }
}

$object->query = "
    SELECT * FROM store_ims 
    LIMIT 1
";

$result = $object->get_result();


include('header.php');

?>

                        <div class="container-fluid px-4">
                            <h1 class="mt-4">Paramètre</h1>

                            <?php
                            foreach($result as $row)
                            {
                            ?>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item active">Paramètre</li>
                            </ol>
                            <div class="row">
                                <div class="col-md-6">
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                echo $message;
                                ?>
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user-edit"></i> Paramètre
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_name" type="text" name="store_name" placeholder="Entrez le nom du magasin" value="<?php echo $row['store_name']; ?>" />
                                                    <label for="store_name">Nom du magasin</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <textarea class="form-control" id="store_address" name="store_address" placeholder="Entrer l'adresse"><?php echo $row['store_address']; ?></textarea>
                                                    <label for="store_address">Adresse</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_contact_no" type="text" name="store_contact_no" placeholder="Entrez le numéro de contact." value="<?php echo $row['store_contact_no']; ?>" />
                                                    <label for="store_contact_no">N° de contact.</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="store_email_address" type="text" name="store_email_address" placeholder="Adresse e-mail" value="<?php echo $row['store_email_address']; ?>" />
                                                    <label for="store_email_address">Adresse e-mail</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <select class="form-control" id="store_timezone" name="store_timezone">
                                                        <?php echo $object->Timezone_list(); ?>
                                                    </select>
                                                    <label for="store_timezone">Fuseau horaire</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <select class="form-control" id="store_currency" name="store_currency">
                                                        <?php echo $object->Currency_list(); ?>
                                                    </select>
                                                    <label for="store_timezone">Devise</label>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                    <input type="hidden" name="store_id" value="<?php echo $row['store_id']; ?>" />
                                                    <input type="submit" name="submit" id="submit_button" class="btn btn-primary" value="submit" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.getElementById('store_timezone').value = "<?php echo $row['store_timezone']; ?>";
                                    document.getElementById('store_currency').value = "<?php echo html_entity_decode($row['store_currency']); ?>";
                                </script>
                        <?php                        
                            }
                        ?>

                            </div>
                        </div>

<?php

include('footer.php');

?>