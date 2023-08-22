<?php
declare(strict_types=1);

$wiki = DI::getService(\Game\Wiki::class);
$shops = $wiki->getShops();
?>
<?php include '_header.php'; ?>
<body style="background-color: #eceef4">
<div class="container" style="position:relative; margin-top:10px">
    <div class="row">
        <?php include '_info.php'; ?>
        <div class="col-lg-6">
            <div class="card shadow bg-white rounded">
                <div class="card-body">
                    <div class="row justify-content">
                        <div class="card border border-success" style="width: 100%;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-titles">Shop list</h5>
                                <p class="card-text">
                                <div class="mdb-lightbox">
                                    <div class="container-fluid">
                                        <div class="container">
                                            <div class="row">
                                                <?php foreach ($shops as $shop) { ?>
                                                    <div class="col card">
                                                        <a href="?tab=shop&shop=<?= urlencode($shop->name)?>">
                                                        <p><?= $shop->name ?></p>
                                                        <p style="color:gray; font-size: 0.7em"><?= $shop->description ?></p>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="d-sm-none">
        </div>
        <?php include '_status.php'; ?>
    </div>
</div>
</body>

</html>
