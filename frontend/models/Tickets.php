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
class Tickets extends Model
{
	public $ticket;			// the 1-dimention array  with current record in the ticket table
	public $tilogprovider;	// array with all records from ticketlog
	public $tispartprovider;// array with all records from ticketlog for spare parts
	public $fitterslist;	// the 2-dimention array  with records from the employee table
	public $useroprights;	// the 1-dimention array  ['id','division_id','oprights',] with currently logged in user rights
	public $elerrorcodelist;// the 2-dimention array  with elevator error codes
	public $spartlist;		// the 2-dimention array  with elevator error codes
	public $uploadedfilelist;	// array of file names in uploads directory for the ticket
	public $tilist;
	public $PartsClassList;

	/*---171020,did start---*/
    public static function GetPartsList($classid=0)
    {
    	if ($classid == 0) {
            $PartsList = Yii::$app->db->createCommand('SELECT id,elspname as elspart,elspunit 
            	                                       FROM elevatorsparepart 
        	                                           WHERE NOT (elspcode LIKE "%.0.0")
            	                                       ')->queryAll();	
    	}else{
            $PartsList = Yii::$app->db->createCommand('SELECT id,elspname as elspart,elspunit 
            	                                       FROM elevatorsparepart 
        		                                       where (elspcode LIKE "'.$classid.'.%.%") 
        	                                             AND NOT (elspcode LIKE "'.$classid.'.0.0")
        	                                           ')->queryAll();	
    	}
    	return $PartsList;
    }
    public static function GetPartUnit($elspid=0)
    {
    	$select= Yii::$app->db->createCommand('SELECT id, elspunit FROM elevatorsparepart WHERE id ='.$elspid.' ; ' )->queryOne();	
    	if (isset($select['elspunit'])) return $select['elspunit'];
    	else return 'шт';

    }
    /*---171020,did end---*/
	public function getTicketsList()
	{
		$tilist = Yii::$app->db->createCommand('SELECT * FROM ticket')->queryAll();
		//Yii::warning($tilist,__METHOD__);
		return $tilist;
	}	
	/**
	 * Gets all records from ticket db table, gets rights for currenly logged in user
	 * @param boolean $ticketsFilterAll - filtering condition
	 */
	public function search($ticketsFilterAll)
	{
		$tiplannedtime = $this->isUserMaster() ? 'tiplannedtimenew':'tiiplannedtime';
		//$sqltext='SELECT ticket.id as id, tipriority, ticode, tistatus, tiplannedtime,tiaddress, CONCAT(lastname," ", firstname) as executant FROM ticket left join employee on employee.id=tiexecutant_id';
		$sqltext='SELECT ticket.*, CONCAT(lastname," ", firstname) as executant FROM ticket left join employee on employee.id=tiexecutant_id';
		
		//---Prepare the sql statement for tickets according to the user rights
		if( $this->isUserMaster() ) {
			$sqltext = $sqltext.' where tidivision_id='.$this->useroprights['division_id'];
			if( !$ticketsFilterAll) $sqltext = $sqltext.' and tistatus not like "MASTER_COMPLETE"';
		} 
		else if( $this->isUserFitter() ) {
			$sqltext = $sqltext.' where tiexecutant_id='.$this->useroprights['id']. " and tistatus not like '%COMPLETE'";
		}
		else
			if( !$ticketsFilterAll)$sqltext = $sqltext." where  tistatus not like 'MASTER_COMPLETE' and tistatus not like 'DISPATCHER_COMPLETE'";
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tipriority',
					'ticode',
					'tistatus',
					'tistatustime',
					$tiplannedtime,
					'tiaddress',
					'executant'
				],
				'defaultOrder' => [ 'ticode' => SORT_DESC ],
			],
		]);
		return $provider;
	}
	/**
	 * Gets an only separate record from ticket table
	 * @param integer $id - key for current record from ticket table
	 * @return The Tickets model instance with filled members for ticket itself, user who logged in, and fitters of the user's department
     */
	public function findOne($id)
	{
		//---Get list of classes of parts // получить классификацию ремкомплекта, 171020,did
        $this->PartsClassList = Yii::$app->db->createCommand('SELECT id,elspcode,elspname FROM elevatorsparepart WHERE elspcode LIKE "%.0.0" ')->queryAll();	
		//---Get known who is current user and take all fitters from his department
		$this->useroprights = $this->getUserOpRights();
		$this->fitterslist = $this->getFittersList($this->useroprights['division_id']);

		//---Get records from ticket for given ticket
		if(!isset($this->ticket)){
			$sql4ticket=
			'SELECT ticket.*, tiobject, tiproblemtypetext,divisionname,CONCAT(lastname," ",firstname," ",patronymic) as executant from ticket 
				left join ticketobject on ticketobject.id=tiobject_id 
				left join ticketproblemtype on ticketproblemtype.id=tiproblemtype_id 
    			left join employee on employee.id=tiexecutant_id
    			left join division on division.id=tidivision_id where ticket.id='.$id; 

    		$this->ticket = Yii::$app->db->createCommand($sql4ticket)->queryOne();
    	}

		//---Get all records from ticket log
		if(!isset($this->tilogprovider)){
			$sql4tilog = 
			'SELECT ticketlog.*, CONCAT(e1.lastname," ",e1.firstname) as sender,d1.divisionname as senderdesk, CONCAT(e2.lastname," ",e2.firstname) as receiver,d2.divisionname as receiverdesk  FROM ticketlog 
					left join employee e1 on e1.id=tilsender_id 
					left join employee e2 on e2.id=tilreceiver_id
					left join division d1 on d1.id=tilsenderdesk_id
					left join division d2 on d2.id=tilreceiverdesk_id where (tiltype="WORKORDER" or tiltype="SVCORDER") and tilticket_id='.$id;
			$this->tilogprovider = new SqlDataProvider([
				'sql' => $sql4tilog,
				'key' => 'id',
				'sort' => [
					'attributes' => [
						'tiltime',
					],
					'defaultOrder' => [ 'tiltime' => SORT_DESC ],
				],
			]);
		}
		//---Get all from spare part records
		if(!isset($this->tispartprovider)){
			$sql4tispart = 
			'SELECT ticketlog.*, CONCAT(e1.lastname," ",e1.firstname) as sender,d1.divisionname as senderdesk, CONCAT(e2.lastname," ",e2.firstname) as receiver,d2.divisionname as receiverdesk  FROM ticketlog 
					left join employee e1 on e1.id=tilsender_id 
					left join employee e2 on e2.id=tilreceiver_id
					left join division d1 on d1.id=tilsenderdesk_id
					left join division d2 on d2.id=tilreceiverdesk_id where tiltype="SPORDER" and tilticket_id='.$id;
			$this->tispartprovider = new SqlDataProvider([
				'sql' => $sql4tispart,
				'key' => 'id',
				'sort' => [
					'attributes' => [
						'tiltime',
					],
					'defaultOrder' => [ 'tiltime' => SORT_DESC ],
				],
			]);
			//$this->ticketlog = Yii::$app->db->createCommand($sql4tilog)->queryAll();
		}
		//---Get spare part catalog
		if(!isset($this->spartlist)){
			$this->spartlist = Yii::$app->db->createCommand('SELECT id,CONCAT(IFNULL(elspcode,"")," ",elspname) as elspart,elspunit FROM elevatorsparepart')->queryAll();	
		}
		//---Get error codes for dropdown list
		if(!isset($this->elerrorcodelist)){
			$this->elerrorcodelist = Yii::$app->db->createCommand('SELECT elerrorcode as errorcode,CONCAT(elerrorcode," ",elerrortext) as errortext FROM elevatorerrorcode')->queryAll();
		}
		//---Get all uploaded files for the ticket
		if(!isset($uploadedfilelist)){
			$this->uploadedfilelist=UploadImage::getUploadedFileList($this->ticket['ticode'].'*');
		}
		return  $this;
	}
	/**
     *  Gets the info on rights, id and division for currently logged in user
     * @return mixed, array ['id','division_id','oprights'] if user is logged in and have a rights for some operations, boolean FALSE otherwise
     *
     */
    public static function getUserOpRights()
    {
        if(Yii::$app->user->isGuest) return FALSE;	// user is not currently logged in

        return Yii::$app->db 	// may be FALSE, if user have not a corresponding record in employee table
        	->createCommand('SELECT id,division_id,oprights from employee where user_id=:uid')->bindValues([':uid'=>Yii::$app->user->id])
        	->queryOne();
    }
	/**
	 * Tests if the currently logged in user have a Master rights
	 * @return boolean result
     */
	public function isUserMaster()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'M') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Tests if the currently logged in user have a foreman rights
	 * @return boolean result
     */
	public function isUserFitter()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'F') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Builds the array of employees [[0]=>['id','name'],...] for given division who are the fitters ( occupation_id = 3 )
	 * @param integer $divisionId - key for the record in division table
	 * @return mixed, string if user is logged in and have a rights for some operations, FALSE otherwise
     */
	protected function getFittersList($divisionId)
	{
		return Yii::$app->db
			->createCommand('SELECT id,concat(lastname," ",firstname," ",patronymic) as name FROM employee where occupation_id=3 and division_id=:id order by name')
			->bindValues([':id' => $divisionId])
			->queryAll();
	}
	/**
	 * Sets the tilreadflag in ticket log for record with newest time !FOR LOGGED IN USER!
	 * @param integer $id - ticket id
     */
	public static function setReadFlag($id){
		if( FALSE === ( $receiver = Tickets::getUserOpRights() ) ) return;
		//--Here 1 version - set readflag in ticketlog
		$result = Yii::$app->db->createCommand('SELECT tiltime, id FROM ticketlog WHERE tilticket_id = '.$id.' AND tilreceiver_id = '.$receiver['id'].' ORDER BY tiltime DESC LIMIT 1' )->queryOne();		
		Yii::$app->db->createCommand()->update('ticketlog',['tilreadflag'=>'1'],['id'=>$result['id']])->execute();
		//--Here 2 version - set readflag in ticket
		Yii::$app->db->createCommand()->update('ticket',['tiexecutantread'=>'1'],['id'=>$id,'tiexecutant_id'=>$receiver['id']] )->execute();
	}
	/**
	 * Sets the tilreadflag in ticket log for record with newest time !FOR LOGGED IN USER!
	 * @param integer $id - ticket id
	 * @param integer $receiver -  id of person to whom message been sent
     */
	public static function isTicketBeenRead( $id, $receiver ){
		if(!isset($receiver))return FALSE;
		$result = Yii::$app->db->createCommand('SELECT tiltime, tilreadflag FROM ticketlog WHERE tilticket_id = '.$id.' AND tilreceiver_id = '.$receiver.' ORDER BY tiltime DESC LIMIT 1' )->queryOne();		
		Yii::warning('READ==='.$result,__METHOD__);
		return $result['tilreadflag'] ? TRUE : FALSE;
	}
	
}