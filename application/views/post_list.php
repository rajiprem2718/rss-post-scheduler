<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Posts - RSS Scheduler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <style>
        tr.draggable-row.dragging {
            opacity: 0.6;
        }

        tr.draggable-row {
            cursor: move;
        }
    </style>
</head>

<body class="bg-light">
    <?php
    // Build map id => name and find X platform id
    $platformMap = [];
    $xPlatformId = null;
    foreach ($platforms as $pl) {
        $platformMap[$pl->id] = $pl->name;
        if ($pl->name === 'X') {
            $xPlatformId = (string)$pl->id;
        }
    }
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
                    <li class="nav-item"><a class="nav-link active" href="<?= base_url('rss/posts') ?>">Posts</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('rss/dashboard') ?>">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row mb-3">
            <div class="col-md-6 d-flex align-items-center">
                <h3 class="mb-0">Posts</h3>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <a href="<?= base_url('rss') ?>" class="btn btn-primary me-2">
                    Import New RSS
                </a>
                <a href="<?= base_url('rss/social-dashboard') ?>" class="btn btn-outline-secondary">
                    Social Dashboard
                </a>
            </div>
        </div>


        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                No posts found. Try importing an RSS feed first.
            </div>
        <?php else: ?>

            <p class="text-muted small mb-2">
                Drag and drop rows to change priority. Top row becomes priority 1, next row priority 2, etc.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 100px;">Priority</th>
                            <th>Title</th>
                            <th style="width: 130px;">Char Count</th>
                            <th style="width: 260px;">Social Platforms</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="posts-tbody">
                        <?php foreach ($posts as $p): ?>
                            <?php
                            $platformIds   = isset($p->platform_ids) ? $p->platform_ids : [];
                            $platformNames = [];
                            foreach ($platformIds as $pid) {
                                if (isset($platformMap[$pid])) {
                                    $platformNames[] = $platformMap[$pid];
                                }
                            }
                            $platformAttr = implode(',', $platformIds);
                            ?>
                            <tr class="draggable-row"
                                draggable="true"
                                data-id="<?= $p->id ?>"
                                data-platforms="<?= $platformAttr ?>"
                                data-charcount="<?= $p->char_count ?>">
                                <td class="priority-cell">
                                    <span class="badge bg-primary">
                                        &#9776; <span class="priority-text"><?= $p->priority ?></span>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($p->image_url)): ?>
                                        <img src="<?= htmlspecialchars($p->image_url) ?>"
                                            alt="<?= htmlspecialchars($p->title) ?>"
                                            class="me-2"
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width:70px;height:70px;background:#ddd;border-radius:4px;" class="me-2"></div>
                                    <?php endif; ?>
                                    <strong class="post-title-text"><?= htmlspecialchars($p->title) ?></strong>
                                    <div class="small text-muted mt-1">
                                        <?= date('d M Y, H:i', strtotime($p->pub_date)) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= $p->char_count ?> chars
                                    </span>
                                </td>
                                <td>
                                    <span class="platforms-text small">
                                        <?= $platformNames ? htmlspecialchars(implode(', ', $platformNames)) : 'None' ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary btn-edit"
                                        data-id="<?= $p->id ?>">
                                        Edit
                                    </button>
                                    <a href="<?= base_url('rss/delete/' . $p->id) ?>"
                                        class="btn btn-sm btn-outline-danger ms-1"
                                        onclick="return confirm('Delete this post?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('rss/posts/' . ($current_page - 1)) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('rss/posts/' . $i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('rss/posts/' . ($current_page + 1)) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Post Platforms</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPostId" name="post_id">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Priority</label>
                                <input type="text" class="form-control" id="editPriority" disabled>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Title</label>
                                <textarea class="form-control" id="editTitle" rows="2" disabled></textarea>
                            </div>
                        </div>

                        <hr class="my-3">

                        <label class="form-label">Social Platforms</label>
                        <div class="row g-1">
                            <?php foreach ($platforms as $pl): ?>
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input edit-platform-checkbox"
                                            type="checkbox"
                                            value="<?= $pl->id ?>"
                                            id="edit_pl<?= $pl->id ?>">
                                        <label class="form-check-label small"
                                            for="edit_pl<?= $pl->id ?>">
                                            <?= htmlspecialchars($pl->name) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="xError" class="text-danger small mt-2 d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // PHP → JS maps
        const PLATFORM_MAP = <?= json_encode($platformMap) ?>;
        const X_PLATFORM_ID = <?= $xPlatformId ? json_encode($xPlatformId) : 'null' ?>;
    </script>

    <script>
        /**
         * Drag & Drop priority
         */
        (function() {
            const tbody = document.getElementById('posts-tbody');
            let draggedRow = null;

            if (!tbody) return;

            tbody.querySelectorAll('tr.draggable-row').forEach(row => {
                row.addEventListener('dragstart', function(e) {
                    draggedRow = this;
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });

                row.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    draggedRow = null;
                });

                row.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    if (!draggedRow || draggedRow === this) return;

                    const bounding = this.getBoundingClientRect();
                    const offset = e.clientY - bounding.top;
                    const half = bounding.height / 2;
                    if (offset > half) {
                        tbody.insertBefore(draggedRow, this.nextSibling);
                    } else {
                        tbody.insertBefore(draggedRow, this);
                    }
                });

                row.addEventListener('drop', function(e) {
                    e.preventDefault();
                    updatePrioritiesOnServer();
                });
            });

            function updatePrioritiesOnServer() {
                const rows = Array.from(tbody.querySelectorAll('tr.draggable-row'));
                rows.forEach((row, index) => {
                    const prioText = row.querySelector('.priority-text');
                    if (prioText) prioText.textContent = (index + 1);
                });

                const row = draggedRow;
                if (!row) return;

                const id = row.getAttribute('data-id');
                const newPriority = rows.indexOf(row) + 1;

                if (!id || newPriority < 1) return;

                fetch('<?= base_url("rss/update_priority") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + encodeURIComponent(id) +
                            '&priority=' + encodeURIComponent(newPriority)
                    })
                    .then(r => r.json())
                    .then(function(res) {
                        if (res.status !== 'ok') {
                            alert('Error updating priority');
                        }
                    })
                    .catch(function() {
                        alert('Error updating priority');
                    });
            }
        })();

        /**
         * Edit modal logic
         */
        (function() {
            const editModalEl = document.getElementById('editModal');
            const editModal = new bootstrap.Modal(editModalEl);
            const tbody = document.getElementById('posts-tbody');
            const xError = document.getElementById('xError');
            let currentEditPostId = null;
            let currentEditCharCount = 0;

            function openEditModal(row) {
                const id = row.dataset.id;
                const priority = row.querySelector('.priority-text').textContent.trim();
                const title = row.querySelector('.post-title-text').textContent.trim();
                const charCount = parseInt(row.dataset.charcount || '0', 10);
                const platformIds = (row.dataset.platforms || '').split(',').filter(Boolean);

                currentEditPostId = id;
                currentEditCharCount = charCount;

                document.getElementById('editPostId').value = id;
                document.getElementById('editPriority').value = priority;
                document.getElementById('editTitle').value = title;
                xError.classList.add('d-none');

                document.querySelectorAll('.edit-platform-checkbox').forEach(cb => {
                    cb.checked = platformIds.includes(cb.value);
                });

                editModal.show();
            }

            tbody.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    if (row) openEditModal(row);
                });
            });

            function validateXLength() {
                if (!X_PLATFORM_ID) return true;
                const checkboxes = document.querySelectorAll('.edit-platform-checkbox');
                let xChecked = false;
                checkboxes.forEach(cb => {
                    if (cb.value === X_PLATFORM_ID && cb.checked) {
                        xChecked = true;
                    }
                });

                if (xChecked && currentEditCharCount > 280) {
                    xError.textContent =
                        'This post has ' + currentEditCharCount +
                        ' characters, which exceeds the 280-character limit for X.';
                    xError.classList.remove('d-none');
                    return false;
                } else {
                    xError.classList.add('d-none');
                    return true;
                }
            }

            document.querySelectorAll('.edit-platform-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.value === X_PLATFORM_ID) {
                        validateXLength();
                    }
                });
            });

            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (!validateXLength()) return;

                const selectedIds = [];
                document.querySelectorAll('.edit-platform-checkbox:checked').forEach(cb => {
                    selectedIds.push(cb.value);
                });

                const formData = new FormData();
                formData.append('post_id', currentEditPostId);
                selectedIds.forEach(id => formData.append('platforms[]', id));

                fetch('<?= base_url("rss/assign_platform") ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(function(res) {
                        if (res.status === 'ok') {
                            const row = tbody.querySelector('tr[data-id="' + currentEditPostId + '"]');
                            if (row) {
                                row.dataset.platforms = selectedIds.join(',');
                                const cell = row.querySelector('.platforms-text');
                                const names = selectedIds
                                    .map(id => PLATFORM_MAP[id] || '')
                                    .filter(Boolean);
                                cell.textContent = names.length ? names.join(', ') : 'None';
                            }
                            editModal.hide();
                        } else {
                            alert('Error saving platforms');
                        }
                    })
                    .catch(function() {
                        alert('Error saving platforms');
                    });
            });
        })();
    </script>
</body>

</html>