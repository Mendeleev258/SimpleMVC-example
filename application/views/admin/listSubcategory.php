<?php
/**
 * @var array $subcategories
 * @var int $totalRows
 * @var string $errorMessage
 * @var string $statusMessage
 */
use ItForFree\SimpleMVC\Router\WebRouter;
use ItForFree\SimpleMVC\Helpers\HtmlHelper;

$this->extend('layouts/admin-main');
?>

<?php $this->start('content') ?>

<h1>Article Subcategories</h1>

<?php if (isset($errorMessage)) { ?>
    <div class="errorMessage"><?= $errorMessage ?></div>
<?php } ?>

<?php if (isset($statusMessage)) { ?>
    <div class="statusMessage"><?= $statusMessage ?></div>
<?php } ?>

<table>
    <tr>
        <th>Subcategory</th>
        <th>Category</th>
    </tr>

    <?php foreach ($subcategories as $subcategory) {
        $categoryModel = new \application\models\Category();
        $category = $categoryModel->getById($subcategory->categoryId);
    ?>

    <tr onclick="location='<?= WebRouter::link('admin/subcategories/edit&subcategoryId=' . $subcategory->id) ?>'">
        <td>
            <?= $subcategory->name?>
        </td>
        <td>
            <?= $category ? $category->name : 'N/A'?>
        </td>
    </tr>

    <?php } ?>

</table>

<p><?= $totalRows ?> subcategor<?= ($totalRows != 1) ? 'ies' : 'y' ?> in total.</p>

<p><a href="<?= WebRouter::link('admin/subcategories/new') ?>">Add a New Subcategory</a></p>

<?php $this->stop() ?>