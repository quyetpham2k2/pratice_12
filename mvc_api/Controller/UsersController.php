<?php
require_once 'Model/Users.php';

class UsersController
{
    private $usersDB;

    public function __construct()
    {
        $this->usersDB = new Users();
    }
    public static function sendResponse($statusCode, $message, $data = null)
    {
        http_response_code($statusCode);
        $response = [
            'status' => $statusCode === 200 || $statusCode === 201 ? 'success' : 'error',
            'message' => $message,
            "backBtn" => "<a style='display:block;text-align:center;border:1px solid black;text-decoration:none;color:black;font-weight:bold;margin-top:16px;padding:8px;'href='javascript:history.back()'>Back</a>"
        ];

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit; // Đảm bảo dừng xử lý sau khi gửi phản hồi
    }

    public function register()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        if ($data !== null) {
            $username = $data["username"];
            $password = $data["password"];
        }

        $user = new Users();
        $user->username = $username;
        $user->password = $password;
        if (count($user->getUserByUsername()) > 0)
            self::sendResponse(409, "Username already exists!");

        if ($user->insert())
            self::sendResponse(201, "user created successfully");
        else
            self::sendResponse(500, "Failed to create user!");
    }
    public function login()
    {
        $username = $_POST['username'] ?? "";
        $password = $_POST['password'] ?? "";

        $inputData = json_decode(file_get_contents("php://input"), true);
        if (isset($inputData['username']) && isset($inputData['password'])) {
            $username = $inputData['username'];
            $password = $inputData['password'];
        }

        $user = new Users();
        $user->username = $username;
        $user->password = $password;

        $userCheck = $user->getUserByUsername();
        if (count($userCheck) > 0)
            if ($userCheck[0]->password == $password) {
                $_SESSION['user_id'] = $userCheck[0]->id;
                $_SESSION["username"] = $userCheck[0]->username;
                self::sendResponse(200, "Login successfully {$userCheck[0]->id}");
            } else
                self::sendResponse(401, "Wrong password!");
        else
            self::sendResponse(404, "User not found!");
    }
    public function logout()
    {
        unset($_SESSION['user_id']);
        self::sendResponse(200, "Logout successfully");
    }
}
?>