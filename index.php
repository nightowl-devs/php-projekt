<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>zaliczmnie.pl</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Witaj na zaliczmnie.pl</h1>
    </header>
    <main>
        <section>
            <h2>O nas</h2>
            <p>Jesteśmy platformą na której możesz znaleźć swoją wymarzoną panią do zaliczenia</p>
        </section>
        <?php
        require_once __DIR__ . '/lib/auth.php';
        $user = auth_current_user();
        if ($user) {
            echo '<p>Witaj, '.htmlspecialchars($user['username']).' | <a href="/index.php?logout=1">Wyloguj</a></p>';
        } else {
            echo '<p><a href="/login.php">Zaloguj</a> | <a href="/register.php">Zarejestruj</a></p>';
        }

        if (isset($_GET['logout'])) {
            auth_logout(); header('Location: /'); exit;
        }

        $conn = db_get_conn();
        $res = $conn->query('SELECT g.id, g.name, g.description, COUNT(gu.user_id) AS hits FROM girls g LEFT JOIN girl_user gu ON gu.girl_id = g.id GROUP BY g.id ORDER BY g.id');
        echo '<h2>Lista dziewczyn</h2>';
        echo '<ul>';
        while ($row = $res->fetch_assoc()) {
            $gid = (int)$row['id'];
            $name = htmlspecialchars($row['name']);
            $desc = htmlspecialchars($row['description'] ?? '');
            $hits = (int)$row['hits'];
            $has = false;
            if ($user) {
                $stmt = $conn->prepare('SELECT 1 FROM girl_user WHERE girl_id = ? AND user_id = ? LIMIT 1');
                $stmt->bind_param('ii', $gid, $user['id']);
                $stmt->execute();
                $stmt->store_result();
                $has = $stmt->num_rows > 0;
                $stmt->close();
            }
            echo '<li><strong>'.$name.'</strong> — '.$desc.' (' . $hits . ' zaliczeń) ';
            if ($user) {
                if ($has) {
                    echo '<em>Już zaliczyłeś</em>';
                } else {
                    echo '<form method="post" action="/zalicz.php" style="display:inline;"><input type="hidden" name="girl_id" value="'.$gid.'"><button type="submit">Zaliczyć</button></form>';
                }
            } else {
                echo '<a href="/login.php">Zaloguj, aby zaliczyć</a>';
            }
            echo '</li>';
        }
        echo '</ul>';

        if ($user && !empty($user['is_admin'])) {
            echo '<p><a href="/admin/admin.php">Panel admina</a></p>';
        }
        ?>
    </main>
</body>
</html>
</html>