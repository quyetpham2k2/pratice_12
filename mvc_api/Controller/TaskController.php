<?php
require_once 'Model/Tasks.php';

class TaskController
{
    // 200 OK: Yêu cầu thành công.
    // 201 Created: Tạo tài nguyên mới thành công.
    // 400 Bad Request: Dữ liệu yêu cầu không hợp lệ.
    // 401 Unauthorized: Không có quyền truy cập.
    // 404 Not Found: Không tìm thấy tài nguyên.
    // 500 Internal Server Error: Lỗi server.

    private $tasksDB;
    public function __construct()
    {
        $this->tasksDB = new Tasks();
    }
    public function searchTasksByTitle($task_title)
    {
        $tasks = $this->tasksDB->searchTasksByTitle($task_title);
        self::sendResponse(200, 'Success to get all Tasks of current user', $tasks);
    }
    public static function sendResponse($statusCode, $message, $data = [])
    {
        http_response_code($statusCode);
        $response = [
            'status' => $statusCode === 200 || $statusCode === 201 ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
            "backBtn" => "<a style='display:block;text-align:center;border:1px solid black;text-decoration:none;color:black;font-weight:bold;margin-top:16px;padding:8px;'href='javascript:history.back()'>Back</a>"
        ];

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit; // Đảm bảo dừng xử lý sau khi gửi phản hồi
    }

    public function getTaskOfCurrentUser()
    {
        $tasks = $this->tasksDB->getTaskOfCurrentUser();
        self::sendResponse(200, 'Success to get all Tasks of current user', $tasks);
    }
    public function createTask()
    {
        if (!isset($_SESSION["user_id"])) {
            self::sendResponse(401, "Unauthorized!");
            return;
        }

        $task_title = $_POST["task_title"];
        $task_content = $_POST["task_content"];
        $task_status = $_POST["task_status"];
        $user_id = $_SESSION["user_id"];

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        if ($data !== null) {
            $task_title = $data["task_title"];
            $task_content = $data["task_content"];
            $task_status = $data["task_status"];
        }

        $task = new Tasks();
        $task->title = $task_title;
        $task->content = $task_content;
        $task->status = $task_status;
        $task->user_id = $user_id;
        if ($task->insert())
            self::sendResponse(201, "Task created successfully");
        else
            self::sendResponse(500, "Failed to create task!");
    }
    public function editTask()
    {
        if (!isset($_SESSION["user_id"])) {
            self::sendResponse(401, "Unauthorized!");
            return;
        }
        $putData = json_decode(file_get_contents("php://input"), true);

        $id = $putData['task_id'] ?? null;
        $task_title = $putData['task_title'] ?? null;
        $task_content = $putData['task_content'] ?? null;
        $task_status = $putData['task_status'] ?? null;
        $user_id = $_SESSION["user_id"] ?? null;

        $task = Tasks::find($id);
        $task->title = $task_title;
        $task->content = $task_content;
        $task->status = $task_status;
        $task->user_id = $user_id;
        if ($task->update())
            self::sendResponse(200, "Task updated successfully");
        else
            self::sendResponse(500, "Failed to update task!");
    }
    public function deleteProduct($id = "")
    {
        if (!isset($_SESSION["user_id"])) {
            self::sendResponse(401, "Unauthorized!");
            return;
        } elseif (empty($id)) {
            self::sendResponse(400, "Task ID is required");
            return;
        }

        if (Tasks::find($id)->delete())
            self::sendResponse(200, "Task deleted successfully");
        else
            self::sendResponse(500, "Failed to delete task!");
    }
}
?>