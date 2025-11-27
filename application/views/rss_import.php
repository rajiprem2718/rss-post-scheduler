<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RSS Import</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">RSS Scheduler</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('rss/posts') ?>">Posts</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('rss/dashboard') ?>">Dashboard</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Import RSS Feed</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('rss/import') ?>">
                        <div class="mb-3">
                            <label for="url" class="form-label">RSS Feed URL</label>
                            <input type="url" name="url" id="url" class="form-control" placeholder="https://example.com/feed" required>
                        </div>

                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort Mode</label>
                            <select name="sort" id="sort" class="form-select">
                                <option value="ASC">ASC (Oldest → Newest)</option>
                                <option value="DESC">DESC (Newest → Oldest)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">
                            Import Feed
                        </button>
                        <a href="<?= base_url('rss/posts') ?>" class="btn btn-outline-secondary ms-2">
                            View Posts
                        </a>
                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h6 class="alert-heading">Note</h6>
                <p class="mb-0">
                    This tool will fetch the RSS items, calculate character count (including emoji), 
                    assign priorities based on sort order, and store them in the database.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
