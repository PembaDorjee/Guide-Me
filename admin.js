const sideMenu = document.querySelector("aside");
  const menuBtn = document.querySelector("#menu_bar");
  const closeBtn = document.querySelector("#close_btn");
  const themeToggler = document.querySelector(".theme-toggler");
  const links = document.querySelectorAll(".sidebar a[data-section]");
  const sections = document.querySelectorAll(".content-section");

  const tabButtons = document.querySelectorAll('.tab-btn');
  const serviceContents = document.querySelectorAll('.service-content');
  const modal = document.getElementById('trekModal');
  const trekInput = document.getElementById('trekNameInput');
  const trekList = document.getElementById('trek-list');
  const modalTitle = document.getElementById('modalTitle');

  let isEditing = false;
  let currentEditEl = null;

  // Sidebar toggle
  menuBtn.addEventListener("click", () => {
    sideMenu.style.display = "block";
  });
  closeBtn.addEventListener("click", () => {
    sideMenu.style.display = "none";
  });

  // Theme toggle
  themeToggler.addEventListener("click", () => {
    document.body.classList.toggle("dark-theme-variables");
    themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
    themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");
  });

  // Page section navigation
  links.forEach(link => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const target = link.getAttribute("data-section");

    sections.forEach(section => section.classList.add("hidden"));
    document.getElementById(target).classList.remove("hidden");
    links.forEach(l => l.classList.remove("active"));
    link.classList.add("active");

    // Call specific fetch functions
    if (target === "services") fetchTreks();
    if (target === "users") fetchUsers(); // 👈 newly added
  });
});


  // Tabs switching inside services
  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      tabButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const tab = btn.getAttribute('data-tab');
      serviceContents.forEach(content => content.classList.add('hidden'));
      document.getElementById(`${tab}-tab`).classList.remove('hidden');

      // Load treks if trekking tab is clicked
      if (tab === 'trekking') {
        fetchTreks();
      }
    });
  });

  // Modal open/close/save
  function openAddTrekModal() {
    isEditing = false;
    currentEditEl = null;
    trekInput.value = '';
    modalTitle.textContent = 'Add Trek';
    modal.classList.remove('hidden');
  }

  function closeTrekModal() {
    modal.classList.add('hidden');
  }

  async function saveTrek() {
  // Get form values
  const trekData = {
    name: document.getElementById("trekNameInput").value.trim(),
    duration: document.getElementById("trekDurationInput").value.trim(),
    difficulty: document.getElementById("trekDifficultyInput").value,
    region: document.getElementById("trekRegionInput").value.trim(),
    altitude: document.getElementById("trekAltitudeInput").value.trim(),
    price: document.getElementById("trekPriceInput").value.trim(),
    description: document.getElementById("trekDescriptionInput").value.trim(),
    image: document.getElementById("trekImageInput").value.trim(),
    service_id: 1 // Default for trekking
  };

  // Validate required fields
  for (const [key, value] of Object.entries(trekData)) {
    if (!value && key !== 'service_id') {
      alert(`Please fill in the ${key.replace(/([A-Z])/g, ' $1').toLowerCase()} field`);
      return;
    }
  }

  // Validate numbers - FIXED: Added missing parenthesis
  if (isNaN(trekData.altitude)) {
    alert("Altitude must be a number");
    return;
  }
  if (isNaN(trekData.price)) {
    alert("Price must be a number");
    return;
  }

  // Validate URL
  try {
    new URL(trekData.image);
  } catch (e) {
    alert("Please enter a valid image URL");
    return;
  }

  // Convert numeric fields
  trekData.altitude = parseFloat(trekData.altitude);
  trekData.price = parseFloat(trekData.price);

  try {
    const response = await fetch("http://localhost/FYP/add_trek.php", {
      method: "POST",
      headers: { 
        "Content-Type": "application/json",
      },
      body: JSON.stringify(trekData)
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || "Failed to add trek");
    }

    if (data.success) {
      alert("Trek added successfully!");
      closeTrekModal();
      fetchTreks(); // Refresh the list
    } else {
      throw new Error(data.error || "Unknown error occurred");
    }
  } catch (error) {
    console.error("Error:", error);
    alert(`Error: ${error.message}`);
  }
}


  function editTrek(btn) {
    const item = btn.closest('.trek-item');
    const name = item.querySelector('strong').textContent;
    trekInput.value = name;
    isEditing = true;
    currentEditEl = item;
    modalTitle.textContent = 'Edit Trek';
    modal.classList.remove('hidden');
  }

  function deleteTrek(btn) {
    if (confirm('Are you sure you want to delete this trek?')) {
      const item = btn.closest('.trek-item');
      item.remove();
    }
  }

  // Fetch trek list from backend
  function fetchTreks() {
    fetch("http://localhost/FYP/frontend/get_treks.php") // Adjust path if needed
      .then((res) => res.json())
      .then((data) => {
        trekList.innerHTML = "";
        data.forEach(trek => {
          const div = document.createElement("div");
          div.className = "trek-item";
          div.innerHTML = `
            <div>
              <strong>${trek.name}</strong>
              <span style="margin-left:10px; color:gray;">${trek.duration} Days | ${trek.difficulty}</span>
            </div>
            <div class="actions">
              <button onclick="editTrek(this)">Edit</button>
              <button class="delete" onclick="deleteTrek(this)">Delete</button>
            </div>
          `;
          trekList.appendChild(div);
        });
      })
      .catch(err => {
        trekList.innerHTML = "<p>Error loading treks.</p>";
        console.error("Trek fetch error:", err);
      });
  }

/***********************user fetching********************** */

function fetchUsers() {
  // Check if we're in the users section
  const usersSection = document.getElementById('users');
  if (!usersSection) {
    console.error("users section not found");
    return;
  }
  
  // Skip trying to find user-list and directly set up tabs and load clients
  setupUserTabs();
  
  // Find the active tab or default to clients
  const activeTab = usersSection.querySelector('.tab-btn.active');
  const tabToLoad = activeTab ? activeTab.getAttribute('data-tab') : 'clients';
  
  // Load the appropriate tab content
  if (tabToLoad === 'clients') {
    loadClients();
  } else {
    loadGuides();
  }
}


// User tab functionality
function setupUserTabs() {
  const tabBtns = document.querySelectorAll('#users .tab-btn');
  
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // Remove active class from all buttons and hide all content
      tabBtns.forEach(b => b.classList.remove('active'));
      document.querySelectorAll('#users .service-content').forEach(content => {
        content.classList.add('hidden');
      });
      
      // Add active class to clicked button and show corresponding content
      btn.classList.add('active');
      const tabName = btn.getAttribute('data-tab');
      document.getElementById(`${tabName}-tab`).classList.remove('hidden');
      
      // Load data for the selected tab
      if (tabName === 'clients') {
        loadClients();
      } else if (tabName === 'guides') {
        loadGuides();
      } else if (tabName === 'guide-approval') {
        loadGuideApprovals();
      }
    });
  });
}

function loadGuideApprovals() {
  const container = document.getElementById('guide-approval-list');
  if (!container) {
    console.error('guide-approval-list element not found');
    return;
  }

  container.innerHTML = '<div class="loading">Loading guide approval requests...</div>';

  fetch('http://localhost/FYPC/admin/get_pending_guides.php')
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      console.log('Received guide approval data:', data);
      container.innerHTML = '';
      
      if (!data || data.length === 0) {
        container.innerHTML = '<div class="empty-state">No pending guide approvals</div>';
        return;
      }
      
      // Create guide approval list
      const approvalList = document.createElement('div');
      approvalList.className = 'approval-list';
      
      data.forEach(guide => {
        // Format registration date
        const regDate = new Date(guide.created_at);
        const formattedDate = regDate.toLocaleDateString('en-US', {
          day: 'numeric',
          month: 'short',
          year: 'numeric'
        });
        
        // Generate initials for fallback avatar
        const nameParts = guide.name ? guide.name.split(' ') : ['?'];
        const initials = nameParts.length > 1 
          ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
          : nameParts[0][0].toUpperCase();
        
        // Check if guide has an avatar_path
        const hasAvatar = guide.avatar_path && guide.avatar_path.trim() !== '';
        
        // Determine avatar HTML based on path type
        let avatarHtml = `<div class="avatar-fallback">${initials}</div>`;
        if (hasAvatar) {
          if (guide.avatar_path.includes('http')) {
            // External URL
            avatarHtml = `<img src="${guide.avatar_path}" alt="${guide.name}" onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\\'avatar-fallback\\'>${initials}</div>';">`;
          } else {
            // Local path
            avatarHtml = `<img src="http://localhost/FYPC/${guide.avatar_path}" alt="${guide.name}" onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\\'avatar-fallback\\'>${initials}</div>';">`;
          }
        }
        
        // Create guide approval card
        const card = document.createElement('div');
        card.className = 'approval-item';
        card.dataset.guideId = guide.id;
        card.innerHTML = `
          <div class="approval-avatar">
            ${avatarHtml}
          </div>
          <div class="approval-info">
            <h3>${guide.name || 'Unknown'}</h3>
            <div class="approval-meta">
              <span class="approval-date">${formattedDate}</span>
              <span class="approval-rating">
                <i class="fas fa-star"></i>
                <span>New</span>
              </span>
            </div>
            <p class="approval-intro">${guide.bio || 'No introduction provided.'}</p>
          </div>
          <div class="approval-actions">
            <button class="btn-view" onclick="viewGuideDetails(${guide.id})">View</button>
          </div>
        `;
        approvalList.appendChild(card);
      });
      
      container.appendChild(approvalList);
    })
    .catch(err => {
      console.error('Fetch error:', err);
      if (container) {
        container.innerHTML = `<div class="error">Error loading guide approvals: ${err.message}</div>`;
      }
    });
}

function viewGuideDetails(guideId) {
  // Fetch detailed guide information
  fetch(`http://localhost/FYPC/admin/get_guide_details.php?id=${guideId}`)
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(guide => {
      console.log("Guide details:", guide); // Add this for debugging
      
      // Create and show modal with guide details
      const modal = document.createElement('div');
      modal.className = 'guide-details-modal';
      
      // Generate initials for fallback avatar
      const nameParts = guide.name ? guide.name.split(' ') : ['?'];
      const initials = nameParts.length > 1 
        ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
        : nameParts[0][0].toUpperCase();
      
      // In the viewGuideDetails function, update the avatar handling:
      let avatarHtml = `<div class="avatar-fallback">${initials}</div>`;
      if (guide.avatar_path && guide.avatar_path.trim() !== '') {
        // If it's an external URL (contains http)
        if (guide.avatar_path.includes('http')) {
          // Skip Facebook URLs as they will cause 403 errors
          if (guide.avatar_path.includes('facebook.com') || guide.avatar_path.includes('fbcdn.net')) {
            avatarHtml = `<div class="avatar-fallback">${initials}</div>`;
          } else {
            avatarHtml = `
              <img src="${guide.avatar_path}" alt="${guide.name}" 
                   onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\\'avatar-fallback\\'>${initials}</div>';">
            `;
          }
        } else {
          // Local path
          avatarHtml = `
            <img src="http://localhost/FYPC/${guide.avatar_path}" alt="${guide.name}" 
                 onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\\'avatar-fallback\\'>${initials}</div>';">
          `;
        }
      }
      
      // Format worked with section
      let workedWithHtml = '<p>No information provided</p>';
      if (guide.worked_with && Array.isArray(guide.worked_with) && guide.worked_with.length > 0) {
        workedWithHtml = `
          <ul class="worked-with-list">
            ${guide.worked_with.map(org => `<li>${org}</li>`).join('')}
          </ul>
        `;
      } else if (guide.worked_with && typeof guide.worked_with === 'string' && guide.worked_with.trim() !== '') {
        workedWithHtml = `<p>${guide.worked_with}</p>`;
      }
      
      // Format certifications section
      let certificationsHtml = '<p>No certifications provided</p>';
      if (guide.certifications) {
        if (Array.isArray(guide.certifications) && guide.certifications.length > 0) {
          certificationsHtml = `
            <ul class="certification-list">
              ${guide.certifications.map(cert => `<li>${cert}</li>`).join('')}
            </ul>
          `;
        } else if (typeof guide.certifications === 'string' && guide.certifications.trim() !== '') {
          certificationsHtml = `<p>${guide.certifications}</p>`;
        }
      }
      
      // Format documents section with view option
      let documentsHtml = '<p>No documents provided</p>';
      if (guide.documents && guide.documents.length > 0) {
        documentsHtml = `
          <div class="document-grid">
            ${guide.documents.map(doc => {
              // Ensure path is properly formatted
              const docPath = doc.path.startsWith('http') ? doc.path : `http://localhost/FYPC/${doc.path}`;
              const docName = doc.name || 'Document';
              const isImage = doc.type && doc.type.includes('image') || 
                             docPath.match(/\.(jpg|jpeg|png|gif)$/i);
              
              return `
              <div class="document-item">
                <div class="document-preview">
                  ${isImage 
                    ? `<img src="${docPath}" alt="${docName}" onerror="this.onerror=null; this.src='http://localhost/FYPC/admin/assets/file-icon.png';">`
                    : `<i class="fas fa-file-alt"></i>`
                  }
                </div>
                <div class="document-info">
                  <span class="document-name">${docName}</span>
                  <a href="${docPath}" class="document-view" target="_blank">View</a>
                </div>
              </div>
              `;
            }).join('')}
          </div>
        `;
      }
      
      modal.innerHTML = `
        <div class="modal-content">
          <div class="modal-header">
            <h2>Guide Registration Details</h2>
            <button class="close-modal" onclick="this.closest('.guide-details-modal').remove()">×</button>
          </div>
          <div class="modal-body">
            <div class="guide-profile">
              <div class="profile-header">
                <div class="profile-avatar">
                  ${avatarHtml}
                </div>
                <div class="profile-info">
                  <h3>${guide.name}</h3>
                  <p class="profile-meta">${guide.email} | ${guide.phone || 'No phone'}</p>
                  <p class="profile-title">${guide.title || 'Guide'}</p>
                </div>
              </div>
              
              <div class="profile-details">
                <div class="detail-section">
                  <h4>Personal Information</h4>
                  <div class="detail-grid">
                    <div class="detail-item">
                      <span class="detail-label">Full Name:</span>
                      <span class="detail-value">${guide.name}</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Email:</span>
                      <span class="detail-value">${guide.email}</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Phone:</span>
                      <span class="detail-value">${guide.phone || 'Not provided'}</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Title:</span>
                      <span class="detail-value">${guide.title || 'Not specified'}</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Experience:</span>
                      <span class="detail-value">${guide.experience || 'Not specified'} years</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Specialization:</span>
                      <span class="detail-value">${guide.specialization || 'Not specified'}</span>
                    </div>
                    <div class="detail-item">
                      <span class="detail-label">Languages:</span>
                      <span class="detail-value">${guide.languages || 'Not specified'}</span>
                    </div>
                  </div>
                </div>
                
                <div class="detail-section">
                  <h4>Bio</h4>
                  <p>${guide.bio || 'No bio provided'}</p>
                </div>
                
                <div class="detail-section">
                  <h4>Worked With</h4>
                  ${workedWithHtml}
                </div>
                
                <div class="detail-section">
                  <h4>Certifications</h4>
                  ${certificationsHtml}
                </div>
                
                <div class="detail-section">
                  <h4>Documents & Certificates</h4>
                  ${documentsHtml}
                </div>
                
                ${guide.achievements ? `
                <div class="detail-section">
                  <h4>Achievements</h4>
                  <p>${typeof guide.achievements === 'string' ? guide.achievements : JSON.stringify(guide.achievements)}</p>
                </div>
                ` : ''}
                
                ${guide.stats ? `
                <div class="detail-section">
                  <h4>Statistics</h4>
                  <p>${typeof guide.stats === 'string' ? guide.stats : JSON.stringify(guide.stats)}</p>
                </div>
                ` : ''}
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-deny" onclick="denyGuide(${guide.id})">Deny Registration</button>
            <button class="btn-approve" onclick="approveGuide(${guide.id})">Approve Registration</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
    })
    .catch(err => {
      console.error('Error fetching guide details:', err);
      alert(`Error loading guide details: ${err.message}`);
    });
}

function approveGuide(guideId) {
  if (!confirm('Are you sure you want to approve this guide?')) {
    return;
  }
  
  const formData = new FormData();
  formData.append('guide_id', guideId);
  formData.append('action', 'approve');
  formData.append('send_email', 'true'); // Add this parameter to trigger email sending
  
  console.log('Sending approval request for guide ID:', guideId);
  
  // Make sure this URL is correct
  fetch('http://localhost/FYPC/admin/update_guide_status.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {
        console.error('Server response:', text);
        throw new Error(`HTTP error! status: ${response.status}`);
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert('Guide approved successfully!' + (data.email_sent ? ' An email notification has been sent to the guide.' : ' (Email notification could not be sent)'));
      // Close modal if open
      const modal = document.querySelector('.guide-details-modal');
      if (modal) modal.remove();
      // Refresh the approvals list
      loadGuideApprovals();
    } else {
      throw new Error(data.message || 'Failed to approve guide');
    }
  })
  .catch(err => {
    console.error('Error:', err);
    alert(`Error approving guide: ${err.message}`);
  });
}

// Similarly update the denyGuide function:
function denyGuide(guideId) {
  if (!confirm('Are you sure you want to deny this guide registration?')) {
    return;
  }
  
  const formData = new FormData();
  formData.append('guide_id', guideId);
  formData.append('action', 'deny');
  
  fetch('http://localhost/FYPC/admin/update_guide_status.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {
        console.error('Server response:', text);
        throw new Error(`HTTP error! status: ${response.status}`);
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert('Guide registration denied!');
      // Close modal if open
      const modal = document.querySelector('.guide-details-modal');
      if (modal) modal.remove();
      // Refresh the approvals list
      loadGuideApprovals();
    } else {
      throw new Error(data.message || 'Failed to deny guide registration');
    }
  })
  .catch(err => {
    console.error('Error:', err);
    alert(`Error denying guide registration: ${err.message}`);
  });
}

// Make sure your fetch URLs are correct
function loadClients() {
  const container = document.getElementById('client-list');
  if (!container) {
    console.error('client-list element not found');
    return;
  }

  container.innerHTML = '<div class="loading">Loading clients...</div>';

  fetch('http://localhost/FYPC/admin/get_users.php?role=client')
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      console.log('Received data:', data);
      container.innerHTML = '';
      
      if (!data || data.length === 0) {
        container.innerHTML = '<div class="empty-state">No clients found</div>';
        return;
      }
      
      // Create client grid
      const clientGrid = document.createElement('div');
      clientGrid.className = 'user-grid';
      
      data.forEach(client => {
        // Generate initials for fallback avatar
        const nameParts = client.name ? client.name.split(' ') : ['?'];
        const initials = nameParts.length > 1 
          ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
          : nameParts[0][0].toUpperCase();
        
        // Check if client has an avatar_path
        const hasAvatar = client.avatar_path && client.avatar_path.trim() !== '' && 
                         !client.avatar_path.includes('facebook.com') && 
                         !client.avatar_path.includes('fbcdn.net');
        
        // Fix the avatar path - check if it already contains the uploads/avatars path
        const avatarPath = hasAvatar 
          ? (client.avatar_path.includes('uploads/avatars') 
             ? `http://localhost/FYPC/${client.avatar_path}` 
             : `http://localhost/FYPC/uploads/avatars/${client.avatar_path}`)
          : '';
        
        // Get random color class for avatar background
        const colorClass = getRandomColorClass();
        
        // Create client card
        const card = document.createElement('div');
        card.className = 'user-card';
        // Fix: Use client.id instead of client.client_id
        card.dataset.userId = client.id;
        card.innerHTML = `
          <div class="user-card-inner">
            <div class="user-avatar ${hasAvatar ? '' : colorClass}">
              ${hasAvatar 
                ? `<img src="${avatarPath}" alt="${client.name}" onerror="this.style.display='none'; this.parentNode.classList.add('${colorClass}'); this.parentNode.innerHTML='${initials}';">`
                : initials
              }
            </div>
            <div class="user-info">
              <h3 class="user-name">${client.name || 'Unknown'}</h3>
              <p class="user-email">${client.email || 'No email'}</p>
              <p class="user-role">Client</p>
            </div>
            <div class="user-actions">
              <button class="view-btn">VIEW</button>
              <!-- Fix: Use client.id instead of client.client_id -->
              <button class="delete-btn" onclick="deleteUser(${client.id}, 'client')">DELETE</button>
            </div>
          </div>
        `;
        clientGrid.appendChild(card);
      });
      
      container.appendChild(clientGrid);
    })
    .catch(err => {
      console.error('Fetch error:', err);
      if (container) {
        container.innerHTML = `<div class="error">Error loading clients: ${err.message}</div>`;
      }
    });
}

// Add this function to handle user deletion
// Update the deleteUser function to use client_id instead of id
function deleteUser(userId, role) {
    if (!confirm(`Are you sure you want to delete this ${role}?`)) {
      return;
    }
    
    console.log(`Deleting ${role} with ID: ${userId}`);
    
    const formData = new FormData();
    formData.append('client_id', userId);
    formData.append('role', role);
    
    fetch('http://localhost/FYPC/admin/delete_user.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) {
        return response.text().then(text => {
          console.error('Server response:', text);
          throw new Error(`HTTP error! status: ${response.status}`);
        });
      }
      return response.json();
    })
    .then(data => {
      console.log('Delete response:', data);
      if (data.success) {
        // Remove the card from the UI
        const card = document.querySelector(`.user-card[data-user-id="${userId}"]`);
        if (card) {
          card.remove();
        } else {
          console.warn(`Card with data-user-id="${userId}" not found`);
        }
        alert(`${role.charAt(0).toUpperCase() + role.slice(1)} deleted successfully!`);
        
        // Refresh the list
        if (role === 'client') {
          loadClients();
        } else {
          loadGuides();
        }
      } else {
        throw new Error(data.message || 'Unknown error occurred');
      }
    })
    .catch(err => {
      console.error('Delete error:', err);
      alert(`Error deleting ${role}: ${err.message}`);
    });
}

// Add this helper function if it doesn't exist
function getRandomColorClass() {
  const colors = ['avatar-blue', 'avatar-purple', 'avatar-green', 'avatar-orange', 'avatar-pink'];
  return colors[Math.floor(Math.random() * colors.length)];
}

function loadGuides() {
  const container = document.getElementById('guide-list');
  if (!container) {
    console.error('guide-list element not found');
    return;
  }

  container.innerHTML = '<div class="loading">Loading guides...</div>';

  fetch('http://localhost/FYPC/admin/get_users.php?role=guide')
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      console.log('Received guides data:', data);
      container.innerHTML = '';
      
      if (!data || data.length === 0) {
        container.innerHTML = '<div class="empty-state">No guides found</div>';
        return;
      }
      
      // Create guide grid
      const guideGrid = document.createElement('div');
      guideGrid.className = 'user-grid';
      
      data.forEach(guide => {
        // Generate initials for fallback avatar
        const nameParts = guide.name ? guide.name.split(' ') : ['?'];
        const initials = nameParts.length > 1 
          ? (nameParts[0][0] + nameParts[1][0]).toUpperCase()
          : nameParts[0][0].toUpperCase();
        
        // Check if guide has an avatar_path
        const hasAvatar = guide.avatar_path && guide.avatar_path.trim() !== '';
        
        // Fix the avatar path - check if it already contains the uploads/guides path
        const avatarPath = hasAvatar 
          ? (guide.avatar_path.includes('uploads/guides') 
             ? `http://localhost/FYPC/${guide.avatar_path}` 
             : `http://localhost/FYPC/uploads/guides/${guide.avatar_path}`)
          : '';
        
        // Get random color class for avatar background
        const colorClass = getRandomColorClass();
        
        // Create guide card
        const card = document.createElement('div');
        card.className = 'user-card';
        card.dataset.userId = guide.id;
        card.innerHTML = `
          <div class="user-card-inner">
            <div class="user-avatar ${hasAvatar ? '' : colorClass}">
              ${hasAvatar 
                ? `<img src="${avatarPath}" alt="${guide.name}" onerror="this.style.display='none'; this.parentNode.classList.add('${colorClass}'); this.parentNode.innerHTML='${initials}';">`
                : initials
              }
            </div>
            <div class="user-info">
              <h3 class="user-name">${guide.name || 'Unknown'}</h3>
              <p class="user-email">${guide.email || 'No email'}</p>
              <p class="user-role">Guide${guide.experience ? ` • ${guide.experience} years` : ''}</p>
              ${guide.specialization ? `<p class="user-specialization">${guide.specialization}</p>` : ''}
            </div>
            <div class="user-actions">
              <button class="view-btn">VIEW</button>
              <button class="delete-btn" onclick="deleteUser(${guide.id}, 'guide')">DELETE</button>
            </div>
          </div>
        `;
        guideGrid.appendChild(card);
      });
      
      container.appendChild(guideGrid);
    })
    .catch(err => {
      console.error('Fetch error:', err);
      if (container) {
        container.innerHTML = `<div class="error">Error loading guides: ${err.message}</div>`;
      }
    });
}

// Helper function to get random color class for avatars
function getRandomColorClass() {
  const colors = ['avatar-blue', 'avatar-purple', 'avatar-green', 'avatar-orange', 'avatar-pink'];
  return colors[Math.floor(Math.random() * colors.length)];
}

// Call this in your DOMContentLoaded event
document.addEventListener('DOMContentLoaded', () => {
  setupUserTabs();
  loadClients(); // Load clients by default
});



