<?php 
use ItForFree\SimpleMVC\Config;
use application\models\primitives\Role;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');

// Получаем все доступные роли для выпадающего списка
$roles = Role::cases();
?>
<?php include('includes/admin-users-nav.php'); ?>


<h2><?= $editAdminusersTitle ?>
    <span>
        <?= $User->returnIfAllowed("admin/adminusers/delete", 
            "<a href=" . $Url::link("admin/adminusers/delete&id=" . $_GET['id']) 
            . ">[Удалить]</a>");?>
    </span>
</h2>

<form id="editUser" method="post" action="<?= $Url::link("admin/adminusers/edit&id=" . $_GET['id'])?>">
    <h5>Введите имя пользователя</h5>
    <input type="text" name="login" placeholder="логин пользователя" value="<?= htmlspecialchars($viewAdminusers->login, ENT_QUOTES, 'UTF-8') ?>"><br>
    <h5>Введите пароль</h5>
    <input type="text" name="pass" placeholder="новый пароль" value=""><br>
    <h5>Введите e-mail</h5>
    <input type="text" name="email"  placeholder="email" value="<?= htmlspecialchars($viewAdminusers->email, ENT_QUOTES, 'UTF-8') ?>"><br>
    
    <h5>Выберите роль</h5>
    <select name="role">
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role->value ?>" <?= $viewAdminusers->role === $role->value ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $role->name)) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    
    <input type="hidden" name="id" value="<?= $_GET['id']; ?>">
    <input type="submit" name="saveChanges" value="Сохранить">
    <input type="submit" name="cancel" value="Назад">
</form>
