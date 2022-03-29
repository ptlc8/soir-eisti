<h2 class="title">
	Quelques photos :
</h2>
<div class="diapo">
	<img class="photo-avant" src="" alt="diaporama" />
	<img class="photo" src="" alt="diaporama" />
	<img class="photo-apres" src="" alt="diaporama" />
	<span class="flecheGauche" onclick="defiler(this.parentElement, imagesDiapo, -1);"></span>
	<span class="flecheDroite" onclick="defiler(this.parentElement, imagesDiapo, 1);"></span>
	<?php 
	$albums = [];
	foreach (array_values(array_diff(scandir("assets/events/"), array("..", "."))) as $album){
	    if(intval($album)."" == $album)
	        $albums[] = $album;
	}
	$photos = [];
	for($i=0; $i<10; $i++){
	    $nomAlbum = $albums[random_int(0, count($albums)-1)];
	    $album = array_values(array_diff(scandir("assets/events/".$nomAlbum), array("..", ".")));
	    $photos[] = "assets/events/".$nomAlbum."/".$album[random_int(0, count($album)-1)];
	}
	$photos = json_encode($photos);
	?>
	<script>
	    var imagesDiapo = <?=$photos?>;
		initialiserDiapo(document.currentScript.parentElement, imagesDiapo);
	</script>
</div>