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
  <title>Opinie o nauczycielach</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="/assets/logo.svg">
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <header class="site-header sticky top-0 z-50">
    <div class="container">
      <div class="logo">
        <a href="/"><img src="/assets/logo.svg" alt="logo"></a>
        <div>
          <div class="brand-title">Opinie o nauczycielach</div>
          <div class="muted text-sm">Serwis opinii uczniów</div>
        </div>
      </div>
      <div class="user nav-links flex items-center gap-4 text-sm text-gray-500">
      <?php if ($user): ?>
        <span>Zalogowany jako <strong><?=htmlspecialchars($user['username'])?></strong></span>
        <a href="/logout.php" class="px-2">Wyloguj</a>
      <?php else: ?>
        <a href="/login.php">Zaloguj się</a>
        <a href="/register.php" class="btn-primary">Zarejestruj</a>
      <?php endif; ?>
      </div>
    </div>
  </header>

  <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-16">
    <div class="container text-center">
      <h1 class="text-3xl md:text-4xl font-bold">Podziel się swoją opinią o nauczycielach</h1>
      <p class="mt-3 text-blue-100 text-lg">Przeglądaj opinie, dodawaj własne i pomagaj innym uczniom wybierać najlepszych nauczycieli.</p>
      <div class="mt-6 max-w-2xl mx-auto">
        <form method="get" action="/" class="flex items-center gap-2">
          <input name="q" type="text" placeholder="Szukaj nauczyciela, przedmiotu..." class="p-3 rounded-l-md border-0 w-full" />
          <button type="submit" class="btn-primary rounded-r-md">Szukaj</button>
        </form>
      </div>
    </div>
  </section>

  <main class="container grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8 py-10">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="card p-4 mb-4">
        <?= htmlspecialchars($_SESSION['flash']) ?>
        <?php unset($_SESSION['flash']); ?>
      </div>
    <?php endif; ?>

    <section>
      <?php if (count($teachers) === 0): ?>
        <div class="card p-6">Brak nauczycieli. Administrator może dodać nowych w panelu admin.</div>
      <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($teachers as $row): ?>
            <?php
              $teacher_id = (int)$row['id'];
              $vcount = 0;
              $r2 = $conn->query("SELECT COUNT(*) AS c FROM vouches WHERE teacher_id = " . $teacher_id);
              if ($r2) { $rc = $r2->fetch_assoc(); $vcount = (int)($rc['c'] ?? 0); }
            ?>
            <article class="card p-5" id="teacher-<?= $teacher_id ?>">
              <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($row['name']) ?></h3>
              <p class="text-sm muted mb-4"><?= nl2br(htmlspecialchars($row['note'])) ?></p>
              <div class="flex items-center justify-between">
                <div class="muted text-sm">📊 <?= $vcount ?> <?= $vcount === 1 ? 'opinia' : 'opinii' ?></div>
                <a href="#teacher-<?= $teacher_id ?>" class="text-sm btn-primary px-3 py-1">Szczegóły</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <aside class="sticky top-24">
      <div class="card p-4 mb-4">
        <h4 class="font-semibold">Dodaj opinię</h4>
        <p class="muted text-sm mt-2">Zaloguj się, aby dodać opinię o nauczycielu.</p>
        <div class="mt-4">
          <?php if ($user): ?>
            <a href="#" class="btn-primary w-full inline-block text-center">Dodaj opinię</a>
          <?php else: ?>
            <a href="/login.php" class="btn-primary w-full inline-block text-center">Zaloguj się</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="card p-4">
        <h4 class="font-semibold">Statystyki</h4>
        <div class="mt-3 text-sm muted">Ilość nauczycieli: <strong><?= count($teachers) ?></strong></div>
      </div>
    </aside>
  </main>

</body>
</html>
