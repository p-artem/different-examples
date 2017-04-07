<?php
/*
    graphCount:3,
    unitX:"",
    unitY:"",
    massX:["Monday",2,"Kg",4,5],
    graphs:{
        "grapsName1" : ['23','27','17','34','65'],
        "grapsName2" : ['23','27','17','34','65'],
        "grapsName3" : ['23','27','17','34','65']
    }
 */


$mapData = [];
if($graph = $data['content']){
    $mapData['labels'] = $graph['axisX'];
    foreach ($graph['axisY'] as $item){
        $mapData['series'][] = $item;
    }
//    $keysSeries = array_keys($graph['axisX']);
//    array_map(function ($arr) use (&$mapData, $keysSeries){
//    foreach ($keysSeries as $key){
//        if(isset($arr[$key])){
//            $mapData['series'][$key][] = $arr[$key];
//        }
//    }
//    }, $graph['axisY']);
    $mapData = json_encode($mapData, JSON_UNESCAPED_UNICODE);



}
?>
<?php if($data['content']):?>
    <div class="graphBox">
        <ul class="legend">
            <?php foreach ($data['content']['graphName'] as $key => $name): ?>
            <li class="val<?=$key+1; ?>"><?=$name; ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="ct-chart js-graph"></div>
    </div>
    <script>
        var params = <?=$mapData; ?>;
    </script>
<?php endif; ?>