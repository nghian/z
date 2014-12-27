<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\VideoItem
 */
use yii\jwplayer\JWPlayer;
use yii\helpers\Html;
use common\components\YoutubeInfo;

$this->title = $model->title;
?>
<h1 class="page-header"><?= Html::encode($model->title) ?></h1>
<div class="row">
    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
        <?= JWPlayer::widget([
            'key' => 'N8zhkmYvvRwOhz4aTGkySoEri4x+9pQwR7GHIQ==',
            'options' => [
                'id' => 'player'
            ],
            'clientOptions' => [
                //'file' => 'http://www.youtube.com/watch?v=' . $model->file,
                //'file' => 'https://r2---sn-8pxuuxa-i5o6.googlevideo.com/videoplayback?c=web&clen=7963997&cpn=DERxN97BXQ78T4Uq&ctier=L&cver=as3&dur=151.050&expire=1418954802&fexp=900235%2C900718%2C902904%2C927622%2C930809%2C932404%2C937008%2C942205%2C943917%2C945127%2C947209%2C947218%2C948124%2C952302%2C952605%2C952901%2C955301%2C957103%2C957105%2C957201&gir=yes&id=o-AKC_hSSXcRrh_i2WqKmg4zx0jq8dxz4k6azK8Zng0jox&initcwndbps=1490000&ip=27.67.6.134&ipbits=0&itag=134&keepalive=yes&key=yt5&lmt=1418900964542853&mm=31&ms=au&mt=1418933139&mv=m&ratebypass=yes&requiressl=yes&signature=05FAF29A9238B3F65BD274E69882B5D95B6C3552.D6373864F4F3EAC205BAE7A8FF310B4C0282CA92&source=youtube&sparams=clen%2Cctier%2Cdur%2Cgir%2Cid%2Cinitcwndbps%2Cip%2Cipbits%2Citag%2Clmt%2Cmm%2Cms%2Cmv%2Crequiressl%2Csource%2Cupn%2Cexpire&sver=3&upn=7hjkICBphaw',
                //'playlist' => [(new YoutubeInfo(['id' => $model->file]))->fetch],
                'sources' => (new YoutubeInfo(['id' => $model->file]))->formats,
                //'playlist' => '/video/playback?id='.$model->id,

                'primary' => 'flash'
            ]
        ]); ?>
        <video src="http://r7---sn-8pxuuxa-i5oy.googlevideo.com/videoplayback?id=o-AIKvtETEeEZg4NNUe0BFzuhzs8MGRXwW6ouwVhZfq5x1&signature=77CD809F2635536B30815C14B02195100387CB76.0E161CCA66C95571D2B5B231A5E754D3C75D539C&mm=31&itag=22&ip=27.67.6.134&ms=au&fexp=900718,927622,932404,9405586,943917,947209,947218,948124,952302,952605,952901,955301,957103,957105,957201&key=yt5&sparams=dur,id,initcwndbps,ip,ipbits,itag,mm,ms,mv,ratebypass,source,upn,expire&mv=m&sver=3&ipbits=0&dur=252.609&ratebypass=yes&expire=1418958449&initcwndbps=1532500&mt=1418936826&upn=vpHOXUB8_GA&source=youtube&title=neymarronhaldinho-y-cr7-freestyle"></video>
    </div>
</div>