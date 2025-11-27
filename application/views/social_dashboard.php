<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Social Dashboard - RSS Scheduler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
</head>
<body class="bg-light">
<?php
// map platform id => name for badges
$platformMap = [];
foreach ($platforms as $pl) {
    $platformMap[$pl->id] = $pl->name;
}
$selected = $selected ?? 'all';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= base_url('rss') ?>">RSS Scheduler</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('rss/posts') ?>">Posts</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('rss/dashboard') ?>">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('rss/social-dashboard') ?>">Social Dashboard</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Social Media Dashboard</h3>
        <a href="<?= base_url('rss/posts') ?>" class="btn btn-outline-secondary">
            ← Back to Posts
        </a>
    </div>

    <!-- Filter row -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="get" action="<?= base_url('rss/social-dashboard') ?>">
                <div class="col-md-6">
                    <label for="platform" class="form-label">Filter by Social Platform</label>
                    <select name="platform" id="platform" class="form-select"
                            onchange="this.form.submit()">
                        <option value="all" <?= ($selected === 'all') ? 'selected' : '' ?>>
                            All Platforms
                        </option>
                        <?php foreach ($platforms as $pl): ?>
                            <option value="<?= $pl->id ?>" <?= ($selected == $pl->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pl->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <?php if ($selected === 'all'): ?>
                        <span class="text-muted">
                            Showing posts for <strong>all social platforms</strong>.
                        </span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark">
                            Showing posts for:
                            <?php
                                foreach ($platforms as $pl) {
                                    if ((string)$pl->id === (string)$selected) {
                                        echo htmlspecialchars($pl->name);
                                        break;
                                    }
                                }
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($posts)): ?>
        <div class="alert alert-warning">
            No posts found for the selected platform.
        </div>
    <?php else: ?>
        <!-- Each post as its own row/card -->
        <div class="row row-cols-1 g-3">
            <?php foreach ($posts as $p): ?>
                <?php
                    $pPlatformNames = [];
                    if (!empty($p->platform_ids)) {
                        foreach ($p->platform_ids as $pid) {
                            if (isset($platformMap[$pid])) {
                                $pPlatformNames[] = $platformMap[$pid];
                            }
                        }
                    }
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-primary">Priority #<?= $p->priority ?></span>
                                <span class="badge bg-secondary">
                                    <?= date('d M Y, H:i', strtotime($p->pub_date)) ?>
                                </span>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($p->title) ?></h5>
                            <p class="card-text small text-muted mb-2">
                                <?= htmlspecialchars(mb_strimwidth(strip_tags($p->content), 0, 220, '...')) ?>
                            </p>
                            <?php if ($pPlatformNames): ?>
                                <div class="mb-1">
                                    <?php foreach ($pPlatformNames as $name): ?>
                                        <span class="badge bg-info text-dark me-1 mb-1">
                                            <?= htmlspecialchars($name) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small mb-1">No platforms assigned.</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <span class="small">
                                <strong><?= $p->char_count ?></strong> characters
                            </span>
                            <a href="<?= base_url('rss/posts') ?>" class="btn btn-sm btn-outline-primary">
                                Manage in Posts
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script
 src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
 integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
 crossorigin="anonymous"></script>
</body>
</html>
