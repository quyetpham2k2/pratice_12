<?php
require_once 'BaseModel.php';
class Users extends BaseModel
{
    public $tableName = 'users';
    public $columns = ['username', 'password'];

    public function getUserByUsername()
    {
        $sql = "select * from $this->tableName where username = :username";
        $stmt = $this->connect->prepare($sql);
        $stmt->execute(['username' => $this->username]);
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
        return $result;
    }
}
?>