<?php
/**
 * @var string $pageHeading
 * @var \App\Models\Category $category
 * @var array $articles
 * @var array $categories
 * @var int $totalRows
 */
use ItForFree\SimpleMVC\Router\WebRouter;
use ItForFree\SimpleMVC\Helpers\HtmlHelper;

$this->extend('layouts/main');
?>

<?php $this->start('content') ?>

<h1><?= HtmlHelper::specialchars($pageHeading) ?></h1>

<?php if ($category) { ?>
<h3 class="categoryDescription"><?= HtmlHelper::specialchars($category->description) ?></h3>
<?php } ?>

<ul id="headlines" class="archive">

<?php foreach ($articles as $article) { ?>

        <li>
            <h2>
                <span class="pubDate">
                    <?= date('j F Y', $article->publicationDate)?>
                </span>
                <a href="<?= WebRouter::link('article/view&id=' . $article->id) ?>">
                    <?= HtmlHelper::specialchars($article->title)?>
                </a>

                <?php if (!$category && $article->categoryId) { ?>
                <span class="category">
                    in
                    <a href="<?= WebRouter::link('article/archive&categoryId=' . $article->categoryId) ?>">
                        <?= HtmlHelper::specialchars($categories[$article->categoryId]->name) ?>
                    </a>
                </span>
                <?php } ?>
                
                <?php if ($article->subcategoryId) {
                    $subcategoryModel = new \application\models\Subcategory();
                    $subcategory = $subcategoryModel->getById($article->subcategoryId);
                    if ($subcategory) { ?>
                    <span class="subcategory">
                        in
                        <a href="<?= WebRouter::link('article/archive&categoryId=' . $article->categoryId . '&subcategoryId=' . $subcategory->id) ?>">
                            <?= HtmlHelper::specialchars($subcategory->name) ?>
                        </a>
                    </span>
                <?php }} ?>
            </h2>
          <p class="summary"><?= HtmlHelper::specialchars($article->summary)?></p>
        </li>

<?php } ?>

</ul>

<p><?= $totalRows ?> article<?= ($totalRows != 1) ? 's' : '' ?> in total.</p>

<p><a href="/">Return to Homepage</a></p>

<?php $this->stop() ?>