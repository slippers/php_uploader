<?php
require_once './config/ConfigSettings.php';

/*
 * This class represents a single User account.
 * */
class UserAccount {
	public $Username;
	public $Group;
	private $Password;

	public function CheckPassword($password){
		if ($this->Password == $password) {
			return TRUE;
		}
		return FALSE;
	}

	public function AddUserToArray(&$userArray){
		$userArray[$this->Username] = $this;
	}

	//constructor
	function UserAccount($name, $password, $group = 'user') {
		$this->Password=$password;
		$this->Username=$name;
	}
	
	//using this to deserialize from val_export
	public static function __set_state($an_array) // As of PHP 5.1.0
	{
		$obj = new UserAccount;
		$obj->Password = $an_array['Password'];
		$obj->Username = $an_array['Username'];
		$obj->Group = $an_array['Group'];
		return $obj;
	}
	
	//setting up the congif.php file to initialize accounts.
	public static function GetUserAccounts(){
		$userAccounts = array();
		if(file_exists(ConfigSettings::userAccounts)){
			$userAccounts = require ConfigSettings::userAccounts;
		} else {
			$account = new UserAccount('admin','password');
			$account->AddUserToArray($userAccounts);
			$content = '<?php' . PHP_EOL . 'return ' . var_export($userAccounts, true) . ';';
			file_put_contents(ConfigSettings::userAccounts, $content );
			array_push($userAccounts, $account);
		}
		return $userAccounts;
	}
}

/*
 * storing user accounts in config.php file.
 * If config.php is not found then create it with the default admin account
 * If config.php is found then load the UsersAccounts defined there.
 * need to apply rights to the location where the config file lives
 * sudo chown -R www-data config
 * php will be able to create a file but will own it to.
 * To update the config.php apply ownership
 * sudo chmod 777 config.php
 * then when your done
 * sudo chmod 775 config.php
 * */
function AuthenticateUser($username, $password){
	$userAccounts = UserAccount::GetUserAccounts();
	
/* 	$userAccounts = array();
	
	if(file_exists(ConfigSettings::userAccounts)){
		$userAccounts = require ConfigSettings::userAccounts;
	} else {
		$account = new UserAccount('admin','password');
		$account->AddUserToArray($userAccounts);
		$content = '<?php' . PHP_EOL . 'return ' . var_export($userAccounts, true) . ';';
		file_put_contents(ConfigSettings::userAccounts, $content );
	}
 */	
	if (array_key_exists($username, $userAccounts)) {
		return $userAccounts[$username]->CheckPassword($password);
	}

	return FALSE;
}

function Authenticated(){
	if (isset($_SESSION['username'])) {
		return TRUE;
	}
	return FALSE;
}

function Authenticate(){

	if (isset($_POST['username']) && isset($_POST['password'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$authed = AuthenticateUser($username, $password);

		if (!$authed) {
			header('Location: ' . $_SERVER['PHP_SELF']);
		} else {
			$_SESSION['username'] = $username;
		}
	}
}

?>
