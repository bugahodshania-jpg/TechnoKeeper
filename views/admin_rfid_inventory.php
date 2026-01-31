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

// Get RFID statistics
$totalCardsQuery = "SELECT COUNT(*) as count FROM RFID_cards";
$totalCardsResult = mysqli_query($connect, $totalCardsQuery);
$totalCards = mysqli_fetch_assoc($totalCardsResult)['count'];

$assignedCardsQuery = "SELECT COUNT(*) as count FROM RFID_cards WHERE status = 'active'";
$assignedCardsResult = mysqli_query($connect, $assignedCardsQuery);
$assignedCards = mysqli_fetch_assoc($assignedCardsResult)['count'];

$unassignedCardsQuery = "SELECT COUNT(*) as count FROM RFID_cards WHERE status != 'active'";
$unassignedCardsResult = mysqli_query($connect, $unassignedCardsQuery);
$unassignedCards = mysqli_fetch_assoc($unassignedCardsResult)['count'];

// Get RFID cards list with user information
$cardsQuery = "
    SELECT 
        rc.card_id,
        rc.rfid_uid,
        rc.status,
        rc.issued_at,
        u.user_id,
        u.first_name,
        u.last_name,
        u.work_position,
        u.role,
        u.id as user_db_id
    FROM RFID_cards rc
    LEFT JOIN users u ON rc.user_id = u.id
    ORDER BY rc.issued_at DESC
    LIMIT 20
";
$cardsResult = mysqli_query($connect, $cardsQuery);
$cards = [];
while ($row = mysqli_fetch_assoc($cardsResult)) {
    $cards[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Inventory</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/admin_rfid_inventory.css">
<link rel="stylesheet" href="../css/admin_rfid_inventory1.css">

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
            <a href="../views/admin_rfid_inventory.php" class="active" title="RFID Inventory"><i class="fa fa-barcode"></i><span>RFID Inventory</span></a>
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
                <h1>RFID Inventory</h1>
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
                        <i class="fa fa-barcode"></i>
                    </div>
                    <div class="card-info">
                        <h3>Total Cards</h3>
                        <p class="card-number"><?php echo $totalCards; ?></p>
                    </div>
                </div>
                <div class="summary-card female-users">
                    <div class="card-icon">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <div class="card-info">
                        <h3>Active</h3>
                        <p class="card-number"><?php echo $assignedCards; ?></p>
                    </div>
                </div>
                <div class="summary-card male-users">
                    <div class="card-icon">
                        <i class="fa fa-user-times"></i>
                    </div>
                    <div class="card-info">
                        <h3>Inactive</h3>
                        <p class="card-number"><?php echo $unassignedCards; ?></p>
                    </div>
                </div>
            </div>

            <!-- Header with Export and Add buttons -->
            <div class="management-header">
                <div class="header-buttons">
                    <button class="btn-export" onclick="exportToCSV()">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                    <button class="btn-add" onclick="showAddCardModal()">
                        <i class="fa fa-plus"></i> Add Card
                    </button>
                </div>
            </div>

            <!-- RFID Cards Section -->
            <div class="user-lists-section">
                <div class="section-header">
                    <h2>RFID Cards</h2>
                    <div class="list-controls">
                        <div class="view-controls">
                            <div class="role-filter">
                                <span>Role</span>
                                <select id="roleFilter" onchange="filterByRole()">
                                    <option value="all">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="unassigned">Unassigned</option>
                                </select>
                            </div>
                            <button class="btn-view-all" onclick="viewAllCards()">
                                <i class="fa fa-eye"></i> View All
                            </button>
                            <div class="show-selector">
                                <span>Show</span>
                                <select id="showLimit" onchange="updateCardLimit()">
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>
                        <div class="search-container">
                            <input type="text" id="cardSearch" placeholder="Search cards..." class="search-input" onkeyup="searchCards()">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Cards Table -->
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Card ID</th>
                                <th>RFID UID</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cardsTableBody">
                            <?php if (!empty($cards)): ?>
                                <?php foreach ($cards as $card): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($card['card_id']); ?></td>
                                        <td><?php echo htmlspecialchars($card['rfid_uid']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $card['status'] === 'active' ? 'assigned' : 'unassigned'; ?>">
                                                <?php echo htmlspecialchars(ucfirst($card['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $card['user_id'] ? htmlspecialchars($card['first_name'] . ' ' . $card['last_name']) : 'N/A'; ?></td>
                                        <td><?php echo $card['work_position'] ? htmlspecialchars($card['work_position']) : 'N/A'; ?></td>
                                        <td>
                                            <?php if ($card['role']): ?>
                                                <span class="role-badge <?php echo $card['role']; ?>">
                                                    <?php echo htmlspecialchars(ucfirst($card['role'])); ?>
                                                </span>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($card['issued_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-edit" onclick="editCard(<?php echo $card['card_id']; ?>)">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-delete" onclick="deleteCard(<?php echo $card['card_id']; ?>)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px;">No RFID cards found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</div>

<!-- Edit Card Modal -->
<div id="editCardModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit RFID Card</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editCardForm">
                <input type="hidden" id="editCardId" name="card_id">
                
                <div class="form-group">
                    <label for="editRfidUid">RFID UID:</label>
                    <input type="text" id="editRfidUid" name="rfid_uid" required>
                </div>
                
                <div class="form-group">
                    <label for="editStatus">Status:</label>
                    <select id="editStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="lost">Lost</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
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

/* RFID Card Management Functions */
function filterByRole() {
    applyFilters();
}

function searchCards() {
    applyFilters();
}

function applyFilters() {
    const searchValue = document.getElementById('cardSearch').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const rows = document.querySelectorAll('#cardsTableBody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        // Skip "no results" rows
        if (row.classList.contains('no-results-row')) {
            return;
        }
        
        const cardId = row.cells[0].textContent.toLowerCase();
        const rfidUid = row.cells[1].textContent.toLowerCase();
        const status = row.cells[2].textContent.toLowerCase();
        const assignedTo = row.cells[3].textContent.toLowerCase();
        const position = row.cells[4].textContent.toLowerCase();
        const roleCell = row.cells[5].textContent.toLowerCase().trim();
        
        // Check if row matches search criteria
        const matchesSearch = searchValue === '' || 
                           cardId.includes(searchValue) || 
                           rfidUid.includes(searchValue) || 
                           status.includes(searchValue) || 
                           assignedTo.includes(searchValue) ||
                           position.includes(searchValue) ||
                           roleCell.includes(searchValue);
        
        // Check if row matches role filter
        let matchesRole = true;
        if (roleFilter !== 'all') {
            if (roleFilter === 'unassigned') {
                matchesRole = roleCell === 'n/a';
            } else if (roleFilter === 'admin') {
                matchesRole = roleCell === 'admin';
            } else if (roleFilter === 'user') {
                matchesRole = roleCell === 'user';
            }
        }
        
        // Show row if it matches both criteria
        if (matchesSearch && matchesRole) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    const tableBody = document.getElementById('cardsTableBody');
    
    // Remove existing no-results row
    const existingNoResults = tableBody.querySelector('.no-results-row');
    if (existingNoResults) {
        existingNoResults.remove();
    }
    
    if (visibleCount === 0) {
        const noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        
        let message = 'No RFID cards found';
        if (searchValue !== '' && roleFilter !== 'all') {
            message = `No RFID cards found matching "${searchValue}" with role "${roleFilter}"`;
        } else if (searchValue !== '') {
            message = `No RFID cards found matching "${searchValue}"`;
        } else if (roleFilter !== 'all') {
            message = `No RFID cards found with role "${roleFilter}"`;
        }
        
        noResultsRow.innerHTML = `
            <td colspan="8" style="text-align: center; padding: 40px; color: #888;">
                <i class="fa fa-search" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                ${message}
            </td>
        `;
        tableBody.appendChild(noResultsRow);
    }
}

function updateCardLimit() {
    const limit = document.getElementById('showLimit').value;
    // In a real implementation, this would fetch data from server with the new limit
    console.log('Update card limit to:', limit);
    // For now, just reload the page
    window.location.reload();
}

function viewAllCards() {
    // In a real implementation, this would fetch all cards
    console.log('View all cards');
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
    a.download = 'rfid_cards_export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function showAddCardModal() {
    // In a real implementation, this would show a modal to add a new card
    console.log('Show add card modal');
    alert('Add RFID Card functionality would be implemented here');
}

function editCard(cardId) {
    // Find the card data from the table
    const rows = document.querySelectorAll('#cardsTableBody tr');
    let cardData = null;
    
    rows.forEach(row => {
        const idCell = row.cells[0];
        if (idCell && idCell.textContent == cardId) {
            cardData = {
                cardId: cardId,
                rfidUid: row.cells[1].textContent,
                status: row.cells[2].textContent.toLowerCase()
            };
        }
    });
    
    if (cardData) {
        // Populate the modal with card data
        document.getElementById('editCardId').value = cardData.cardId;
        document.getElementById('editRfidUid').value = cardData.rfidUid;
        
        // Set status
        const statusSelect = document.getElementById('editStatus');
        statusSelect.value = cardData.status;
        
        // Show the modal
        document.getElementById('editCardModal').style.display = 'flex';
    }
}

function closeEditModal() {
    document.getElementById('editCardModal').style.display = 'none';
}

// Handle form submission
document.getElementById('editCardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const cardData = Object.fromEntries(formData);
    
    console.log('Updating card:', cardData);
    
    // Send AJAX request to update the card
    fetch('../server/update_rfid_card.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(cardData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Card updated successfully!');
            closeEditModal();
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert('Error updating card: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating card. Please try again.');
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editCardModal');
    if (event.target === modal) {
        closeEditModal();
    }
}

function deleteCard(cardId) {
    if (confirm('Are you sure you want to delete this RFID card?')) {
        // In a real implementation, this would send a request to delete the card
        console.log('Delete card:', cardId);
        alert('Delete RFID Card functionality would be implemented here for card ID: ' + cardId);
    }
}
</script>

</body>
</html>