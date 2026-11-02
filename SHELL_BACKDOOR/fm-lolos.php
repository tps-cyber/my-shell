<?php
/* ===========================================================
   MINI FILE MANAGER - FULL VERSION (super mini, super lengkap)
   =========================================================== */

/* ---------------------------
   PASSWORD PROTECTION
   --------------------------- */
$PASSWORD = "224364";
session_start();

if (!isset($_SESSION['fm_login'])) {
    if (isset($_POST['pass']) && $_POST['pass'] == $PASSWORD) {
        $_SESSION['fm_login'] = true;
        header("Location: ?");
        exit;
    }
    echo "<style>
    body{font-family:Arial;background:#222;color:#fff;}
    input,button{padding:8px;width:100%}
    </style>
    <form method='post' style='width:300px;margin:150px auto;border:1px solid #555;padding:20px'>
        <h3>Login File Manager</h3>
        <input name='pass' type='password' placeholder='Password'><br><br>
        <button>Login</button>
    </form>";
    exit;
}

/* ---------------------------
   BASIC CONFIG
   --------------------------- */
$root = __DIR__;
$req  = isset($_GET['path']) ? trim($_GET['path'], "/") : "";
$path = realpath($root . "/" . $req);

if (!$path || strpos($path, $root) !== 0) die("Invalid path");

function h($s){ return htmlspecialchars($s); }
function list_dir($d){ return array_diff(scandir($d), ['.','..']); }

/* ---------------------------
   FILE ACTIONS
   --------------------------- */

# Create folder
if (isset($_POST['newfolder'])) mkdir($path."/".$_POST['newfolder']);

# Create empty file
if (isset($_POST['newfile'])) file_put_contents($path."/".$_POST['newfile'], "");

# Upload file
if (!empty($_FILES['upload']['name']))
    move_uploaded_file($_FILES['upload']['tmp_name'], $path."/".$_FILES['upload']['name']);

# Delete file/folder
if (isset($_GET['delete'])) {
    $target = $path."/".$_GET['delete'];
    if (is_dir($target)) rmdir($target);
    if (is_file($target)) unlink($target);
    header("Location: ?path=".urlencode($req)); exit;
}

# Rename
if (isset($_POST['rename_old'])) {
    rename($path."/".$_POST['rename_old'], $path."/".$_POST['rename_new']);
}

# Save edited file
if (isset($_POST['savefile']) && isset($_GET['edit'])) {
    file_put_contents($path."/".$_GET['edit'], $_POST['savefile']);
    header("Location: ?path=".urlencode($req)); exit;
}

# Download
if (isset($_GET['download'])) {
    $file = $path."/".$_GET['download'];
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".basename($file));
    readfile($file); exit;
}

/* ---------------------------
   EDIT FILE VIEW
   --------------------------- */
if (isset($_GET['edit'])) {
    $file = $path."/".$_GET['edit'];
    $content = file_get_contents($file);

    echo "<h2>Edit File: ".h($_GET['edit'])."</h2>
    <form method='post'>
        <textarea name='savefile' style='width:100%;height:350px'>".h($content)."</textarea><br><br>
        <button>SAVE</button>
    </form>
    <p><a href='?path=".h($req)."'>Back</a></p>";
    exit;
}

/* ---------------------------
   DARK MODE + STYLE
   --------------------------- */
echo "
<style>
body{font-family:Arial;background:#111;color:#eee;margin:20px;}
a{color:#6cf;text-decoration:none}
table{width:100%;border-collapse:collapse;margin-top:10px}
td,th{padding:8px;border:1px solid #444}
input,button{padding:6px;margin:2px}
body.light{background:#f5f5f5;color:#000;}
body.light a{color:#06c;}
body.light td, body.light th{border-color:#bbb;}
</style>

<script>
function toggleDark(){
    document.body.classList.toggle('light');
    localStorage.setItem('light', document.body.classList.contains('light'));
}
window.onload = () => {
    if(localStorage.getItem('light')=='true') document.body.classList.add('light');
};
</script>
";

/* ---------------------------
   BREADCRUMB VIRTUAL PATH
   --------------------------- */
echo "<h2>Mini File Manager</h2>";
echo "<button onclick='toggleDark()'>🌙/☀ Dark Mode</button><br><br>";

$crumbs = explode("/", $req);
echo "<b>Path: </b>";
echo "<a href='?path='>/</a>";

$tmp = "";
foreach ($crumbs as $c) {
    if (!$c) continue;
    $tmp .= ($tmp ? "/" : "") . $c;
    echo " / <a href='?path=".urlencode($tmp)."'>".h($c)."</a>";
}

/* ---------------------------
   BREADCRUMB REAL PATH (klik-able)
   --------------------------- */
echo "<br><b>Real Path: </b>";

$real = trim($path, "/");
$parts = explode("/", $real);
$realBuild = "";

foreach ($parts as $i => $p) {
    $realBuild .= "/".$p;
    $virtualCandidate = str_replace($root, "", $realBuild);
    $virtualCandidate = trim($virtualCandidate, "/");

    echo "<a href='?path=".urlencode($virtualCandidate)."'>/".h($p)."</a>";
}

echo "<br><br>";

/* ---------------------------
   BACK BUTTON
   --------------------------- */
$parent = dirname($req);
echo "<a href='?path=".urlencode($parent)."'>⬅ Back to parent</a><br><br>";

/* ---------------------------
   FORMS
   --------------------------- */
echo "
<h3>Create Folder</h3>
<form method='post'>
    <input name='newfolder' placeholder='folder name'>
    <button>Create</button>
</form>

<h3>Create File</h3>
<form method='post'>
    <input name='newfile' placeholder='file.txt'>
    <button>Create</button>
</form>

<h3>Upload File</h3>
<form method='post' enctype='multipart/form-data'>
    <input type='file' name='upload'>
    <button>Upload</button>
</form>
<hr>
";

/* ---------------------------
   DIRECTORY TABLE
   --------------------------- */
echo "<h3>Directory Listing</h3>";
echo "<table>
<tr><th>Name</th><th>Actions</th></tr>";

foreach (list_dir($path) as $item) {
    $full = $path."/".$item;
    echo "<tr><td>".h($item)."</td><td>";

    if (is_dir($full)) {
        echo "<a href='?path=".urlencode(($req ? "$req/" : "").$item)."'>Open</a> | ";
    } else {
        echo "<a href='?path=".urlencode($req)."&edit=".urlencode($item)."'>Edit</a> | ";
        echo "<a href='?path=".urlencode($req)."&download=".urlencode($item)."'>Download</a> | ";
    }

    echo "
    <form method='post' style='display:inline'>
        <input type='hidden' name='rename_old' value='".h($item)."'>
        <input name='rename_new' placeholder='rename...' style='width:120px'>
        <button>Rename</button>
    </form> | 
    ";

    echo "<a href='?path=".urlencode($req)."&delete=".urlencode($item)."' onclick='return confirm(\"Delete?\")'>Delete</a>";

    echo "</td></tr>";
}

echo "</table>";
?>
