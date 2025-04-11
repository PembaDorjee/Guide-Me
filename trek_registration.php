<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trek Registration - Guide Me</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #15d455;
      --secondary: #f3a42e;
      --dark: #1E2A38;
      --light: #f5f6f8;
      --text: #333;
      --text-light: #777;
    }
    
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--light);
      color: var(--text);
      line-height: 1.6;
    }

    header {
      background-color: var(--dark);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
    }

    .logo i {
      margin-right: 10px;
      font-size: 1.5rem;
    }

    .logo span {
      color: var(--secondary);
    }

    .container {
      max-width: 800px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    .form-header {
      background: var(--dark);
      color: white;
      padding: 2rem;
      text-align: center;
    }

    .form-header h1 {
      margin: 0;
      font-size: 2rem;
    }

    .form-header p {
      margin-top: 0.5rem;
      opacity: 0.8;
    }

    .form-body {
      padding: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--dark);
    }

    input, select, textarea {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 1rem;
      font-family: inherit;
    }

    textarea {
      min-height: 100px;
      resize: vertical;
    }

    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .form-col {
      flex: 1;
    }

    .checkbox-group {
      margin-top: 0.5rem;
    }

    .checkbox-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .checkbox-item input {
      width: auto;
      margin-right: 0.5rem;
    }

    .section-title {
      font-size: 1.3rem;
      color: var(--dark);
      margin: 2rem 0 1rem;
      position: relative;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #eee;
    }

    .btn {
      padding: 0.8rem 1.5rem;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      cursor: pointer;
      border: none;
      font-size: 1rem;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: #13b74b;
    }

    .btn-secondary {
      background: var(--light);
      color: var(--dark);
    }

    .btn-secondary:hover {
      background: #e5e5e5;
    }

    .btn i {
      margin-right: 8px;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 2rem;
    }

    .required::after {
      content: '*';
      color: red;
      margin-left: 4px;
    }

    .form-note {
      background: #f8f9fa;
      padding: 1rem;
      border-radius: 6px;
      margin-top: 2rem;
      font-size: 0.9rem;
      color: var(--text-light);
    }

    footer {
      text-align: center;
      padding: 2rem;
      margin-top: 3rem;
      background-color: var(--dark);
      color: white;
    }

    @media (max-width: 768px) {
      .container {
        margin: 0;
        border-radius: 0;
      }
      
      .form-body {
        padding: 1.5rem;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <i class="fas fa-mountain"></i>
    <div>Guide <span>Me</span></div>
  </div>
  <div><a href="treks.php" style="color: white; text-decoration: none;"><i class="fas fa-arrow-left"></i> Back to Treks</a></div>
</header>

<div class="container">
  <div class="form-header">
    <h1>Trek Registration</h1>
    <p>Fill out the form below to register for your adventure</p>
  </div>
  
  <div class="form-body">
    <form id="trekRegistrationForm" action="process_registration.php" method="POST">
      <h2 class="section-title">Personal Information</h2>
      
      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label for="firstName" class="required">First Name</label>
            <input type="text" id="firstName" name="firstName" required>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label for="lastName" class="required">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>
          </div>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label for="email" class="required">Email Address</label>
            <input type="email" id="email" name="email" required>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label for="phone" class="required">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label for="nationality">Nationality</label>
        <input type="text" id="nationality" name="nationality">
      </div>
      
      <div class="form-group">
        <label for="passportNumber" class="required">Passport Number</label>
        <input type="text" id="passportNumber" name="passportNumber" required>
      </div>
      
      <h2 class="section-title">Trek Details</h2>
      
      <div class="form-group">
        <label for="trekName" class="required">Trek Name</label>
        <select id="trekName" name="trekName" required>
          <option value="">Select a Trek</option>
          <option value="Everest Base Camp">Everest Base Camp Trek</option>
          <option value="Annapurna Circuit">Annapurna Circuit Trek</option>
          <option value="Langtang Valley">Langtang Valley Trek</option>
          <option value="Manaslu Circuit">Manaslu Circuit Trek</option>
          <option value="Upper Mustang">Upper Mustang Trek</option>
        </select>
      </div>
      
      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label for="startDate" class="required">Preferred Start Date</label>
            <input type="date" id="startDate" name="startDate" required>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label for="groupSize" class="required">Number of Participants</label>
            <input type="number" id="groupSize" name="groupSize" min="1" max="20" required>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label>Additional Services</label>
        <div class="checkbox-group">
          <div class="checkbox-item">
            <input type="checkbox" id="guideService" name="services[]" value="guide">
            <label for="guideService">Professional Guide</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="porterService" name="services[]" value="porter">
            <label for="porterService">Porter Service</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="permitService" name="services[]" value="permits">
            <label for="permitService">Permit Arrangement</label>
          </div>
          <div class="checkbox-item">
            <input type="checkbox" id="equipmentService" name="services[]" value="equipment">
            <label for="equipmentService">Equipment Rental</label>
          </div>
        </div>
      </div>
      
      <h2 class="section-title">Health & Experience</h2>
      
      <div class="form-group">
        <label for="trekExperience">Previous Trekking Experience</label>
        <select id="trekExperience" name="trekExperience">
          <option value="none">None</option>
          <option value="beginner">Beginner (1-2 treks)</option>
          <option value="intermediate">Intermediate (3-5 treks)</option>
          <option value="experienced">Experienced (6+ treks)</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="fitnessLevel">Fitness Level</label>
        <select id="fitnessLevel" name="fitnessLevel">
          <option value="moderate">Moderate</option>
          <option value="good">Good</option>
          <option value="excellent">Excellent</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="medicalConditions">Medical Conditions or Allergies</label>
        <textarea id="medicalConditions" name="medicalConditions" placeholder="Please list any medical conditions, allergies, or dietary restrictions we should be aware of."></textarea>
      </div>
      
      <div class="form-group">
        <label for="specialRequests">Special Requests or Questions</label>
        <textarea id="specialRequests" name="specialRequests" placeholder="Any special requests or questions about the trek?"></textarea>
      </div>
      
      <div class="form-group">
        <div class="checkbox-item">
          <input type="checkbox" id="termsAgreement" name="termsAgreement" required>
          <label for="termsAgreement" class="required">I agree to the terms and conditions</label>
        </div>
      </div>
      
      <div class="form-note">
        <p><strong>Note:</strong> A 30% deposit is required to confirm your booking. Our team will contact you with payment details after reviewing your registration.</p>
      </div>
      
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
          <i class="fas fa-arrow-left"></i> Back
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-paper-plane"></i> Submit Registration
        </button>
      </div>
    </form>
  </div>
</div>

<footer>
  <p>&copy; 2025 Guide Me | All Rights Reserved</p>
  <div style="margin-top: 1rem;">
    <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-facebook-f"></i></a>
    <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-instagram"></i></a>
    <a href="#" style="color: white; margin: 0 10px;"><i class="fab fa-twitter"></i></a>
  </div>
</footer>

<script>
  // Form validation
  document.getElementById('trekRegistrationForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('startDate').value);
    const today = new Date();
    
    // Check if start date is at least 2 weeks from today
    const minDate = new Date();
    minDate.setDate(today.getDate() + 14);
    
    if (startDate < minDate) {
      e.preventDefault();
      alert('Please select a start date at least 2 weeks from today to allow for proper preparation.');
      return false;
    }
    
    // Additional validation could be added here
  });
  
  // Pre-fill trek name if coming from a specific trek page
  window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const trekParam = urlParams.get('trek');
    
    if (trekParam) {
      const trekSelect = document.getElementById('trekName');
      for (let i = 0; i < trekSelect.options.length; i++) {
        if (trekSelect.options[i].value.toLowerCase() === trekParam.toLowerCase()) {
          trekSelect.selectedIndex = i;
          break;
        }
      }
    }
  });
</script>

</body>
</html>