<?php
/* @var $this View */

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Agen nKing';

$pendapatanBasis = $model->thisMonthSale * (20.0 / 100.0);
$pendapatanBonus = $model->thisMonthSale * ($model->bonus / 100.0);

$totalPendapatan = $pendapatanBasis + $pendapatanBonus + $model->bonusAdjustment;
$totalDisetor = $model->thisMonthSale - $totalPendapatan;
?>

<style>
    table.mytable{
        width: 100%;
        border: 1px solid gray;
        border-collapse: collapse;
    }
    table.mytable th, table.mytable td {
        border: 1px solid gray;
        padding: 4px;
    }
    .detail-tab > .menu {
        border-radius: 0 !important;
    }
    .detail-tab > .menu > .item {
        border-bottom-width: 3px !important;
    }
</style>
<div class="ui dimmer modal transition">
    <div class="ui mini adjust-bonus modal transition">
        <div class="header">Adjust Bonus</div>
        <div class="content ui form">
            <div class="field">
                <label>Bonus Adjustment</label>
                <div class="ui input">
                    <input name="adjust-bonus" type="text" placeholder="num...">
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui negative button">Batal</button>
            <button class="ui positive button">Simpan</button>
        </div>
    </div>
</div>
<script>
    $('.adjust-bonus.modal .positive.button').click((e) => {
        $('.adjust-bonus.modal input').clone().appendTo('#form-filter');
        $('#form-filter').submit();
    });
</script>
<?php $form = ActiveForm::begin([
    'id' => 'form-filter',
    'method' => 'get',
]); ?>
<div class="ui vertical segment" style="padding: 1em;">
    <div class="ui form filter">
        <div class="fields">
            <?php IF (Yii::$app->user->identity->roleId == 1) : ?>
            <div class="six wide field">
                <label>Agen</label>
                <?= Html::activeDropDownList($model, 'agenCode', ArrayHelper::merge(['' => 'Semua'], 
                ArrayHelper::map(
                    User::findAll(['roleId' => 2]), 
                    'agenCode', 'agenCode')), 
                [
                    'class' => 'ui dropdown agenCode'
                ]) ?>
            </div>
            <?php ENDIF; ?>
            <div class="four wide field">
                <label>Tahun</label>
                <?= Html::activeDropDownList($model, 'year', [
                    '' => 'Semua',
                    '2019' => '2019',
                    '2020' => '2020',
                ], [
                    'class' => 'ui dropdown year'
                ]) ?>
            </div>
            <div class="four wide field">
                <label>Bulan</label>
                <?= Html::activeDropDownList($model, 'month', !$model->year ? ['' => 'Tidak Ada'] : [
                    '' => 'Semua',
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ], [
                    'class' => 'ui dropdown month'
                ]) ?>
            </div>
            <div class="three wide field">
                <label>Tanggal</label>
                <?= Html::activeDropDownList($model, 'day', ArrayHelper::merge(['' => 'Semua'], $model->getDayList()), [
                    'class' => 'ui dropdown month'
                ]) ?>
            </div>
            <script>
                $('.ui.dropdown').dropdown({
                    forceSelection: false,
                    clearable: true
                });

                ready(() => {
                   setTimeout(() => {
                        $('.ui.dropdown select').change(function(){
                            $('#form-filter').submit();
                        });
                   }, 1000);
                });
            </script>
        </div>
    </div>
</div>
<?php $form->end(); ?>
<div class="ui vertical segment" style="padding: 1em;">
    <div class="ui medium header">PENJUALAN</div>
    <div class="ui two tiny statistics">
        <div class="blue statistic">
            <div class="value">
                <i class="chart line icon"></i> <?= $model->thisDaySaleCount ?> vcr
            </div>
            <div class="label">
                Hari Ini, Rp. <?= floatToDecimal($model->thisDaySaleAmount) ?>
            </div>
        </div>
        <div class="red statistic">
            <div class="value">
                <i class="calendar alternate outline icon"></i> <?= $model->thisMonthSaleCount ?> vcr
            </div>
            <div class="label">
                Bulan Ini, Rp. <?= floatToDecimal($model->thisMonthSale) ?>
            </div>
        </div>
    </div>
</div>
<div class="ui vertical segment" style="padding: 1em;">
    <div class="ui medium header">PENDAPATAN</div>
    <table class="mytable">
        <thead>
            <tr>
                <th colspan="3">Tabel Kalkulasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Penjualan</td>
                <td><?= $model->thisMonthSaleCount ?> vcr</td>
                <td style="text-align: right;"><?= floatToDecimal($model->thisMonthSale) ?></td>
            </tr>
            <tr>
                <td>Pendapatan Basis</td>
                <td>20%</td>
                <td style="text-align: right;"><?= floatToDecimal($pendapatanBasis) ?></td>
            </tr>
            <tr>
                <td>Pendapatan Bonus</td>
                <td><?= $model->bonus ?>%</td> <!-- 8, 10, 15 -->
                <td style="text-align: right;"><?= floatToDecimal($pendapatanBonus) ?></td>
            </tr>
            <tr>
                <td>Bonus Adjustment</td>
                <td></td> <!-- 8, 10, 15 -->
                <td style="text-align: right;">
                    <i onclick="$('.modal.adjust-bonus').modal('show');" class="icon teal pencil alternate"></i>
                    <?= floatToDecimal($model->bonusAdjustment) ?>
                </td>
            </tr>
            <tr>
                <td>Total Yang Didapat</td>
                <td><?= $model->thisMonthSale ? round((100 / $model->thisMonthSale) * $totalPendapatan, 2) : 0 ?>%</td> <!-- 8, 10, 15 -->
                <td style="text-align: right;"><?= floatToDecimal($totalPendapatan) ?></td>
            </tr>
            <tr>
                <td>Total Yang Disetor</td>
                <td><?= $model->thisMonthSale ? round((100 / $model->thisMonthSale) * $totalDisetor, 2) : 0 ?>%</td> <!-- 8, 10, 15 -->
                <td style="text-align: right;"><?= floatToDecimal($totalDisetor) ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="ui vertical segment detail-tab" style="padding: 4px 0px;">
    <div class="ui top attached pointing secondary menu">
        <a class="item active" data-tab="tab-1">PENGGUNA AKTIF</a>
        <a class="item" data-tab="tab-2">PENJUALAN HARI INI</a>
    </div>
    <div class="ui bottom attached tab segment active" data-tab="tab-1" style="margin: 0px">
        <?php Pjax::begin([
            'id' => 'pjax-active-users',
            'enablePushState' => false,
        ]) ?>
        
        <?php Pjax::end(); ?>
        <script>
            ready(function(){
               repeat(20, 10000, 0, function(){
                    var data = $('form#form-filter').serialize();
                    $.pjax.reload({container: "#pjax-active-users", url: "/site/active-users?"+ data, push: false, history: false});
                }, 10000);
            });
        </script>
    </div>
    <div class="ui bottom attached  tab segment" data-tab="tab-2" style="margin: 0px">
       <div class="sub header"><?= count($model->thisDaySales) ?> Voucher</div>
       <div class="ui middle aligned divided list">
            <?php FOREACH ($model->thisDaySales as $sale) : ?>
            <div class="item">
                <i class="large middle aligned icon" style="min-width: 46px"><?= $sale['profileAlias'] ?></i>
                <div class="content">
                    <a class="header"><?= "{$sale['agenCode']} - {$sale['name']}" ?></a>
                    <div class="description"><?= "Rp. {$sale['price']}, &nbsp; Jam: ".date('H:i', strtotime($sale['saleDate'])) ?></div>
                </div>
            </div>
            <?php ENDFOREACH; ?>
        </div>
    </div>
</div>
<script>
    ready(function(){
        $('.detail-tab .menu .item').tab({
            context: '.detail-tab'
        }); 
    });
</script>