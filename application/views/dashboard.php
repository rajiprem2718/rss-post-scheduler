<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - RSS Scheduler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
</head>
<body class="bg-light">
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
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('rss/dashboard') ?>">Dashboard</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Dashboard</h3>
        <a href="<?= base_url('rss') ?>" class="btn btn-primary">Import New RSS</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="get" action="<?= base_url('rss/dashboard') ?>">
                <div class="col-md-6">
                    <label for="platform" class="form-label">Filter by Platform</label>
                    <select name="platform" id="platform" class="form-select" onchange="this.form.submit()">
                        <option value="">All Platforms</option>
                        <?php foreach ($platforms as $pl): ?>
                            <option value="<?= $pl->id ?>" <?= ($selected == $pl->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pl->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <?php if ($selected): ?>
                        <span class="badge bg-info text-dark">
                            Showing posts for: 
                            <?php 
                                foreach ($platforms as $pl) {
                                    if ($pl->id == $selected) {
                                        echo htmlspecialchars($pl->name);
                                        break;
                                    }
                                }
                            ?>
                        </span>
                    <?php else: ?>
                        <span class="text-muted">Showing posts for all platforms.</span>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($posts)): ?>
        <div class="alert alert-warning">
            No posts found for the selected filter.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php foreach ($posts as $p): ?>
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
                            <p class="card-text small text-muted">
                                <?= htmlspecialchars(mb_strimwidth(strip_tags($p->content), 0, 180, '...')) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <span class="small">
                                <strong><?= $p->char_count ?></strong> characters
                            </span>
                            <a href="<?= base_url('rss/posts') ?>" class="btn btn-sm btn-outline-primary">
                                Manage Post
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
