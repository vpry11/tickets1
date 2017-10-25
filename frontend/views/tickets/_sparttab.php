<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;


/**
 *	Ticket spare part partial view
 */

?>

<div class="tickets-_sparttab">
	<?php if($model->isUserMaster() ) {

		ArrayHelper::multisort($model->PartsClassList,'elspcode');
		echo '<div class="row">';
        echo '  <div class="col-md-8">';
		echo      Html::dropDownList('cllist', 'null', ArrayHelper::map($model->PartsClassList,'elspcode','elspname'),['id'=>'ClassListSelect','class'=>'form-control','onChange'=>'onSelectClass()']);
        echo '  </div>';
        echo '</div>';
		echo '<div class="row">';
		echo '  <div id="PartListdiv" class="col-md-8"></div>';	// здесь будет список зап.частей для выбора
        echo '  <div class="col-md-1">'.
                  Html::input('text','','1',['id'=>'spNumInput','class'=>'form-control']).
             '  </div>';
        echo '  <div class="col-md-1">'.
                  Html::label('шт.','',['id'=>"PartUnitLabel"]).
             '  </div>';             
        echo '</div>';

		echo Html::a( Yii::t('app','Add'), ['spartadd','id'=>$model->ticket['id']], ['id'=>'add-btn','class' => 'btn btn-success','style'=>'display:none','onclick'=>'AddSPart();return(false);']); //,'style'=>'visibility:hidden'

	}?>

    <!--?php echo $this->render('_spartgrid', ['model' => $model, 'data' => $data]); ?-->

    <div id='TicketsPartsDiv'>
    <?php echo $this->render('_spartgrid', ['model' => $model]); ?>
    </div>


	<?php
	//----User is MASTER:
    if($model->isUserMaster() ) { 
    	echo Html::beginForm(['spartaddsdate','id'=>$model->ticket['id']],'get');
	        echo Html::label('Плановый срок поставки :').
	        '<div class="row">'.
	            '<div class="col-md-2">'.
	                DatePicker::widget(['name'  => 'plannedsdate','value'  => $model->ticket['tisplannedtime'],'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]).
	            '</div>'.
	            '<div class="col-md-4">'.
	            	Html::submitButton(Yii::t('app','Set'),['class'=>'submit btn btn-success'/*,'formaction'=>Url::toRoute(['spartaddsdate','id'=>$model->ticket['id']])*/ ]).' '.
	                
	            '</div>'.
	        '</div>';
        echo Html::endForm();
     }
    ?>
</div>

<?php       
// Регистрация скриптов

$addr = Url::toRoute(["get-parts-list"]);
$addr2 = Url::toRoute(['spartadd','id'=>$model->ticket['id']]); 
$addr3 = Url::toRoute(['get-part-unit']); 
$vSenderId = $model->useroprights['id'];
$vSenderdeskId = $model->useroprights['division_id'];
$script = <<< JS

function onSelectPart(){
    $("#PartUnitLabel").html('--');
    $.ajax({
    	   url: '$addr3',
    	   type: 'GET',
           data: { PartId: $("#PartSelectList").val() },
           success: function(data) {
              	$("#PartUnitLabel").html(data);
           },
           error:   function() {
              	$("#PartUnitLabel").html('XX');
           }
	});
	return true;
};

function onSelectClass(){
    // $("#add-btn").hide();
    // $("#PartListdiv").html('-Загрузка данных-');
    $.ajax({
    	   url: '$addr',
           data: {ClassStr: $("#ClassListSelect").val()},
           success: function(data) {
              	$("#PartListdiv").html(data);
              	onSelectPart();
                $("#add-btn").show();
           },
           error:   function() {
              	$("#PartListdiv").html('AJAX error!');
           }

	});
	return false;
}

$(window).load(function () {
  $("#add-btn").hide();
  onSelectClass();
});

function AddSPart(){
//    $("#add-btn").hide();
    $.ajax({
    	   url: '$addr2',
    	   type: 'POST',
           data: { spId: $("#PartSelectList").val(),
                   spNum:  $("#spNumInput").val(),
                   senderId: $vSenderId,
                   senderdeskId: $vSenderdeskId,
                   tistatus: 'MASTER_ASSIGN'
                 },
           success: function(data) {
              	$("#TicketsPartsDiv").html(data);
                $("#add-btn").show();
           },
           error:   function() {
              	$("#TicketsPartsDiv").html('AJAX error!');
           }

	});
	return false;
};


JS;
$this->registerJs($script, yii\web\View::POS_END);
?>
