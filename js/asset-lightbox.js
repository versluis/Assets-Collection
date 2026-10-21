/*
 * Contact-sheet lightbox for single Asset posts.
 * Thumbnails link to the full image, so without JS they still work;
 * with JS they open in a native <dialog> (Esc, click outside or the button closes it).
 */
(function () {
	var links = document.querySelectorAll('.asset-catalog__sheet');
	if (!links.length || typeof HTMLDialogElement !== 'function') {
		return;
	}

	var dialog = document.createElement('dialog');
	dialog.className = 'asset-lightbox';
	dialog.innerHTML = '<button type="button" class="asset-lightbox__close" aria-label="Close">&times;</button><img alt="">';
	document.body.appendChild(dialog);

	var img = dialog.querySelector('img');

	links.forEach(function (link) {
		link.addEventListener('click', function (event) {
			event.preventDefault();
			var thumb = link.querySelector('img');
			img.src = link.href;
			img.alt = thumb ? thumb.alt : '';
			dialog.showModal();
		});
	});

	dialog.querySelector('.asset-lightbox__close').addEventListener('click', function () {
		dialog.close();
	});

	// a click on the backdrop lands on the dialog itself, not its contents
	dialog.addEventListener('click', function (event) {
		if (event.target === dialog) {
			dialog.close();
		}
	});

	dialog.addEventListener('close', function () {
		img.removeAttribute('src');
	});
})();
