<?php

namespace application\controllers;

use application\models\Article;
use application\models\Category;
use application\models\Subcategory;

/**
 * Контроллер для работы со статьями
 */
class ArticleController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Пусть к файлу макета 
     */
    public string $layoutPath = 'main.php';
    
    /**
     * Отображает конкретную статью по ID
     */
    public function viewAction()
    {
        $articleId = (int)$this->getParam('id');
        
        if (!$articleId) {
            // Если ID не указан, перенаправляем на главную
            header("Location: /");
            exit;
        }
        
        $articleModel = new Article();
        $article = $articleModel->getById($articleId);
        
        if (!$article) {
            // Если статья не найдена, можно выбросить исключение или перенаправить
            header("Location: /");
            exit;
        }
        
        // Получаем категории и подкатегории для отображения
        $category = null;
        if ($article->categoryId) {
            $categoryModel = new Category();
            $category = $categoryModel->getById($article->categoryId);
        }
        
        $subcategory = null;
        if ($article->subcategoryId) {
            $subcategoryModel = new Subcategory();
            $subcategory = $subcategoryModel->getById($article->subcategoryId);
        }
        
        $this->view->addVar('article', $article);
        $this->view->addVar('category', $category);
        $this->view->addVar('subcategory', $subcategory);
        $this->view->addVar('pageTitle', $article->title . " | Простая CMS");
        
        $this->view->render('article/view.php');
    }
    
    /**
     * Отображает архив статей
     */
    public function archiveAction()
    {
        $categoryId = (int)$this->getParam('categoryId');
        $subcategoryId = (int)$this->getParam('subcategoryId');
        
        // Загружаем статьи
        $Article = new Article();
        
        // Если указана подкатегория, фильтруем по ней
        if ($subcategoryId && $categoryId) {
            $data = $Article->getListBySubcategory($categoryId, $subcategoryId, 10000, 'publicationDate DESC', true);
        } else if ($categoryId && $subcategoryId === 0) {
            // Статьи только по категории, исключая подкатегории
            $data = $Article->getListWithoutSubcategory($categoryId, 10000, 'publicationDate DESC', true);
        } else if ($categoryId) {
            // Статьи только по категории
            $data = $Article->getList(10000, $categoryId, 'publicationDate DESC', true);
        } else {
            // Все статьи
            $data = $Article->getList(10000, null, 'publicationDate DESC', true);
        }
        
        $articles = $data['results'];
        $totalRows = $data['totalRows'];
        
        // Загружаем категории
        $Category = new Category();
        $categoryData = $Category->getList();
        $categories = [];
        foreach ($categoryData['results'] as $category) {
            $categories[$category->id] = $category;
        }
        
        // Загружаем подкатегории
        $Subcategory = new Subcategory();
        $subcategoryData = $Subcategory->getList();
        $subcategories = [];
        foreach ($subcategoryData['results'] as $subcategory) {
            $subcategories[$subcategory->id] = $subcategory;
        }
        
        $category = null;
        if ($categoryId) {
            $categoryModel = new Category();
            $category = $categoryModel->getById($categoryId);
        }
        
        $subcategory = null;
        if ($subcategoryId) {
            $subcategoryModel = new Subcategory();
            $subcategory = $subcategoryModel->getById($subcategoryId);
        }
        
        $this->view->addVar('articles', $articles);
        $this->view->addVar('totalRows', $totalRows);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('category', $category);
        $this->view->addVar('subcategory', $subcategory);
        
        $pageHeading = "Архив статей";
        if ($subcategory) {
            $pageHeading = $subcategory->name . " (" . ($category ? $category->name : "Все категории") . ")";
        } else if ($category) {
            $pageHeading = $category->name;
        }
        
        $this->view->addVar('pageHeading', $pageHeading);
        $this->view->addVar('pageTitle', $pageHeading . " | Widget News");
        
        $this->view->render('article/archive.php');
    }
    
    /**
     * Отображает список статей по категории
     */
    public function categoryAction()
    {
        $categoryId = (int)$this->getParam('id');
        
        if (!$categoryId) {
            header("Location: /");
            exit;
        }
        
        // Перенаправляем на архив с параметром категории
        header("Location: /archive?categoryId=" . $categoryId);
        exit;
    }
    
    /**
     * Отображает список статей по подкатегории
     */
    public function subcategoryAction()
    {
        $subcategoryId = (int)$this->getParam('id');
        
        if (!$subcategoryId) {
            header("Location: /");
            exit;
        }
        
        // Перенаправляем на архив с параметром подкатегории
        header("Location: /archive?subcategoryId=" . $subcategoryId);
        exit;
    }
}