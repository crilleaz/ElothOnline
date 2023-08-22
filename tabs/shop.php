<?php
declare(strict_types=1);

if (!isset($_GET['shop'])) {
    return;
}

$wiki = DI::getService(\Game\Wiki::class);
$shop = $wiki->getShop($_GET['shop']);

$playerGold = $player->getGold();
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
                                <h5 class="card-titles"><?=$shop->name?></h5>
                                <p class="card-text">
                                <div class="mdb-lightbox">
                                    <div class="container-fluid">
                                        <div class="container">
                                            <div class="row">
                                                <table id="myTable">
                                                    <tr class="header">
                                                        <th style="width:20%;">Offer</th>
                                                        <th style="width:20%;">Price</th>
                                                        <th style="width:20%;">Action</th>
                                                    </tr>
                                                    <?php
                                                    foreach ($shop->listStock() as $offer) {
                                                        echo '<td>';
                                                        echo $offer->item->name;
                                                        echo '</td>';
                                                        echo '<td>' . $offer->inExchange->quantity . ' ' . $offer->inExchange->name . '</td>';

                                                        if ($offer->inExchange->id === 1) {
                                                            if ($playerGold >= $offer->inExchange->quantity) {
                                                                echo '<td><button type="button" class="btn btn-primary">Buy</button></td>';
                                                            } else {
                                                                echo '<td><button type="button" class="btn btn-danger" disabled>Buy</button></td>';
                                                            }
                                                        } else {
                                                            echo '<td><button type="button" class="btn btn-primary">Exchange</button></td>';
                                                        }
                                                        echo '</tr>';
                                                    }
                                                    ?>
                                                </table>
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
