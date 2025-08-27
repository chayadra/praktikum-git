<<?php
    //total array
    $todos = [];

    //Untuk mengecek apakah file todo di temukan atau tidak
    if(file_exists('todo.txt')){
    
    //Membaca file todo
    $file=file_get_contents('todo.txt');
    //mengubah format serialize menjadi array
    $todos=unserialize($file);
    }

    function simpanData($lists)
    {
        file_put_contents('todo.txt',$lists);
        header('location:index.php');
    }

    if(isset($_POST['todo'])){
        $data=$_POST['todo'];
        $todos[]=[
            'todo'=>$data,
            'status'=>0
        ];
        $lists=serialize($todos);
        simpanData($lists);
    }

    //jika ditemukan $_GET['status']

    if(isset($_GET['status']))
    {
        $todos[$_GET['key']]['status']=$_GET['status'];
        $lists=serialize($todos);
        simpanData($lists);
    }
    //print_r($todos);

    //jika ditemukan perintah hapus
    if(isset($_GET['hapus']))
    {
        //hapus data sesuai key yang dipilih
        unset($todos[$_GET['key']]);
        $lists=serialize($todos);
        simpanData($lists);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journey App</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <style>
        body {
            background: url('img/bg1.jpeg') no-repeat center center fixed;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100vh;
            padding: 40px;
        }
        .todo-container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .checked-text {
            text-decoration: line-through;
            color: #888;
        }
    </style>
    <script>
        function toggleStatus(key, currentStatus) {
            window.location.href = `?status=${currentStatus == 1 ? 0 : 1}&key=${key}`;
        }
    </script>
</head>
<body>
    <div class="todo-container">
        <h3 class="text-center mb-4"> ✧.* My Journey ToDo App ✧.*</h3>
        <form method="post" class="d-flex mb-4">
            <input type="text" name="todo" class="form-control me-2" placeholder="Tulis kegiatan..." required>
            <button type="submit" class="btn btn-primary">Tambah</button>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todos as $key => $value): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td class="<?= $value['status'] == 1 ? 'checked-text' : '' ?>">
                            <?= htmlspecialchars($value['todo']) ?>
                        </td>
                        <td>
                            <button class="btn btn-sm <?= $value['status'] == 1 ? 'btn-success' : 'btn-warning' ?>" 
                                    onclick="toggleStatus(<?= $key ?>, <?= $value['status'] ?>)">
                                <?= $value['status'] == 1 ? 'Sudah Dilakukan' : 'Belum Dilakukan' ?>
                            </button>
                        </td>
                        <td>
                            <a href="?hapus=1&key=<?= $key ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
