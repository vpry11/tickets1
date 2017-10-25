<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use frontend\models\Tickets;
use frontend\models\TicketAction;
use frontend\models\UploadImage;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Tickets controller
 *	!!!It's an example code for how to pass parameters to view!!!
 *	There are 2 methods to pass parameters to view: 
 *	1.push: pass to view any number of variables - in an associative array (see below how)
 *	2.pull: use in view $this->context to access all members of a controller class (see below how)
 *
 */
class TicketsController extends Controller
{
	/*	2. HOW TO PASS PARAMETERS TO VIEW = PULL !!!
		------------------------------------
		Passing parameters to view using 'pull' method, 		
		It is possible to access the controller's methods and members inside the view, via $this->context
		For example below inside the view may be used such code: $this->context->tilist2 for accessing  the member, 
		which we should initialize before, in class constructor:*/
	public $tilist2;
	function __construct($id, $module, $config = []) {
		 parent::__construct($id, $module, $config);	
		 $ticketsModel = new Tickets();
		 $this->tilist2 = $ticketsModel->getTicketsList();
	}
	 
    /*public function actionIndex(){
        $tiall = isset($session['ticketsFilterAll']) ? $session['ticketsFilterAll'] : 0;
        $this->redirect(['indexf','tiall' => $tiall]);
        
    }*/
	public function actionIndex()	// http://yii2-advanced-frontend/index.php?r=tickets%2Findex
    {
        //---Get the filtering conditions from session variable & request
        $session = Yii::$app->session;
        $ticketsFilterAll = Yii::$app->request->get('tiall');
        if( is_null($ticketsFilterAll) ) $ticketsFilterAll = $session['ticketsFilterAll'];
        else if($session['ticketsFilterAll'] != $ticketsFilterAll )$session['ticketsFilterAll'] = $ticketsFilterAll;
        $ticketsFilterAll = is_null($ticketsFilterAll) ? FALSE : $ticketsFilterAll;
        //Yii::warning(Yii::$app->request->isPjax?'!!!!!GOT Pjax!!!!! ':'IndexController got NOT Pjax',__METHOD__);

    	$ticketsModel = new Tickets();
    	$provider = $ticketsModel->search($ticketsFilterAll);

    	//--- 1. HOW TO PASS PARAMETERS TO VIEW = PUSH!!!
    	//------------------------------------
    	//--- Passing parameters to view using 'push' method, 
    	//--- by passing the data as the second parameter to the view rendering methods,
    	//--- which should be an associative array. 
    	//--- View rendering methods call PHP extract() to import array keys into the local symbol table as variables.
    	//--- Inside the view for this example	 variable $tilist1 will be accessible: 
    	//\Yii::$app->language = 'ru-RU';
        if(Yii::$app->request->isAjax)
            return $this->renderpartial( '_index', ['provider'=>$provider, 'model'=>$ticketsModel,'tiall'=>$ticketsFilterAll ] );
        else 
            return $this->render( 'index', [/*'tilist1' => $ticketsModel->getTicketsList(),*/'provider'=>$provider, 'model'=>$ticketsModel,'tiall'=>$ticketsFilterAll ] );
    }

	public function actionView($id)	// http://tickets/index.php?r=tickets%2Fview&id=X
	{
        $imagemodel = new UploadImage();
        Tickets::setReadFlag($id);
        return $this->render('view', [
            'model' => $this->findModel($id),'imagemodel'=>$imagemodel
        ]);
	}
	public function actionAppoint($tistatus)    // http://tickets/index.php?r=tickets%2Fappoint&id=X&list=X
    {   
        //Yii::warning(Yii::$app->request->post(),__METHOD__);
        $model = new TicketAction();
        $data = Yii::$app->request->post();
        if( isset( $data['ticketId']) && isset($tistatus) ){
            $model->tistatus    = $tistatus;
            $model->tiplannedtimenew = $data['ticketplanneddate'];
            $model->tiiplannedtime = $data['fitterplanneddate'];
            $model->ticketId    = $data['ticketId'];
            $model->tiltext     = $data['tiltext'];
            $model->receiverId  = $data['receiverId'];
            $model->senderId    = $data['senderId'];
            $model->senderdeskId = $data['senderdeskId'];
            $model->errorcode = $data['errorcode'];

            $model->actor = $data['actor'];
            //if( $tistatus=='MASTER_ASSIGN_DATE' )   $model->updateTiplanneddate();
            /*else*/                                    $model->save();
        }
        else {
            echo ('Unknown error in '. __METHOD__.',line '.__LINE__);
        }
        return $this->redirect(['view','id'=>$data['ticketId']]);
    }

    public function actionSpartaddsdate($id,$plannedsdate)
    {
        TicketAction::savespartdate($id,$plannedsdate);
        $this->redirect(['view','id'=>$id]);//print_r($data);
    }
    
    /*--- 171020, DIDENKO, new spare part logic ---*/
    public function actionSpartadd($id)
    {
        $data = Yii::$app->request->post();

        TicketAction::savespart($id,$data);
        return $this->renderpartial('_spartgrid', ['model' => $this->findModel($id)]);
    }
    public function actionSpartdelete($id,$spartid)
    {
        TicketAction::deletespart($spartid);
          //if (Yii::$app->request->isAjax)
          //  return $this->renderpartial('_spartgrid', ['model' => $this->findModel($id)]);
          //else
          //  $this->redirect(['view','id'=>$id]);//print_r($data);   
        $imagemodel = new UploadImage();
        return $this->render('view', [
            'model' => $this->findModel($id),'imagemodel'=>$imagemodel
        ]);            
    }
    public function actionGetPartsList($ClassStr='0.0.0')
    {
        $classid = $ClassStr + 0.0;     // Берем только первую цифирь
        $res = Html::dropDownList('PartList', 1, ArrayHelper::map( Tickets::GetPartsList($classid) ,'id','elspart'),['id'=>'PartSelectList','class'=>'form-control', 'onChange'=>'onSelectPart()']);
        //$res = Html::dropDownList('PartList', 1, ArrayHelper::map( Tickets::GetPartsList($classid),'elspart','elspunit' ,'id'),['id'=>'PartSelectList','class'=>'form-control', 'onChange'=>'onSelectPart()']);        
        //return var_dump(ArrayHelper::map( Tickets::GetPartsList($classid),'elspart','elspunit' ,'id'));
        //$res =  Html::dropDownList('PartList', 1, ['1'=>'item 1', '2'=>'item 2'],['id'=>'PartSelectList','class'=>'form-control', 'onChange'=>'onSelectPart()']);        
        return $res;
    }

    public function actionGetPartUnit($PartId = '0')
    {
        //return $PartId;
        return Tickets::GetPartUnit($PartId);
    }
    /*--- 171020, DIDENKO, new spare part logic ---*/

    public function actionUpload($id,$ticode)
    {
      $model = new UploadImage();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload($ticode)) {
                // file is uploaded successfully
                return $this->redirect(['view','id'=>$id]);;
            }
        }   
        return $this->redirect(['index']);//$this->redirect(['view','id'=>$id]);
    }
    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
    	$model = new Tickets();
        if (($model->findOne($id)) !== null) {
        	//Yii::warning('1Ticode='.$model->ticode,__METHOD__);
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
    