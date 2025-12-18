<?php
/**
 * @var array $categories
 * @var int $totalRows
 * @var string $errorMessage
 * @var string $statusMessage
 */
use ItForFree\SimpleMVC\Router\WebRouter;
use ItForFree\SimpleMVC\Helpers\HtmlHelper;

$this->extend('layouts/admin-main');
?>

<?php $this->start('content') ?>

<h1>Article Categories</h1>

<?php if (isset($errorMessage)) { ?>
    <div class="errorMessage"><?= $errorMessage ?></div>
<?php } ?>

<?php if (isset($statusMessage)) { ?>
    <div class="statusMessage"><?= $statusMessage ?></div>
<?php } ?>

<table>
    <tr>
        <th>Category</th>
    </tr>

    <?php foreach ($categories as $category) { ?>

    <tr onclick="location='<?= WebRouter::link('admin/categories/edit&categoryId=' . $category->id) ?>'">
        <td>
            <?= $category->name?>
        </td>
    </tr>

    <?php } ?>

</table>

<p><?= $totalRows ?> categor<?= ($totalRows != 1) ? 'ies' : 'y' ?> in total.</p>

<p><a href="<?= WebRouter::link('admin/categories/new') ?>">Add a New Category</a></p>

<?php $this->stop() ?>