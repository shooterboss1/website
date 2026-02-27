<?php include 'includes/header.php'; ?>

<style>
/* Page-Specific Styles - Dark Premium Theme */
.coming-soon-page {
    background: #0a0a0a;
    color: #fff;
    min-height: 100vh;
    padding-bottom: 80px;
}

.mystery-hero {
    height: 70vh;
    background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}

.mystery-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: url('images/noise.png'); /* Optional noise texture */
    opacity: 0.05;
}

.glitch-text {
    font-size: 80px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 10px;
    position: relative;
    text-shadow: 2px 2px 0px #ff0000, -2px -2px 0px #00ffff;
    animation: glitch 3s infinite;
}

@keyframes glitch {
    0% { transform: translate(0); }
    2% { transform: translate(-2px, 2px); }
    4% { transform: translate(2px, -2px); }
    6% { transform: translate(0); }
    100% { transform: translate(0); }
}

.drop-timer {
    display: flex;
    gap: 20px;
    margin-top: 40px;
}

.timer-box {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 20px;
    min-width: 100px;
    border-radius: 4px;
}

.timer-val {
    font-family: monospace;
    font-size: 32px;
    font-weight: bold;
    display: block;
    color: #fff;
}

.timer-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #888;
}

.notify-form {
    margin-top: 50px;
    display: flex;
    gap: 10px;
}

.notify-input {
    background: transparent;
    border: 1px solid #333;
    padding: 15px 25px;
    color: #fff;
    font-family: inherit;
    width: 300px;
}

.notify-btn {
    background: #fff;
    color: #000;
    border: none;
    padding: 15px 40px;
    text-transform: uppercase;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}

.notify-btn:hover {
    background: #ccc;
}

/* Locked Grid */
.locked-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2px; /* minimal gap for editorial look */
    max-width: 1600px;
    margin: 80px auto;
    padding: 0 20px;
}

.locked-card {
    position: relative;
    aspect-ratio: 3/4;
    background: #111;
    overflow: hidden;
    group;
}

.locked-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: blur(10px) grayscale(100%);
    opacity: 0.5;
    transition: all 0.5s ease;
}

.locked-card:hover .locked-image {
    filter: blur(5px) grayscale(50%);
    opacity: 0.7;
    transform: scale(1.05);
}

.lock-overlay {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 2;
}

.lock-icon {
    font-size: 32px;
    margin-bottom: 15px;
    color: #fff;
}

.lock-title {
    font-family: monospace;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    background: #000;
    padding: 5px 10px;
}

.release-date {
    position: absolute;
    bottom: 20px;
    left: 0; 
    width: 100%;
    text-align: center;
    font-size: 12px;
    color: #666;
    letter-spacing: 1px;
}
</style>


<!-- Breadcrumb -->
<div class="breadcrumb" style="background: #0a0a0a; color: #666; padding: 20px 50px;">
    <a href="index.php" style="color: #fff;">Home</a> / <span>Coming Soon</span>
</div>

<div class="coming-soon-page">
    
    <!-- HERO SECTION -->
    <div class="mystery-hero">
        <div style="font-size: 14px; color: #ff0000; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 20px; font-weight: bold;">Confidential • Level 4 Access</div>
        <h1 class="glitch-text">UNRELEASED</h1>
        <p style="font-size: 18px; color: #888; max-width: 600px; margin: 20px auto; line-height: 1.6;">
            The next evolution of global style. Digitally secured. Exclusive access only.
            <br>Sign up to receive the drop password.
        </p>

        <!-- COUNTDOWN -->
        <div class="drop-timer">
            <div class="timer-box">
                <span class="timer-val" id="days">04</span>
                <span class="timer-label">Days</span>
            </div>
            <div class="timer-box">
                <span class="timer-val" id="hours">12</span>
                <span class="timer-label">Hours</span>
            </div>
            <div class="timer-box">
                <span class="timer-val" id="mins">45</span>
                <span class="timer-label">Mins</span>
            </div>
            <div class="timer-box">
                <span class="timer-val" id="secs">30</span>
                <span class="timer-label">Secs</span>
            </div>
        </div>

        <!-- FORM -->
        <form class="notify-form" onsubmit="event.preventDefault(); alert('Access Requested. We will contact you.');">
            <input type="email" class="notify-input" placeholder="ENTER YOUR EMAIL" required>
            <button type="submit" class="notify-btn">REQUEST ACCESS</button>
        </form>
    </div>

    <!-- LOCKED COLLECTION -->
    <div class="container" style="color:#000;"> <!-- Reset text color for container if needed, but we want dark mode -->
    </div>
    
    <div style="text-align:center; margin-top: 60px;">
        <h2 style="font-size: 24px; text-transform: uppercase; letter-spacing: 4px; font-weight: 300;">Encrypted Files</h2>
        <div style="width: 50px; height: 2px; background: #333; margin: 20px auto;"></div>
    </div>

    <div class="locked-grid">
        <!-- Locked Item 1 -->
        <div class="locked-card">
            <img src="images/men-hero.jpg" class="locked-image" alt="Classified">
            <div class="lock-overlay">
                <i class="fa fa-lock lock-icon"></i>
                <div class="lock-title">PROJECT: VANTABLACK</div>
            </div>
            <div class="release-date">DROPPING 03.24.2026</div>
        </div>

        <!-- Locked Item 2 -->
        <div class="locked-card">
            <img src="images/women-hero.jpg" class="locked-image" alt="Classified">
            <div class="lock-overlay">
                <i class="fa fa-lock lock-icon"></i>
                <div class="lock-title">CODENAME: ETHER</div>
            </div>
            <div class="release-date">DROPPING 03.24.2026</div>
        </div>

        <!-- Locked Item 3 -->
        <div class="locked-card">
            <img src="images/herolarge.jpg" class="locked-image" alt="Classified">
            <div class="lock-overlay">
                <i class="fa fa-lock lock-icon"></i>
                <div class="lock-title">ARCHIVE 003</div>
            </div>
            <div class="release-date">LOCKED</div>
        </div>

        <!-- Locked Item 4 -->
        <div class="locked-card">
            <img src="images/shop-hero.jpg" class="locked-image" alt="Classified">
            <div class="lock-overlay">
                <i class="fa fa-lock lock-icon"></i>
                <div class="lock-title">PROTOTYPE X</div>
            </div>
            <div class="release-date">LOCKED</div>
        </div>
    </div>
    
    <!-- Load More Button (The user requested this specifically) -->
    <div style="text-align:center; margin-top:80px;">
        <p style="color:#444; margin-bottom: 20px; font-family: monospace;">> ACCESSING ARCHIVES...</p>
        <button id="load-more-btn" style="padding:15px 40px; background:transparent; border:1px solid #333; color:#666; font-family:monospace; cursor:pointer; text-transform:uppercase;">
            [ LOAD MORE DATA ]
        </button>
    </div>

</div>

<script>
// Simple countdown logic
function updateTimer() {
    // Set drop date to 4 days from now for demo
    // In real app, stick to a fixed date
    const d = document.getElementById('days');
    const h = document.getElementById('hours');
    const m = document.getElementById('mins');
    const s = document.getElementById('secs');
    
    let time = new Date();
    let end = new Date();
    end.setDate(end.getDate() + 4); 
    // Just simulating a running clock for aesthetic
    
    // Instead, let's just tick down the seconds roughly
    let secVal = parseInt(s.innerText);
    if(secVal > 0) {
        s.innerText = (secVal - 1).toString().padStart(2, '0');
    } else {
        s.innerText = '59';
        // could ripple up to minutes, but for visual effect this is fine
    }
}
setInterval(updateTimer, 1000);

// Load More Logic for this specific page
document.getElementById('load-more-btn').addEventListener('click', function() {
    this.innerHTML = '[ DECRYPTING... ]';
    this.disabled = true;
    
    setTimeout(() => {
        // Simulate loading more "locked" cards
        const grid = document.querySelector('.locked-grid');
        
        const newCard = document.createElement('div');
        newCard.className = 'locked-card';
        newCard.innerHTML = `
            <img src="images/herolarge.jpg" class="locked-image" alt="Classified">
            <div class="lock-overlay">
                <i class="fa fa-lock lock-icon"></i>
                <div class="lock-title">FILE CORRUPTED</div>
            </div>
            <div class="release-date">UNKNOWN</div>
        `;
        
        // Add fade in
        newCard.style.opacity = '0';
        grid.appendChild(newCard);
        
        // Reflow
        setTimeout(() => newCard.style.opacity = '1', 50);
        
        this.innerHTML = '[ ACCESS DENIED ]';
        this.style.color = '#ff0000';
        this.style.borderColor = '#ff0000';
    }, 1500);
});
</script>

<?php include 'includes/footer.php'; ?>
