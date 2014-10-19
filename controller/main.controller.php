<?php
/**
 * This is for the front end site
 *
 * @author Animax
 */
class main extends TFController {
    public function index(){
        $view = new TFView('Frontsite/index.html');
        
//        $_clients = new Clients();
//        var_dump($_clients->find(1)->toArray());
        
        // Import NC Bar Memeber
        //WebsiteCatch::LoadingNCBarMembers();
        
        // Unzip file a.zip to folder a/
        //ZipFunction::unzip("/Volumes/Macintosh HD/Downloads/a.zip", "/Volumes/Macintosh HD/Downloads/a/", true, true);
        
        //BraintreeManager::getBraintree()->Sale('1000.00', '5105105105105100', '05', '12');
//        BraintreeManager::getBraintree()->CreateCustomerWithCreditCard("Animax","Deng", "Company", '5105105105105100', "05/12", "123");
        
        
        //var_dump(BraintreeManager::getBraintree()->FindCustomerByID(25846153));
        
//        $customers = BraintreeManager::getBraintree()->GetAllCustomer();
//        $_list = BraintreeManager::getBraintree()->ConvertCustomersToArray($customers);
//        foreach ($_list as $value) {
//            var_dump($value);
//        }
//        
//        
//        var_dump(BraintreeManager::getBraintree()->GetAllPlans());
//        
        return $view;
    }
    
    public function test_case_import() {
//        $getAllFolders = CaseManager::GetAllFolders();
//        $fileName = CaseManager::GetNextFile($getAllFolders);
//        var_dump(CaseManager::ParsingCase($fileName));
        CaseManager::Execute();
        return "";
    }
    
    public function whatwedo(){
        $view = new TFView('Frontsite/whatwedo.html');
        return $view;
    }
    public function aboutus(){
        $view = new TFView('Frontsite/aboutus.html');
        return $view;
    }
	public function privacy(){
        $view = new TFView('Frontsite/privacy.html');
        return $view;
    }
	public function agreement(){
        $view = new TFView('Frontsite/agreement.html');
        return $view;
    }
	public function faq(){
        $view = new TFView('Frontsite/faq.html');
        return $view;
    }
	public function contact(){
        $view = new TFView('Frontsite/contact.html');
        return $view;
    }
    public function subscribe(){
        $view = new TFView('Frontsite/subscribe.html');
		if (parent::has("selUserType")) {
			$userType = parent::get("selUserType");
			//0: Not Register Yet
			//1: Non-Attorney 
			//2: Individual Attorney
			//3: Law Firm Admin
			//4: Law Firm Attorney
			//5: Corporation Admin
			switch ($userType) {
				case '0':
					$view->addDisplayVar("message", "Please select an account type.");
					break;
				case '1':	
					break;
				case '2':
					//$view = new TFView('Frontsite/individual.html');
                    parent::jumpToAction("individual");
					break;
				case '3':
					//$view = new TFView('Frontsite/lawfirm.html');
                    parent::jumpToAction("lawfirm");
					break;
				case '4':
					break;
				case '5':
					parent::jumpToAction("corporation");
					break;
				default:
					break;
				
			}
		}
		return $view;
    }
    public function signin(){

        $view = new TFView('Frontsite/signin.html');
        
        if (parent::has("username") && parent::has("password")) {
            $userbase = new UserBase();
            $select = $userbase->select()->where("LoginName like ?", parent::get("username"))->
                    where("Password like ?", md5(parent::get("password")));
            
            // The sql
            //$select->__toString();
            
            $row = $userbase->fetchRow($select);
            
            $userbase->fetchRow($select);
            if ($row != null) {
				$user = array(
					'ID' => $row['ID'],
					'LoginName' => $row['LoginName'],
					'NCBarID' => $row['NCBarID'],
					'EmailAddress' => $row['EmailAddress'],
					'UserType' => $row['UserType'],
					'DisplayName' => $row['DisplayName'],
					'PhoneNumber' => $row['PhoneNumber'],
					'CreatedDate' => $row['CreatedDate']
				);
				parent::setCookie("current_user", $user);
				//0: Not Register Yet
                //1: Non-Attorney 
                //2: Individual Attorney
                //3: Law Firm Admin
                //4: Law Firm Attorney
                //5: Corporation Admin
                switch (intval($row["UserType"])) {
                    case 0:
                        $view->addDisplayVar("message", "User Not Register Yet.");
                        break;
                    case 1:
						parent::jumpToController("attorney");
                        break;
                    case 2:
                        parent::jumpToController("attorney");
                        break;
                    case 4:
                        parent::jumpToController("attorney");
                        break;
                    case 3:
						parent::jumpToController("attorney");
                        break;
                    case 5:
                        parent::jumpToController("attorney");
                        break;
                    default:
                        break;
                }
            } else {
                $view->addDisplayVar("message", "Password or username is incorrect.");
            }
        }
        return $view;
    }
    
    public function getCalendar() {
        global_function::iCalendar(time(), time(), "ical.ics", "abc_def", "description", null, "summary");
    }
	
	public function individual() {
		$view = new TFView('Frontsite/individual.html');
		
		$state = new State();
		$select = $state->select();
		$result = $state->fetchAll($select);
		$view->addDisplayVar("states", $result);
		
		return $view;
	}
	
	public function lawfirm() {
		$view = new TFView('Frontsite/lawfirm.html');
		
		$state = new State();
		$select = $state->select();
		$result = $state->fetchAll($select);
		$view->addDisplayVar("states", $result);
		
		return $view;
	}
	
	public function corporation() {
		$view = new TFView('Frontsite/corporation.html');
		
		$state = new State();
		$select = $state->select();
		$result = $state->fetchAll($select);
		$view->addDisplayVar("states", $result);
		
		return $view;
	}
	
	public function attorney() {
		if (isset($_COOKIE['current_user'])) {
			$view = new TFView('Frontsite/attorney.html');
			
			$state = new State();
			$select = $state->select();
			$result = $state->fetchAll($select);
			$view->addDisplayVar("states", $result);
			
			return $view;
		}
	}
	
	/*public function clienttype() {
		if (isset($_COOKIE['current_user'])) {
			$view = new TFView('Frontsite/clienttype.html');
		} else {
			parent::jumpToAction("signin");
		}
		return $view;
	}*/
	
	public function client() {
		if (isset($_COOKIE['current_user'])) {
			$view = new TFView('Frontsite/client.html');
			
			$state = new State();
			$select = $state->select();
			$result = $state->fetchAll($select);
			$view->addDisplayVar("states", $result);
			
		} else {
			parent::jumpToAction("signin");
		}
		return $view;
	}
	
	public function corpcontact() {
		if (isset($_COOKIE['current_user'])) {
			$view = new TFView('Frontsite/corpcontact.html');
			
			$state = new State();
			$select = $state->select();
			$result = $state->fetchAll($select);
			$view->addDisplayVar("states", $result);
			
		} else {
			parent::jumpToAction("signin");
		}
		return $view;
	}
	
	public function corpattorney() {
		if (isset($_COOKIE['current_user'])) {
			$view = new TFView('Frontsite/corpattorney.html');
			
			$state = new State();
			$select = $state->select();
			$result = $state->fetchAll($select);
			$view->addDisplayVar("states", $result);
			
			return $view;
		}
	}
	
	public function getNCBarMemberByID() {
		$view = new TFServer();
		if (parent::has("id")){
			$userbase = new UserBase();
            $select = $userbase->select()->where("NCBarID = ?", parent::get("id"))->where("Status = ?", 'Active');
			$row = $userbase->fetchRow($select);
			$view->addDisplayVar("FullName", $row['DisplayName']);
			$view->addDisplayVar("NameSuffix", $row['NameSuffix']);
			$view->addDisplayVar("EmailAddress", $row['EmailAddress']);
			$view->addDisplayVar("PhoneNumber", $row['PhoneNumber']);
			$view->addDisplayVar("Address", $row['Address']);
			$view->addDisplayVar("City", $row['City']);
			$view->addDisplayVar("State", $row['State']);
			$view->addDisplayVar("ZipCode", $row['ZipCode']);
		}
		return $view;
	}
	
	public function addattorney(){
		$view = new TFServer();
		$userbase = new UserBase();
		$pass = parent::get('Password');
		$data = array(
			'UserType' => parent::get('UserType'),
			'DisplayName' => parent::get('DisplayName'),
			'CourtCalendarName' => parent::get('CourtCalendarName'),
			'NameSuffix' => parent::get('Suffix'),
			'Company'=>parent::get('Company'),
			'EmailAddress'=>parent::get('Email'),
			'PhoneNumber'=>parent::get('Phone'),
			'Address'=>parent::get('Address'),
			'City'=>parent::get('City'),
			'State'=>parent::get('State'),
			'ZipCode'=>parent::get('Zip'),
			'LoginName' => parent::get('UserName'),
			'Password'=> md5($pass)
		);
		$where = $userbase->getAdapter()->quoteInto('NCBarID = ?', parent::get('MemberID'));
		$userbase -> update($data, $where);
	
		$view->addDisplayVar("username", parent::get('UserName'));
		$view->addDisplayVar("password", $pass);
		
		return $view;
	}
	
	public function assignattorney(){
		$view = new TFServer();
		
		if (isset($_COOKIE['current_user'])) {
			$pass = parent::get('Password');
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
			switch ($usertype) { 
				//0: Not Register Yet
				//1: Non-Attorney 
				//2: Individual Attorney
				//3: Law Firm Admin
				//4: Law Firm Attorney
				//5: Corporation Admin
				case '3':
					$userbase = new UserBase();
					$data = array(
						'UserType' => parent::get('UserType'),
						'DisplayName' => parent::get('DisplayName'),
						'CourtCalendarName' => parent::get('CourtCalendarName'),
						'NameSuffix' => parent::get('Suffix'),
						'Company'=>parent::get('Company'),
						'EmailAddress'=>parent::get('Email'),
						'PhoneNumber'=>parent::get('Phone'),
						'Address'=>parent::get('Address'),
						'City'=>parent::get('City'),
						'State'=>parent::get('State'),
						'ZipCode'=>parent::get('Zip'),
						'LoginName' => parent::get('UserName'),
						'Password'=> md5($pass)
					);
					$where = $userbase->getAdapter()->quoteInto('NCBarID = ?', parent::get('MemberID'));
					$userbase -> update($data, $where);
					
					$action = parent::get('action');
					$select = $userbase->select()->where("LoginName like ?", parent::get('UserName'))->
							where("Password like ?", md5($pass));
					$row = $userbase->fetchRow($select);
					if ($row != null) {
						$attorneyID = $row['ID'];
					}
					
					if (is_array($user)) {
						$adminID = $user['ID'];
					}
					
					$attorney = new Attorneys();
					$data = array(
						'AdminID' => $adminID,
						'AttorneyID' => $attorneyID,
						'CreatedDate' => date("Y-m-d H:i:s")
					);
					$attorney -> insert($data);
					$view->addDisplayVar("action", $action);
					break;
				case '5':
					$action = parent::get('action');
					$select = $userbase->select()->where("NCBarID = ?", parent::get('MemberID'));
					$row = $userbase->fetchRow($select);
					if ($row != null) {
						$attorneyID = $row['ID'];
					}
					
					if (is_array($user)) {
						$adminID = $user['ID'];
					}
					
					$attorney = new Attorneys();
					$data = array(
						'AdminID' => $adminID,
						'AttorneyID' => $attorneyID,
						'CreatedDate' => date("Y-m-d H:i:s")
					);
					$attorney -> insert($data);
					$view->addDisplayVar("action", $action);
					break;
			}
		} else {
			$view->addDisplayVar("message", 'Please sign in to assign attorneys.');
		}
		return $view;
	}
	
	public function addlawfirm(){
		$view = new TFServer();
		$userbase = new UserBase();
		$pass = parent::get('Password');
		$data = array(
			'UserType' => parent::get('UserType'),
			'DisplayName' => parent::get('LawFirmName'),
			'EmailAddress'=>parent::get('Email'),
			'PhoneNumber'=>parent::get('Phone'),
			'Address'=>parent::get('Address'),
			'City'=>parent::get('City'),
			'State'=>parent::get('State'),
			'ZipCode'=>parent::get('Zip'),
			'LoginName' => parent::get('UserName'),
			'Password'=> md5($pass)
		);
		$userbase -> insert($data);
		$view->addDisplayVar("username", parent::get('UserName'));
		$view->addDisplayVar("password", $pass);
		return $view;
	}
	
	public function addcorporate(){
		$view = new TFServer();
		$userbase = new UserBase();
		$pass = parent::get('Password');
		$data = array(
			'UserType' => parent::get('UserType'),
			'DisplayName' => parent::get('DisplayName'),
			'EmailAddress'=>parent::get('Email'),
			'PhoneNumber'=>parent::get('Phone'),
			'Address'=>parent::get('Address'),
			'City'=>parent::get('City'),
			'State'=>parent::get('State'),
			'ZipCode'=>parent::get('Zip'),
			'CorporationAlias1'=>parent::get('CorporationAlias1'),
			'CorporationAlias2'=>parent::get('CorporationAlias2'),
			'CorporationAlias3'=>parent::get('CorporationAlias3'),
			'CorporationAlias4'=>parent::get('CorporationAlias4'),
			'CorporationAlias5'=>parent::get('CorporationAlias5'),
			'LoginName' => parent::get('UserName'),
			'Password'=> md5($pass)
		);
		$userbase -> insert($data);
		$view->addDisplayVar("username", parent::get('UserName'));
		$view->addDisplayVar("password", $pass);
		return $view;
	}
	
	public function addclient(){
		$view = new TFServer();
		if (isset($_COOKIE['current_user'])) {
			$action = parent::get('action');
			$userbase = new UserBase();
			$pass = parent::get('Password');
			$data = array(
				'UserType' => parent::get('UserType'),
				'DisplayName' => parent::get('DisplayName'),
				'NameSuffix' => parent::get('Suffix'),
				'EmailAddress'=>parent::get('Email'),
				'PhoneNumber'=>parent::get('Phone'),
				'Address'=>parent::get('Address'),
				'City'=>parent::get('City'),
				'State'=>parent::get('State'),
				'ZipCode'=>parent::get('Zip'),
				'LoginName' => parent::get('UserName'),
				'Password'=> md5($pass)
			);
			$userbase -> insert($data);
			$select = $userbase->select()->where("LoginName like ?", parent::get('UserName'))->
						where("Password like ?", md5($pass));
			$row = $userbase->fetchRow($select);
			if ($row != null) {
				$clientID = $row['ID'];
			}
			
			$user = parent::getCookie('current_user');
			if (is_array($user)) {
				$attorneyID = $user['ID'];
			}
					
			$client = new Clients();
			$data = array(
				'AttorneyID' => $attorneyID,
				'ClientID' => $clientID,
				'CreatedDate' => date("Y-m-d H:i:s")
			);
			$client -> insert($data);
			$view->addDisplayVar("action", $action);
		} else {
			$view->addDisplayVar("message", "Please login to add client.");
		}
		return $view;
	}
	
	public function addcontact(){
		$view = new TFServer();
		if (isset($_COOKIE['current_user'])) {
			$action = parent::get('action');
			$userbase = new UserBase();
			$pass = parent::get('Password');
			$data = array(
				'UserType' => parent::get('UserType'),
				'DisplayName' => parent::get('DisplayName'),
				'NameSuffix' => parent::get('Suffix'),
				'Title' => parent::get('Title'),
				'Company' => parent::get('Company'),
				'Department' => parent::get('Department'),
				'EmailAddress'=>parent::get('Email'),
				'PhoneNumber'=>parent::get('Phone'),
				'Address'=>parent::get('Address'),
				'City'=>parent::get('City'),
				'State'=>parent::get('State'),
				'ZipCode'=>parent::get('Zip'),
				'LoginName' => parent::get('UserName'),
				'Password'=> md5($pass)
			);
			$userbase -> insert($data);
			$select = $userbase->select()->where("LoginName like ?", parent::get('UserName'))->
						where("Password like ?", md5($pass));
			$row = $userbase->fetchRow($select);
			if ($row != null) {
				$contactID = $row['ID'];
			}
			
			$user = parent::getCookie('current_user');
			if (is_array($user)) {
				$corpAdminID = $user['ID'];
			}
					
			$contact = new Contacts();
			$data = array(
				'CorporationAdminID' => $corpAdminID,
				'ContactID' => $contactID,
				'CreatedDate' => date("Y-m-d H:i:s")
			);
			$contact -> insert($data);
			$view->addDisplayVar("action", $action);
		} else {
			$view->addDisplayVar("message", "Please login to assign contacts.");
		}
		return $view;
	}
	
	public function signout(){
		$view = new TFView('Frontsite/signout.html');
		if (parent::has("signout")) {
			if(isset($_COOKIE['current_user'])){
				parent::setcookie('current_user','',time()-3600);
			}
			parent::jumpToController("main");
		}
		return $view;
    }
	
	public function forgotpassword(){
		$view = new TFView('Frontsite/forgot.html');
        if (parent::has("email")) {
			$userbase = new UserBase();
            $select = $userbase->select()->where("EmailAddress like ?", parent::get("email"));
            
            $row = $userbase->fetchRow($select);
            
            if ($row != null) {
                $loginName = $row["LoginName"];
				$sendto = parent::get("email");
				
				$token = md5(microtime (TRUE)*100000);
				$tokenToSendInMail = $token;
				$tokenToStoreInDB = hash('ripemd128', $token);
				
				if(isset($_SERVER['HTTPS'])){
					$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
				}
				else{
					$protocol = 'http';
				}
				$reseturl = $protocol . "://" . $_SERVER['HTTP_HOST']."/?a=resetpassword&token=".$tokenToSendInMail;
				
				print 'tokenToSendInMail:'.$tokenToSendInMail;
				print '<br>';
				print 'tokenToStoreInDB:'.$tokenToStoreInDB;
				print '<br>';
				print $reseturl;
				
				$subject = "Change Your Password";
				$message = "Can't remember your password? Don't worry about it — it happens.<br /><br />";
				$message .= "Your username is: ".$loginName."<br /><br />";
				$message .= "Click this link to reset your password:<br />";
				$message .= $reseturl."<br /><br />";
				$message .= "Didn't ask to reset your password?<br />";
				$message .= "If you didn't ask for your password, it's likely that another user entered your username or e-mail address by mistake while trying to reset their password. If that's the case, you don't need to take any further action and can safely disregard this e-mail.";
				/*$sent = mail($sendto, $subject, $message);
				if ($sent) {
					$view->addDisplayVar("message", "Email has been sent!");
				} else {
					$view->addDisplayVar("message", "Email has not been sent!");
				}*/
				
				$resetlog = new ResetLog();
				$data = array(
					'UserID' => $row['ID'],
					'Token' => $tokenToStoreInDB,
				);
				$result = $resetlog -> insert($data);
				//$where = $resetlog->getAdapter()->quoteInto('ID = 1');
				//$result = $resetlog -> update($data, $where);
				$view->addDisplayVar("message", "Reset Token saved!");
            } else {
				$view->addDisplayVar("message", "Email does not exist!");
			}
            }
		return $view;
        }
	
	public function resetpassword(){
		$view = new TFView('Frontsite/reset.html');
		if(isset($_COOKIE['current_user'])){
			
		} else {
			if (isset($_REQUEST['token'])) {
				$requesttoken = hash('ripemd128', $_REQUEST['token']);
				$resetlog = new ResetLog();
				$select = $resetlog->select()->where("Token = ? AND status = 1", $requesttoken);
				
				$row = $resetlog->fetchRow($select);
				if ($row != null) {
					$userbase = new UserBase();
					$name = $userbase->getUserName($row['UserID']);
					$view->addDisplayVar("userid", $row['UserID']);
					$view->addDisplayVar("username", $name);
					$view->addDisplayVar("token", $_REQUEST['token']);
				} else {
					$view->addDisplayVar("message", "Invalid token!");
				}
			} else {
				//$view = new TFView('Frontsite/signin.html');
				$view->addDisplayVar("message", "Please login to reset your password!");
			}
		}
		return $view;
	}
	
	public function savepassword(){
		$view = new TFServer();
		$userbase = new UserBase();
		$pass = parent::get('Password');
		$data = array(
			'Password' => md5($pass)
		);
		$where = $userbase->getAdapter()->quoteInto('ID = ?', parent::get('ID'));
		$userbase -> update($data, $where);
		if (parent::get('Token') != '') {
			$requesttoken = hash('ripemd128', parent::get('Token'));
			$resetlog = new ResetLog();
			$data = array(
				'Status' => '0',
				'ResetDate' => date("Y-m-d H:i:s")
			);
			$where = array(
				$resetlog->getAdapter()->quoteInto('UserID = ?', parent::get('ID')),
				$resetlog->getAdapter()->quoteInto('Token = ?', $requesttoken)
			);
			$resetlog -> update($data, $where);
		}
		//$view->addDisplayVar("token", parent::get('Token'));
		return $view;
	}
        
        public function getPlans(){
            $plans = StripeManager::GetAllPlans();
            var_dump($plans);
        }
}
