<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bookings - Vendor Requests | INTZI</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0; padding: 0;
      background: #f7f8fc;
      font-family: 'Poppins', Arial, sans-serif;
      color: #222;
    }
    .navbar {
      background: #5f64ec;
      display: flex;
      align-items: center;
      padding: 0 48px;
      height: 60px;
      font-family: 'Poppins', sans-serif;
      user-select: none;
      box-shadow: 0 4px 12px rgba(95, 100, 236, 0.18);
      position: sticky; top: 0; z-index: 100;
    }
    .navbar .brand {
      color: #fff; font-weight: 700; font-size: 1.8rem;
      letter-spacing: 2.5px; flex-shrink: 0; text-transform: uppercase;
    }
    .navbar ul {
      list-style: none; display: flex;
      flex-grow: 1; justify-content: space-between;
      margin: 0 0 0 48px; padding: 0;
    }
    .navbar ul li {
      flex: 1; text-align: center;
    }
    .navbar ul li a {
      color: #c3c9f9; text-decoration: none;
      font-weight: 600; font-size: 1rem;
      padding: 18px 0; display: block;
      border-radius: 6px 6px 0 0;
      transition: background-color 0.3s, color 0.3s;
    }
    .navbar ul li a:hover {
      background-color: #3e47d3; color: #eef2ff;
    }
    .navbar ul li a.active {
      background-color: #eef2ff; color: #5f64ec; font-weight: 700;
    }
    .main-container {
      max-width: 1050px;
      margin: 48px auto 50px auto;
      background: #fff;
      box-shadow: 0 8px 24px rgba(95, 100, 236, 0.15);
      border-radius: 24px;
      padding: 35px 28px 45px 28px;
      min-height: 600px;
    }
    .users-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }
    @media (max-width: 900px) {
      .users-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .main-container {
        padding: 20px 5vw;
      }
    }
    @media (max-width: 700px) {
      .users-grid {
        grid-template-columns: 1fr;
      }
    }

    .user-card {
      background: #eef2ff;
      color: #3637a5;
      border-radius: 14px;
      box-shadow: 0 6px 18px rgba(95, 100, 236, 0.12);
      padding: 28px 22px 26px 22px;
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      position: relative;
      transition: box-shadow 0.24s, transform 0.20s;
    }
    .user-card:hover {
      box-shadow: 0 12px 36px rgba(95, 100, 236, 0.25);
      transform: translateY(-4px) scale(1.02);
    }
    .user-avatar {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 12px;
      border: 3px solid #5f64ec;
    }
    .user-name {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 5px;
      letter-spacing: 0.015em;
    }
    .user-rating {
      color: #ffd878;
      font-size: 1.15rem;
      margin-bottom: 5px;
    }
    .user-address {
      font-size: 0.95em;
      color: #5f64ec;
      background: #d9defd;
      padding: 5px 14px;
      border-radius: 8px;
      margin-bottom: 10px;
      font-weight: 600;
      text-align: center;
      max-width: 220px;
    }
    .user-slot {
      font-size: 1rem;
      color: #484b89;
      margin-bottom: 6px;
    }
    .user-price {
      font-size: 1.1rem;
      color: #5f64ec;
      font-weight: 700;
      margin-bottom: 10px;
    }
    .user-actions {
      display: flex;
      gap: 14px;
    }
    .btn {
      padding: 9px 17px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-accept {
      background: #18c799;
      color: #fff;
    }
    .btn-accept:hover {
      background: #129a70;
    }
    .btn-reject {
      background: #ff6b81;
      color: #fff;
    }
    .btn-reject:hover {
      background: #d94d5f;
    }
    .btn-confirm {
      background: #5f64ec;
      color: #fff;
      font-weight: 700;
    }
    .btn-confirm:hover {
      background: #3637a5;
    }

    #profileBg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(95, 100, 236, 0.85);
      z-index: 120;
      display: none;
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease forwards;
    }
    #profileBg.active {
      display: flex;
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .profile-container {
      max-width: 740px;
      width: 95vw;
      min-height: 600px;
      background: #eef2ff;
      box-shadow: 0 18px 72px rgba(95, 100, 236, 0.3);
      border-radius: 40px;
      padding: 60px 48px 54px 48px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .back-arrow {
      position: fixed;
      top: 40px;
      left: 44px;
      font-size: 2.8rem;
      font-weight: 900;
      color: #3637a5;
      cursor: pointer;
      z-index: 130;
      opacity: 1;
      transition: color 0.2s;
    }
    .back-arrow:hover {
      color: #2a2d7a;
    }

    .profile-avatar {
      width: 165px;
      height: 165px;
      border-radius: 50%;
      background: #d9defd;
      border: 6px solid #5f64ec;
      object-fit: cover;
      margin-bottom: 12px;
      margin-top: 15px;
    }
    .profile-title {
      font-weight: 700;
      font-size: 1.9rem;
      letter-spacing: 0.02em;
      margin-bottom: 8px;
      color: #3637a5;
      text-align: center;
    }
    .profile-rating {
      color: #ffd878;
      font-size: 1.3rem;
      margin-bottom: 12px;
    }
    .profile-section {
      width: 100%;
      margin-bottom: 18px;
      font-size: 1.15em;
      color: #3637a5;
      text-align: center;
    }
    .profile-label {
      font-weight: 700;
    }
    .profile-price {
      font-size: 1.2em;
      color: #18c799;
      font-weight: 700;
      margin-bottom: 14px;
    }
    #chatSection {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 3px 16px rgba(136, 141, 255, 0.18);
      margin-top: 16px;
      padding: 16px 20px 18px 20px;
      width: 100%;
      max-width: 95vw;
    }
    #chatTitle {
      font-weight: 700;
      font-size: 1.15em;
      color: #5f64ec;
      margin-bottom: 10px;
    }
    #chatBox {
      background: #f0f2ff;
      border: 1.3px solid #d1d7fe;
      min-height: 80px;
      height: 120px;
      max-height: 140px;
      overflow-y: auto;
      padding: 10px 14px 10px 16px;
      font-size: 1em;
      margin-bottom: 10px;
      border-radius: 9px;
    }
    .msg {
      margin-bottom: 8px;
    }
    .msg.vendor {
      color: #fff;
      background: #5f64ec;
      padding: 7px 16px;
      border-radius: 10px 14px 12px 8px;
      max-width: 80%;
    }
    .msg.user {
      color: #5f64ec;
      background: #e2e6fc;
      padding: 7px 16px;
      border-radius: 14px 8px 10px 15px;
      max-width: 80%;
    }
    .chatControls {
      display: flex;
      gap: 10px;
    }
    #chatInput {
      flex: 1;
      font-family: inherit;
      border-radius: 8px;
      padding: 9px 12px;
      border: 1.5px solid #d1d7fe;
      font-size: 1rem;
    }
    #chatSend {
      background: #5f64ec;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 9px 20px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.25s ease;
    }
    #chatSend:hover {
      background: #3637a5;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="brand">INTZI</div>
    <ul>
      <li><a href="vendorhomepage.php">Dashboard</a></li>
      <li><a href="vendorservices.php">My Services</a></li>
      <li><a href="vendorbookings.php">Bookings</a></li>
      <li><a href="vendorearnings.php">Earnings</a></li>
      <li><a href="vendorreviews.php">Reviews</a></li>
      <li><a href="vendorprofile.php" class="active">Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main-container">
    <div id="requestsView">
      <div class="users-grid" id="usersGrid"></div>
    </div>
  </div>

  <div id="profileBg" aria-hidden="true">
    <span class="back-arrow" onclick="closeProfile()" title="Back to Bookings">&#8592;</span>
    <div id="profileView"></div>
  </div>

  <script>
    const requests = [
      {
        name: "Priya S.",
        avatar: "https://randomuser.me/api/portraits/women/57.jpg",
        address: "C-302, Lotus Residency, Hyderabad",
        rating: 5,
        slot: "2025-09-14 • 4:00 pm - 6:00 pm",
        price: 650
      },
      {
        name: "Mohan K.",
        avatar: "https://randomuser.me/api/portraits/men/43.jpg",
        address: "12, Sunrise Lane, HIG 17, Hyderabad",
        rating: 4,
        slot: "2025-09-15 • 10:00 am - 12:00 pm",
        price: 480
      },
      {
        name: "Ayesha F.",
        avatar: "https://randomuser.me/api/portraits/women/90.jpg",
        address: "21, Rose Apartments, Bandar, Hyderabad",
        rating: 5,
        slot: "2025-09-15 • 2:00 pm - 4:00 pm",
        price: 850
      },
      {
        name: "Seema S.",
        avatar: "https://randomuser.me/api/portraits/women/45.jpg",
        address: "5, Lake View Road, Hyderabad",
        rating: 4,
        slot: "2025-09-17 • 12:00 pm - 2:00 pm",
        price: 540
      },
      {
        name: "Amit D.",
        avatar: "https://randomuser.me/api/portraits/men/60.jpg",
        address: "35, Blossom Heights, Hyderabad",
        rating: 5,
        slot: "2025-09-18 • 4:00 pm - 6:00 pm",
        price: 900
      }
    ];

    const usersGrid = document.getElementById('usersGrid');
    const profileBg = document.getElementById('profileBg');
    const profileView = document.getElementById('profileView');

    function renderRequests(list = requests) {
      usersGrid.innerHTML = '';
      list.forEach((req, idx) => {
        const stars = "★".repeat(req.rating) + "☆".repeat(5 - req.rating);
        usersGrid.innerHTML += `
          <div class="user-card" onclick="openProfile(event,${idx})" id="user-card-${idx}" role="button" tabindex="0" aria-label="Booking request from ${req.name}">
            <img class="user-avatar" src="${req.avatar}" alt="Avatar of ${req.name}">
            <div class="user-name">${req.name}</div>
            <div class="user-rating" aria-label="User rating: ${req.rating} out of 5">${stars}</div>
            <div class="user-address">${req.address}</div>
            <div class="user-slot">${req.slot}</div>
            <div class="user-price">₹${req.price}</div>
            <div class="user-actions" id="actions-${idx}">
              <button class="btn btn-accept" onclick="acceptRequest(event,${idx})" aria-label="Accept booking from ${req.name}">Accept</button>
              <button class="btn btn-reject" onclick="rejectRequest(event,${idx})" aria-label="Reject booking from ${req.name}">Reject</button>
            </div>
          </div>`;
      });
    }
    renderRequests();

    function acceptRequest(event, idx) {
      event.stopPropagation();
      document.getElementById(`actions-${idx}`).innerHTML = `<button class="btn btn-confirm" onclick="confirmOrder(event,${idx})" aria-label="Confirm order from ${requests[idx].name}">Confirm Order</button>`;
    }
    function rejectRequest(event, idx) {
      event.stopPropagation();
      document.getElementById(`user-card-${idx}`).style.display = 'none';
      alert(`You rejected ${requests[idx].name}'s booking.`);
    }
    function confirmOrder(event, idx) {
      event.stopPropagation();
      document.getElementById(`user-card-${idx}`).style.display = 'none';
      alert(`✅ Order confirmed for ${requests[idx].name}`);
    }

    function openProfile(event, idx) {
      if (event.target.closest('button')) return;
      const req = requests[idx];
      const stars = "★".repeat(req.rating) + "☆".repeat(5 - req.rating);
      profileBg.classList.add('active');
      profileBg.setAttribute('aria-hidden', 'false');
      profileView.innerHTML = `
        <div class="profile-container" role="dialog" aria-modal="true" aria-label="Booking details for ${req.name}">
          <img class="profile-avatar" src="${req.avatar}" alt="Avatar of ${req.name}">
          <div class="profile-title">${req.name}</div>
          <div class="profile-rating" aria-label="User rating: ${req.rating} out of 5">${stars}</div>
          <div class="profile-section"><span class="profile-label">Address:</span> ${req.address}</div>
          <div class="profile-section"><span class="profile-label">Slot:</span> ${req.slot}</div>
          <div class="profile-section profile-price"><span class="profile-label">Price:</span> ₹${req.price}</div>
          <div id="chatSection">
            <div id="chatTitle">Chat with Customer</div>
            <div id="chatBox" tabindex="0" aria-live="polite" aria-relevant="additions"></div>
            <form class="chatControls" id="chatForm" onsubmit="return sendMsgVendor();">
              <input id="chatInput" type="text" placeholder="Type message..." autocomplete="off" aria-label="Type message to customer" />
              <button id="chatSend" type="submit" aria-label="Send message">Send</button>
            </form>
          </div>
        </div>`;
      attachChatLogic();
    }
    function closeProfile() {
      profileBg.classList.remove('active');
      profileBg.setAttribute('aria-hidden', 'true');
      profileView.innerHTML = '';
    }
    function attachChatLogic() {
      const chatForm = document.getElementById('chatForm');
      chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        if (!message) return;
        const chatBox = document.getElementById('chatBox');
        chatBox.innerHTML += `<div class="msg vendor">${message}</div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;
        setTimeout(() => {
          chatBox.innerHTML += `<div class="msg user">Customer reply: I'll get back to you soon!</div>`;
          chatBox.scrollTop = chatBox.scrollHeight;
        }, 800);
      });
    }
  </script>
</body>
</html>