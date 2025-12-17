<?php
namespace application\models;

/**
 * Класс для обработки категорий статей
 */
class Category extends BaseExampleModel
{
    public string $tableName = "categories";
    
    public string $orderBy = 'name ASC';
    
    // Свойства

    /**
    * @var int ID категории из базы данных
    */
    public ?int $id = null;

    /**
    * @var string Название категории
    */
    public $name = null;

    /**
    * @var string Короткое описание категории
    */
    public $description = null;


    /**
    * Устанавливаем свойства объекта с использованием значений в передаваемом массиве
    *
    * @param array Значения свойств
    */

    public function __construct( $data=array() ) {
        parent::__construct();
        
        if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
        if ( isset( $data['name'] ) ) $this->name = $data['name'];
        if ( isset( $data['description'] ) ) $this->description = $data['description'];
    }

    /**
    * Устанавливаем свойства объекта с использованием значений из формы редактирования
    *
    * @param array Значения из формы редактирования
    */

    public function storeFormValues ( $params ) {

      // Store all the parameters
      $this->__construct( $params );
    }


    /**
    * Возвращаем объект Category, соответствующий заданному ID
    *
    * @param int ID категории
    * @return Category|false Объект Category object или false, если запись не была найдена или в случае другой ошибки
    */

    public function getById(int $id, string $tableName = ''): ?\ItForFree\SimpleMVC\MVC\Model
    {
        $tableName = !empty($tableName) ? $tableName : $this->tableName;
        $sql = "SELECT * FROM " . $tableName . " WHERE id = :id";
        $st = $this->pdo->prepare( $sql );
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        if ($row) {
            return new Category($row);
        }
        return null;
    }


    /**
    * Возвращаем все (или диапазон) объектов Category из базы данных
    *
    * @param int Optional Количество возвращаемых строк (по умолчаниюt = all)
    * @param string Optional Столбец, по которому сортируются категории(по умолчанию = "name ASC")
    * @return Array|false Двух элементный массив: results => массив с объектами Category; totalRows => общее количество категорий
    */
    public function getList(int $numRows = 1000000): array
    {
        $args = func_get_args();
        $order = isset($args[1]) ? $args[1] : $this->orderBy;
        
        $fromPart = "FROM " . $this->tableName;
        $sql = "SELECT * $fromPart
                ORDER BY $order LIMIT :numRows";

        $st = $this->pdo->prepare( $sql );
        $st->bindValue( ":numRows", $numRows, \PDO::PARAM_INT );
        $st->execute();
        $list = array();

        while ( $row = $st->fetch() ) {
          $category = new Category( $row );
          $list[] = $category;
        }

        // Получаем общее количество категорий, которые соответствуют критериям
        $sql = "SELECT COUNT(*) AS totalRows $fromPart";
        $totalRows = $this->pdo->query( $sql )->fetch();
        return (array("results" => $list, "totalRows" => $totalRows[0]));
    }


    /**
    * Вставляем текущий объект Category в базу данных и устанавливаем его свойство ID.
    */

    public function insert() {

      // У объекта Category уже есть ID?
      if ( !is_null( $this->id ) ) trigger_error ( "Category::insert(): Attempt to insert a Category object that already has its ID property set (to $this->id).", E_USER_ERROR );

      // Вставляем категорию
      $sql = "INSERT INTO " . $this->tableName . " ( name, description ) VALUES ( :name, :description )";
      $st = $this->pdo->prepare ( $sql );
      $st->bindValue( ":name", $this->name, \PDO::PARAM_STR );
      $st->bindValue( ":description", $this->description, \PDO::PARAM_STR );
      $st->execute();
      $this->id = $this->pdo->lastInsertId();
    }


    /**
    * Обновляем текущий объект Category в базе данных.
    */

    public function update() {

      // У объекта Category  есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Category::update(): Attempt to update a Category object that does not have its ID property set.", E_USER_ERROR );

      // Обновляем категорию
      $sql = "UPDATE " . $this->tableName . " SET name=:name, description=:description WHERE id = :id";
      $st = $this->pdo->prepare ( $sql );
      $st->bindValue( ":name", $this->name, \PDO::PARAM_STR );
      $st->bindValue( ":description", $this->description, \PDO::PARAM_STR );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }


    /**
    * Удаляем текущий объект Category из базы данных.
    */

    public function delete(): void {

      // У объекта Category  есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Category::delete(): Attempt to delete a Category object that does not have its ID property set.", E_USER_ERROR );

      // Удаляем категорию
      $st = $this->pdo->prepare ( "DELETE FROM " . $this->tableName . " WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }
}