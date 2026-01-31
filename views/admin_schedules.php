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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Schedules</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin_schedules.css">

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
            <a href="../views/admin_users_management.php" title="Users"><i class="fa fa-users"></i><span>Users</span></a>
            <a href="../views/admin_rfid_inventory.php" title="RFID Inventory"><i class="fa fa-barcode"></i><span>RFID Inventory</span></a>
            <a href="../views/admin_access_logs.php" title="Access Logs"><i class="fa fa-door-open"></i><span>Access Logs</span></a>
            <a href="../views/admin_schedules.php" class="active" title="Schedules"><i class="fa fa-calendar"></i><span>Schedules</span></a>
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
                <h1>Schedules</h1>
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
        <?php
// Include database connection
require_once '../server/db.php';

// Get schedules with user information
$schedules_query = "
    SELECT 
        s.schedule_id,
        s.date,
        s.time_start,
        s.time_end,
        s.status,
        u.user_id,
        u.first_name,
        u.last_name,
        rc.rfid_uid
    FROM schedules s
    LEFT JOIN users u ON s.user_id = u.id
    LEFT JOIN RFID_cards rc ON s.card_id = rc.card_id
    ORDER BY s.date DESC, s.time_start DESC
";
$schedules_result = mysqli_query($connect, $schedules_query);

// Get all schedules for search functionality
$all_schedules_query = "
    SELECT 
        s.schedule_id,
        s.date,
        s.time_start,
        s.time_end,
        s.status,
        u.user_id,
        u.first_name,
        u.last_name,
        rc.rfid_uid
    FROM schedules s
    LEFT JOIN users u ON s.user_id = u.id
    LEFT JOIN RFID_cards rc ON s.card_id = rc.card_id
    ORDER BY s.date DESC, s.time_start DESC
";
$all_schedules_result = mysqli_query($connect, $all_schedules_query);
$all_schedules = [];
while ($schedule = mysqli_fetch_assoc($all_schedules_result)) {
    $all_schedules[] = $schedule;
}

// Get dates that have schedules for calendar highlighting
$calendar_dates_query = "
    SELECT DISTINCT DATE(date) as schedule_date
    FROM schedules
    WHERE YEAR(date) = YEAR(CURRENT_DATE) AND MONTH(date) = MONTH(CURRENT_DATE)
";
$calendar_dates_result = mysqli_query($connect, $calendar_dates_query);
$schedule_dates = [];
while ($date_row = mysqli_fetch_assoc($calendar_dates_result)) {
    $schedule_dates[] = date('j', strtotime($date_row['schedule_date']));
}

// Get today's access logs
$today = date('Y-m-d');
$access_logs_query = "
    SELECT 
        al.access_timestamp,
        al.access_status,
        u.first_name,
        u.last_name
    FROM access_log al
    LEFT JOIN users u ON al.user_id = u.id
    WHERE DATE(al.access_timestamp) = '$today'
    ORDER BY al.access_timestamp DESC
";
$access_logs_result = mysqli_query($connect, $access_logs_query);
$total_access_logs = mysqli_num_rows($access_logs_result);
?>

<div class="schedules-container">
    <div class="schedules-left">

        <!-- Add Schedule Button -->
        <button class="add-schedule-btn" id="addScheduleBtn">
            <i class="fa fa-plus"></i> Add Schedule
        </button>

        <!-- Search and Filters -->
        <div class="search-filters">
            <div class="search-bar">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Search schedules..." id="searchInput">
            </div>
            <div class="filters">
                <select id="dateFilter">
                    <option value="">Date Filter</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
                <select id="statusFilter">
                    <option value="">Status Filter</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="overridden">Overridden</option>
                </select>
            </div>
        </div>

        

        <!-- Schedules Table -->
        <div class="schedules-table-container">
            <table class="schedules-table">
                <thead>
                    <tr>
                        <th>ID No.</th>
                        <th>RFID UID.</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="schedulesTableBody">
                    <?php 
                    if (count($all_schedules) > 0) {
                        foreach ($all_schedules as $schedule) { 
                    ?>
                    <tr class="schedule-row" data-search="<?php echo strtolower(($schedule['user_id'] ?? '') . ' ' . ($schedule['rfid_uid'] ?? '') . ' ' . ($schedule['first_name'] ?? '') . ' ' . ($schedule['last_name'] ?? '') . ' ' . date('M d, Y', strtotime($schedule['date'])) . ' ' . date('h:i A', strtotime($schedule['time_start'])) . ' ' . date('h:i A', strtotime($schedule['time_end'])) . ' ' . ($schedule['status'] ?? '')); ?>" data-date="<?php echo $schedule['date']; ?>" data-status="<?php echo strtolower($schedule['status'] ?? 'pending'); ?>">
                        <td><?php echo htmlspecialchars($schedule['user_id'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($schedule['rfid_uid'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(($schedule['first_name'] ?? '') . ' ' . ($schedule['last_name'] ?? '')); ?></td>
                        <td><?php echo date('M d, Y', strtotime($schedule['date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($schedule['time_start'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($schedule['time_end'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($schedule['status'] ?? 'pending'); ?>">
                                <?php echo ucfirst($schedule['status'] ?? 'Pending'); ?>
                            </span>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                            <i class="fa fa-calendar-times" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                            No schedules found
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="schedules-right">
        <!-- Calendar -->
        <div class="calendar-container">
            <div class="calendar-header">
                <h3><?php echo date('F Y'); ?></h3>
            </div>
            <div class="calendar-grid">
                <div class="calendar-weekdays">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>
                <div class="calendar-days">
                    <?php
                    // Generate calendar days for current month
                    $current_month = date('n');
                    $current_year = date('Y');
                    $first_day = mktime(0, 0, 0, $current_month, 1, $current_year);
                    $first_weekday = date('w', $first_day);
                    $days_in_month = date('t', $first_day);
                    $today_day = date('j');
                    
                    // Empty cells for days before month starts
                    for ($i = 0; $i < $first_weekday; $i++) {
                        echo '<div class="calendar-day empty"></div>';
                    }
                    
                    // Days of the month
                    for ($day = 1; $day <= $days_in_month; $day++) {
                        $class = 'calendar-day';
                        $current_date = date('Y-m-d', mktime(0, 0, 0, $current_month, $day, $current_year));
                        $today = date('Y-m-d');
                        
                        // Highlight today
                        if ($day == $today_day) {
                            $class .= ' today';
                        }
                        
                        // Check if this date has schedules and determine if it's past or upcoming
                        if (in_array($day, $schedule_dates)) {
                            if ($current_date < $today) {
                                $class .= ' has-past-schedule'; // Past schedule - light gray
                            } elseif ($current_date > $today) {
                                $class .= ' has-upcoming-schedule'; // Upcoming schedule - red
                            } else {
                                $class .= ' has-today-schedule'; // Today's schedule
                            }
                        }
                        
                        echo "<div class=\"$class\">$day</div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Access Logs -->
        <div class="access-logs-container">
            <h3>Access Logs (Today)</h3>
            <div class="access-logs-list">
                <?php 
                if ($total_access_logs > 0) {
                    $display_count = 0;
                    while (($log = mysqli_fetch_assoc($access_logs_result)) && $display_count < 4) { 
                        $display_count++;
                ?>
                <div class="access-log-item">
                    <div class="log-info">
                        <span class="log-name"><?php echo htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? 'Unknown')); ?></span>
                        <span class="log-status <?php echo $log['access_status']; ?>">
                            <?php echo ucfirst($log['access_status']); ?>
                        </span>
                    </div>
                    <span class="log-time"><?php echo date('h:i A', strtotime($log['access_timestamp'])); ?></span>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div style="text-align: center; padding: 40px; color: #888;">
                    <i class="fa fa-door-closed" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                    No access logs found for today
                </div>
                <?php } ?>
            </div>
            <?php if ($total_access_logs > 4): ?>
            <button class="see-more-btn">See More</button>
            <?php endif; ?>
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

/* Search and Filter functionality */
const searchInput = document.getElementById('searchInput');
const dateFilter = document.getElementById('dateFilter');
const statusFilter = document.getElementById('statusFilter');
const scheduleRows = document.querySelectorAll('.schedule-row');

function filterSchedules() {
    const searchTerm = searchInput.value.toLowerCase();
    const dateValue = dateFilter.value;
    const statusValue = statusFilter.value;
    
    let visibleCount = 0;
    
    scheduleRows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        const rowDate = row.getAttribute('data-date');
        const rowStatus = row.getAttribute('data-status');
        
        let matchesSearch = searchData.includes(searchTerm);
        let matchesDate = dateValue === '' || checkDateFilter(rowDate, dateValue);
        let matchesStatus = statusValue === '' || rowStatus === statusValue;
        
        if (matchesSearch && matchesDate && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    const tableBody = document.getElementById('schedulesTableBody');
    
    if (visibleCount === 0 && (searchTerm !== '' || dateValue !== '' || statusValue !== '')) {
        // Check if no results message already exists
        let noResultsRow = tableBody.querySelector('.no-results-row');
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            tableBody.appendChild(noResultsRow);
        }
        
        let message = 'No schedules found';
        if (searchTerm !== '') message += ` matching "${searchTerm}"`;
        if (dateValue !== '') message += ` for ${dateValue}`;
        if (statusValue !== '') message += ` with status "${statusValue}"`;
        
        noResultsRow.innerHTML = `
            <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
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

function checkDateFilter(rowDate, filterValue) {
    const scheduleDate = new Date(rowDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    switch(filterValue) {
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            return rowDate === todayStr;
            
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            return scheduleDate >= weekStart && scheduleDate <= weekEnd;
            
        case 'month':
            return scheduleDate.getMonth() === today.getMonth() && 
                   scheduleDate.getFullYear() === today.getFullYear();
            
        default:
            return true;
    }
}

// Event listeners
searchInput.addEventListener('input', filterSchedules);
dateFilter.addEventListener('change', filterSchedules);
statusFilter.addEventListener('change', filterSchedules);
</script>

</body>
</html>
