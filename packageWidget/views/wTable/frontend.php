<?php if($data['content']): ?>
    <div class="specification">
        <div class="caption"><?=$data['title']; ?></div>
        <ul class="acordeonList js-acordeonList">
            <?php $cntGroup = 0; ?>
            <?php foreach ($data['content'] as $group):?>
                <li class="<?=($cntGroup == 0) ? 'open' : ''; ?>">
                    <div class="item"><?=$group['groupTitle']; ?></div>
                    <div class="subList" style="display:<?=($cntGroup == 0) ? 'block' : 'none'; ?>">
                        <?php foreach ($group['blocks'] as $block):?>
                            <div class="acordeonWrapper">
                                <div class="title"><?=$block['param']; ?></div>
                                <div class="description">
                                    <?php foreach ($block['value'] as $row): ?>
                                        <div class="body"><?=$row; ?></div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div>
                </li>
            <?php $cntGroup++; endforeach;?>
        </ul>
    </div>
<?php endif; ?>