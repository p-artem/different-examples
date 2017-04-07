<?php

?>
<?php if($data['content']): ?>

    <div class="modalGallery">
        <div class="overlay js-closeModal"></div>
        <div class="imgBox js-closeModal js-galleryBox">
            <?php foreach ($data['content'] as $keyImg => $img): ?>
                <?php $visible = ($keyImg !== 0) ? '' : 'class="visible"'?>
                <img onload="$A.switchImgOrientation(this, 'modal')" src="<?=$img['url']; ?>" <?=$visible; ?>>
            <?php endforeach;?>
        </div>
        <div class="prev js-prev"></div>
        <div class="next js-next"></div>
    </div>
<?php endif; ?>


