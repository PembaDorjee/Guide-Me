<?php
session_start();
require_once '../database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />
  <link rel="stylesheet" href="admin.css" />
 
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside>
      <div class="top">
        <div class="logo">
          <h2>Guide <span>Me</span></h2>
        </div>
        <div class="close" id="close_btn">
          <span class="material-symbols-sharp">close_small</span>
        </div>
      </div>

      <div class="sidebar">
        <a href="#" class="active" data-section="dashboard">
          <span class="material-symbols-sharp">dashboard</span>
          <h3>Dashboard</h3>
        </a>
        <a href="#" data-section="services">
          <span class="material-symbols-sharp">hiking</span>
          <h3>Services</h3>
        </a>
        <a href="#" data-section="users">
          <span class="material-symbols-sharp">groups</span>
          <h3>Users</h3>
        </a>
        <a href="#" data-section="bookings">
          <span class="material-symbols-sharp">book</span>
          <h3>Bookings</h3>
        </a>
        <a href="#" data-section="messages">
          <span class="material-symbols-sharp">mail_outline</span>
          <h3>Messages</h3>
          <span class="msg_count">14</span>
        </a>
        <a href="#" data-section="settings">
          <span class="material-symbols-sharp">settings</span>
          <h3>Settings</h3>
        </a>
        <a href="#">
          <span class="material-symbols-sharp">logout</span>
          <h3>Logout</h3>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div id="main-content">
      <main id="dashboard" class="content-section">
        <h1>Dashboard</h1>
        <div class="date">
          <input type="date">
        </div>
        <div class="insights">
          <div class="sales">
            <span class="material-symbols-sharp">book</span>
            <div class="middle">
              <div class="left">
                <h3>treks booked</h3>
                <h1>100</h1>
              </div>
              <div class="progress">
                <svg><circle r="30" cy="40" cx="40"></circle></svg>
                <div class="numbers">80%</div>
              </div>
            </div>
            <small>this month</small>
          </div>

          <div class="expenses">
            <span class="material-symbols-sharp">trending_up</span>
            <div class="middle">
              <div class="left">
                <h3>total revenue</h3>
                <h1>$250000</h1>
              </div>
              <div class="progress">
                <svg><circle r="30" cy="40" cx="40"></circle></svg>
                <div class="numbers">80%</div>
              </div>
            </div>
            <small>this month</small>
          </div>

          <div class="income">
            <span class="material-symbols-sharp">footprint</span>
            <div class="middle">
              <div class="left">
                <h3>active treks</h3>
                <h1>25</h1>
              </div>
              <div class="progress">
                <svg><circle r="30" cy="40" cx="40"></circle></svg>
                <div class="numbers">80%</div>
              </div>
            </div>
            <small>this month</small>
          </div>
        </div>

        <div class="recent_order">
          <h1>Recent Bookings</h1>
          <table>
            <thead>
              <tr>
                <th>Trek Name</th>
                <th>Booking ID</th>
                <th>Payments</th>
                <th>Start Date</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Everest Base Camp</td>
                <td>10123</td>
                <td>Paid</td>
                <td class="warning">Mar 15, 2025</td>
                <td class="primary">View</td>
              </tr>
              <tr>
                <td>Annapurna Circuit</td>
                <td>6768</td>
                <td>Due</td>
                <td class="warning">Mar 20, 2025</td>
                <td class="primary">View</td>
              </tr>
              <tr>
                <td>Langtang Valley</td>
                <td>6768</td>
                <td>Due</td>
                <td class="warning">May 22, 2025</td>
                <td class="primary">View</td>
              </tr>
              <tr>
                <td>Everest Base Camp</td>
                <td>6768</td>
                <td>Due</td>
                <td class="warning">Apr 24, 2025</td>
                <td class="primary">View</td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>

      <main id="services" class="content-section hidden">
        <h1>Services</h1>

  <div class="service-tabs">
    <button class="tab-btn active" data-tab="trekking">Trekking</button>
    <button class="tab-btn" data-tab="hiking">Hiking</button>
    <button class="tab-btn" data-tab="mountaineering">Mountaineering</button>
  </div>

  <div class="service-content" id="trekking-tab">
    <div class="trek-actions">
      <h2>Trek Management</h2>
      <div><button onclick="openAddTrekModal()">Add Trek</button></div>
    </div>

    <div class="trek-list" id="trek-list"></div>
  </div>

  <div class="service-content hidden" id="hiking-tab">
    <h2>Hiking Management (Coming Soon)</h2>
  </div>

  <div class="service-content hidden" id="mountaineering-tab">
    <h2>Mountaineering Management (Coming Soon)</h2>
  </div>
</main>

<!-- Modal -->
<div id="trekModal" class="modal hidden">
  <div class="modal-content">
    <h2 id="modalTitle">Add Trek</h2>
    <input type="text" id="trekNameInput" placeholder="Trek Name" required>
    <input type="number" id="trekDurationInput" placeholder="Duration (in days)" required>
    
    <select id="trekDifficultyInput" required>
      <option value="">Select Difficulty</option>
      <option value="Easy">Easy</option>
      <option value="Moderate">Moderate</option>
      <option value="Difficult">Difficult</option>
    </select>

    <input type="text" id="trekRegionInput" placeholder="Region (e.g. Khumbu)" required>
    <input type="number" id="trekAltitudeInput" placeholder="Max Altitude (meters)" required>
    <input type="number" id="trekPriceInput" placeholder="Price (USD)" required>
    
    <textarea id="trekDescriptionInput" placeholder="Short Description" required></textarea>
    <input type="url" id="trekImageInput" placeholder="Thumbnail Image URL" required>

    <div class="modal-actions">
      <button class="cancel" onclick="closeTrekModal()">Cancel</button>
      <button class="save" onclick="saveTrek()">Save</button>
    </div>
  </div>
</div>

      </main>

      <!-- Users Section -->
      <div id="users" class="content-section hidden">
        <h2>User Management</h2>
        
        <div class="service-tabs">
          <button class="tab-btn active" data-tab="clients">Our Clients</button>
          <button class="tab-btn" data-tab="guides">Our Guides</button>
          <button class="tab-btn" data-tab="guide-approval">Guide Approval</button>
        </div>
        
        <div id="clients-tab" class="service-content">
          <div id="client-list">
            <!-- Client cards will be loaded here -->
          </div>
        </div>
        
        <div id="guides-tab" class="service-content hidden">
          <div id="guide-list">
            <!-- Guide cards will be loaded here -->
          </div>
        </div>
        
        <div id="guide-approval-tab" class="service-content hidden">
          <div id="guide-approval-list">
            <!-- Guide approval requests will be loaded here -->
          </div>
        </div>
      </div>


      <main id="bookings" class="content-section hidden">
        <h1>Bookings</h1>
        <p>Track trek bookings and history.</p>
      </main>

      <main id="messages" class="content-section hidden">
        <h1>Messages</h1>
        <p>Check your latest messages and inquiries.</p>
      </main>

      <main id="settings" class="content-section hidden">
        <h1>Settings</h1>
        <p>Configure admin preferences and application settings.</p>
      </main>
    </div>

    <!-- Right Section -->
    <div class="right">

        <!-------top section start---->
        <div class="top">
            <button id="menu_bar">
                <span class="material-symbols-sharp">menu</span>
            </button>
            <div class="theme-toggler">
                <span class="material-symbols-sharp active">light_mode</span>
                <span class="material-symbols-sharp">dark_mode</span>

            </div>
            <div class="profile">
               <div class="info">
                    <p> <b>Admin</b></p>
                    <small class="text-muted"></small>
                </div>
                <div class="profile-photo">
                    <img src="https://shorturl.at/c9nb2" alt="">
                </div>
              </div>
            </div>
            <!-------------end top section-->

            <!-------------start recent updates------------>
            <div class="recent-updates">
                <h2>Recent Updates</h2>
                <div class="updates"> 
                <div class="update">
                        <div class="profile-photo">
                            <img src="https://shorturl.at/lcVll">
                        </div>
                        <div class="message">
                            <p><b>Pemba Sherpa</b> joined as new guide.</p>
                            <small class="text-muted">2 minutes ago</small>
                        </div>   
                </div>

                <div class="update">
                    <div class="profile-photo">
                        <img src="https://goto.now/b5Vvj" alt="">
                    </div>
                    <div class="message">
                        <p><b>Adventure Explore</b> registered as a new partner.</p>
                        <small class="text-muted">5 minutes ago</small>
                    </div>
                </div> 

                <div class="update">
                    <div class="profile-photo">
                        <img src="https://goto.now/6Dd3A" alt="">
                    </div>
                    <div class="message">
                        <p><b>The Hiking Club</b> registered as a new partner.</p>
                        <small class="text-muted">10 minutes ago</small>
                    </div>
                </div> 

                
            </div> 
            </div>
            

            <!-------------end recent updates------------>


            


        </div>


</div>
    </div>
  </div>

  <!-- Add this line before closing body tag -->
  <script src="admin.js"></script>
</body>
</html>