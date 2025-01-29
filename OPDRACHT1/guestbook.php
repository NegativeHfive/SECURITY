<?php
 
session_start();
$servername = "localhost:3310";
$username = "root"; // PAS DEZE AAN ALS DAT NODIG IS
$password = ""; // PAS DEZE AAN ALS DAT NODIG IS
$db = "leaky_guest_book";
 
try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (Exception $e) {
    die("Failed to open database connection: " . htmlspecialchars($e->getMessage()));
}
 
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];
 
function userIsAdmin($conn)
{
    if (isset($_COOKIE['admin'])) {
        $adminCookie = $_COOKIE['admin'];
        $stmt = $conn->prepare("SELECT cookie FROM admin_cookies WHERE cookie = :cookie");
        $stmt->bindParam(':cookie', $adminCookie);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
    return false;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        die("Invalid CSRF token.");
    }
 
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $text = trim($_POST['text']);
 
    // Maak gebruiker admin als speciale code wordt ingevoerd
    $admin = 0;
    if (isset($_POST['admin_code']) && $_POST['admin_code'] === '1') {
        setcookie('admin', bin2hex(random_bytes(16)), time() + 3600, '/');
        $admin = 1;
    } elseif (userIsAdmin($conn)) {
        $admin = 1;
    }
 
    $color = $admin ? 'green' : 'red';
    if ($admin && isset($_POST['color'])) {
        $color = $_POST['color'];
    }
 
    if (!$email || strlen($text) < 4) {
        die("Invalid input.");
    }
 
    $stmt = $conn->prepare("INSERT INTO entries (email, color, admin, text) VALUES (:email, :color, :admin, :text)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
    $stmt->bindParam(':text', $text);
    $stmt->execute();
}
?>
<html>
 
<head>
    <title>Leaky-Guestbook</title>
    <style>
        body {
            width: 100%;
        }
 
        .body-container {
            background-color: aliceblue;
            width: 300px;
            margin: auto;
            padding: 20px;
        }
 
        .heading {
            text-align: center;
        }
    </style>
</head>
 
<body>
    <div class="body-container">
        <h1 class="heading">Gastenboek 'De lekkage'</h1>
        <form action="guestbook.php" method="post">
            Email: <input type="email" name="email" required><br />
            Bericht: <textarea name="text" minlength="4" required></textarea><br />
            <?php if (userIsAdmin($conn)) {
                echo '<input type="text" name="color" placeholder="Kies een kleur">';
            } ?>
            <br />
            Admin Code: <input type="text" name="admin_code" placeholder="Voer admin code in"><br />
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <input type="submit">
        </form>
        <hr />
        <?php
        $stmt = $conn->query("SELECT email, text, color, admin FROM entries");
        foreach ($stmt as $row) {
            echo '<div style="color: ' . htmlspecialchars($row['color']) . '">Email: ' . htmlspecialchars($row['email']);
            if ($row['admin']) echo ' &#9812;';
            echo ': ' . htmlspecialchars($row['text']) . '</div><br/>';
        }
        ?>
    </div>
</body>
 
</html>
 
has context menu