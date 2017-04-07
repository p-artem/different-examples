<?php
//echo '<pre>';
//print_r($data['content']);die;
?>
<div class="widget wGraph">
    <div class="head">
        <i class="icon icon-wGraph"></i>
        <span class="title">График</span>
        <div class="btns"><i class="icon icon-cross js-delWidget"></i></div>
    </div>

    <div class="body">
        <div class="textField inputBox widgetTitle">
            <input type="hidden" name="w[<?=$data['position']; ?>][wGraph][id]" value="<?=$data['id']; ?>">
            <input placeholder="Заголовок блока" name="w[<?=$data['position']; ?>][wGraph][title]" type="text" value="<?=$data['title']; ?>" data-pattern="text">
        </div>
        <?php if($data['content']): ?>
            <div class="row">
            <div class="col">
                <h2>Количество графиков</h2>
                <div class="selectField inputBox">
                    <select name="w[<?=$data['position']; ?>][wGraph][graphCount]" class="js-renderTable">
                        <?php for($i = 1; $i <=6; $i++): ?>
                            <?php $selected = ($i == $data['content']['graphCount']) ? 'selected' : ''; ?>
                            <option value="<?=$i; ?>" <?=$selected?>><?=$i; ?></option>
                        <?php endfor;?>
                    </select>
                </div>
                <h2>Ед. измерения Х</h2>
                <div class="textField inputBox">
                    <input class="initialized" placeholder="Введите значение" name="w[<?=$data['position']; ?>][wGraph][unitX]" type="text" data-pattern="text" value="<?=$data['content']['unitX']; ?>">
                </div>
                <h2>Ед. измерения Y</h2>
                <div class="textField inputBox">
                    <input class="initialized" placeholder="Введите значение" name="w[<?=$data['position']; ?>][wGraph][unitY]" type="text" data-pattern="text" value="<?=$data['content']['unitY']; ?>">
                </div>
            </div>
            <div class="col">
                <table>
                    <thead>
                    <tr>
                        <th><input class="initialized" placeholder="Значение по X" disabled></th>
                        <?php foreach (array_keys($data['content']['axisY']) as $graphName): ?>
                            <th><input class="initialized" placeholder="Название графика" name="w[<?=$data['position']; ?>][wGraph][graphName][]" type="text" value="<?=$graphName; ?>"></th>
                        <?php endforeach; ?>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $lasIndexRow = count($data['content']['axisX']) - 1; ?>
                    <?php foreach ($data['content']['axisX'] as $keyX => $itemX): ?>
                        <?php $indexCell = 0; ?>
                        <tr>
                            <td><input class="initialized" placeholder="Значение по X" name="w[<?=$data['position']; ?>][wGraph][axisX][]" type="text" value="<?=$itemX; ?>"></td>
                            <?php foreach ($data['content']['axisY'] as $itemY): ?>
                                <td><input class="initialized" placeholder="Значение по Y" name="w[<?=$data['position']; ?>][wGraph][axisY][<?=$indexCell; ?>][]" type="text" value="<?=$itemY[$keyX]; ?>"></td>
                                <?php $indexCell++; ?>
                            <?php endforeach; ?>
                            <td>
                                <?php if($keyX == $lasIndexRow) : ?>
                                    <div class="js-plusRow">+</div>
                                <?php else : ?>
                                    <i class="icon icon-cross js-delTableRow"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <div class="row">
                <div class="col">
                    <h2>Количество графиков</h2>
                    <div class="selectField inputBox">
                        <select name="w[][wGraph][graphCount]" class="js-renderTable">
                            <option value="1" selected>1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <h2>Ед. измерения Х</h2>
                    <div class="textField inputBox">
                        <input class="initialized" placeholder="Введите значение" name="w[][wGraph][unitX]" type="text" data-pattern="text">
                    </div>
                    <h2>Ед. измерения Y</h2>
                    <div class="textField inputBox">
                        <input class="initialized" placeholder="Введите значение" name="w[][wGraph][unitY]" type="text" data-pattern="text">
                    </div>
                </div>
                <div class="col">
                    <table>
                        <thead>
                        <tr>
                            <th><input class="initialized" placeholder="Значение по X" disabled></th>
                            <th><input class="initialized" placeholder="Название графика" name="w[][wGraph][graphName][]" type="text"></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input class="initialized" placeholder="Значение по X" name="w[][wGraph][axisX][]" type="text"></td>
                            <td><input class="initialized" placeholder="Значение по Y" name="w[][wGraph][axisY][]" type="text"></td>
                            <td><div class="js-plusRow">+</div></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

