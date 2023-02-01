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
        $error .= '<li>Le nom du produit est requis</li>';
    }
    else
    {
        $formdata['item_id'] = trim($_POST["item_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Le fournisseur est requis</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["item_batch_no"]))
    {
        $error .= '<li>Le numéro de lot est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9 ']*$/", $_POST["item_batch_no"]))
        {
            $error .= '<li>Seules les lettres et les chiffres sont autorisés</li>';
        }
        else
        {
            $formdata['item_batch_no'] = trim($_POST["item_batch_no"]);
        }
    }

    if(empty($_POST["item_purchase_qty"]))
    {
        $error .= '<li>La quantité d achat est requise</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.']*$/", $_POST["item_purchase_qty"]))
        {
            $error .= '<li>Seuls les numéros autorisés</li>';
        }
        else
        {
            $formdata['item_purchase_qty'] = trim($_POST["item_purchase_qty"]);
        }
    }

    if(empty($_POST["item_purchase_price_per_unit"]))
    {
        $error .= '<li>Le prix d achat par unité est requis</li>';
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
        $error .= '<li>Le mois de fabrication est requis</li>';
    }
    else
    {
        $formdata['item_manufacture_year'] = trim($_POST["item_manufacture_year"]);
    }

    if(empty($_POST["item_expired_month"]))
    {
        $error .= '<li>Mois expiré est requis</li>';
    }
    else
    {
        $formdata['item_expired_month'] = trim($_POST["item_expired_month"]);
    }

    if(empty($_POST["item_expired_year"]))
    {
        $error .= '<li>L'année d'expiration est requise</li>';
    }
    else
    {
        $formdata['item_expired_year'] = trim($_POST["item_expired_year"]);
    }

    if(empty($_POST["item_sale_price_per_unit"]))
    {
        $error .= '<li>Le prix de vente par unité est requis</li>';
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
        $error .= '<li>Le nom du produit est requis</li>';
    }
    else
    {
        $formdata['item_id'] = trim($_POST["item_id"]);
    }

    if(empty($_POST["supplier_id"]))
    {
        $error .= '<li>Le fournisseur est requis</li>';
    }
    else
    {
        $formdata['supplier_id'] = trim($_POST["supplier_id"]);
    }

    if(empty($_POST["item_batch_no"]))
    {
        $error .= '<li>Le numéro de lot est requis</li>';
    }
    else
    {
        if (!preg_match("/^[a-zA-Z-0-9 ']*$/", $_POST["item_batch_no"]))
        {
            $error .= '<li>Seules les lettres et les chiffres sont autorisés</li>';
        }
        else
        {
            $formdata['item_batch_no'] = trim($_POST["item_batch_no"]);
        }
    }

    if(empty($_POST["item_purchase_qty"]))
    {
        $error .= '<li>La quantité d achat est requise</li>';
    }
    else
    {
        if (!preg_match("/^[0-9.']*$/", $_POST["item_purchase_qty"]))
        {
            $error .= '<li>Seuls les numéros autorisés</li>';
        }
        else
        {
            $formdata['item_purchase_qty'] = trim($_POST["item_purchase_qty"]);
        }
    }

    if(empty($_POST["item_purchase_price_per_unit"]))
    {
        $error .= '<li>Le prix d achat par unité est requis</li>';
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
        $error .= '<li>Le mois de fabrication est requis</li>';
    }
    else
    {
        $formdata['item_manufacture_year'] = trim($_POST["item_manufacture_year"]);
    }

    if(empty($_POST["item_expired_month"]))
    {
        $error .= '<li>Mois expiré est requis</li>';
    }
    else
    {
        $formdata['item_expired_month'] = trim($_POST["item_expired_month"]);
    }

    if(empty($_POST["item_expired_year"]))
    {
        $error .= '<li>L année d expiration est requise</li>';
    }
    else
    {
        $formdata['item_expired_year'] = trim($_POST["item_expired_year"]);
    }

    if(empty($_POST["item_sale_price_per_unit"]))
    {
        $error .= '<li>Le prix de vente par unité est requis</li>';
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
                            <h1 class="mt-4">Gestion des achats</h1>

                        <?php
                        if(isset($_GET["action"], $_GET["code"]))
                        {
                            if($_GET["action"] == 'add')
                            {
                        ?>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="product_purchase.php">Gestion des achats</a></li>
                                <li class="breadcrumb-item active">Ajouter un achat</li>
                            </ol>

                            <?php
                            if(isset($error) && $error != '')
                            {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            }
                            ?>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-plus"></i> Ajouter un achat
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Sélectionner un produit</label>
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
                                                    <label>Sélectionnez le fournisseur</label>
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
                                                    <input class="form-control" id="item_purchase_qty" type="number" placeholder="Entrez la quantité" name="item_purchase_qty" value="<?php if(isset($_POST["item_purchase_qty"])) echo $_POST["item_purchase_qty"]; ?>" />
                                                    <label for="item_purchase_qty">Quantité</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_purchase_price_per_unit" type="number" placeholder="Entrez le prix d'achat par unité" name="item_purchase_price_per_unit" step=".01" value="<?php if(isset($_POST["item_purchase_price_per_unit"])) echo $_POST["item_purchase_price_per_unit"]; ?>" />
                                                    <label for="item_purchase_price_per_unit">Prix d'achat par unité</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_manufacture_month" class="form-control" id="item_manufacture_month">
                                                                <option value="">Sélectionner</option>
                                                                <option value="01">Janvier</option>
                                                                <option value="02">Février</option>
                                                                <option value="03">Mars</option>
                                                                <option value="04">Avril</option>
                                                                <option value="05">Mai</option>
                                                                <option value="06">Juin</option>
                                                                <option value="07">Juillet</option>
                                                                <option value="08">Août</option>
                                                                <option value="09">Septembre</option>
                                                                <option value="10">Octobre</option>
                                                                <option value="11">Novembre</option>
                                                                <option value="12">Décembre</option>
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
                                                            <label for="item_manufacture_month">Mois de fabrication</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_manufacture_year" class="form-control" id="item_manufacture_year">
                                                                <option value="">Sélectionner</option>
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
                                                            <label for="item_manufacture_year">Année de fabrication</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_expired_month" class="form-control" id="item_expired_month">
                                                                <option value="">Sélectionner</option>
                                                                <option value="01">Janvier</option>
                                                                <option value="02">Février</option>
                                                                <option value="03">Mars</option>
                                                                <option value="04">Avril</option>
                                                                <option value="05">Mai</option>
                                                                <option value="06">Juin</option>
                                                                <option value="07">Juillet</option>
                                                                <option value="08">Août</option>
                                                                <option value="09">Septembre</option>
                                                                <option value="10">Octobre</option>
                                                                <option value="11">Novembre</option>
                                                                <option value="12">Décembre</option>
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
                                                            <label for="item_expired_month">Mois d'expiration</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <select name="item_expired_year" class="form-control" id="item_expired_year">
                                                                <option value="">Sélectionner</option>
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
                                                            <label for="item_expired_year">Année d'expiration</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_batch_no" type="text" placeholder="Entrez le numéro de lot" name="item_batch_no" value="<?php if(isset($_POST["item_batch_no"])) echo $_POST["item_batch_no"]; ?>" />
                                                    <label for="item_batch_no">N ° de lot.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" id="item_sale_price_per_unit" type="number" placeholder="Entrez le prix de vente par unité" name="item_sale_price_per_unit" step=".01" value="<?php if(isset($_POST["item_sale_price_per_unit"])) echo $_POST["item_sale_price_per_unit"]; ?>" />
                                                    <label for="item_sale_price_per_unit">Prix de vente par unité</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="add_purchase" class="btn btn-success" value="Ajouter" />
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
                                    <li class="breadcrumb-item"><a href="product_purchase.php">Gestion des achats</a></li>
                                    <li class="breadcrumb-item active">Modifier les données d'achat</li>
                                </ol>
                                <?php
                                if(isset($error) && $error != '')
                                {
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                ?>
                                
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user-plus"></i> Modifier les données d'achat
                                    </div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Sélectionner un produit</label>
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
                                                            <input class="form-control" id="item_purchase_qty" type="number" placeholder="Entrez la quantité" name="item_purchase_qty" value="<?php echo $purchase_row["item_purchase_qty"]; ?>" />
                                                            <label for="item_purchase_qty">Quantité</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_purchase_price_per_unit" type="number" placeholder="Entrez le prix d'achat par unité" name="item_purchase_price_per_unit" step=".01" value="<?php echo $purchase_row["item_purchase_price_per_unit"]; ?>" />
                                                        <label for="item_purchase_price_per_unit">Prix d'achat par unité</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_manufacture_month" class="form-control" id="item_manufacture_month">
                                                                    <option value="">Sélectionner</option>
                                                                    <option value="01">Janvier</option>
                                                                    <option value="02">Février</option>
                                                                    <option value="03">Mars</option>
                                                                    <option value="04">Avril</option>
                                                                    <option value="05">Mai</option>
                                                                    <option value="06">Juin</option>
                                                                    <option value="07">Juillet</option>
                                                                    <option value="08">Août</option>
                                                                    <option value="09">Septembre</option>
                                                                    <option value="10">Octobre</option>
                                                                    <option value="11">Novembre</option>
                                                                    <option value="12">Décembre</option>
                                                                </select>
                                                                <label for="item_manufacture_month">Mois de fabrication</label>
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
                                                                <label for="item_manufacture_year">Année de fabrication</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_expired_month" class="form-control" id="item_expired_month">
                                                                    <option value="">Sélectionner</option>
                                                                    <option value="01">Janvier</option>
                                                                    <option value="02">Février</option>
                                                                    <option value="03">Mars</option>
                                                                    <option value="04">Avril</option>
                                                                    <option value="05">Mai</option>
                                                                    <option value="06">Juin</option>
                                                                    <option value="07">Juillet</option>
                                                                    <option value="08">Août</option>
                                                                    <option value="09">Septembre</option>
                                                                    <option value="10">Octobre</option>
                                                                    <option value="11">Novembre</option>
                                                                    <option value="12">Décembre</option>
                                                                </select>
                                                                <label for="item_expired_month">Mois d'expiration</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select name="item_expired_year" class="form-control" id="item_expired_year">
                                                                    <option value="">Sélectionner</option>
                                                                    <?php 
                                                                    for($i = date("Y"); $i < date("Y") + 10; $i++)
                                                                    {
                                                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="item_expired_year">Année d'expiration</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_batch_no" type="text" placeholder="Entrez le numéro de lot" name="item_batch_no" value="<?php echo $purchase_row["item_batch_no"]; ?>" />
                                                        <label for="item_batch_no">N ° de lot.</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input class="form-control" id="item_sale_price_per_unit" type="number" placeholder="Entrez le prix de vente par unité" name="item_sale_price_per_unit" step=".01" value="<?php echo $purchase_row["item_sale_price_per_unit"]; ?>" />
                                                        <label for="item_sale_price_per_unit">Prix de vente par unité</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <input type="hidden" name="item_purchase_id" value="<?php echo trim($_GET["code"]); ?>" />
                                                <input type="submit" name="edit_purchase" class="btn btn-primary" value="Éditer" />
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
                                <li class="breadcrumb-item active">Gestion des achats</li>
                            </ol>

                            <?php

                            if(isset($_GET["msg"]))
                            {
                                if($_GET["msg"] == 'add')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Nouveau détail d achat de produit ajouté<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'edit')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Données d achat de produit modifiées <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'disable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification du statut d achat du produit à Désactiver <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                                if($_GET["msg"] == 'enable')
                                {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Modification du statut d achat du produit sur Activer <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                                }
                            }

                            ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col col-md-6">
                                            <i class="fas fa-table me-1"></i> Gestion des achats
                                        </div>
                                        <div class="col col-md-6" align="right">
                                            <a href="product_purchase.php?action=add&code=<?php echo $object->convert_data('add'); ?>" class="btn btn-success btn-sm">Ajouter</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <table id="purchase_data" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom du produit</th>
                                                <th>N ° de lot.</th>
                                                <th>Le fournisseur</th>
                                                <th>Quantité</th>
                                                <th>Quantité disponible</th>
                                                <th>Prix ​​par unité</th>
                                                <th>Coût total</th>
                                                <th>Date de fabrication</th>
                                                <th>Date d'expiration</th>
                                                <th>Prix ​​de vente</th>
                                                <th>Date d'achat</th>
                                                <th>Status</th>
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