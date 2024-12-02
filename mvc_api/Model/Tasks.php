<?php
require_once 'BaseModel.php';
class Tasks extends BaseModel
{
    public $tableName = 'tasks';
    public $columns = ['title', 'content', 'status', 'user_id'];

    public function getTaskOfCurrentUser()
    {
        $stmt = $this->connect->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION["user_id"]); // Thay thế :user_id bằng ID người dùng hiện tại
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
        return $result;
    }

    public function searchTasksByTitle($task_title)
    {
        $stmt = $this->connect->prepare("SELECT * FROM tasks WHERE title LIKE '%' :task_title '%'");
        $stmt->bindParam(':task_title', $task_title);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
        return $result;
    }
}
?>