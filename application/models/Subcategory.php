<?php
namespace application\models;

/**
 * Класс для обработки подкатегорий статей
 */
class Subcategory extends BaseExampleModel
{
    public string $tableName = "subcategories";
    
    public string $orderBy = 'name ASC';
    
    // Свойства

    /**
    * @var int ID подкатегории из базы данных
    */
    public ?int $id = null;

    /**
    * @var string Название подкатегории
    */
    public $name = null;

    /**
    * @var int ID категории, к которой относится подкатегория
    */
    public ?int $categoryId = null;


    /**
    * Устанавливаем свойства объекта с использованием значений в передаваемом массиве
    *
    * @param array Значения свойств
    */

    public function __construct( $data=array() ) {
        parent::__construct();
        
        if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
        if ( isset( $data['name'] ) ) $this->name = $data['name'];
        if ( isset( $data['categoryId'] ) ) $this->categoryId = (int) $data['categoryId'];
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
    * Возвращаем объект Subcategory, соответствующий заданному ID
    *
    * @param int ID подкатегории
    * @return Subcategory|false Объект Subcategory или false, если запись не была найдена или в случае другой ошибки
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
            return new Subcategory($row);
        }
        return null;
    }


    /**
    * Возвращаем все (или диапазон) объектов Subcategory из базы данных
    *
    * @param int Optional Количество возвращаемых строк (по умолчанию = all)
    * @param int Optional ID категории для фильтрации подкатегорий (по умолчанию = null)
    * @param string Optional Столбец, по которому сортируются подкатегории (по умолчанию = "name ASC")
    * @return Array|false Двух элементный массив: results => массив с объектами Subcategory; totalRows => общее количество подкатегорий
    */
    public function getList(int $numRows = 100000): array
    {
        $args = func_get_args();
        $order = isset($args[2]) ? $args[2] : $this->orderBy;
        $categoryId = isset($args[1]) ? $args[1] : null;
        
        $fromPart = "FROM " . $this->tableName;
        $whereClause = "";
        $conditions = [];
        
        // Добавляем условие для категории если указано
        if ($categoryId) {
            $conditions[] = "categoryId = :categoryId";
        }
        
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }
        
        $sql = "SELECT * $fromPart $whereClause
                ORDER BY $order LIMIT :numRows";

        $st = $this->pdo->prepare( $sql );
        $st->bindValue( ":numRows", $numRows, \PDO::PARAM_INT );
        
        if ($categoryId) {
            $st->bindValue( ":categoryId", $categoryId, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $list = array();

        while ( $row = $st->fetch() ) {
          $subcategory = new Subcategory( $row );
          $list[] = $subcategory;
        }

        // Получаем общее количество подкатегорий, которые соответствуют критериям
        $sql = "SELECT COUNT(*) AS totalRows $fromPart $whereClause";
        $st = $this->pdo->prepare( $sql );
        
        if ($categoryId) {
            $st->bindValue( ":categoryId", $categoryId, \PDO::PARAM_INT);
        }
        
        $st->execute();
        $totalRows = $st->fetch();
        return (array("results" => $list, "totalRows" => $totalRows[0]));
    }


    /**
    * Вставляем текущий объект Subcategory в базу данных и устанавливаем его свойство ID.
    */

    public function insert() {

      // У объекта Subcategory уже есть ID?
      if ( !is_null( $this->id ) ) trigger_error ( "Subcategory::insert(): Attempt to insert a Subcategory object that already has its ID property set (to $this->id).", E_USER_ERROR );

      // Вставляем подкатегорию
      $sql = "INSERT INTO " . $this->tableName . " ( name, categoryId ) VALUES ( :name, :categoryId )";
      $st = $this->pdo->prepare ( $sql );
      $st->bindValue( ":name", $this->name, \PDO::PARAM_STR );
      $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
      $st->execute();
      $this->id = $this->pdo->lastInsertId();
    }


    /**
    * Обновляем текущий объект Subcategory в базе данных.
    */

    public function update() {

      // У объекта Subcategory есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Subcategory::update(): Attempt to update a Subcategory object that does not have its ID property set.", E_USER_ERROR );

      // Обновляем подкатегорию
      $sql = "UPDATE " . $this->tableName . " SET name=:name, categoryId=:categoryId WHERE id = :id";
      $st = $this->pdo->prepare ( $sql );
      $st->bindValue( ":name", $this->name, \PDO::PARAM_STR );
      $st->bindValue( ":categoryId", $this->categoryId, \PDO::PARAM_INT );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }


    /**
    * Удаляем текущий объект Subcategory из базы данных.
    */

    public function delete(): void {

      // У объекта Subcategory есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Subcategory::delete(): Attempt to delete a Subcategory object that does not have its ID property set.", E_USER_ERROR );

      // Удаляем подкатегорию
      $st = $this->pdo->prepare ( "DELETE FROM " . $this->tableName . " WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }
}