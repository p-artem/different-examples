<?php
?>
<?php if($data['content']): ?>
    <div class="specification">
        <div class="caption"><?=$data['title']; ?></div>
        <ul class="list">
            <?php foreach($data['content'] as $item): ?>
                <li class="block">
                    <span class="property"><?=$item['param']; ?></span>
                    <span class="value"><?=$item['value']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>