<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;


/*
 * This is example code for how to get data from db:
 *	1. With createComman: see dgetTicketsList()
 *	2. With SqlDataProvider: see search()
 */
class TicketAction extends Model
{
	public $ticketId;		// ticket id, filled from post 
	public $senderId;		// currently logged in user id, filled from post 
	public $senderdeskId;	// currently logged in user department id, filled from post 
	public $tiltext;		// operation description, filled from post 
	public $tistatus;		// operation description, filled from get
	public $tiplannedtimenew;// planned time for ticket
	public $tiiplannedtime;	// planned time for fitter
	public $errorcode;		// error code set by fitter in his report
	public $actor;			// 'FOREMAN' or 'FITTER', filled from post 
	
	public $receiverId;		// got it from post only for case the foreman is current user, otherwise should get it from db
	public function updateTiplanneddate()
	{
		$this->tiplannedtimenew = $this->tiplannedtimenew.'T17:00';
		Yii::$app->db->createCommand()->update('ticket',['tiplannedtimenew'=>$this->tiplannedtimenew],['id'=>$this->ticketId])->execute();
	}
	public function save()
	{
		$opdate = date("Y-m-d H:i:s");

		if( $this->actor =='MASTER' ) {
			$executant = $this->receiverId;
			//---Getting the department id of receiver by his id
			$result=Yii::$app->db->createCommand('SELECT division_id from employee where id=:empid')->bindValues([':empid'=>$this->receiverId])->queryOne();
			$receiverdeskId = $result['division_id'];
			$this->errorcode='-';
		}
		else if( $this->actor == 'FITTER' ) { 
			// should find  the receiverId (who had placed the workorder) if fitter is current user
			$result=Yii::$app->db->createCommand('SELECT tiltime, tilsender_id,tilsenderdesk_id from ticketlog where tiltype like "WORKORDER" and tilstatus like "MASTER_%ASSIGN" and tilreceiver_id=:empid ORDER BY tiltime LIMIT 1')->bindValues([':empid'=>$this->senderId])->queryOne();
			$this->receiverId = $result['tilsender_id']; 
			$receiverdeskId = $result['tilsenderdesk_id']; 
			$executant = $this->senderId;
			$errortext = Yii::$app->db->createCommand('SELECT elerrortext from elevatorerrorcode where elerrorcode=:errorcode')->bindValues([':errorcode'=>$this->errorcode])->queryOne()['elerrortext'];
			$fields4update[] = ['tiresulterrorcode'=>$this->errorcode];
		}
	// Update ticket - set new status

		$fields4update=[
				'tistatus'=>$this->tistatus,
				'tistatustime'=>$opdate,
				'tiexecutant_id'=>$executant,
				'ticlosedtime'=>$closedtime,
				'tiresumedtime'=>$resumedtime
		];
		if( $this->actor =='MASTER' ) switch($this->tistatus){
				case 'MASTER_ASSIGN':   $markasunread = TRUE; $tilplannedtime=$this->tiiplannedtime.'T17:00'; break;
				case 'MASTER_REASSIGN': $markasunread = TRUE; $tilplannedtime=$this->tiiplannedtime.'T17:00'; break;
				case 'MASTER_COMPLETE': $resumedtime = $opdate; $closedtime=$opdate; break;
				case 'MASTER_REFUSE':
					$this->receiverId=null;
					$markasunread = FALSE; 
					$fields4update['tiexecutant_id']=null;
				break;
				case 'MASTER_ASSIGN_DATE': 
					$this->receiverId=null;
					unset($fields4update['tiexecutant_id']);
					$this->tiltext='Новый срок: '.$this->tiplannedtimenew.' 17:00';
					$tilplannedtime=$this->tiplannedtimenew.'T17:00';
					$fields4update['tiplannedtimenew'] = $tilplannedtime;
					
				break;
		}
		if($this->tiiplannedtime)	{ $this->tiiplannedtime = $this->tiiplannedtime.'T17:00'; $fields4update['tiiplannedtime'] = $this->tiiplannedtime; }
		if( $markasunread )			$fields4update['tiexecutantread'] = null;	// mark as unread
		if($this->actor == 'FITTER'){
			$fields4update['tiresulterrorcode'] = $this->errorcode;
			$fields4update['tiresulterrortext'] = $errortext;
		}
		//Yii::warning($fields4update,__METHOD__);
		Yii::$app->db->createCommand()->update('ticket',$fields4update,['id'=>$this->ticketId])->execute();
		Yii::$app->db->createCommand()->insert('ticketlog',[
			'tiltime'       => $opdate,
			'tilplannedtime'=>$tilplannedtime,
			'tiltype'       => 'WORKORDER',
			'tiltext'		=> $this->tiltext,
			'tilstatus'     => $this->tistatus, 
			'tilerrorcode'	=> $this->errorcode,
			'tilticket_id'  => $this->ticketId,
			'tilsender_id'	=> $this->senderId,
			'tilsenderdesk_id'	=> $this->senderdeskId,
			'tilreceiver_id'=> $this->receiverId,
			'tilreceiverdesk_id'	=> $receiverdeskId
			])->execute();
		}
	/*---171020,did start---*/
	public static function savespart( $ticketId, $data ){
		$opdate = date("Y-m-d H:i:s");
		$spId = $data['spId'];
		$spartlist = Yii::$app->db->createCommand('SELECT id,CONCAT(IFNULL(elspcode,"")," ",elspname) as elspart,elspunit FROM elevatorsparepart WHERE id ='.$spId)->queryOne();
		Yii::$app->db->createCommand()->insert('ticketlog',[
				'tiltime'       => $opdate,
				'tiltype'       => 'SPORDER',
				'tiltext'		=> 'Заказ МТЦ',
				'tilstatus'     => $data['tistatus'], 
				'tilspcode'	    => $spId,
				'tilspname'     => $spartlist['elspart'],
				'tilspunit'	    => $spartlist['elspunit'],
				'tilspquantity'	=> $data['spNum'],
				'tilticket_id'  => $ticketId,
				'tilsender_id'	=> $data['senderId'],
				'tilsenderdesk_id'	=> $data['senderdeskId'],
				'tilreceiver_id'=> $data['receiverId'],
				'tilreceiverdesk_id'	=> $data['receiverdeskId']
			])->execute();
	}
	/*---171020,did end---*/
	public static function savespartdate($id,$plannedsdate)
	{
		$plannedsdate = $plannedsdate.'T17:00';
		Yii::$app->db->createCommand()->update('ticket',['tisplannedtime'=>$plannedsdate],'id='.$id)->execute();

	}
	public static function deletespart($id)
	{
		Yii::$app->db->createCommand()->delete('ticketlog','id='.$id)->execute();		
	}
}
