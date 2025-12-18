<?php
/**
 * @var \App\Models\Article $article
 */
use ItForFree\SimpleMVC\Router\WebRouter;
use ItForFree\SimpleMVC\Helpers\HtmlHelper;

$this->extend('layouts/main');
?>

<?php $this->start('content') ?>

<h1 style="width: 75%;"><?= HtmlHelper::specialchars($article->title) ?></h1>
<div style="width: 75%; font-style: italic;"><?= HtmlHelper::specialchars($article->summary) ?></div>
<div style="width: 75%;"><?= $article->content ?></div>
<p class="pubDate">Published on <?= date('j F Y', $article->publicationDate) ?>

<?php if ($category) { ?>
    in
    <a href="<?= WebRouter::link('article/archive&categoryId=' . $category->id) ?>">
        <?= HtmlHelper::specialchars($category->name) ?>
    </a>
<?php } ?>

<?php if ($subcategory) { ?>
    in
    <a href="<?= WebRouter::link('article/archive&categoryId=' . $category->id . '&subcategoryId=' . $subcategory->id) ?>">
        <?= HtmlHelper::specialchars($subcategory->name) ?>
    </a>
<?php } ?>
    
</p>

<?php if (!empty($article->authors)) { ?>
    <p><strong>Authors:</strong>
    <?php
    $authorNames = array();
    foreach ($article->authors as $author) {
        $authorNames[] = HtmlHelper::specialchars($author['login']);
    }
    echo implode(', ', $authorNames);
    ?>
    </p>
<?php } ?>

<p><a href="/">Вернуться на главную страницу</a></p>

<?php $this->stop() ?>