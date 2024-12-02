<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .register-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .register-container form input[type="text"],
        .register-container form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .register-container form button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }

        .register-container form button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>

        <form id="register-form" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
            <p style="padding-top: 10px;">Already have an account? <a href="javascript:history.back()">Login</a></p>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#register-form').submit(function (event) {
            event.preventDefault();
            let username = $('#username').val();
            let password = $('#password').val();

            $.ajax({
                url: '../index.php/register',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ username: username, password: password }),
                success: function (response) {
                    alert('Register successfully! ');
                    window.location.href = 'javascript:history.back()';
                },
                error: function (xhr, status, error) {
                    alert(`Failed to register! response: ${xhr.responseText}`);
                }
            });
        });
    </script>
</body>

</html>