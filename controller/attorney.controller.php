<?php
/**
 * Description of attorney
 *
 * @author denganimax
 */
class attorney extends TFController  {
    public function index(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/index.html');
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
    }
	
	public function client(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/client.html');
			
			$userbase = new UserBase();
			$clients = $userbase->getClientList($user['ID']);
			if ($clients != '') {
				$view->addDisplayVar("clients", $clients);
			}
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
    }
	
	public function contact(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/contact.html');
			
			$userbase = new UserBase();
			$contacts = $userbase->getContactList($user['ID']);
			if ($contacts != '') {
				$view->addDisplayVar("contacts", $contacts);
			}
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
    }
	
	public function attorney(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/attorney.html');
			
			$userbase = new UserBase();
			$attorneys = $userbase->getAttorneyList($user['ID']);
			if ($attorneys != '') {
				$view->addDisplayVar("attorneys", $attorneys);
			}
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
    }
	
	public function corporate(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/corporate.html');
			
			$userbase = new UserBase();
			$corporates = $userbase->getCorporateList($user['ID']);
			if ($corporates != '') {
				$view->addDisplayVar("corporates", $corporates);
			}
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
    }
	
	public function subscription(){
		if (isset($_COOKIE['current_user'])) {
			$user = parent::getCookie('current_user');
			$uid = $user['ID'];
			$usertype = $user['UserType'];
        	$view = new TFView('Attorneysite/subscription.html');
			
			$state = new State();
			$select = $state->select();
			$result = $state->fetchAll($select);
			$view->addDisplayVar("states", $result);
			
			$userbase = new UserBase();
			$select = $userbase->select()->where("ID = ?", $uid);
			$row = $userbase->fetchRow($select);
					
			$view->addDisplayVar("user", $row);
			$view->addDisplayVar("usertype", $usertype);
		} else {
			parent::jumpToController("main","signin");
		}
		return $view;
	}
	
	public function updateAttorney(){
		$view = new TFServer();
		$userbase = new UserBase();
		$data = array(
			'DisplayName' => parent::get('DisplayName'),
			'NameSuffix' => parent::get('Suffix'),
			'EmailAddress'=>parent::get('Email'),
			'PhoneNumber'=>parent::get('Phone'),
			'Address'=>parent::get('Address'),
			'City'=>parent::get('City'),
			'State'=>parent::get('State'),
			'ZipCode'=>parent::get('Zip')
		);
		$where = $userbase->getAdapter()->quoteInto('ID = ?', parent::get('ID'));
		$userbase -> update($data, $where);
		
		return $view;
	}
	
	public function changePassword(){
		$view = new TFServer();
		$userbase = new UserBase();
		$pass = parent::get('Password');
		$data = array(
			'Password' => md5($pass)
		);
		$where = $userbase->getAdapter()->quoteInto('ID = ?', parent::get('ID'));
		$userbase -> update($data, $where);
		return $view;
	}
	
	public function cancelSubscription(){
		$view = new TFServer();
		$userbase = new UserBase();
		$data = array(
			'Payment' => '0'
		);
		$where = $userbase->getAdapter()->quoteInto('ID = ?', parent::get('ID'));
		$userbase -> update($data, $where);
		return $view;
	}
}
