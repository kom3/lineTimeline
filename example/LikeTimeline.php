<?php

require '../src/LineTimeline.php';

$app = new Line\LineTimeline();

$app->setSession('Examples');

try {
    $app->likeTimeline(NULL, 5, 0, 1, 1, function($error){
        if($error){
            echo "Gagal";
        } else {
            echo "Sukses";
        }
    });
} catch (Exception $e){
    echo $e->getMessage();
}
