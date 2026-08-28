<?php
session_start();

$errors = [];
$success = '';
$name = '';
$email = '';
$message = '';

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Basic CSRF check
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		$errors[] = 'Invalid form submission.';
	} else {
		$name = trim($_POST['name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$message = trim($_POST['message'] ?? '');

		if ($name === '') {
			$errors[] = 'Name is required.';
		}
		if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'A valid email is required.';
		}
		if ($message === '') {
			$errors[] = 'Message is required.';
		}

		if (empty($errors)) {
			// Optionally send email here (commented out)
			// $to = 'info@nextgenfdm.com';
			// $subject = 'Contact form message from What's Real Shall Prosper';
			// $body = "Name: $name\nEmail: $email\n\n$message";
			// $headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email;
			// mail($to, $subject, $body, $headers);

			$success = 'Your message has been sent successfully. We\'ll get back to you soon!';
			// Reset inputs to avoid re-displaying them
			$name = $email = $message = '';
			// Regenerate token to prevent double submit
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
	}
}
?>

<?php include 'includes/header.php'; ?>

<style>
/* Contact Page Styling */
.contact-wrapper {
	min-height: 80vh;
	padding: 80px 50px;
	max-width: 1400px;
	margin: 0 auto;
}

.contact-hero {
	text-align: center;
	margin-bottom: 80px;
	animation: fadeInUp 0.8s ease;
}

.contact-hero h1 {
	font-family: var(--font-serif);
	font-size: 64px;
	font-weight: 400;
	margin-bottom: 20px;
	background: linear-gradient(135deg, #1a1a1a 0%, #d90000 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

.contact-hero p {
	font-size: 18px;
	color: var(--muted);
	font-weight: 300;
	max-width: 600px;
	margin: 0 auto;
	line-height: 1.8;
}

.contact-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 80px;
	align-items: start;
}

/* Left Side - Connect With Us */
.connect-section {
	animation: fadeInLeft 0.8s ease 0.2s both;
}

.connect-section h2 {
	font-family: var(--font-serif);
	font-size: 42px;
	font-weight: 400;
	margin-bottom: 30px;
	color: var(--text);
}

.connect-section .subtitle {
	font-size: 16px;
	color: var(--muted);
	margin-bottom: 50px;
	line-height: 1.6;
}

.contact-info {
	display: flex;
	flex-direction: column;
	gap: 35px;
}

.info-item {
	display: flex;
	align-items: flex-start;
	gap: 20px;
	padding: 25px;
	background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
	border: 1px solid #f0f0f0;
	border-radius: 12px;
	transition: all 0.4s ease;
	cursor: pointer;
}

.info-item:hover {
	transform: translateX(10px);
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
	border-color: var(--accent);
}

.info-icon {
	width: 50px;
	height: 50px;
	background: linear-gradient(135deg, #d90000 0%, #ff4444 100%);
	border-radius: 50%;
	display: grid;
	place-items: center;
	color: white;
	font-size: 20px;
	flex-shrink: 0;
	transition: transform 0.3s ease;
}

.info-item:hover .info-icon {
	transform: rotate(360deg);
}

.info-details h3 {
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 2px;
	color: var(--muted);
	margin: 0 0 8px 0;
	font-weight: 600;
}

.info-details p {
	font-size: 18px;
	color: var(--text);
	margin: 0;
	font-weight: 500;
}

.info-details a {
	color: var(--text);
	text-decoration: none;
	transition: color 0.3s ease;
}

.info-details a:hover {
	color: var(--accent);
}

/* Social Media Section */
.social-section {
	margin-top: 60px;
	padding-top: 40px;
	border-top: 1px solid var(--border);
}

.social-section h3 {
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 2px;
	color: var(--muted);
	margin-bottom: 25px;
	font-weight: 600;
}

.social-links {
	display: flex;
	gap: 15px;
	flex-wrap: wrap;
}

.social-link {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 12px 24px;
	background: #000;
	color: #fff;
	border-radius: 50px;
	text-decoration: none;
	font-size: 14px;
	font-weight: 500;
	transition: all 0.3s ease;
	border: 2px solid #000;
}

.social-link:hover {
	background: transparent;
	color: #000;
	transform: translateY(-3px);
	box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.social-link svg {
	width: 18px;
	height: 18px;
	fill: currentColor;
}

/* Right Side - Contact Form */
.form-section {
	animation: fadeInRight 0.8s ease 0.4s both;
}

.contact-form {
	background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
	padding: 50px;
	border-radius: 20px;
	border: 1px solid #f0f0f0;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.05);
}

.contact-form h2 {
	font-family: var(--font-serif);
	font-size: 32px;
	font-weight: 400;
	margin-bottom: 30px;
	color: var(--text);
}

.form-group {
	margin-bottom: 25px;
}

.form-group label {
	display: block;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 1.5px;
	color: var(--muted);
	margin-bottom: 10px;
	font-weight: 600;
}

.form-group input,
.form-group textarea {
	width: 100%;
	padding: 15px 20px;
	border: 1px solid #e0e0e0;
	border-radius: 8px;
	font-family: var(--font-base);
	font-size: 15px;
	color: var(--text);
	background: white;
	transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
	outline: none;
	border-color: var(--accent);
	box-shadow: 0 0 0 3px rgba(217, 0, 0, 0.1);
}

.form-group textarea {
	min-height: 150px;
	resize: vertical;
}

.submit-btn {
	width: 100%;
	padding: 18px;
	background: linear-gradient(135deg, #d90000 0%, #ff4444 100%);
	color: white;
	border: none;
	border-radius: 8px;
	font-family: var(--font-base);
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 2px;
	font-weight: 700;
	cursor: pointer;
	transition: all 0.3s ease;
	margin-top: 10px;
}

.submit-btn:hover {
	transform: translateY(-2px);
	box-shadow: 0 10px 30px rgba(217, 0, 0, 0.3);
}

.submit-btn:active {
	transform: translateY(0);
}

/* Messages */
.alert {
	padding: 15px 20px;
	border-radius: 8px;
	margin-bottom: 25px;
	font-size: 14px;
	animation: slideDown 0.3s ease;
}

.alert-success {
	background: #d4edda;
	color: #155724;
	border: 1px solid #c3e6cb;
}

.alert-error {
	background: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
}

.errors {
	list-style: none;
	padding: 0;
	margin: 0;
}

/* Animations */
@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes fadeInLeft {
	from {
		opacity: 0;
		transform: translateX(-30px);
	}
	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes fadeInRight {
	from {
		opacity: 0;
		transform: translateX(30px);
	}
	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes slideDown {
	from {
		opacity: 0;
		transform: translateY(-10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* Responsive */
@media (max-width: 968px) {
	.contact-wrapper {
		padding: 60px 30px;
	}
	
	.contact-hero h1 {
		font-size: 48px;
	}
	
	.contact-grid {
		grid-template-columns: 1fr;
		gap: 60px;
	}
	
	.contact-form {
		padding: 35px;
	}
}

@media (max-width: 640px) {
	.contact-wrapper {
		padding: 40px 20px;
	}
	
	.contact-hero h1 {
		font-size: 36px;
	}
	
	.connect-section h2 {
		font-size: 32px;
	}
	
	.social-links {
		flex-direction: column;
	}
	
	.social-link {
		justify-content: center;
	}
	
	.contact-form {
		padding: 25px;
	}
}
</style>


<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / <span>Contact</span>
</div>

<div class="contact-wrapper">
	<!-- Hero Section -->
	<div class="contact-hero">
		<h1>Get In Touch</h1>
		<p>Have a question or want to collaborate? We'd love to hear from you. Connect with us through any of the channels below.</p>
	</div>

	<!-- Main Grid -->
	<div class="contact-grid">
		<!-- Left: Connect With Us -->
		<div class="connect-section">
			<h2>Connect With Us</h2>
			<p class="subtitle">Whether you have a question about our products, need styling advice, or just want to say hello - we're here for you.</p>
			
			<div class="contact-info">
				<!-- Email -->
				<div class="info-item">
					<div class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
					</div>
					<div class="info-details">
						<h3>Email Us</h3>
						<p><a href="mailto:info@nextgenfdm.com">info@nextgenfdm.com</a></p>
					</div>
				</div>

				<!-- Instagram -->
				<div class="info-item">
					<div class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
							<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
						</svg>
					</div>
					<div class="info-details">
						<h3>Instagram</h3>
						<p><a href="https://instagram.com/nextgenfdm.official" target="_blank">@nextgenfdm.official</a></p>
					</div>
				</div>

				<!-- TikTok -->
				<div class="info-item">
					<div class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
							<path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
						</svg>
					</div>
					<div class="info-details">
						<h3>TikTok</h3>
						<p><a href="https://tiktok.com/@nextgenfdm" target="_blank">@nextgenfdm</a></p>
					</div>
				</div>

				<!-- Twitter/X -->
				<div class="info-item">
					<div class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
						</svg>
					</div>
					<div class="info-details">
						<h3>X (Twitter)</h3>
						<p><a href="https://twitter.com/nextgenfdm_ltd" target="_blank">@nextgenfdm_ltd</a></p>
					</div>
				</div>

				<!-- Facebook -->
				<div class="info-item">
					<div class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
							<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
						</svg>
					</div>
					<div class="info-details">
						<h3>Facebook</h3>
						<p><a href="https://facebook.com/nextgenfdm" target="_blank">@nextgenfdm</a></p>
					</div>
				</div>
			</div>

			<!-- Social Links -->
			<div class="social-section">
				<h3>Follow Our Journey</h3>
				<div class="social-links">
					<a href="https://instagram.com/nextgenfdm.official" target="_blank" class="social-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
						</svg>
						Instagram
					</a>
					<a href="https://tiktok.com/@nextgenfdm" target="_blank" class="social-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
						</svg>
						TikTok
					</a>
					<a href="https://twitter.com/nextgenfdm_ltd" target="_blank" class="social-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
						</svg>
						X (Twitter)
					</a>
					<a href="https://facebook.com/nextgenfdm" target="_blank" class="social-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
							<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
						</svg>
						Facebook
					</a>
				</div>
			</div>
		</div>

		<!-- Right: Contact Form -->
		<div class="form-section">
			<div class="contact-form">
				<h2>Send Us a Message</h2>
				
				<?php if (!empty($errors)): ?>
					<div class="alert alert-error">
						<ul class="errors">
							<?php foreach ($errors as $err): ?>
								<li><?php echo htmlspecialchars($err, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ($success): ?>
					<div class="alert alert-success">
						<?php echo htmlspecialchars($success, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
					</div>
				<?php endif; ?>

				<form action="contact.php" method="POST">
					<div class="form-group">
						<label for="name">Your Name</label>
						<input type="text" id="name" name="name" placeholder="John Doe" required value="<?php echo htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
					</div>

					<div class="form-group">
						<label for="email">Email Address</label>
						<input type="email" id="email" name="email" placeholder="john@example.com" required value="<?php echo htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
					</div>

					<div class="form-group">
						<label for="message">Your Message</label>
						<textarea id="message" name="message" placeholder="Tell us what's on your mind..." required><?php echo htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>
					</div>

					<!-- CSRF token -->
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

					<button type="submit" class="submit-btn">Send Message</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

