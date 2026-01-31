<?php
// Include authentication check
require_once '../server/auth_check.php';

// Only allow admin users
if (!isAdmin()) {
    header('Location: login.php?error=access_denied');
    exit;
}

// Get current user data
$currentUser = getCurrentUser();

// Include database connection
require_once '../server/db.php';

// Get user statistics
$totalUsersQuery = "SELECT COUNT(*) as count FROM users WHERE is_active = 1 AND role = 'user'";
$totalUsersResult = mysqli_query($connect, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['count'];

$femaleUsersQuery = "SELECT COUNT(*) as count FROM users WHERE is_active = 1 AND role = 'user' AND sex = 'Female'";
$femaleUsersResult = mysqli_query($connect, $femaleUsersQuery);
$femaleUsers = mysqli_fetch_assoc($femaleUsersResult)['count'];

$maleUsersQuery = "SELECT COUNT(*) as count FROM users WHERE is_active = 1 AND role = 'user' AND sex = 'Male'";
$maleUsersResult = mysqli_query($connect, $maleUsersQuery);
$maleUsers = mysqli_fetch_assoc($maleUsersResult)['count'];

// Get users list with RFID cards
$usersQuery = "
    SELECT 
        u.user_id,
        u.first_name,
        u.last_name,
        u.work_position,
        u.sex,
        rc.rfid_uid,
        u.id as user_db_id
    FROM users u
    LEFT JOIN RFID_cards rc ON u.id = rc.user_id
    WHERE u.is_active = 1 AND u.role = 'user'
    ORDER BY u.first_name, u.last_name
    LIMIT 20
";
$usersResult = mysqli_query($connect, $usersQuery);
$users = [];
while ($row = mysqli_fetch_assoc($usersResult)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users Management</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin_users_management.css">

</head>

<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="../images/technokeeper_logo_dashboard.png" alt="TechnoKeeper Logo">
                <span>TechnoKeeper</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="profile" id="profileBox">
            <img src="https://randomuser.me/api/portraits/women/44.jpg">
            <div class="profile-info">
                <span class="name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></span>
                <span class="email"><?php echo htmlspecialchars($currentUser['email']); ?></span>
            </div>
            <i class="fa fa-chevron-down profile-arrow"></i>

            <div class="profile-drawer">
                <b><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></b><br>
                <small><?php echo htmlspecialchars($currentUser['email']); ?></small>
                <br>
                <span class="user-role-badge" style="display: inline-block; padding: 3px 10px; border-radius: 2px; font-size: 11px; font-weight: 500; margin-top: 10px; background-color: <?php echo isset($currentUser['role']) && $currentUser['role'] === 'admin' ? '#471D21F9' : '#051B0A'; ?>; color: white;">
                <?php echo isset($currentUser['role']) && $currentUser['role'] === 'admin' ? 'ADMIN' : 'USER'; ?>
                </span><br>
                <button style="width:100%;padding:8px 0;border:none;background:#f6f6f8;color:#232323;border-radius:6px;cursor:pointer;margin-top:8px;">View Profile</button>
            </div>
        </div>

        <nav>
            <a href="../views/admin_dashboard.php" title="Dashboard"><i class="fa fa-th-large"></i><span>Dashboard</span></a>
            <a href="../views/admin_users_management.php" class="active" title="Users"><i class="fa fa-users"></i><span>Users</span></a>
            <a href="../views/admin_rfid_inventory.php" title="RFID Inventory"><i class="fa fa-barcode"></i><span>RFID Inventory</span></a>
            <a href="../views/admin_access_logs.php" title="Access Logs"><i class="fa fa-door-open"></i><span>Access Logs</span></a>
            <a href="../views/admin_schedules.php" title="Schedules"><i class="fa fa-calendar"></i><span>Schedules</span></a>
            <a href="../views/admin_messages.php" title="Messages"><i class="fa fa-envelope"></i><span>Messages</span></a>
        </nav>

        <div class="sidebar-bottom">
            <a href="../server/logout.php" class="logout" style="text-decoration: none;">
                <i class="fa fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <div class="dashboard-header">
            <div class="header-left">
                <h1>Users Management</h1>
                <div class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>
            </div>
            <div class="header-right">
                <!-- Icons container -->
                <div class="header-icons" id="headerIcons">
                    <div class="profile-mini">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile">
                        <div class="profile-mini-drawer">
                            <b><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></b>
                            <small><?php echo htmlspecialchars($currentUser['email']); ?></small>
                            <span class="user-role-badge" style="display: inline-block; padding: 3px 10px; border-radius: 2px; font-size: 11px; font-weight: 500; margin-top: 8px; background-color: <?php echo isset($currentUser['role']) && $currentUser['role'] === 'admin' ? '#471D21F9' : '#051B0A'; ?>; color: white;">
                            <?php echo isset($currentUser['role']) && $currentUser['role'] === 'admin' ? 'ADMIN' : 'USER'; ?>
                            </span><br>
                            <hr style="margin: 8px 0; border: none; border-top: 1px solid #ddd;">
                            <a href="#" style="display: block; padding: 12px 0; color: #232323e2; text-decoration: none;">
                                <i class="fa fa-user"></i> View Profile
                            </a>
                            <a href="#" style="display: block; padding: 12px 0; color: #232323e2; text-decoration: none;">
                                <i class="fa fa-cog"></i> Settings
                            </a>
                        </div>
                    </div>

                    <div class="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-badge" style="display: none;"></span>
                    </div>
                    
                
                    <div class="logout-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="icon-label">Logout</span>

                    </div>
                </div>

                <div class="header-icons-mobile" id="headerIconsMobile">
                     <div class="profile-mini">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile">
                        <div class="profile-info-mobile">
                            <span class="icon-label"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></span>
                            <span class="icon-label" style="font-size: 0.8em; color: #666;"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                        </div>
                    </div>
                    <div class="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-badge" style="display: none;"></span>
                        <span class="icon-label">Notifications</span>
                    </div>
                    <div class="logout-icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="icon-label">Logout</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content">
           

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card total-users">
                    <div class="card-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="card-info">
                        <h3>Total User</h3>
                        <p class="card-number"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
                <div class="summary-card female-users">
                    <div class="card-icon">
                        <i class="fa fa-female"></i>
                    </div>
                    <div class="card-info">
                        <h3>Female</h3>
                        <p class="card-number"><?php echo $femaleUsers; ?></p>
                    </div>
                </div>
                <div class="summary-card male-users">
                    <div class="card-icon">
                        <i class="fa fa-male"></i>
                    </div>
                    <div class="card-info">
                        <h3>Male</h3>
                        <p class="card-number"><?php echo $maleUsers; ?></p>
                    </div>
                </div>
            </div>

            <!-- Header with Export and Add buttons -->
            <div class="management-header">
                <div class="header-buttons">
                    <button class="btn-export" onclick="exportToCSV()">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                    <button class="btn-add" onclick="showAddUserModal()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                </div>
            </div>

            <!-- User Lists Section -->
            <div class="user-lists-section">
                <div class="section-header">
                    <h2>User Lists</h2>
                    <div class="list-controls">
                        <div class="view-controls">
                            <button class="btn-view-all" onclick="viewAllUsers()">
                                <i class="fa fa-eye"></i> View All
                            </button>
                            <div class="show-selector">
                                <span>Show</span>
                                <select id="showLimit" onchange="updateUserLimit()">
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="search-container">
                            <input type="text" id="userSearch" placeholder="Search users..." class="search-input" onkeyup="searchUsers()">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID No.</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Sex</th>
                                <th>RFID UID.</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['work_position']); ?></td>
                                        <td><?php echo htmlspecialchars($user['sex']); ?></td>
                                        <td><?php echo htmlspecialchars($user['rfid_uid'] ?? 'N/A'); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-edit" onclick="editUser(<?php echo $user['user_db_id']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-delete" onclick="deleteUser(<?php echo $user['user_db_id']; ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</div>

<script>
/* Profile mini drawer click functionality */
const profileMini = document.querySelector('.profile-mini');
profileMini.onclick = e => {
    e.stopPropagation();
    profileMini.classList.toggle('active');
};

/* Close profile mini drawer when clicking outside */
document.addEventListener('click', () => {
    profileMini.classList.remove('active');
});

/* Check screen size and apply mobile collapse on load */
function checkMobileView() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
    } else {
        sidebar.classList.remove('collapsed');
    }
}

/* Run on page load */
document.addEventListener('DOMContentLoaded', checkMobileView);

/* Run on window resize */
window.addEventListener('resize', checkMobileView);

/* Sidebar toggle (three-dot menu on mobile) */
const sidebarToggleBtn = document.getElementById('sidebarToggle'); // sidebar toggle button
const sidebar = document.getElementById('sidebar');

sidebarToggleBtn.onclick = e => {
    e.stopPropagation();
    sidebar.classList.toggle('collapsed'); // Collapse/expand sidebar on mobile
};

/* Profile dropdown */
const profileBox = document.getElementById('profileBox');
profileBox.onclick = e => {
    profileBox.classList.toggle('open');
    e.stopPropagation();
};

/* Mobile top-right menu toggle (inside main content) */
const mobileMenuBtn = document.getElementById('mobileMenuToggle');
const headerIconsMobile = document.getElementById('headerIconsMobile');

mobileMenuBtn.onclick = e => {
    e.stopPropagation();
    headerIconsMobile.classList.toggle('show');
};

/* Click outside to close profile drawer */
document.addEventListener('click', () => {
    profileBox.classList.remove('open');
});

/* Close mobile menu when clicking outside */
document.onclick = e => {
    if (!mobileMenuToggle.contains(e.target) && !headerIconsMobile.contains(e.target)) {
        headerIconsMobile.classList.remove('show');
    }
    profileBox.classList.remove('open');
};

/* User Management Functions */
function searchUsers() {
    const searchValue = document.getElementById('userSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#usersTableBody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const position = row.cells[2].textContent.toLowerCase();
        const sex = row.cells[3].textContent.toLowerCase();
        const rfid = row.cells[4].textContent.toLowerCase();
        
        const matchesSearch = name.includes(searchValue) || 
                           position.includes(searchValue) || 
                           sex.includes(searchValue) || 
                           rfid.includes(searchValue);
        
        if (matchesSearch || searchValue === '') {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    const tableBody = document.getElementById('usersTableBody');
    
    if (visibleCount === 0 && searchValue !== '') {
        // Check if no results message already exists
        let noResultsRow = tableBody.querySelector('.no-results-row');
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            tableBody.appendChild(noResultsRow);
        }
        
        noResultsRow.innerHTML = `
            <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                <i class="fa fa-search" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                No users found matching "${searchValue}"
            </td>
        `;
    } else {
        // Remove no results message if it exists
        const noResultsRow = tableBody.querySelector('.no-results-row');
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
}

function updateUserLimit() {
    const limit = document.getElementById('showLimit').value;
    // In a real implementation, this would fetch data from server with the new limit
    console.log('Update user limit to:', limit);
    // For now, just reload the page
    window.location.reload();
}

function viewAllUsers() {
    // In a real implementation, this would fetch all users
    console.log('View all users');
    // For now, just reload the page
    window.location.reload();
}

function exportToCSV() {
    const table = document.querySelector('.users-table');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Get data rows
    rows.forEach(row => {
        if (row.querySelector('th')) return; // Skip header row
        const rowData = [];
        row.querySelectorAll('td').forEach(td => {
            rowData.push(td.textContent.trim());
        });
        csv.push(rowData.join(','));
    });
    
    // Create and download CSV file
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'users_export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function showAddUserModal() {
    // In a real implementation, this would show a modal to add a new user
    console.log('Show add user modal');
    alert('Add User functionality would be implemented here');
}

function editUser(userId) {
    // In a real implementation, this would show a modal to edit the user
    console.log('Edit user:', userId);
    alert('Edit User functionality would be implemented here for user ID: ' + userId);
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        // In a real implementation, this would send a request to delete the user
        console.log('Delete user:', userId);
        alert('Delete User functionality would be implemented here for user ID: ' + userId);
    }
}
</script>

</body>
</html>
