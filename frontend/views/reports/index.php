<?php
use yii\helpers\Html;


$this->title = Yii::t('app','Reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reports-index">
	<?= '<p>'.Html::a('Лифты в простое', ['oosnow'], ['class' => 'btn btn-success']).'</p>' ?>
</div>