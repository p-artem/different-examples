<?php
use yii\helpers\Html;
?>
<?php if($data['content']): ?>
    <div class="textNewBar" data-view="<?=$data['identityId']; ?>">
        <?php if($caption = trim($data['title'])): ?>
            <div class="caption"><?=$caption ; ?></div>
        <?php endif; ?>
        <?=Html::decode($data['content']); ?>
    </div >
<?php endif; ?>