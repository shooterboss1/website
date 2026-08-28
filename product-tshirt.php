<?php include 'includes/header.php'; ?>

<?php
// small helper (same pattern as other pages)
function image_or_placeholder(array $paths, $alt = '') {
	foreach ($paths as $p) {
		if (file_exists(__DIR__ . '/' . $p)) return $p;
	}
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="300">'
	     . '<rect width="100%" height="100%" fill="#eee"/>'
	     . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#666" font-family="Arial" font-size="20">'
	     . htmlspecialchars($alt, ENT_QUOTES)
	     . '</text></svg>';
	return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
}

$img = image_or_placeholder([
	'images/tshirt.jpg',
	'images/tshirt.png',
	'images/tshirt.jpeg',
	'images/tee.jpg'
], 'What's Real Shall Prosper T‑Shirt');
?>

<section class="page">
	<a href="index.php" class="btn" style="margin-bottom:1rem;display:inline-block;">← Back to shop</a>

	<div class="product-page" style="display:flex;gap:24px;flex-wrap:wrap;">
		<div class="product-media" style="flex:1 1 320px;max-width:480px;">
			<img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="T‑Shirt" style="width:100%;height:auto;border-radius:6px;">
		</div>

		<div class="product-details" style="flex:1 1 280px;">
			<h1>T‑Shirt</h1>
			<p class="price" style="font-weight:700;font-size:1.2rem;">$29.99</p>

			<p>Classic cut cotton T‑shirt with the What's Real Shall Prosper logo. Comfortable, durable, and designed for everyday wear.</p>

			<form action="cart.php" method="post" style="margin-top:1rem;">
				<input type="hidden" name="product" value="tshirt">
				<label>
					Size
					<select name="size" required>
						<option value="">Select</option>
						<option>XS</option>
						<option>S</option>
						<option>M</option>
						<option>L</option>
						<option>XL</option>
					</select>
				</label>
				<br><br>
				<label>
					Quantity
					<input type="number" name="qty" value="1" min="1" style="width:64px;">
				</label>
				<br><br>
				<button type="submit" class="btn">Add to cart</button>
			</form>
		</div>
	</div>
</section>

<?php include 'includes/footer.php'; ?>

