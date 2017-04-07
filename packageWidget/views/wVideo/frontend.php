<?php

?>
<?php if($data['content']): ?>
    <div class="js-firstSlider videoBox swiper-container js-video-slider">
        <div class="swiper-wrapper">
            <?php foreach ($data['content'] as $video): ?>
                <div class="posterSlide swiper-slide">
                    <div class="slideWrap js-posterSlide" data-src="<?=$video; ?>">
                        <img src="http://img.youtube.com/vi/<?=$video; ?>/0.jpg">
                        <div class="playBtn"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if(count($data['content']) > 2): ?>
            <div class="swiper-pagination"></div>
        <?php endif;?>
    </div>
<?php endif; ?>