<?php 

use application\assets\DemoJavascriptAsset;
DemoJavascriptAsset::add();

?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php if (isset($article)): ?>
                <h1><?php echo htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8') ?></h1>
                
                <p class="pubDate">
                    <?php echo date('j F Y', strtotime($article->publicationDate))?>
                </p>
                
                <?php if (isset($category)): ?>
                    <span class="category">
                        Категория: 
                        <a href="/category/<?php echo $article->categoryId?>">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8')?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <?php if (isset($subcategory)): ?>
                    <span class="subcategory">
                        Подкатегория: 
                        <a href="/subcategory/<?php echo $article->subcategoryId?>">
                            <?php echo htmlspecialchars($subcategory->name, ENT_QUOTES, 'UTF-8')?>
                        </a>
                    </span>
                <?php endif; ?>
                
                <div class="article-content">
                    <?php echo $article->content ?>
                </div>
                
                <p><a href="/">← Назад к списку статей</a></p>
            <?php else: ?>
                <p>Статья не найдена</p>
                <p><a href="/">← На главную</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>