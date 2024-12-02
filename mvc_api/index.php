<?php
require_once 'Controller/TaskController.php';
require_once 'Controller/UsersController.php';

$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [""];

session_start();
$taskController = new TaskController();
$userController = new UsersController();

switch ($_SERVER['REQUEST_METHOD']) {
    // r-me: GET
    case 'GET':
        if (!isset($_SESSION['user_id']))
            include_once 'View/login.php';
        elseif ($request[0] === 'tasks' && isset($_GET["search_term"]))
            $taskController->searchTasksByTitle($_GET['search_term']);
        elseif ($request[0] === 'tasks')
            $taskController->getTaskOfCurrentUser();
        else
            include_once 'View/task_list.php';
        // TaskController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: POST
    case 'POST':
        if ($request[0] === 'tasks')
            $taskController->createTask();
        elseif ($request[0] === 'register')
            $userController->register();
        elseif ($request[0] === 'login')
            $userController->login();
        elseif ($request[0] == 'logout')
            $userController->logout();
        else
            TaskController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: PUT
    case 'PUT':
        if ($request[0] === 'edit-task')
            $taskController->editTask();
        else
            TaskController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: DELETE
    case 'DELETE':
        if ($request[0] === 'delete-task')
            $taskController->deleteProduct($request[1]);
        else
            TaskController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: Default
    default:
        TaskController::sendResponse(405, "Method Not Allowed");
        break;
}
?>