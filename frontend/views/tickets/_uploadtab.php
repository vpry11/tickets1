<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\helpers\ArrayHelper;

/**
 *	Ticket spare part partial view
 */

?>

<div class="tickets-_uploadtab">

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row panel panel-info">
        <div class="col-md-6">
    		<?= $form->field($imagemodel, 'imageFile')->fileInput() ?>
    	</div>
        <div class="col-md-2">
			<?=  '<br>'.Html::submitButton(Yii::t('app','Add'), ['class' => 'submit btn btn-primary','formaction'=>Url::toRoute(['upload','id'=>$model->ticket['id'],'ticode'=>$model->ticket['ticode']])]) ?>
       	</div>
	</div>

<?php ActiveForm::end() ?>
	<?php 
	foreach($model->uploadedfilelist as $uimage){
		//echo basename($uimage).'<br>';
		echo Html::label(basename($uimage).':').'<br>';
		echo Html::img('http://'.$_SERVER["HTTP_HOST"].'/uploads/'.basename($uimage),['width'=>'800']).'<br>';
	}
	?>
</div>
