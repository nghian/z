<?php
use yii\helpers\Html;

?>
<rss version="2.0" xmlns:jwplayer="http://rss.jwpcdn.com/">
    <channel>
        <item>
            <title><?= Html::encode($item['title']) ?></title>
            <description><?= Html::encode($item['title']) ?></description>
            <jwplayer:image><?= Html::encode($item['image']) ?></jwplayer:image>
            <?php foreach ($item['sources'] as $source): ?>
                <jwplayer:source file="<?= Html::encode($source['file']) ?>" label="<?= Html::encode($source['label']) ?>" type=".<?=strtolower($source['type']);?>" />
            <?php endforeach; ?>
        </item>
    </channel>
</rss>