<?php 
use ItForFree\SimpleMVC\Config;

$Url = Config::getObject('core.router.class');
?>

<?php include('includes/admin-users-nav.php'); ?>

<h2><?= $deleteAdminusersTitle ?></h2>

<form method="post" action="<?= $Url::link("admin/adminusers/delete&id=". $_GET['id'])?>" >
    <p>Вы уверены, что хотите удалить следующего пользователя?</p>
    <p><strong>Логин:</strong> <?= $deletedAdminusers->login ?></p>
    <p><strong>Email:</strong> <?= $deletedAdminusers->email ?></p>
    <p><strong>Роль:</strong> <?= ucfirst(str_replace('_', ' ', $deletedAdminusers->role)) ?></p>
    
    <input type="hidden" name="id" value="<?= $deletedAdminusers->id ?>">
    <input type="submit" name="deleteUser" value="Удалить">
    <input type="submit" name="cancel" value="Вернуться"><br>
</form>
