<?php
namespace application\controllers\admin;

use application\models\Article;
use application\models\Category;
use application\models\Subcategory;
use application\models\UserModel;
use ItForFree\SimpleMVC\Config;

/**
 * Admin controller for managing articles
 */
class ArticlesController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';

    /**
     * List all articles
     */
    public function indexAction()
    {
        $Article = new Article();
        $data = $Article->getList(100000, null, 'publicationDate DESC', true);
        $articles = $data['results'];

        // Load categories and subcategories for display
        $Category = new Category();
        $categoryData = $Category->getList();
        $categories = [];
        foreach ($categoryData['results'] as $category) {
            $categories[$category->id] = $category;
        }

        $Subcategory = new Subcategory();
        $subcategoryData = $Subcategory->getList();
        $subcategories = [];
        foreach ($subcategoryData['results'] as $subcategory) {
            $subcategories[$subcategory->id] = $subcategory;
        }

        $this->view->addVar('articles', $articles);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('pageTitle', 'Article Management');

        $this->view->render('admin/articles/index.php');
    }

    /**
     * Add new article
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');

        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $Article = new Article();
                $Article->storeFormValues($_POST);

                // Handle authors
                $Article->authorIds = isset($_POST['authorIds']) ? $_POST['authorIds'] : [];

                // Validate subcategory matches category
                $errors = [];
                $categoryId = (int)$_POST['categoryId'];
                $subcategoryId = (int)$_POST['subcategoryId'];

                if ($subcategoryId > 0) {
                    $Subcategory = new Subcategory();
                    $subcategory = $Subcategory->getById($subcategoryId);
                    if ($subcategory && $subcategory->categoryId != $categoryId) {
                        $errors[] = "Error: Selected category does not match subcategory!";
                    }
                }

                if (empty($errors)) {
                    $Article->insert();
                    $this->redirect($Url::link("admin/articles/index"));
                } else {
                    // Return to form with errors
                    $this->view->addVar('article', $Article);
                    $this->view->addVar('errors', $errors);
                    $this->prepareFormData();
                    $this->view->render('admin/articles/edit.php');
                }
            } elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/index"));
            }
        } else {
            $this->view->addVar('article', new Article());
            $this->view->addVar('formAction', 'add');
            $this->view->addVar('pageTitle', 'New Article');
            $this->prepareFormData();
            $this->view->render('admin/articles/edit.php');
        }
    }

    /**
     * Edit existing article
     */
    public function editAction()
    {
        $id = $_GET['id'] ?? null;
        $Url = Config::get('core.router.class');

        if (!$id) {
            $this->redirect($Url::link("admin/articles/index"));
            return;
        }

        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $Article = new Article();
                $article = $Article->getById($id);

                if (!$article) {
                    $this->redirect($Url::link("admin/articles/index"));
                    return;
                }

                // Validate subcategory matches category
                $errors = [];
                $categoryId = (int)$_POST['categoryId'];
                $subcategoryId = (int)$_POST['subcategoryId'];

                if ($subcategoryId > 0) {
                    $Subcategory = new Subcategory();
                    $subcategory = $Subcategory->getById($subcategoryId);
                    if ($subcategory && $subcategory->categoryId != $categoryId) {
                        $errors[] = "Error: Selected category does not match subcategory!";
                    }
                }

                if (empty($errors)) {
                    $article->storeFormValues($_POST);
                    // Handle authors
                    $article->authorIds = isset($_POST['authorIds']) ? $_POST['authorIds'] : [];
                    $article->update();
                    $this->redirect($Url::link("admin/articles/index"));
                } else {
                    // Return to form with errors
                    $article->storeFormValues($_POST);
                    $article->authorIds = isset($_POST['authorIds']) ? $_POST['authorIds'] : [];
                    $this->view->addVar('article', $article);
                    $this->view->addVar('errors', $errors);
                    $this->prepareFormData();
                    $this->view->render('admin/articles/edit.php');
                }
            } elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/index"));
            }
        } else {
            $Article = new Article();
            $article = $Article->getById($id);

            if (!$article) {
                $this->redirect($Url::link("admin/articles/index"));
                return;
            }

            $this->view->addVar('article', $article);
            $this->view->addVar('formAction', 'edit');
            $this->view->addVar('pageTitle', 'Edit Article');
            $this->prepareFormData();
            $this->view->render('admin/articles/edit.php');
        }
    }

    /**
     * Delete article
     */
    public function deleteAction()
    {
        $id = $_GET['id'] ?? null;
        $Url = Config::get('core.router.class');

        if (!$id) {
            $this->redirect($Url::link("admin/articles/index"));
            return;
        }

        if (!empty($_POST)) {
            if (!empty($_POST['deleteArticle'])) {
                $Article = new Article();
                $article = $Article->getById($id);

                if ($article) {
                    $article->delete();
                }

                $this->redirect($Url::link("admin/articles/index"));
            } elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/edit&id=$id"));
            }
        } else {
            $Article = new Article();
            $article = $Article->getById($id);

            if (!$article) {
                $this->redirect($Url::link("admin/articles/index"));
                return;
            }

            $this->view->addVar('article', $article);
            $this->view->addVar('pageTitle', 'Delete Article');
            $this->view->render('admin/articles/delete.php');
        }
    }

    /**
     * Prepare common form data
     */
    private function prepareFormData()
    {
        // Load categories
        $Category = new Category();
        $categoryData = $Category->getList();
        $this->view->addVar('categories', $categoryData['results']);

        // Load subcategories and group by category
        $Subcategory = new Subcategory();
        $subcategoryData = $Subcategory->getList();
        $groupedSubcategories = [];
        foreach ($subcategoryData['results'] as $subcategory) {
            $categoryId = $subcategory->categoryId;
            if (!isset($groupedSubcategories[$categoryId])) {
                $groupedSubcategories[$categoryId] = [];
            }
            $groupedSubcategories[$categoryId][] = $subcategory;
        }
        $this->view->addVar('subcategories', $subcategoryData['results']);
        $this->view->addVar('groupedSubcategories', $groupedSubcategories);

        // Load users for authors
        $UserModel = new UserModel();
        $userData = $UserModel->getList();
        $this->view->addVar('users', $userData['results']);
    }
}