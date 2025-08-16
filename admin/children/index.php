<?php
declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

// Admin-only access guard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$pdo = $container->getPdo();

// Search and pagination params
$allowed_per_page = [20, 50, 100];
$per_page = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 20;
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 20;
}
$records_per_page = $per_page;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $records_per_page;

$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$q_like = '%' . $q . '%';

// Detect optional children.status column
$has_status_column = false;
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM children LIKE 'status'");
    $stmt->execute();
    $has_status_column = (bool) $stmt->fetch();
} catch (\Throwable $e) {
    $has_status_column = false;
}

// Build WHERE clauses and params
$where = ' WHERE 1=1';
$params = [];

if ($has_status_column) {
    $where .= ' AND children.status = :status_active';
    $params[':status_active'] = 'active';
}

if ($q !== '') {
    $where .= ' AND (children.name LIKE :q OR users.name LIKE :q OR users.email LIKE :q)';
    $params[':q'] = $q_like;
}

// Sorting (whitelisted columns)
$allowed_sorts = [
    'name' => 'children.name',
    'date_of_birth' => 'children.date_of_birth',
    'parent_name' => 'users.name',
    'parent_email' => 'users.email',
];
if ($has_status_column) {
    $allowed_sorts['status'] = 'children.status';
}

$order_by = isset($_GET['order_by']) ? (string) $_GET['order_by'] : 'name';
if (!array_key_exists($order_by, $allowed_sorts)) {
    $order_by = 'name';
}
$order_direction = strtoupper((string) ($_GET['order_direction'] ?? 'ASC'));
if ($order_direction !== 'ASC' && $order_direction !== 'DESC') {
    $order_direction = 'ASC';
}
$order_by_sql = $allowed_sorts[$order_by];

// Count query for pagination
$count_sql = 'SELECT COUNT(*) AS total FROM children JOIN users ON children.user_id = users.id' . $where;
$count_stmt = $pdo->prepare($count_sql);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_records = (int) $count_stmt->fetchColumn();
$total_pages = (int) ceil($total_records / $records_per_page);

// Main query
$select_fields = 'children.id, children.name, children.date_of_birth, users.name AS parent_name, users.email AS parent_email';
if ($has_status_column) {
    $select_fields .= ', children.status';
}

$sql = 'SELECT ' . $select_fields . ' FROM children JOIN users ON children.user_id = users.id' . $where . ' ORDER BY ' . $order_by_sql . ' ' . $order_direction . ' LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$children = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$page_title = 'Manage Children';

require_once __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Children</h1>
        <p class="text-gray-500">Search, filter, and manage children.</p>
    </div>
    <a href="create.php" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">Add New Child</a>
  </header>

  <?php
    $success_message = $_SESSION['success_message'] ?? null;
    $error_message = $_SESSION['error_message'] ?? null;
    unset($_SESSION['success_message'], $_SESSION['error_message']);
  ?>
  <?php if ($success_message): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($success_message); ?></span>
      </div>
  <?php endif; ?>
  <?php if ($error_message): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($error_message); ?></span>
      </div>
  <?php endif; ?>

  <div class="sticky top-0 z-10 bg-white border-b py-2" style="top: -2rem;">
      <form method="get" class="mb-2">
        <div class="flex gap-2 items-center">
            <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search child or parent..." class="w-full md:w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <label for="per_page" class="text-sm text-gray-600">Per page</label>
            <div class="relative inline-block">
                <select id="per_page" name="per_page" class="border border-gray-300 rounded-lg pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white w-24">
                    <?php foreach ($allowed_per_page as $opt): ?>
                        <option value="<?= $opt; ?>" <?= $per_page === $opt ? 'selected' : ''; ?>><?= $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">Search</button>
        </div>
      </form>
      <script nonce="<?= $nonce; ?>">
        document.addEventListener('DOMContentLoaded', function() {
          var perPage = document.getElementById('per_page');
          if (perPage && perPage.form) {
            perPage.addEventListener('change', function () { this.form.submit(); });
          }
        });
      </script>

      <?php
        $shown_count = is_array($children) ? count($children) : 0;
        $start_index = $total_records > 0 ? ($offset + 1) : 0;
        $end_index = $total_records > 0 ? ($offset + $shown_count) : 0;
      ?>
      <div class="text-sm text-gray-600">
          Showing <?= (int) $start_index; ?>–<?= (int) $end_index; ?> of <?= (int) $total_records; ?>
      </div>
  </div>

  <div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
      <table class="min-w-full leading-normal">
          <thead>
              <tr>
                  <?php
                    $sort_url = function (string $key) use ($q, $per_page, $order_by, $order_direction): string {
                        $new_dir = ($order_by === $key && strtoupper($order_direction) === 'ASC') ? 'DESC' : 'ASC';
                        $params = [
                            'page' => 1,
                            'per_page' => $per_page,
                            'order_by' => $key,
                            'order_direction' => $new_dir,
                        ];
                        if ($q !== '') { $params['q'] = $q; }
                        return 'index.php?' . http_build_query($params);
                    };
                  ?>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <a href="<?= $sort_url('name'); ?>" class="flex items-center">Child
                          <?php if ($order_by === 'name'): ?>
                              <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down'; ?> ml-1"></i>
                          <?php endif; ?>
                      </a>
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <a href="<?= $sort_url('date_of_birth'); ?>" class="flex items-center">DOB → Age
                          <?php if ($order_by === 'date_of_birth'): ?>
                              <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down'; ?> ml-1"></i>
                          <?php endif; ?>
                      </a>
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <a href="<?= $sort_url('parent_name'); ?>" class="flex items-center">Parent Name
                          <?php if ($order_by === 'parent_name'): ?>
                              <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down'; ?> ml-1"></i>
                          <?php endif; ?>
                      </a>
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                      <a href="<?= $sort_url('parent_email'); ?>" class="flex items-center">Parent Email
                          <?php if ($order_by === 'parent_email'): ?>
                              <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down'; ?> ml-1"></i>
                          <?php endif; ?>
                      </a>
                  </th>
                  <?php if ($has_status_column): ?>
                      <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                          <a href="<?= $sort_url('status'); ?>" class="flex items-center">Status
                              <?php if ($order_by === 'status'): ?>
                                  <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down'; ?> ml-1"></i>
                              <?php endif; ?>
                          </a>
                      </th>
                  <?php endif; ?>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
              </tr>
          </thead>
          <tbody>
          <?php if ($total_records === 0): ?>
              <tr>
                  <td colspan="<?= $has_status_column ? '6' : '5'; ?>" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No children found.</td>
              </tr>
          <?php else: ?>
              <?php foreach ($children as $child): ?>
                  <?php
                  $dob = $child['date_of_birth'] ?? null;
                  $age_display = '—';
                  if (!empty($dob)) {
                      try {
                          $dob_dt = new DateTime($dob);
                          $now = new DateTime('today');
                          $age_years = $dob_dt->diff($now)->y;
                          $age_display = htmlspecialchars($dob) . ' → ' . $age_years . 'y';
                      } catch (Throwable $e) {
                          $age_display = htmlspecialchars($dob);
                      }
                  }
                  ?>
                  <tr>
                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($child['name']); ?></td>
                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $age_display; ?></td>
                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($child['parent_name']); ?></td>
                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($child['parent_email']); ?></td>
                      <?php if ($has_status_column): ?>
                          <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                              <?php $status = (string) $child['status']; ?>
                              <?php if (strtolower($status) === 'active'): ?>
                                  <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                              <?php else: ?>
                                  <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full"><?= htmlspecialchars(ucfirst($status)); ?></span>
                              <?php endif; ?>
                          </td>
                      <?php endif; ?>
                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                          <a href="edit.php?id=<?= (int) $child['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                          <form method="post" action="archive_handler.php" class="inline" onsubmit="return confirm('Archive this child?');">
                              <input type="hidden" name="id" value="<?= (int) $child['id']; ?>" />
                              <?php if (isset($_SESSION['csrf_token'])): ?>
                                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>" />
                              <?php endif; ?>
                              <button type="submit" class="text-red-600 hover:text-red-900">Archive</button>
                          </form>
                      </td>
                  </tr>
              <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
      </table>
  </div>

  <?php if ($total_pages > 1): ?>
      <?php
      $build_url = function (int $p) use ($q, $per_page, $order_by, $order_direction): string {
          $params = ['page' => $p, 'per_page' => $per_page, 'order_by' => $order_by, 'order_direction' => $order_direction];
          if ($q !== '') {
              $params['q'] = $q;
          }
          return 'index.php?' . http_build_query($params);
      };
      ?>
      <nav class="mt-6 flex items-center justify-center space-x-2 sticky bottom-0 z-10 bg-white border-t py-2" style="bottom: -2rem;">
          <a href="<?= $build_url(1); ?>" class="px-3 py-1 border rounded <?= $page <= 1 ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-gray-50'; ?>">First</a>
          <a href="<?= $build_url(max(1, $page - 1)); ?>" class="px-3 py-1 border rounded <?= $page <= 1 ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-gray-50'; ?>">Prev</a>
          <?php
          // Compact windowed pagination with ellipses
          $display_pages = [];
          if ($total_pages <= 9) {
              for ($i = 1; $i <= $total_pages; $i++) { $display_pages[] = $i; }
          } else {
              $window = 2;
              $set = [1, $total_pages];
              for ($i = max(1, $page - $window); $i <= min($total_pages, $page + $window); $i++) {
                  $set[] = $i;
              }
              $set = array_values(array_unique($set));
              sort($set);
              $prev = null;
              foreach ($set as $num) {
                  if ($prev !== null && $num > $prev + 1) { $display_pages[] = '…'; }
                  $display_pages[] = $num;
                  $prev = $num;
              }
          }
          ?>
          <?php foreach ($display_pages as $p_item): ?>
              <?php if ($p_item === '…'): ?>
                  <span class="px-2 text-gray-400 select-none">&hellip;</span>
              <?php else: ?>
                  <a href="<?= $build_url((int)$p_item); ?>" class="px-3 py-1 border rounded <?= (int)$p_item === $page ? 'bg-gray-800 text-white' : 'hover:bg-gray-50'; ?>"><?= (int)$p_item; ?></a>
              <?php endif; ?>
          <?php endforeach; ?>
          <a href="<?= $build_url(min($total_pages, $page + 1)); ?>" class="px-3 py-1 border rounded <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-gray-50'; ?>">Next</a>
          <a href="<?= $build_url($total_pages); ?>" class="px-3 py-1 border rounded <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-gray-50'; ?>">Last</a>

          <form method="get" class="flex items-center gap-2 ml-4">
              <?php if ($q !== ''): ?>
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" />
              <?php endif; ?>
              <input type="hidden" name="per_page" value="<?= (int) $per_page; ?>" />
              <input type="hidden" name="order_by" value="<?= htmlspecialchars($order_by, ENT_QUOTES, 'UTF-8'); ?>" />
              <input type="hidden" name="order_direction" value="<?= htmlspecialchars($order_direction, ENT_QUOTES, 'UTF-8'); ?>" />
              <label for="jump_page" class="text-sm text-gray-600">Jump to</label>
              <input id="jump_page" type="number" name="page" min="1" max="<?= (int) $total_pages; ?>" value="<?= (int) $page; ?>" class="w-20 border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <button type="submit" class="px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200">Go</button>
          </form>
      </nav>
  <?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>
