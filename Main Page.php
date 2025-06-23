<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sample Web Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 20px;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Welcome to My Sample Web Page</h1>
    <p>This is a simple HTML page with some styling and interactivity.</p>

    <img src="https://via.placeholder.com/300x200" alt="Sample Image" />

    <br><br>

    <button onclick="sayHello()">Click Me</button>

    <script>
        function sayHello() {
            alert('Hello! Thanks for visiting.');
        }
    </script>

</body>
</html>