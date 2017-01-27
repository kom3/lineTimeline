<?php

require '../src/LineTimeline.php';

$app = new Line\LineTimeline();

$app->setSession('Examples');

$app->likeTimeline('_dQXvILQLzuN5-jSNMrfUNcemoCbkLSmRijRjFrU', 5, 0, 1, 1, function($error){
    if($error){
        echo "Gagal";
    } else {
        echo "Sukses";
    }
});
