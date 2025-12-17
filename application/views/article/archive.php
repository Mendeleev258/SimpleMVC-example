<?php 

use application\assets\DemoJavascriptAsset;
use application\assets\JqueryAsset;
DemoJavascriptAsset::add();
JqueryAsset::add();

?>
<div class="container">
    <div class="row">
        <div class="col">
            <h1><?php echo isset($pageHeading) ? htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') : 'Архив статей' ?></h1>
            
            <ul id="headlines" class="archive">
            <?php foreach ($articles as $article): ?>
                <li class='<?php echo $article->id?>'>
                    <h2>
                        <span class="pubDate">
                            <?php echo date('j F Y', strtotime($article->publicationDate))?>
                        </span>
                        
                        <a href="/article/<?php echo $article->id?>">
                            <?php echo htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8')?>
                        </a>
                    </h2>
                        
                    <?php if (isset($article->categoryId) && isset($categories[$article->categoryId])): ?>
                        <span class="category">
                            Категория
                            <a href="/category/<?php echo $article->categoryId?>">
                                <?php echo htmlspecialchars($categories[$article->categoryId]->name, ENT_QUOTES, 'UTF-8')?>
                            </a>
                        </span>
                    <?php else: ?>
                        <span class="category">
                            <?php echo "Без категории"?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (isset($article->subcategoryId) && isset($subcategories[$article->subcategoryId])): ?>
                    <span class="subcategory">
                        Подкатегория
                        <a href="/subcategory/<?php echo $article->subcategoryId?>">
                            <?php echo htmlspecialchars($subcategories[$article->subcategoryId]->name, ENT_QUOTES, 'UTF-8')?>
                        </a>
                    </span>
                    <?php endif; ?>
                    
                    <p class="summary">
                        <?php
                        // Получаем краткое содержание статьи
                        $shortContent = '';
                        if (isset($article->content50char)) {
                            $shortContent = $article->content50char;
                        } else {
                            // Если content50char не установлен, берем первые 50 символов из content
                            $content = isset($article->content) ? $article->content : '';
                            $shortContent = mb_substr($content, 0, 50, 'UTF-8');
                            if (mb_strlen($content, 'UTF-8') > 50) {
                                $shortContent .= '...';
                            }
                        }
                        echo htmlspecialchars($shortContent, ENT_QUOTES, 'UTF-8')
                        ?>
                    </p>
                    
                    <a href="/article/<?php echo $article->id?>" class="showContent">
                        Читать далее
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
            
            <p><a href="/">← На главную</a></p>
        </div>
    </div>
</div>