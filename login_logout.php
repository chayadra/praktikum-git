<?php
session_start();

// File to store users data
$users_file = 'users_data.txt';

// Load users from file
function load_users() {
    global $users_file;
    if (file_exists($users_file)) {
        $content = file_get_contents($users_file);
        return unserialize($content) ?: [];
    }
    return [
        'admin' => password_hash('admin123', PASSWORD_DEFAULT)
    ];
}

// Save users to file
function save_users($users) {
    global $users_file;
    file_put_contents($users_file, serialize($users));
}

$users = load_users();

// Fungsi simpan log ke file
function simpan_log($pesan) {
    $log = date("Y-m-d H:i:s") . " - " . $pesan . "\n";
    file_put_contents("log_login.txt", $log, FILE_APPEND);
}

// Proses Logout
if (isset($_GET['logout'])) {
    simpan_log("User '" . ($_SESSION['username'] ?? 'unknown') . "' logout");
    session_destroy();
    header("Location: login_system.php");
    exit;
}

// Proses Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        simpan_log("User '$username' berhasil login");
    } else {
        $error = "Username atau password salah!";
    }
}

// Proses Registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_username) || empty($new_password)) {
        $register_error = "Username dan password tidak boleh kosong!";
    } elseif ($new_password !== $confirm_password) {
        $register_error = "Password dan konfirmasi password tidak sama!";
    } elseif (isset($users[$new_username])) {
        $register_error = "Username sudah terdaftar!";
    } elseif (strlen($new_password) < 6) {
        $register_error = "Password harus minimal 6 karakter!";
    } else {
        $users[$new_username] = password_hash($new_password, PASSWORD_DEFAULT);
        save_users($users);
        $register_success = "Registrasi berhasil! Silakan login.";
        simpan_log("User '$new_username' berhasil registrasi");
    }
}

// Tampilan login jika belum login
if (!isset($_SESSION['login'])):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Sederhana</title>
    <style>
        .container { width: 300px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .error { color: red; }
        .success { color: green; }
        .tabs { display: flex; margin-bottom: 20px; }
        .tab { padding: 10px; cursor: pointer; }
        .tab.active { background: #ddd; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="tabs">
            <div class="tab active" onclick="showTab('login')">Login</div>
            <div class="tab" onclick="showTab('register')">Register</div>
        </div>

        <div id="login" class="tab-content active">
            <h2>Form Login</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (isset($register_success)) echo "<p class='success'>$register_success</p>"; ?>
            <form method="POST">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label>Username:</label><br>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label><br>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Login">
                </div>
            </form>
        </div>

        <div id="register" class="tab-content">
            <h2>Form Registrasi</h2>
            <?php if (isset($register_error)) echo "<p class='error'>$register_error</p>"; ?>
            <form method="POST">
                <input type="hidden" name="register" value="1">
                <div class="form-group">
                    <label>Username:</label><br>
                    <input type="text" name="new_username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label><br>
                    <input type="password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password:</label><br>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                <div class="form-group">
                    <input type="submit" value="Register">
                </div>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab.textContent.toLowerCase() === tabId) {
                    tab.classList.add('active');
                }
            });
        }
    </script>
</body>
</html>

<?php
// Tampilan dashboard jika sudah login
else:
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .welcome { font-size: 24px; margin-bottom: 20px; }
        .logout { display: inline-block; padding: 8px 15px; background: #f44336; color: white; text-decoration: none; border-radius: 4px; }
        .logout:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="welcome">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    <a href="?logout=true" class="logout">Logout</a>
</body>
</html>
<?php endif; ?>