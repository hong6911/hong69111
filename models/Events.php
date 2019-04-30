<?php
namespace app\models;

use app\base\Model;
use app\models\Users;

class Events extends Model
{


	//public $weight = [0.25,0.25,0.25,0.25];	// task,appointment,event,activities

	public function __construct()
	{
		parent::__construct();
		
		$this->tableName = 'events';
	}


	public function createEventRules($eventInfo,$emailList,$feelingPoint)
	{
		$successAdd = 0;
		$successAdd = 1;
		return $successAdd;
	}

	
	public function createEvent($eventInfo,$emailList,$feelingPoint)
	{	
		if($this->createEventRules($eventInfo,$emailList,$feelingPoint) == 1)
		{
			$model = new Users;
			$result = 0;
			$lastID = $this->insert($eventInfo);
			if($lastID)
			{

				$eventDetils = $model->splitEmail($emailList);
				$ownData1 = ['eventID' => $lastID, 'organise_by' => $_SESSION['userID'],'member_confirm' => 1,'feeling_point' => $feelingPoint/10];
				$ownData2 = ['memberID' => $_SESSION['userID']];
				$ownData = array_merge($ownData1,$ownData2);	
				$this->tableName = 'events_details';
				$this->insert($ownData);


				for ($i=0; $i <sizeof($eventDetils) ; $i++) 
				{ 

					$userID =$model->findMemberID($eventDetils[$i]);
					if($userID != 0)
					{
						$data1 = ['eventID' => $lastID, 'organise_by' => $_SESSION['userID']];
						$data2 = ['memberID' => $userID];
						$data = array_merge($data1,$data2);	

						$this->tableName = 'events_details';
						$this->insert($data);
						
						$data = [];
						$data2 = [];	
						
					}
					$results = 1;
				}	
			}
			return $result;

		}else{

			return $this->createEventRules($eventInfo,$emailList,$feelingPoint);

		}
		
	}

	public function updateGroup($eventID,$memberID,$data,$feelingPoint)
	{
		$model = new Users;
		$result = 0;
		$groups = $this->flexiUpdate('eventID',$eventID,$data);
		if($groups)
		{
			$this->tableName = 'events_details';
			$updateFeeling_Point = $this->flexi2Update('eventID',$eventID,'memberID',$memberID,['feeling_point' => $feelingPoint]);
			
		}
		return $result;
	}
    public function createBugEvent($userID,$time,$emailList,$feelingPoint)
	{	
		

		for ($i=1; $i <= $time ; $i++) 
		{ 
			$stringStart1 = '2019-04-';
			if($i < 10)
			{
				$stringStart2 =  '0'. $i;

			}else{
				$stringStart2 =  $i;
			}
			
			$stringStart3 = '~ 01:00 AM';
			$stringStart = $stringStart1 . $stringStart2 . $stringStart3;


			$stringEndt1 = '2019-04-';
			if($i < 10)
			{
				$stringEndt2 =  '0'. $i;

			}else{
				$stringEndt2 =  $i;
			}
			$stringEndt3 = '~ 02:00 AM';
			$stringEndt = $stringEndt1 . $stringEndt2 . $stringEndt3;

			for ($z=0; $z < 4 ; $z++) 
			{ 
				 $marks = 0;
				if($feelingPoint == 'sad')
				{
					$marks = rand(0,2)/10;
				}
				else if($feelingPoint == 'happy')
				{
					$marks = rand(8,10)/10;
				}
				else if($feelingPoint == 'random')
				{
					$marks = rand(1,9)/10;
				}
				else
				{
					$marks = rand(4,6)/10;
				}
		
				$eventInfo = [
					'title' => 'Test Title',
					'description' => 'Test Desc',
					'activities_type' => $z,
					'activities_start_date' => $stringStart,
					'activities_end_date' => $stringEndt,
					'remark' => 'Test Remark',
					'organise_by' => $userID
				];
				$result = 0;
				$this->tableName = 'events';
				$lastID = $this->insert($eventInfo);
				
				if($lastID)
				{
					$ownData = [
						'eventID' => $lastID, 
						'memberID' => $userID,
						'organise_by' => $userID,
						'member_confirm' => 1,
						'feeling_point' => $marks
					];
					$this->tableName = 'events_details';
					$this->insert($ownData);
				}// end add
				
			}
		}
		
	}
	


}



?>