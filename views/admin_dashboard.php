<?php
// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

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

// Get total users count (only users, not admins)
$totalUsersQuery = "SELECT COUNT(*) as count FROM users WHERE is_active = 1 AND role = 'user'";
$totalUsersResult = mysqli_query($connect, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['count'];

// Get today's schedules count
$todayDate = date('Y-m-d'); // Uses PHP timezone (Asia/Manila)
$todaySchedulesQuery = "SELECT COUNT(*) as count FROM schedules WHERE DATE(date) = '$todayDate'";
$todaySchedulesResult = mysqli_query($connect, $todaySchedulesQuery);
$todaySchedulesCount = mysqli_fetch_assoc($todaySchedulesResult)['count'];

// Get today's granted access count
$todayDate = date('Y-m-d'); // Uses PHP timezone (Asia/Manila)
$todayGrantedQuery = "SELECT COUNT(*) as count FROM access_log WHERE DATE(access_timestamp) = '$todayDate' AND access_status = 'granted'";
$todayGrantedResult = mysqli_query($connect, $todayGrantedQuery);
$todayGrantedCount = mysqli_fetch_assoc($todayGrantedResult)['count'];

// Get today's denied access count
$todayDeniedQuery = "SELECT COUNT(*) as count FROM access_log WHERE DATE(access_timestamp) = '$todayDate' AND access_status = 'denied'";
$todayDeniedResult = mysqli_query($connect, $todayDeniedQuery);
$todayDeniedCount = mysqli_fetch_assoc($todayDeniedResult)['count'];

// Get today's access logs
$todayAccessLogsQuery = "
    SELECT 
        u.user_id,
        u.first_name,
        u.last_name,
        al.access_timestamp,
        al.access_status,
        al.method_access
    FROM access_log al
    LEFT JOIN users u ON al.user_id = u.id
    WHERE DATE(al.access_timestamp) = '$todayDate'
    ORDER BY al.access_timestamp DESC
    LIMIT 10
";
$todayAccessLogsResult = mysqli_query($connect, $todayAccessLogsQuery);
$todayAccessLogs = [];
while ($row = mysqli_fetch_assoc($todayAccessLogsResult)) {
    $todayAccessLogs[] = $row;
}

// Get today's schedules list
$todaySchedulesListQuery = "
    SELECT 
        u.first_name,
        u.last_name,
        s.date,
        s.time_start,
        s.time_end,
        s.status
    FROM schedules s
    LEFT JOIN users u ON s.user_id = u.id
    WHERE DATE(s.date) = '$todayDate'
    ORDER BY s.time_start ASC
";
$todaySchedulesListResult = mysqli_query($connect, $todaySchedulesListQuery);
$todaySchedulesList = [];
while ($row = mysqli_fetch_assoc($todaySchedulesListResult)) {
    $todaySchedulesList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin_dashboard.css">
<link rel="stylesheet" href="../css/admin_dashboard_mobile.css">

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
            <a href="../views/admin_dashboard.php" class="active" title="Dashboard"><i class="fa fa-th-large"></i><span>Dashboard</span></a>
            <a href="../views/admin_users_management.php" title="Users"><i class="fa fa-users"></i><span>Users</span></a>
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
                <h1>Dashboard</h1>
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
                        <a href="../server/logout.php" style="color: inherit; text-decoration: none;">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="icon-label">Logout</span>
                        </a>
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
                        <a href="../server/logout.php" style="color: inherit; text-decoration: none;">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="icon-label">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="card">
                    <div class="card-info">
                        <h3>TOTAL USERS</h3>
                        <p class="card-number"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3>TODAY SCHEDULES</h3>
                        <p class="card-number"><?php echo $todaySchedulesCount; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3>ACCESS GRANTED</h3>
                        <p class="card-number"><?php echo $todayGrantedCount; ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h3>ACCESS DENIED</h3>
                        <p class="card-number"><?php echo $todayDeniedCount; ?></p>
                    </div>
                </div>
                <div class="card datetime-card">
                    <div class="card-info">
                        <h3>CURRENT TIME & DATE</h3>
                        <div class="datetime-content">
                            <p class="card-number" id="currentTime"><?php echo date('h:i A'); ?></p>
                            <p class="card-date"><?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Access Logs Section -->
                <div class="access-logs-section">
                    <div class="section-header">
                        <h2>Access Logs <br>(TODAY)</h2>
                        <div class="filter-controls">
                            <div class="filter-time-group">
                                <span class="filter-label">Time From:</span>
                                <input type="time" id="filterTimeFrom" class="time-filter">
                            </div>
                            <div class="filter-time-group">
                                <span class="filter-label">To:</span>
                                <input type="time" id="filterTimeTo" class="time-filter">
                            </div>
                            <button id="applyTimeFilter" class="filter-btn">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <select id="filterDropdown" class="filter-dropdown">
                                <option value="">Access Status</option>
                                <option value="all">All</option>
                                <option value="granted">Granted</option>
                                <option value="denied">Denied</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="search-filters">
                        <div class="search-inputs">
                            <input type="text" id="searchId" placeholder="Search ID No..." class="search-input">
                            <input type="text" id="searchName" placeholder="Search Name..." class="search-input">
                            <select id="methodFilter" class="filter-dropdown">
                                <option value="">Access Method</option>
                                <option value="all">All Methods</option>
                                <option value="rfid">RFID</option>
                                <option value="pin">PIN</option>
                                <option value="override">OVERRIDE</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="access-logs-table">
                            <thead>
                                <tr>
                                    <th>ID No.</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Method</th>
                                    <th>Access Log</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($todayAccessLogs)): ?>
                                    <?php foreach ($todayAccessLogs as $log): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['user_id'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($log['access_timestamp'])); ?></td>
                                            <td><?php echo date('h:i A', strtotime($log['access_timestamp'])); ?></td>
                                            <td>
                                                <span class="access-method-badge <?php echo strtolower($log['method_access'] ?? 'unknown'); ?>">
                                                    <?php echo htmlspecialchars(ucfirst($log['method_access'] ?? 'Unknown')); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="access-status <?php echo strtolower($log['access_status']); ?>">
                                                    <?php echo htmlspecialchars(ucfirst($log['access_status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 20px;">No access logs for today</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Schedules Today Section -->
                <div class="schedules-section">
                    <div class="section-header">
                        <h2>SCHEDULES TODAY</h2>
                    </div>
                    <div class="schedules-list">
                        <?php if (!empty($todaySchedulesList)): ?>
                            <?php foreach ($todaySchedulesList as $schedule): ?>
                                <div class="schedule-item">
                                    <div class="schedule-info">
                                        <div class="schedule-name">
                                            <h4><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']); ?></h4>
                                        </div>
                                        <div class="schedule-datetime">
                                            <p class="schedule-date"><?php echo date('M d, Y', strtotime($schedule['date'])); ?></p>
                                            <p class="schedule-time"><?php echo date('h:i A', strtotime($schedule['time_start'])) . ' - ' . date('h:i A', strtotime($schedule['time_end'])); ?></p>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-schedules">
                                <p>No schedules for today</p>
                            </div>
                        <?php endif; ?>
                    </div>
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

if (sidebarToggleBtn) {
    sidebarToggleBtn.onclick = e => {
        e.stopPropagation();
        console.log('Hamburger clicked!'); // Debug log
        sidebar.classList.toggle('collapsed'); // Collapse/expand sidebar on mobile
        console.log('Sidebar collapsed:', sidebar.classList.contains('collapsed')); // Debug log
    };
} else {
    console.error('Sidebar toggle button not found');
}

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

/* Search and Filter Functionality */
document.addEventListener('DOMContentLoaded', function(){
    const searchId = document.getElementById('searchId');
    const searchName = document.getElementById('searchName');
    const filterDropdown = document.getElementById('filterDropdown');
    const filterTimeFrom = document.getElementById('filterTimeFrom');
    const filterTimeTo = document.getElementById('filterTimeTo');
    const applyTimeFilterBtn = document.getElementById('applyTimeFilter');
    const methodFilter = document.getElementById('methodFilter');
    const tableRows = document.querySelectorAll('.access-logs-table tbody tr');

    function filterTable() {
        const idValue = searchId.value.toLowerCase();
        const nameValue = searchName.value.toLowerCase();
        const timeFromValue = filterTimeFrom.value;
        const timeToValue = filterTimeTo.value;
        const statusValue = filterDropdown.value.toLowerCase();
        const methodValue = methodFilter.value.toLowerCase();

        let visibleCount = 0;

        tableRows.forEach(row => {
            if (row.cells.length === 1) return; // Skip "No logs" row

            const id = row.cells[0].textContent.toLowerCase();
            const name = row.cells[1].textContent.toLowerCase();
            const dateText = row.cells[2].textContent;
            const timeText = row.cells[3].textContent;
            const method = row.cells[4].textContent.toLowerCase();
            const status = row.cells[5].textContent.toLowerCase();

            // Parse time for comparison
            const [time, period] = timeText.split(' ');
            const [hours, minutes] = time.split(':');
            let hours24 = parseInt(hours);
            if (period === 'PM' && hours24 !== 12) hours24 += 12;
            if (period === 'AM' && hours24 === 12) hours24 = 0;
            const timeValue24 = `${hours24.toString().padStart(2, '0')}:${minutes}`;

            let showRow = true;

            // Filter by ID
            if (idValue && !id.includes(idValue)) showRow = false;

            // Filter by Name
            if (nameValue && !name.includes(nameValue)) showRow = false;

            // Filter by Time Range
            if (timeFromValue && timeValue24 < timeFromValue) showRow = false;
            if (timeToValue && timeValue24 > timeToValue) showRow = false;

            // Filter by Status
            if (statusValue && statusValue !== 'all' && !status.includes(statusValue)) {
                showRow = false;
            }

            // Filter by Method
            if (methodValue && methodValue !== 'all' && !method.includes(methodValue)) {
                showRow = false;
            }

            if (showRow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show "no results" message if all rows are hidden
        const tableBody = document.querySelector('.access-logs-table tbody');
        
        if (visibleCount === 0 && (idValue !== '' || nameValue !== '' || timeFromValue !== '' || timeToValue !== '' || statusValue !== '' || methodValue !== '')) {
            // Check if no results message already exists
            let noResultsRow = tableBody.querySelector('.no-results-row');
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                tableBody.appendChild(noResultsRow);
            }
            
            let message = 'No access logs found';
            if (idValue !== '') message += ` matching ID "${idValue}"`;
            if (nameValue !== '') message += ` matching name "${nameValue}"`;
            if (timeFromValue !== '' || timeToValue !== '') message += ` in time range`;
            if (statusValue !== '' && statusValue !== 'all') message += ` with status "${statusValue}"`;
            if (methodValue !== '' && methodValue !== 'all') message += ` with method "${methodValue}"`;
            
            noResultsRow.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 40px; color: #888;">
                    <i class="fa fa-search" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                    ${message}
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
    }

    // Add event listeners
    searchId.addEventListener('input', filterTable);
    searchName.addEventListener('input', filterTable);
    filterDropdown.addEventListener('change', filterTable);
    methodFilter.addEventListener('change', filterTable);
    
    // Add click event for filter button
    applyTimeFilterBtn.addEventListener('click', filterTable);
    
    // Also filter on time input change for immediate feedback
    filterTimeFrom.addEventListener('change', filterTable);
    filterTimeTo.addEventListener('change', filterTable);
    
    // Update current time every second
    function updateCurrentTime() {
        const currentTimeElement = document.getElementById('currentTime');
        if (currentTimeElement) {
            // Create date object and convert to Asia/Manila timezone
            const now = new Date();
            const options = {
                timeZone: 'Asia/Manila',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            const timeString = now.toLocaleTimeString('en-US', options);
            currentTimeElement.textContent = timeString;
        }
    }
    
    // Update time immediately and then every second
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
});
</script>

</body>
</html>
