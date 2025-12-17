<?php

namespace application\controllers;

/**
 * Контроллер для домашней страницы
 */
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Название страницы
     */
    public $homepageTitle = "Домашняя страница";
    
    /**
     * @var string Пусть к файлу макета 
     */
    public string $layoutPath = 'main.php';
      
    /**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction()
    {
        $this->view->addVar('homepageTitle', $this->homepageTitle); // передаём переменную по view
        
        // Загружаем статьи для главной страницы
        $Article = new \application\models\Article();
        $HOMEPAGE_NUM_ARTICLES = 5; // количество статей для главной страницы
        $data = $Article->getList($HOMEPAGE_NUM_ARTICLES);
        $articles = $data['results'];
        $totalRows = $data['totalRows'];
        
        // Загружаем категории
        $Category = new \application\models\Category();
        $categoryData = $Category->getList();
        $categories = [];
        foreach ($categoryData['results'] as $category) {
            $categories[$category->id] = $category;
        }
        
        // Загружаем подкатегории
        $Subcategory = new \application\models\Subcategory();
        $subcategoryData = $Subcategory->getList();
        $subcategories = [];
        foreach ($subcategoryData['results'] as $subcategory) {
            $subcategories[$subcategory->id] = $subcategory;
        }
        
        $this->view->addVar('articles', $articles);
        $this->view->addVar('totalRows', $totalRows);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        
        $this->view->render('homepage/index.php');
    }
}

