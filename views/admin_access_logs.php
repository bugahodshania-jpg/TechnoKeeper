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
<title>Access Logs</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin_access_logs.css">

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
            <a href="../views/admin_access_logs.php" class="active" title="Access Logs"><i class="fa fa-door-open"></i><span>Access Logs</span></a>
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
                <h1>Access Logs</h1>
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
        <div class="dashboard-content">
            <div class="access-logs-section">
                <div class="section-header">
                    <h2>Access Logs (All)</h2>
                    <div class="header-actions">
                        <button class="btn-view-all">View All</button>
                        <div class="show-selector">
                            <span>Show</span>
                            <select id="showLimit" onchange="updateLogLimit()">
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">All</option>
                            </select>
                        </div>
                        <button class="btn-delete-selected" id="deleteSelectedBtn" disabled>Delete Selected</button>
                    </div>
                </div>
                
                <div class="search-section">
                    <div class="search-controls">
                        <div class="search-bar">
                            <i class="fa fa-search"></i>
                            <input type="text" placeholder="Search" id="searchInput">
                        </div>
                        <div class="method-filter">
                            <select id="methodFilter" class="method-select">
                                <option value="all">All Methods</option>
                                <option value="rfid">RFID</option>
                                <option value="pin">PIN</option>
                                <option value="override">Override</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="access-logs-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>ID No.</th>
                                <th>Name</th>
                                <th>RFID UID.</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="accessLogsTableBody">
                            <?php
                            require_once '../server/db.php';
                            
                            // Query to get access logs with limit
                            $query = "SELECT * FROM access_logs_view ORDER BY access_timestamp DESC LIMIT 20";
                            $result = mysqli_query($connect, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $name = '';
                                    if (!empty($row['user_first_name']) && !empty($row['user_last_name'])) {
                                        $name = $row['user_first_name'] . ' ' . $row['user_last_name'];
                                    } elseif (!empty($row['admin_first_name']) && !empty($row['admin_last_name'])) {
                                        $name = $row['admin_first_name'] . ' ' . $row['admin_last_name'];
                                    } else {
                                        $name = 'Unknown';
                                    }
                                    
                                    $date = date('Y-m-d', strtotime($row['access_timestamp']));
                                    $time = date('H:i:s', strtotime($row['access_timestamp']));
                                    $statusClass = $row['access_status'] === 'granted' ? 'status-granted' : 'status-denied';
                                ?>
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox" data-log-id="<?php echo htmlspecialchars($row['log_id']); ?>"></td>
                                        <td><?php echo htmlspecialchars($row['log_id']); ?></td>
                                        <td><?php echo htmlspecialchars($name); ?></td>
                                        <td><?php echo htmlspecialchars($row['card_rfid']); ?></td>
                                        <td><?php echo htmlspecialchars($date); ?></td>
                                        <td><?php echo htmlspecialchars($time); ?></td>
                                        <td><?php echo htmlspecialchars($row['method_access']); ?></td>
                                        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst(htmlspecialchars($row['access_status'])); ?></span></td>
                                        <td>
                                            <div class="row-actions">
                                                <button class="btn-edit" onclick="editLog(<?php echo htmlspecialchars($row['log_id']); ?>)" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-delete" onclick="deleteLog(<?php echo htmlspecialchars($row['log_id']); ?>)" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                echo '<tr><td colspan="9" style="text-align: center; padding: 20px;">No access logs found.</td></tr>';
                            }
                            
                            mysqli_close($connect);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Single Log Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Access Log</h3>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this access log?</p>
                <p>This action cannot be undone.</p>
                <div class="form-group">
                    <label for="deletePassword">Enter your password to confirm:</label>
                    <input type="password" id="deletePassword" placeholder="Enter your password">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn-confirm-delete" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Delete Selected Modal -->
    <div class="modal" id="deleteSelectedModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999999;">
        <div class="modal-content" style="background: white; margin: 15% auto; padding: 20px; width: 80%; max-width: 500px; border-radius: 8px;">
            <div class="modal-header">
                <h3>Delete Selected Access Logs</h3>
                <span class="close" onclick="closeModal('deleteSelectedModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="selectedCount">0</span> selected access logs?</p>
                <p>This action cannot be undone.</p>
                <div class="form-group">
                    <label for="deleteSelectedPassword">Enter your password to confirm:</label>
                    <input type="password" id="deleteSelectedPassword" placeholder="Enter your password">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('deleteSelectedModal')">Cancel</button>
                <button class="btn-confirm-delete" onclick="confirmDeleteSelected()">Delete All</button>
            </div>
        </div>
    </div>

    <!-- Edit Log Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Access Log</h3>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editLogForm">
                    <input type="hidden" id="editLogId">
                    <div class="form-group">
                        <label for="editStatus">Status:</label>
                        <select id="editStatus">
                            <option value="granted">Granted</option>
                            <option value="denied">Denied</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editMethod">Access Method:</label>
                        <select id="editMethod">
                            <option value="RFID">RFID</option>
                            <option value="PIN">PIN</option>
                            <option value="OVERRIDE">Override</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button class="btn-confirm-edit" onclick="confirmEdit()">Save Changes</button>
            </div>
        </div>
    </div>

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

/* ================= ACCESS LOGS FUNCTIONALITY ================= */

let currentDeleteId = null;
let currentEditId = null;

// Select all checkbox functionality
const selectAllCheckbox = document.getElementById('selectAll');

selectAllCheckbox.addEventListener('change', function() {
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateDeleteSelectedButton();
});

function updateDeleteSelectedButton() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.disabled = checkedBoxes.length === 0;
    }
}

function attachCheckboxListeners() {
    const newRowCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    newRowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(newRowCheckboxes).every(cb => cb.checked);
            const anyChecked = Array.from(newRowCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = anyChecked && !allChecked;
            updateDeleteSelectedButton();
        });
    });
}

// Initial setup for checkboxes that exist on page load
attachCheckboxListeners();

// Search functionality
const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('accessLogsTableBody');

// Add debounce for better user experience
let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(filterTable, 300); // 300ms delay
});

// Method filter functionality
const methodFilter = document.getElementById('methodFilter');
let currentMethodFilter = 'all';

methodFilter.addEventListener('change', function() {
    currentMethodFilter = this.value;
    filterTable();
});

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const rows = tableBody.getElementsByTagName('tr');
    
    let visibleCount = 0;
    
    Array.from(rows).forEach(row => {
        const text = row.textContent.toLowerCase();
        const methodColumn = row.cells[6]?.textContent.toLowerCase() || ''; // Method column index
        
        let matchesSearch = text.includes(searchTerm);
        let matchesMethod = currentMethodFilter === 'all' || methodColumn === currentMethodFilter;
        
        if (matchesSearch && matchesMethod) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    if (visibleCount === 0 && (searchTerm !== '' || currentMethodFilter !== 'all')) {
        // Check if no results message already exists
        let noResultsRow = tableBody.querySelector('.no-results-row');
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            tableBody.appendChild(noResultsRow);
        }
        
        let message = 'No access logs found';
        if (searchTerm !== '') message += ` matching "${searchTerm}"`;
        if (currentMethodFilter !== 'all') message += ` with method "${currentMethodFilter}"`;
        
        noResultsRow.innerHTML = `
            <td colspan="8" style="text-align: center; padding: 40px; color: #888;">
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

// View All button functionality
const viewAllBtn = document.querySelector('.btn-view-all');
const limitBtn = document.querySelector('.btn-limit');
let isViewAll = false;

viewAllBtn.addEventListener('click', function() {
    if (!isViewAll) {
        fetchAccessLogs(false);
        this.textContent = 'Limit 20';
        limitBtn.textContent = 'Limit 20';
        isViewAll = true;
    } else {
        fetchAccessLogs(true);
        this.textContent = 'View All';
        limitBtn.textContent = 'Limit 20';
        isViewAll = false;
    }
});

limitBtn.addEventListener('click', function() {
    if (isViewAll) {
        fetchAccessLogs(true);
        viewAllBtn.textContent = 'View All';
        this.textContent = 'Limit 20';
        isViewAll = false;
    }
});


function fetchAccessLogs(limit = true) {
    const limitClause = limit ? 'LIMIT 20' : '';
    
    fetch(`../server/get_access_logs.php?limit=${limit ? '20' : 'all'}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTable(data.logs);
                attachCheckboxListeners();
            }
        })
        .catch(error => {
            console.error('Error fetching access logs:', error);
        });
}

function updateTable(logs) {
    let html = '';
    
    if (logs.length === 0) {
        html = '<tr><td colspan="9" style="text-align: center; padding: 20px;">No access logs found.</td></tr>';
    } else {
        logs.forEach(row => {
            const statusClass = row.access_status === 'granted' ? 'status-granted' : 'status-denied';
            html += `
                <tr>
                    <td><input type="checkbox" class="row-checkbox" data-log-id="${row.log_id}"></td>
                    <td>${row.log_id}</td>
                    <td>${row.name}</td>
                    <td>${row.card_rfid}</td>
                    <td>${row.date}</td>
                    <td>${row.time}</td>
                    <td>${row.method_access}</td>
                    <td><span class="status-badge ${statusClass}">${row.access_status.charAt(0).toUpperCase() + row.access_status.slice(1)}</span></td>
                    <td>
                        <div class="row-actions">
                            <button class="btn-edit" onclick="editLog(${row.log_id})" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteLog(${row.log_id})" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    tableBody.innerHTML = html;
}

// Modal functions
function openModal(modalId) {
    console.log('Opening modal:', modalId); // Debug log
    const modal = document.getElementById(modalId);
    console.log('Modal element:', modal); // Debug log
    console.log('Modal current display:', modal ? modal.style.display : 'modal not found');
    
    if (modal) {
        modal.style.display = 'block';
        modal.style.visibility = 'visible';
        modal.style.opacity = '1';
        modal.style.zIndex = '999999';
        document.body.style.overflow = 'hidden';
        console.log('Modal display set to:', modal.style.display);
        console.log('Modal should now be visible'); // Debug log
        
        // Also add a visual indicator
        setTimeout(() => {
            console.log('Modal display after timeout:', modal.style.display);
            alert('Modal display is now: ' + modal.style.display);
        }, 100);
    } else {
        console.error('Modal not found:', modalId);
        alert('Modal not found: ' + modalId);
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Clear password fields
    if (modalId === 'deleteModal') {
        document.getElementById('deletePassword').value = '';
        currentDeleteId = null;
    } else if (modalId === 'deleteSelectedModal') {
        document.getElementById('deleteSelectedPassword').value = '';
    } else if (modalId === 'editModal') {
        currentEditId = null;
    }
}

// CRUD Operations
function deleteLog(logId) {
    currentDeleteId = logId;
    openModal('deleteModal');
}

function confirmDelete() {
    const password = document.getElementById('deletePassword').value;
    
    if (!password) {
        alert('Please enter your password');
        return;
    }
    
    fetch('../server/delete_access_log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            logId: currentDeleteId,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteModal');
            fetchAccessLogs(isViewAll ? false : true);
            showNotification('Access log deleted successfully', 'success');
        } else {
            alert(data.message || 'Failed to delete access log');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the access log');
    });
}

function confirmDeleteSelected() {
    const password = document.getElementById('deleteSelectedPassword').value;
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const logIds = Array.from(checkedBoxes).map(cb => cb.dataset.logId);
    
    if (!password) {
        alert('Please enter your password');
        return;
    }
    
    fetch('../server/delete_selected_logs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            logIds: logIds,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteSelectedModal');
            fetchAccessLogs(isViewAll ? false : true);
            showNotification(`${logIds.length} access logs deleted successfully`, 'success');
        } else {
            alert(data.message || 'Failed to delete access logs');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the access logs');
    });
}

function editLog(logId) {
    currentEditId = logId;
    
    // Fetch current log data
    fetch(`../server/get_access_log.php?id=${logId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editLogId').value = logId;
                document.getElementById('editStatus').value = data.log.access_status;
                document.getElementById('editMethod').value = data.log.method_access;
                openModal('editModal');
            } else {
                alert('Failed to fetch access log data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching the access log');
        });
}

function confirmEdit() {
    const status = document.getElementById('editStatus').value;
    const method = document.getElementById('editMethod').value;
    
    fetch('../server/update_access_log.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            logId: currentEditId,
            status: status,
            method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('editModal');
            fetchAccessLogs(isViewAll ? false : true);
            showNotification('Access log updated successfully', 'success');
        } else {
            alert(data.message || 'Failed to update access log');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the access log');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 4000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Update log limit function
function updateLogLimit() {
    const limit = document.getElementById('showLimit').value;
    const isViewAll = limit === 'all';
    
    // Update the current limit state
    currentLimit = isViewAll ? null : parseInt(limit);
    
    // Fetch logs with new limit
    fetchAccessLogs(isViewAll);
    
    // Show notification
    showNotification(`Showing ${isViewAll ? 'all' : limit} records`, 'success');
}

// FINAL DELETE SELECTED FUNCTION - Override everything else
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        if (deleteBtn) {
            // Remove all existing event listeners by cloning
            const newBtn = deleteBtn.cloneNode(true);
            deleteBtn.parentNode.replaceChild(newBtn, deleteBtn);
            
            // Add fresh event listener
            newBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    document.getElementById('selectedCount').textContent = checkedBoxes.length;
                    const modal = document.getElementById('deleteSelectedModal');
                    if (modal) {
                        modal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                        alert('Modal is now visible! Check the screen.');
                    } else {
                        alert('Modal not found!');
                    }
                } else {
                    alert('Please select at least one item to delete');
                }
            });
        }
    }, 2000); // Wait 2 seconds after page load
});

</script>

</body>
</html>
