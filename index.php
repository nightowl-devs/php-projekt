<?php
require_once __DIR__ . '/lib/auth.php';

$user = auth_current_user();
require_once __DIR__ . '/lib/database.php';

$conn = db_get_conn();
$res = $conn->query('SELECT id, name, note FROM teachers ORDER BY id DESC');
$teachers = [];
if ($res) { while ($r = $res->fetch_assoc()) { $teachers[] = $r; } }

?><!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>lista vouchy</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <header class="top">
    <h1>zgłoś doświadczenie z nauczycielem</h1>
    
    <div class="user">
      <?php if ($user): ?>
        Zalogowany jako <strong><?=htmlspecialchars($user['username'])?></strong> — <a href="/logout.php">Wyloguj</a>
      <?php else: ?>
        <a href="/login.php">Zaloguj</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <?php if (!empty($_SESSION['flash'])): ?>
     <script>
       alert("<?= $_SESSION['flash'] ?>");
        <?php unset($_SESSION['flash']); ?>
     </script>
    <?php endif; ?>
    <?php if (count($teachers) === 0): ?>
      <p class="empty">Brak nauczycieli. Administrator może dodać nowych w panelu admin.</p>
    <?php else: ?>


      <section class="cases">
        <?php foreach ($teachers as $row): ?>
          <?php
            $teacher_id = (int)$row['id'];
            $vcount = 0;
            $r2 = $conn->query("SELECT COUNT(*) AS c FROM vouches WHERE teacher_id = " . $teacher_id);
            if ($r2) { $rc = $r2->fetch_assoc(); $vcount = (int)($rc['c'] ?? 0); }
            $opinions = [];
            $r3 = $conn->query("SELECT v.opinion, u.username, v.created_at FROM vouches v LEFT JOIN users u ON u.id = v.user_id WHERE v.teacher_id = " . $teacher_id . " ORDER BY v.created_at DESC LIMIT 10");
            if ($r3) { while ($ro = $r3->fetch_assoc()) { $opinions[] = $ro; } }
          ?>
          <article class="case" id="teacher-<?= $teacher_id ?>">
            <h2><?= htmlspecialchars($row['name']) ?></h2>
            <p><?= nl2br(htmlspecialchars($row['note'])) ?></p>
            <div class="actions">
              <?php if ($user): ?>
                <form method="post" action="/vouch.php" class="vouch-form">
                  <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                  <textarea name="opinion" required placeholder="Krótka opinia"></textarea>
                  <button type="submit" class="vouch-btn">Dodaj opinię</button>
                </form>
              <?php else: ?>
                <a href="/login.php">Zaloguj, aby dodać opinię</a>
              <?php endif; ?>
              <div class="vouch-count"><?= $vcount ?>  <?= $vcount === 1 ? 'opinia' : (($vcount % 10 >= 2 && $vcount % 10 <= 4) ? 'opinie' : 'opinii') ?></div>
            </div>
            <div class="opinions">
              <?php if (count($opinions) === 0): ?>
                <em>Brak opinii</em>
              <?php else: ?>
                <?php foreach ($opinions as $i => $o): ?>
                  <?php if ($i === 0): ?>
                  <table class="opinions-table">
                    <thead>
                    <tr><th>Użytkownik</th><th>Opinia</th><th>Czas</th></tr>
                    </thead>
                    <tbody>
                  <?php endif; ?>
                    <tr>
                      <td><?= htmlspecialchars($o['username'] ?? '') ?></td>
                      <td><?= nl2br(htmlspecialchars($o['opinion'] ?? '')) ?></td>
                      <td class="time"><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
                    </tr>
                  <?php if ($i === count($opinions) - 1): ?>
                    </tbody>
                  </table>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

   
  </main>

</body>
</html>
