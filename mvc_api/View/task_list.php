<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="View/css/style.css">
</head>

<body>
    <div class="container">
        <div class="page-title">
            <h1>Task List</h1>
            <div>Hi, username "<?php echo $_SESSION["username"]; ?>"</div>
        </div>

        <form id="searchForm">
            <input type="text" id="searchQuery" placeholder="Search tasks by task_title">
            <button type="submit">Search</button>
            <button type="reset">Cancel</button>
        </form>

        <table id="taskTable" class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Status</th>

                    <th>Edit Actions</th> <!-- Cột hành động thêm vào -->
                    <th>Delete Actions</th> <!-- Cột hành động thêm vào -->
                </tr>
            </thead>
            <tbody>
                <!-- Tasks will be displayed here -->
            </tbody>
        </table>

        <div class="form-container">
            <form id="addTaskForm">
                <h2>Add New Task</h2>
                <input type="text" id="addTaskTitle" name="task_title" placeholder="Task Title" required>
                <input type="text" id="addTaskContent" name="task_content" placeholder="Task Content" required>
                <select name="task_status" id="addTaskStatus">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>

                <button type="submit">Save</button>
                <button type="reset">Cancel</button>
            </form>

            <form id="editTaskForm">
                <h2>Edit Task</h2>
                <input type="hidden" id="editTaskId" name="id"> <!-- Ẩn input id của task -->
                <input type="text" id="editTaskTitle" name="task_title" placeholder="Task Title" required>
                <input type="text" id="editTaskContent" name="task_content" placeholder="Task Content" required>
                <select name="task_status" id="editTaskStatus">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>

                <button type="submit">Save</button>
                <button type="reset">Cancel</button>
            </form>
        </div>

        <a class="btn" href="index.php/tasks?search_term=t">url</a>
        <a class="btn" href="../"> Trang chủ</a>
        <button id="logoutBtn" class="btn"> Đăng xuất </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '#logoutBtn', function () {
            $.ajax({
                url: 'index.php/logout',
                method: 'POST',
                success: function () {
                    alert(`Logout successfully`);
                    window.location.href = '';
                },
                error: function (xhr, status, error) {
                    alert(`Failed to logout!`);
                }
            })
        })
        function getTasks() {
            let tableBody = $('#taskTable tbody');
            tableBody.empty();

            $.ajax({
                url: 'index.php/tasks',
                method: 'GET',
                success: function (response) {
                    let tasks = JSON.parse(response);

                    if (tasks["data"].length === 0)
                        tableBody.append(`
                        <tr>
                            <td colspan="50">Chưa có task nào!</td>
                        </tr> 
                        `);
                    else
                        tasks["data"].forEach(function (task) {
                            tableBody.append(`
                        <tr>
                            <td>${task.id}</td>
                            <td>${task.title}</td>
                            <td>${task.content}</td>
                            <td>${task.status}</td>
                            <td>
                                <button class="editButton" data-task_id="${task.id}" data-task_title="${task.title}" data-task_content="${task.content}" data-task_status="${task.status}">Edit</button>
                            </td>
                            <td><button class="delete-btn" data-task_id="${task.id}">Delete</button></td>
                        </tr> 
                        `);
                        });

                    $('.editButton').click(function () {
                        let taskId = $(this).data('task_id');
                        let taskTitle = $(this).data('task_title');
                        let taskContent = $(this).data('task_content');
                        let taskStatus = $(this).data('task_status');

                        $('#editTaskId').val(taskId);
                        $('#editTaskTitle').val(taskTitle);
                        $('#editTaskContent').val(taskContent);
                        $('#editTaskStatus').val(taskStatus);
                    });
                }
                ,
                error: function (xhr, status, error) {
                    tableBody.append(`
                        <tr>
                            <td colspan="50">Failed to get tasks!</td>
                        </tr> 
                        `);
                }
            });
        }
        $(document).ready(function () {
            getTasks();
        });

        $('#addTaskForm').submit(function (event) {
            event.preventDefault();
            let task_title = $('#addTaskTitle').val();
            let task_content = $('#addTaskContent').val();
            let task_status = $('#addTaskStatus').val();

            $.ajax({
                url: 'index.php/tasks',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ task_title: task_title, task_content: task_content, task_status: task_status }), // Gửi dữ liệu qua POST
                success: function (response) {
                    alert('Task added successfully!');
                    getTasks();

                    $('#addTaskTitle').val("");
                    $('#addTaskContent').val("");
                    $('#addTaskStatus').val("");
                },
                error: function (xhr, status, error) {
                    alert(`Failed to add task!`);
                }
            });
        });

        $('#editTaskForm').submit(function (event) {
            event.preventDefault();
            let task_id = $('#editTaskId').val();
            let task_title = $('#editTaskTitle').val();
            let task_content = $('#editTaskContent').val();
            let task_status = $('#editTaskStatus').val();

            $.ajax({
                url: `index.php/edit-task`,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify({ task_id: task_id, task_title: task_title, task_content: task_content, task_status: task_status }),
                success: function (response) {
                    alert('Task updated successfully!');
                    getTasks();

                    $('#editTaskId').val("");
                    $('#editTaskTitle').val("");
                    $('#editTaskContent').val("");
                    $('#editTaskStatus').val("");
                },
                error: function (xhr, status, error) {
                    alert(`Failed to edit task!`);
                }
            });
        });

        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('task_id'); // Lấy ID task từ thuộc tính data-task_id
            deleteTask(id);
        });
        function deleteTask(id) {
            $.ajax({
                url: `index.php/delete-task/${id}`, // Sửa URL để đúng với cấu trúc API
                method: 'DELETE',
                success: function (response) {
                    alert('Task deleted successfully!');
                    getTasks();
                },
                error: function (xhr, status, error) {
                    alert(`Failed to delete task!`);
                }
            });
        }

        $('#searchForm').submit(function (event) {
            event.preventDefault();
            let tableBody = $('#taskTable tbody');
            tableBody.empty();

            $.ajax({
                url: 'index.php/tasks?search_term=' + $('#searchQuery').val(),
                method: 'GET',
                success: function (response) {
                    let tasks = JSON.parse(response);

                    if (tasks["data"].length === 0)
                        tableBody.append(`
                        <tr>
                            <td colspan="50">Không tồn tại tên task có chứa chuỗi ký tự "${$('#searchQuery').val()}"!</td>
                        </tr>
                        `);
                    else
                        tasks["data"].forEach(function (task) {
                            tableBody.append(`
                        <tr>
                            <td>${task.id}</td>
                            <td>${task.title}</td>
                            <td>${task.content}</td>
                            <td>${task.status}</td>
                            <td>
                                <button class="editButton" data-task_id="${task.id}" data-task_title="${task.title}" data-task_content="${task.content}" data-task_status="${task.status}">Edit</button>
                            </td>
                            <td><button class="delete-btn" data-task_id="${task.id}">Delete</button></td>
                        </tr> 
                        `);
                        });

                    $('.editButton').click(function () {
                        let taskId = $(this).data('task_id');
                        let taskTitle = $(this).data('task_title');
                        let taskContent = $(this).data('task_content');
                        let taskStatus = $(this).data('task_status');

                        $('#editTaskId').val(taskId);
                        $('#editTaskTitle').val(taskTitle);
                        $('#editTaskContent').val(taskContent);
                        $('#editTaskStatus').val(taskStatus);
                    });
                },
                error: function (xhr, status, error) {
                    tableBody.append(`
                        <tr>
                            <td colspan="50">Failed to get tasks by "${$('#searchQuery').val()}"!</td>
                        </tr> 
                        `);
                }
            });
        });
    </script>
</body>

</html>