<?php
namespace application\models;

/**
 * Класс для обработки статей
 */
class Article extends BaseExampleModel
{
    public string $tableName = "articles";
    
    public string $orderBy = 'publicationDate DESC';
    
    // Свойства
    /**
    * @var int ID статей из базы данных
    */
    public ?int $id = null;

    /**
    * @var string Дата первой публикации статьи
    */
    public $publicationDate = null;

    /**
    * @var string Полное название статьи
    */
    public $title = null;

     /**
     * @var int ID категории статьи
     */
     public ?int $categoryId = null;
     
     /**
     * @var int ID подкатегории статьи
     */
     public ?int $subcategoryId = null;

    /**
    * @var string Краткое описание статьи
    */
    public $summary = null;

    /**
    * @var string HTML содержание статьи
    */
    public $content = null;
    
    /**
    * @var int Активна ли статья (0 или 1)
    */
    public int $active = 1;  // Добавляем новое поле со значением по умолчанию 1
    
    /**
     * @var array IDs of authors for this article
     */
    public array $authorIds = [];
    
    /**
     * @var array Author information (id and login) for this article
     */
    public array $authors = [];

    /**
     * Создаст объект статьи
     * 
     * @param array $data массив значений (столбцов) строки таблицы статей
     */
    public function __construct($data=array())
    {
        parent::__construct();
        
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }
        
        if (isset($data['publicationDate'])) {
            $this->publicationDate = (string) $data['publicationDate'];     
        }

        if (isset($data['title'])) {
            $this->title = $data['title'];        
        }
        
        if (isset($data['categoryId'])) {
            $this->categoryId = (int) $data['categoryId'];
        }
        
        if (isset($data['subcategoryId']) && $data['subcategoryId'] !== null && $data['subcategoryId'] !== '') {
            $this->subcategoryId = (int) $data['subcategoryId'];
        } else {
            $this->subcategoryId = null;
        }
        
        if (isset($data['summary'])) {
            $this->summary = $data['summary'];
        }
        
        if (isset($data['content'])) {
            $this->content = $data['content'];  
        }
        
        // Добавляем обработку поля active
        if (isset($data['active'])) {
            $this->active = (int) $data['active'];  
        }
    }


    /**
     * Устанавливаем свойства с помощью значений формы редактирования записи в заданном массиве
     *
     * @param array Значения записи формы
     */
    public function storeFormValues($params) {
        // Сохраняем все параметры
        $this->__construct($params);

        // Разбираем и сохраняем дату публикации
        if (isset($params['publicationDate'])) {
            $publicationDate = explode('-', $params['publicationDate']);
            if (count($publicationDate) == 3) {
                list($y, $m, $d) = $publicationDate;
                $this->publicationDate = mktime(0, 0, 0, $m, $d, $y);
            }
        }
        
        // Обрабатываем поле active (для checkbox)
        if (!isset($params['active'])) {
            $this->active = 0; // Если checkbox не отмечен
        }
        
        // Обрабатываем поле subcategory
        // For now, we'll store the value but won't save it to DB until column exists
        if (isset($params['subcategoryId'])) {
            $subcategoryId = (int) $params['subcategoryId'];
            // If subcategoryId is 0 (meaning "(none)" was selected), set it to null
            $this->subcategoryId = ($subcategoryId > 0) ? $subcategoryId : null;
        }
    }


    /**
     * Возвращаем объект статьи соответствующий заданному ID статьи
     *
     * @param int ID статьи
     * @param string Имя таблицы (опционально)
     * @return Article|null Объект статьи или null, если запись не найдена или возникли проблемы
     */
    public function getById(int $id, string $tableName = ''): ?\ItForFree\SimpleMVC\MVC\Model {
        $tableName = !empty($tableName) ? $tableName : $this->tableName;
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) AS publicationDate FROM " . $tableName . " WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
        if ($row) {
            $article = new Article($row);
            
            // Получаем авторов статьи
            $sql = "SELECT u.id, u.login FROM users u
                    INNER JOIN article_authors aa ON u.id = aa.user_id
                    WHERE aa.article_id = :articleId";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":articleId", $id, \PDO::PARAM_INT);
            $st->execute();
            
            $authorIds = array();
            $authors = array();
            while ($authorRow = $st->fetch()) {
                $authorIds[] = $authorRow['id'];
                $authors[] = $authorRow;
            }
            $article->authorIds = $authorIds;
            $article->authors = $authors;
            
            return $article;
        }
        
        return null;
    }


    /**
     * Возвращает все (или диапазон) объекты Article из базы данных
     *
     * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000000)
     * @return array Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
     */
    public function getList(int $numRows=100000): array {
        $args = func_get_args();
        $categoryId = isset($args[1]) ? $args[1] : null;
        $order = isset($args[2]) ? $args[2] : $this->orderBy;
        $includeInactive = isset($args[3]) ? $args[3] : false;
        
        if ($categoryId || count($args) > 1) {
            // Если передан categoryId или другие параметры, используем getListWithFilters
            return $this->getListWithFilters($numRows, $categoryId, $order, $includeInactive);
        } else {
            // Иначе используем стандартную реализацию
            $fromPart = "FROM " . $this->tableName;
            $whereClause = "WHERE active = 1";  // по умолчанию возвращаем только активные статьи
            $conditions = [];
            
            $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate)
                    AS publicationDate
                    $fromPart $whereClause
                    ORDER BY " . $this->orderBy . " LIMIT :numRows";
            
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
            
            $st->execute();
            $list = array();
while ($row = $st->fetch()) {
    $article = new Article($row);
    
                
                // Получаем авторов статьи
                $sql = "SELECT u.id, u.login FROM users u
                        INNER JOIN article_authors aa ON u.id = aa.user_id
                        WHERE aa.article_id = :articleId";
                $st_authors = $this->pdo->prepare($sql);
                $st_authors->bindValue(":articleId", $article->id, \PDO::PARAM_INT);
                $st_authors->execute();
                
                $authorIds = array();
                $authors = array();
                while ($authorRow = $st_authors->fetch()) {
                    $authorIds[] = $authorRow['id'];
                    $authors[] = $authorRow;
                }
                $article->authorIds = $authorIds;
                $article->authors = $authors;
                
                $list[] = $article;
            }

            // Получаем общее количество статей, которые соответствуют критерию
            $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
            $st = $this->pdo->prepare($sql);
            $st->execute();
            $totalRows = $st->fetch();
            
            return (array(
                "results" => $list,
                "totalRows" => $totalRows[0]
                )
            );
        }
    }
    
    /**
     * Возвращает все (или диапазон) объекты Article из базы данных с дополнительными параметрами фильтрации
     *
     * @param int $numRows Количество возвращаемых строк (по умолчанию = 100000)
     * @param int $categoryId Вернуть статьи только из категории с указанным ID
     * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
     * @param bool $includeInactive Включать ли неактивные статьи (по умолчанию = false)
     * @return Array Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
     */
    public function getListWithFilters($numRows=1000,
        $categoryId=null, $order="publicationDate DESC", $includeInactive=false) {
        $fromPart = "FROM " . $this->tableName;
        $whereClause = "";
        $conditions = [];
        
        // Добавляем условие для категории если указано
        if ($categoryId) {
            $conditions[] = "categoryId = :categoryId";
        }
        
        // Добавляем условие для активных статей, если не запрошены все
        if (!$includeInactive) {
            $conditions[] = "active = 1";
        }
        
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }
        
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate)
                AS publicationDate
                $fromPart $whereClause
                ORDER BY $order  LIMIT :numRows";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
    
        if ($categoryId) {
            $st->bindValue( ":categoryId", $categoryId, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $list = array();
while ($row = $st->fetch()) {
    $article = new Article($row);
    
            
            // Получаем авторов статьи
            $sql = "SELECT u.id, u.login FROM users u
                    INNER JOIN article_authors aa ON u.id = aa.user_id
                    WHERE aa.article_id = :articleId";
            $st_authors = $this->pdo->prepare($sql);
            $st_authors->bindValue(":articleId", $article->id, \PDO::PARAM_INT);
            $st_authors->execute();
            
            $authorIds = array();
            $authors = array();
            while ($authorRow = $st_authors->fetch()) {
                $authorIds[] = $authorRow['id'];
                $authors[] = $authorRow;
            }
            $article->authorIds = $authorIds;
            $article->authors = $authors;
            
            $list[] = $article;
        }

        // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        if ($categoryId) {
            $st->bindValue( ":categoryId", $categoryId, \PDO::PARAM_INT);
        }
        $st->execute();
        $totalRows = $st->fetch();
        
        return (array(
            "results" => $list,
            "totalRows" => $totalRows[0]
            )
        );
    }

    /**
     * Возвращает все (или диапазон) объекты Article из базы данных, отфильтрованные по подкатегории
     *
     * @param int $numRows Количество возвращаемых строк (по умолчанию = 10000)
     * @param int $categoryId ID категории
     * @param int $subcategoryId ID подкатегории
     * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
     * @param bool $includeInactive Включать ли неактивные статьи (по умолчанию = false)
     * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
     */
    public function getListBySubcategory($categoryId, $subcategoryId, $numRows=1000,
        $order="publicationDate DESC", $includeInactive=false) {
        $fromPart = "FROM " . $this->tableName;
        $whereClause = "WHERE categoryId = :categoryId AND subcategoryId = :subcategoryId";
        $conditions = [];
        
        // Добавляем условие для активных статей, если не запрошены все
        if (!$includeInactive) {
            $conditions[] = "active = 1";
            $whereClause .= " AND " . implode(" AND ", $conditions);
        }
        
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate)
                AS publicationDate
                $fromPart $whereClause
                ORDER BY $order LIMIT :numRows";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $subcategoryId, \PDO::PARAM_INT);
        
        $st->execute();
        $list = array();
while ($row = $st->fetch()) {
    $article = new Article($row);
    
            
            // Получаем авторов статьи
            $sql = "SELECT u.id, u.login FROM users u
                    INNER JOIN article_authors aa ON u.id = aa.user_id
                    WHERE aa.article_id = :articleId";
            $st_authors = $this->pdo->prepare($sql);
            $st_authors->bindValue(":articleId", $article->id, \PDO::PARAM_INT);
            $st_authors->execute();
            
            $authorIds = array();
            $authors = array();
            while ($authorRow = $st_authors->fetch()) {
                $authorIds[] = $authorRow['id'];
                $authors[] = $authorRow;
            }
            $article->authorIds = $authorIds;
            $article->authors = $authors;
            
            $list[] = $article;
        }

        // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $subcategoryId, \PDO::PARAM_INT);
        $st->execute();
        $totalRows = $st->fetch();
        
        return (array(
            "results" => $list,
            "totalRows" => $totalRows[0]
            )
        );
    }
    
    /**
     * Возвращает все (или диапазон) объекты Article из базы данных, отфильтрованные по категории и без подкатегории
     *
     * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000)
     * @param int $categoryId ID категории
     * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
     * @param bool $includeInactive Включать ли неактивные статьи (по умолчанию = false)
     * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
     */
    public function getListWithoutSubcategory($categoryId, $numRows=1000,
        $order="publicationDate DESC", $includeInactive=false) {
        $fromPart = "FROM " . $this->tableName;
        $whereClause = "WHERE categoryId = :categoryId AND (subcategoryId IS NULL OR subcategoryId = 0)";
        $conditions = [];
        
        // Добавляем условие для активных статей, если не запрошены все
        if (!$includeInactive) {
            $conditions[] = "active = 1";
            $whereClause .= " AND " . implode(" AND ", $conditions);
        }
        
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate)
                AS publicationDate
                $fromPart $whereClause
                ORDER BY $order LIMIT :numRows";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        
        $st->execute();
        $list = array();
while ($row = $st->fetch()) {
    $article = new Article($row);
    
            
            // Получаем авторов статьи
            $sql = "SELECT u.id, u.login FROM users u
                    INNER JOIN article_authors aa ON u.id = aa.user_id
                    WHERE aa.article_id = :articleId";
            $st_authors = $this->pdo->prepare($sql);
            $st_authors->bindValue(":articleId", $article->id, \PDO::PARAM_INT);
            $st_authors->execute();
            
            $authorIds = array();
            $authors = array();
            while ($authorRow = $st_authors->fetch()) {
                $authorIds[] = $authorRow['id'];
                $authors[] = $authorRow;
            }
            $article->authorIds = $authorIds;
            $article->authors = $authors;
            
            $list[] = $article;
        }

        // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        $totalRows = $st->fetch();
        
        return (array(
            "results" => $list,
            "totalRows" => $totalRows[0]
            )
        );
    }
    /**
     * Вставляем текущий объект Article в базу данных, устанавливаем его ID
     */
    public function insert() {

        // Есть уже у объекта Article ID?
        if ( !is_null( $this->id ) ) trigger_error ( "Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR );

        // Вставляем статью
        // Check if subcategoryId column exists in the database
        $columnCheck = $this->pdo->query("SHOW COLUMNS FROM articles LIKE 'subcategoryId'");
        if ($columnCheck->rowCount() > 0) {
            // Column exists, include subcategoryId in the query
            $sql = "INSERT INTO " . $this->tableName . " ( publicationDate, categoryId, subcategoryId, title, summary, content, active ) VALUES ( FROM_UNIXTIME(:publicationDate), :categoryId, :subcategoryId, :title, :summary, :content, :active )";
            $st = $this->pdo->prepare ( $sql );
            $st->bindValue( ":publicationDate", $this->publicationDate, \PDO::PARAM_INT );
            $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
            // Bind subcategoryId with proper handling of NULL values
            if ($this->subcategoryId !== null) {
                $st->bindValue( ":subcategoryId", $this->subcategoryId, \PDO::PARAM_INT );
            } else {
                $st->bindValue( ":subcategoryId", null, \PDO::PARAM_NULL );
            }
            $st->bindValue( ":title", $this->title, \PDO::PARAM_STR );
            $st->bindValue( ":summary", $this->summary, \PDO::PARAM_STR );
            $st->bindValue( ":content", $this->content, \PDO::PARAM_STR );
            $st->bindValue( ":active", $this->active, \PDO::PARAM_INT );
        } else {
            // Column doesn't exist, exclude subcategoryId from the query
            $sql = "INSERT INTO " . $this->tableName . " ( publicationDate, categoryId, title, summary, content, active ) VALUES ( FROM_UNIXTIME(:publicationDate), :categoryId, :title, :summary, :content, :active )";
            $st = $this->pdo->prepare ( $sql );
            $st->bindValue( ":publicationDate", $this->publicationDate, \PDO::PARAM_INT );
            $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
            $st->bindValue( ":title", $this->title, \PDO::PARAM_STR );
            $st->bindValue( ":summary", $this->summary, \PDO::PARAM_STR );
            $st->bindValue( ":content", $this->content, \PDO::PARAM_STR );
            $st->bindValue( ":active", $this->active, \PDO::PARAM_INT );
        }
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
        
        // Добавляем связи с авторами
        if (!empty($this->authorIds)) {
            foreach ($this->authorIds as $userId) {
                $sql = "INSERT INTO article_authors (article_id, user_id) VALUES (:article_id, :user_id)";
                $st = $this->pdo->prepare($sql);
                $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
                $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
                $st->execute();
            }
        }
    }

    /**
    * Обновляем текущий объект статьи в базе данных
    */
    public function update() {

      // Есть ли у объекта статьи ID?
      if ( is_null( $this->id ) ) trigger_error ( "Article::update(): "
              . "Attempt to update an Article object "
              . "that does not have its ID property set.", E_USER_ERROR );

      // Обновляем статью
      // Check if subcategoryId column exists in the database
      $columnCheck = $this->pdo->query("SHOW COLUMNS FROM articles LIKE 'subcategoryId'");
      if ($columnCheck->rowCount() > 0) {
          // Column exists, include subcategoryId in the query
          $sql = "UPDATE " . $this->tableName . " SET publicationDate=FROM_UNIXTIME(:publicationDate),"
                  . " categoryId=:categoryId, subcategoryId=:subcategoryId, title=:title, summary=:summary,"
                  . " content=:content, active=:active WHERE id = :id";
          
          $st = $this->pdo->prepare ( $sql );
          $st->bindValue( ":publicationDate", $this->publicationDate, \PDO::PARAM_INT );
          $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
          // Bind subcategoryId with proper handling of NULL values
          if ($this->subcategoryId !== null) {
              $st->bindValue( ":subcategoryId", $this->subcategoryId, \PDO::PARAM_INT );
          } else {
              $st->bindValue( ":subcategoryId", null, \PDO::PARAM_NULL );
          }
          $st->bindValue( ":title", $this->title, \PDO::PARAM_STR );
          $st->bindValue( ":summary", $this->summary, \PDO::PARAM_STR );
          $st->bindValue( ":content", $this->content, \PDO::PARAM_STR );
          $st->bindValue( ":active", $this->active, \PDO::PARAM_INT );
          $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      } else {
          // Column doesn't exist, exclude subcategoryId from the query
          $sql = "UPDATE " . $this->tableName . " SET publicationDate=FROM_UNIXTIME(:publicationDate),"
                  . " categoryId=:categoryId, title=:title, summary=:summary,"
                  . " content=:content, active=:active WHERE id = :id";
          
          $st = $this->pdo->prepare ( $sql );
          $st->bindValue( ":publicationDate", $this->publicationDate, \PDO::PARAM_INT );
          $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
          $st->bindValue( ":title", $this->title, \PDO::PARAM_STR );
          $st->bindValue( ":summary", $this->summary, \PDO::PARAM_STR );
          $st->bindValue( ":content", $this->content, \PDO::PARAM_STR );
          $st->bindValue( ":active", $this->active, \PDO::PARAM_INT );
          $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      }
      $st->execute();
      
      // Удаляем существующие связи с авторами
      $sql = "DELETE FROM article_authors WHERE article_id = :article_id";
      $st = $this->pdo->prepare($sql);
      $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
      $st->execute();
      
      // Добавляем новые связи с авторами
      if (!empty($this->authorIds)) {
          foreach ($this->authorIds as $userId) {
              $sql = "INSERT INTO article_authors (article_id, user_id) VALUES (:article_id, :user_id)";
              $st = $this->pdo->prepare($sql);
              $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
              $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
              $st->execute();
          }
      }
    }


    /**
     * Удаляем текущий объект статьи из базы данных
     */
    public function delete(): void {

      // Есть ли у объекта статьи ID?
      if ( is_null( $this->id ) ) trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );

      // Удаляем статью
      $st = $this->pdo->prepare ( "DELETE FROM " . $this->tableName . " WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }

    /**
     * Метод для мягкого удаления (деактивации) статьи
     */
    public function deactivate() {
        if ( is_null( $this->id ) ) trigger_error ( "Article::deactivate(): Attempt to deactivate an Article object that does not have its ID property set.", E_USER_ERROR );

        $st = $this->pdo->prepare ( "UPDATE " . $this->tableName . " SET active = 0 WHERE id = :id" );
        $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
        $st->execute();
        $this->active = 0;
    }

    /**
     * Метод для активации статьи
     */
    public function activate() {
        if ( is_null( $this->id ) ) trigger_error ( "Article::activate(): Attempt to activate an Article object that does not have its ID property set.", E_USER_ERROR );

        $st = $this->pdo->prepare ( "UPDATE " . $this->tableName . " SET active = 1 WHERE id = :id" );
        $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
        $st->execute();
        $this->active = 1;
    }
    
    /**
     * Get authors for this article as an array of user objects
     */
    public function getAuthors() {
        $sql = "SELECT u.id, u.login FROM users u
                INNER JOIN article_authors aa ON u.id = aa.user_id
                WHERE aa.article_id = :articleId";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":articleId", $this->id, \PDO::PARAM_INT);
        $st->execute();
        
        $authors = array();
        while ($row = $st->fetch()) {
            $authors[] = $row;
        }
        
        return $authors;
    }
}