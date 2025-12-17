<?php
namespace application\models;

use ItForFree\SimpleMVC\MVC\Model;
use application\models\primitives\Role;
/**
 * Класс для обработки пользователей
 */
class UserModel extends Model
{
    // Свойства
    /**
    * @var string логин пользователя
    */
    public $login = null;
    
    public ?int $id = null;

    /**
    * @var string пароль пользователя
    */
    public $pass = null;
    
    /**
    * @var string роль пользователя
    */
    public $role = null;
    
    public $email = null;
    
    public $timestamp = null;
    
    /**
     * @var string Критерий сортировки строк таблицы
     */
    public string $orderBy = "login ASC";
    
    /**
     *  @var string название таблицы
     */
    public string $tableName = 'users';
    
    public $salt = null;
    

    public function insert()
    {
        // Проверяем, что пароль не пустой перед вставкой
        if (empty($this->pass)) {
            // Если пароль пустой, используем стандартный или бросаем исключение
            // В реальном приложении может потребоваться более сложная логика
            $this->pass = 'default_password';
        }
        
        // Хешируем пароль
        $this->salt = rand(0,1000000);
        $passWithSalt = $this->pass . $this->salt;
        $hashPass = password_hash($passWithSalt, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO $this->tableName (timestamp, login, salt, pass, role, email) VALUES (:timestamp, :login, :salt, :pass, :role, :email)"; 
        $st = $this->pdo->prepare ( $sql );
        $st->bindValue( ":timestamp", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
        $st->bindValue( ":login", $this->login, \PDO::PARAM_STR );
        $st->bindValue( ":salt", $this->salt, \PDO::PARAM_STR );
//        \DebugPrinter::debug($this->salt);
        
        $st->bindValue( ":pass", $hashPass, \PDO::PARAM_STR );
        
        $st->bindValue( ":role", $this->role, \PDO::PARAM_STR );
        $st->bindValue( ":email", $this->email, \PDO::PARAM_STR );
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        // Проверяем, нужно ли обновлять пароль
        if (!empty($this->pass)) {
            // Если пароль не пустой, обновляем его с хешированием
            $this->salt = rand(0,10000);
            $passWithSalt = $this->pass . $this->salt;
            $hashPass = password_hash($passWithSalt, PASSWORD_BCRYPT);
            
            $sql = "UPDATE $this->tableName SET timestamp=:timestamp, login=:login, pass=:pass, salt=:salt, role=:role, email=:email  WHERE id = :id";  
            $st = $this->pdo->prepare($sql);
            
            $st->bindValue(":timestamp", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
            $st->bindValue(":login", $this->login, \PDO::PARAM_STR);
            $st->bindValue(":pass", $hashPass, \PDO::PARAM_STR);
            $st->bindValue(":salt", $this->salt, \PDO::PARAM_STR);
            $st->bindValue(":role", $this->role, \PDO::PARAM_STR);
            $st->bindValue(":email", $this->email, \PDO::PARAM_STR);
            $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        } else {
            // Если пароль пустой, не обновляем его
            $sql = "UPDATE $this->tableName SET timestamp=:timestamp, login=:login, role=:role, email=:email  WHERE id = :id";  
            $st = $this->pdo->prepare($sql);
            
            $st->bindValue(":timestamp", (new \DateTime('NOW'))->format('Y-m-d H:i:s'), \PDO::PARAM_STMT);
            $st->bindValue(":login", $this->login, \PDO::PARAM_STR);
            $st->bindValue(":role", $this->role, \PDO::PARAM_STR);
            $st->bindValue(":email", $this->email, \PDO::PARAM_STR);
            $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        }
        
        $st->execute();
    }
    
    /**
     * Вернёт id пользователя
     * 
     * @return ?int
     */
    public function getId()
    {
        if ($this->userName !== 'guest'){
            $sql = "SELECT id FROM users where login = :userName";
            $st = $this->pdo->prepare($sql); 
            $st -> bindValue( ":userName", $this->userName, \PDO::PARAM_STR );
            $st -> execute();
            $row = $st->fetch();
            return $row['id']; 
        } else  {
            return null;
        }  
    }
    
    /**
     * Проверка логина и пароля пользователя.
     */
    public function getAuthData($login): ?array {
	$sql = "SELECT salt, pass FROM users WHERE login = :login";
	$st = $this->pdo->prepare($sql);
	$st->bindValue(":login", $login, \PDO::PARAM_STR);
	$st->execute();
	$authData = $st->fetch();
	return $authData ? $authData : null;
    }
    
    /**
     * Проверяем активность пользователя.
     */
    public function getRole($login): array {
	$sql = "SELECT role FROM users WHERE login = :login";
	$st = $this->pdo->prepare($sql);
	$st->bindValue(":login", $login, \PDO::PARAM_STR);
	$st->execute();	
	return $st->fetch();
    }
    
    /**
     * Проверяем активность пользователя и возвращаем его роль.
     */
    public function getUserRole($login): string {
	$sql = "SELECT role FROM users WHERE login = :login";
	$st = $this->pdo->prepare($sql);
	$st->bindValue(":login", $login, \PDO::PARAM_STR);
	$st->execute();	
	$result = $st->fetch();
	return $result ? $result['role'] : 'guest';
    }

}