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
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <header class="bg-white border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Opinie o nauczycielach</h1>
      <div class="user flex items-center gap-4 text-sm text-gray-500">
      <?php if ($user): ?>
        <span>Zalogowany jako <strong><?=htmlspecialchars($user['username'])?></strong></span>
        <a href="/logout.php">Wyloguj</a>
      <?php else: ?>
        <a href="/login.php">Zaloguj się</a> / <a href="/register.php">Zarejestruj</a>
      <?php endif; ?>
      </div>
    </div>
  </header>

  <section class="bg-gradient-to-br from-gray-900 to-gray-700 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <h1 class="text-4xl md:text-5xl font-bold leading-tight">Podziel się swoją opinią</h1>
      <p class="text-gray-200 mt-3 text-lg">Pomóż innym uczniom poprzez recenzje swoich nauczycieli. Twój feedback ma znaczenie.</p>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-4 lg:px-8 grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 py-8">
    <?php if (!empty($_SESSION['flash'])): ?>
     <script>
       alert("<?= $_SESSION['flash'] ?>");
        <?php unset($_SESSION['flash']); ?>
     </script>
    <?php endif; ?>
    <?php if (count($teachers) === 0): ?>
      <p class="text-gray-500 p-4 bg-gray-50 border border-gray-200 rounded">Brak nauczycieli. Administrator może dodać nowych w panelu admin.</p>
    <?php else: ?>


      <section class="grid gap-6">
          <?php foreach ($teachers as $row): ?>
          <?php
            $teacher_id = (int)$row['id'];
            $vcount = 0;
            $r2 = $conn->query("SELECT COUNT(*) AS c FROM vouches WHERE teacher_id = " . $teacher_id);
            if ($r2) { $rc = $r2->fetch_assoc(); $vcount = (int)($rc['c'] ?? 0); }
            $opinions = [];
            $r3 = $conn->query("SELECT v.opinion, u.username, v.created_at, v.is_anonymous FROM vouches v LEFT JOIN users u ON u.id = v.user_id WHERE v.teacher_id = " . $teacher_id . " ORDER BY v.created_at DESC LIMIT 10");
            if ($r3) { while ($ro = $r3->fetch_assoc()) { $opinions[] = $ro; } }
          ?>
          <article class="bg-white border rounded-lg p-6 shadow-sm hover:shadow-lg transition" id="teacher-<?= $teacher_id ?>">
            <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($row['name']) ?></h2>
            <p class="text-gray-600 mt-2"><?= nl2br(htmlspecialchars($row['note'])) ?></p>
            <div class="mt-4 flex items-center justify-between gap-4">
              <div class="text-sm text-gray-500">📊 <span><?= $vcount ?>  <?= $vcount === 1 ? 'opinia' : (($vcount % 10 >= 2 && $vcount % 10 <= 4) ? 'opinie' : 'opinii') ?></span></div>
            </div>
            <div class="flex flex-col md:flex-row gap-4">
              <?php if ($user): ?>
                <form method="post" action="/vouch.php" class="flex flex-col gap-3 w-full">
                  <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                <div class="flex items-center gap-2">
                  <input type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                  <label for="is_anonymous" class="text-sm text-gray-600">Dodaj opinię anonimowo</label>
                </div>
                  <textarea name="opinion" required placeholder="Udostępnij swoją opinię..." class="w-full min-h-[80px] p-3 border rounded bg-gray-50 text-gray-900" ></textarea>
                  <button type="submit" class="inline-block self-start bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700">Podziel się opinią</button>
                </form>
              <?php else: ?>
                <div class="text-sm text-gray-500"> <a href="/login.php" class="text-blue-600 font-semibold">Zaloguj się</a>, aby dodać opinię</div>
              <?php endif; ?>
            </div>
            <div class="opinions">
              <?php if (count($opinions) === 0): ?>
                <em class="text-gray-500">Brak opinii</em>
              <?php else: ?>
                <?php foreach ($opinions as $i => $o): ?>
                  <?php if ($i === 0): ?>
                  <div class="overflow-x-auto mt-3">
                  <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-50 text-sm text-gray-600">
                    <tr><th class="p-3 text-left">Użytkownik</th><th class="p-3 text-left">Opinia</th><th class="p-3 text-left">Data</th></tr>
                    </thead>
                    <tbody>
                  <?php endif; ?>
                    <tr class="border-t">
                      <td class="p-3 text-sm text-gray-800"><?= $o['is_anonymous'] == 1 ? 'Anonimowy' : htmlspecialchars($o['username'] ?? 'Anonimowy') ?></td>
                      <td class="p-3 text-sm text-gray-600"><?= nl2br(htmlspecialchars($o['opinion'] ?? '')) ?></td>
                      <td class="p-3 text-xs text-gray-400"><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
                    </tr>
                  <?php if ($i === count($opinions) - 1): ?>
                    </tbody>
                  </table>
                  </div>
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
