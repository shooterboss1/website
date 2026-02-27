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
	'images/cap.jpg',
	'images/cap.png',
	'images/hat.jpg'
], 'NEXTGEN FDM Cap');
?>

<section class="page">
	<a href="index.php" class="btn" style="margin-bottom:1rem;display:inline-block;">← Back to shop</a>

	<div class="product-page" style="display:flex;gap:24px;flex-wrap:wrap;">
		<div class="product-media" style="flex:1 1 240px;max-width:400px;">
			<img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="Cap" style="width:100%;height:auto;border-radius:6px;">
		</div>

		<div class="product-details" style="flex:1 1 260px;">
			<h1>Cap</h1>
			<p class="price" style="font-weight:700;font-size:1.2rem;">$19.99</p>

			<p>Adjustable snapback cap with embroidered logo. One size fits most — lightweight and breathable.</p>

			<div class="product-actions" style="margin-top:1rem;" 
				 data-id="101" 
				 data-title="Cap" 
				 data-price="19.99" 
				 data-img="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>">
				
				<label>
					Quantity
					<input type="number" name="qty" value="1" min="1" style="width:64px;">
				</label>
				<br><br>
				<button type="button" class="btn quick-add-btn">Add to Bag</button>
			</div>
		</div>
	</div>
</section>

<?php include 'includes/footer.php'; ?>
