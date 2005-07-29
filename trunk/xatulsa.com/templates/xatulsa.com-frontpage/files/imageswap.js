function imgover(image) {
	document.getElementById('img-' + image).src = 'images/' + image + '-over.jpg';
}
function imgout(image) {
	document.getElementById('img-' + image).src = 'images/' + image + '-out.jpg';
}