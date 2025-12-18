<?php

use application\assets\DemoJavascriptAsset;
use application\assets\JqueryAsset;
DemoJavascriptAsset::add();
JqueryAsset::add();

?>
<a href="."><img id="logo" src="/images/logo.jpg" alt="Widget News" /></a>
<ul id="headlines">
    <?php
    foreach ($articles as $article) { ?>
        
        <li class='<?php echo $article->id?>'>
            <h2>
                <span class="pubDate">
                    <?php
                    // Проверяем, является ли publicationDate числом (Unix timestamp)
                    if (is_numeric($article->publicationDate)) {
                        echo date('j F', $article->publicationDate);
                    } else {
                        // Если это строка даты, преобразуем её в Unix timestamp
                        $date = strtotime($article->publicationDate);
                        if ($date !== false) {
                            echo date('j F', $date);
                        } else {
                            // Если дата не может быть преобразована, выводим сообщение об ошибке
                            echo "Некорректная дата";
                        }
                    }
                    ?>
                </span>
                
                <a href="/article/<?php echo $article->id?>">
                    <?php echo htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8')?>
                </a>
            </h2>
                
            <?php if (isset($article->categoryId) && isset($categories[$article->categoryId])) { ?>
                <span class="category">
                    Категория
                    <a href="/category/<?php echo $article->categoryId?>">
                        <?php echo htmlspecialchars($categories[$article->categoryId]->name, ENT_QUOTES, 'UTF-8')?>
                    </a>
                </span>
            <?php }
            else { ?>
                <span class="category">
                    <?php echo "Без категории"?>
                </span>
            <?php } ?>
            <?php if (isset($article->subcategoryId) && isset($subcategories[$article->subcategoryId])) { ?>
            <span class="subcategory">
                Подкатегория
                <a href="/subcategory/<?php echo $article->subcategoryId?>">
                    <?php echo htmlspecialchars($subcategories[$article->subcategoryId]->name, ENT_QUOTES, 'UTF-8')?>
                </a>
            </span>
            <?php } ?>
            <p class="summary"><?php
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
                            echo htmlspecialchars($shortContent, ENT_QUOTES, 'UTF-8')?>
                            </p>
            
            <a href="/article/<?php echo $article->id?>" class="showContent">
                Читать далее
            </a>
        </li>
    <?php } ?>
    </ul>
    <p><a href="/archive">Архив статей</a></p>
