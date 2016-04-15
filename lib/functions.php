<?php
/**
 * @copyright   Copyright (C) 2016 Kaposvári Egyetem Egyetemi Könyvtár
 */
 
require_once(dirname(__FILE__) . '/adLDAP.php');

class Functions{

	/**
	 * A mentés helye - globális változó
	 *
	 * @var    string
	 */
	protected $target_dir;

	/**
	 * Adatbázis host - általában localhost
	 *
	 * @var    string
	 */
	protected $dbHost;

	/**
	 * Az adatbázis neve
	 *
	 * @var    string
	 */
	protected $dbName;

	/**
	 * Adatbázis felhasználó
	 *
	 * @var    string
	 */
	protected $dbUser;

	/**
	 * Adatbázis jelszó
	 *
	 * @var    string
	 */
	protected $dbPassword;

	/**
	 * url cél 
	 *
	 * @var    string
	 */
	protected $url;
	
	/**
	 * files mappa helye pdf-ek tárolási helye 
	 *
	 * @var    string
	 */
	protected $filesDir;
	
	/**
	 * lista link cél 1 rész
	 *
	 * @var    string
	 */
	protected $preURL;
	
	/**
	 * lista link cél 2 rész
	 *
	 * @var    string
	 */
	protected $postURL;
	
	/**
	 *  A globális $target_dir vááltozó értékének beállítása
	 *
	 *  @return nincs 
	 */ 
	function setSaveDir($path){
		
		global $target_dir;
		
		$target_dir = $path; 
	}
	
	/**
	 *  A globális $db változók értékeinek beállítása az adatbáziskapcsolathoz
	 *
	 *  @return nincs 
	 */ 
	function setDBConnection($host,$name,$usr,$psw){
		
		global $dbHost, $dbName, $dbUser, $dbPassword;
		
		$dbHost = $host;
		$dbName = $name;
		$dbUser = $usr;
		$dbPassword = $psw;
	}
	
	/**
	 *  A globális $url változó értékének beállítása
	 *
	 *  @return nincs 
	 */ 
	function setURLSkip($setUrl, $setFile){
		
		global $url, $filesDir;
		
		$url = $setUrl;
		$filesDir = $setFile;
	}
	/**
	 *  nyelvi elemek betöltése konstansokba
	 *
	 *  @return nincs 
	 */ 
	function setLanguage($lang_code){
	
		$xml=simplexml_load_file("./lang/".$lang_code) or die("Error: Cannot create object");

		foreach($xml->children() as $key=>$value)
		{ 
			define($key,$value);
		}
	}
	
	/**
	 *  A globális $target_dir vááltozó értékének beállítása
	 *
	 *  @return nincs 
	 */ 
	function setListLinks($setPreURL, $setPostURL){
		
		global $preURL,$postURL;
		$preURL = $setPreURL; 
		$postURL = $setPostURL; 
	}
	
	/**
	 *  LDAP bejelentkezés kezdeményezése és session beállítása
	 *
	 *  @return nincs 
	 */ 
	function authenticateUser($username, $password){

		$adldap = new adLDAP();
		$adldap->connect();
		$succesLDAP = $adldap->authenticate($username,$password,$prevent_rebind=false);
		$userName = ($adldap->user_info($username,$fields=NULL,$isGUID=false)[0]['displayname'][0]);
		
		if($succesLDAP==1)
		{
			$_SESSION["logged"]=$succesLDAP;
			$_SESSION["name"]=$userName;
			$_SESSION["user"]=$username;
			
			$this->uploadForm();
			$this->elementList();
		}
		else
		{
			$this->destroySession();
		}
	}

	/**
	 *  Törli s sessionban beállított értékeket és átirányit a gyökérbe - index.php
	 *
	 *  @return nincs 
	 */ 
	function destroySession(){
		
		session_unset();
		session_destroy(); 
		header('Location: ./');
	}

	/**
	 *  A sessionban tárolt 'logged' változó értéket ellenőrzi 
	 *
	 *  @return nincs 
	 */ 
	function showForms(){

		if(isset($_SESSION["logged"]))
		{	
			if(($_SESSION["logged"]==1))
			{	
				//sikeres bejelentkezés
				$this->uploadForm();
				$this->elementList();
			}
			else
			{
				//ha a logged értéke nem 1 - sikertelen bejelentkezés 
				$this->destroySession();
			}
		}	
		else
		{
			//bejelentkező form mutatása - sikertelen bejelentkezés 
			$this->loginForm();
		}
	}

	/**
	 *  Feltöltési form HTML elemeit jeleníti meg
	 *
	 *  @return nincs 
	 */ 
	function uploadForm(){
		
		echo "<b>".WELCOM_TEXT."</b> ".$_SESSION["name"]." <b>|</b> <a href='index.php?destroy'>".LOGOUT_BUTN."</a><br><br>";
		?>
		<form action="" method="post" enctype="multipart/form-data">
		<label for="fileName"><?php echo UPLOAD_NAME; ?></label>
		<input type="text" class="form-control" name="fileName" id="fileName" pattern="[\d]{3,9}" placeholder="<?php echo UPLOAD_REST; ?>" required autofocus><br>
		<input class="btn btn-warning btn-block " type="file" name="fileToUpload" id="fileToUpload" required><br>
		<input type="submit" class="btn btn-primary btn-block" value="<?php echo UPLOAD_BUTN; ?>" name="submit">
		</form>
		<?php
		if(isset($_POST['submit']))
		{
			global $target_dir;
			$orig_file = basename($_FILES["fileToUpload"]["name"]);
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$newname =  $target_dir.basename($_POST['fileName']).".pdf";
			//=================== Fájl feltöltés hibakezelés ===================//
			echo "<br>";	
			if (file_exists($newname))
			{
				?>	
				<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"><?php echo ERROR_TEXT; ?></span>
				<?php echo ERROR_DUPL; ?></div>
				<?php
				$uploadOk = 0;
			}
		
			if($FileType != "pdf") 
			{
				?>	
				<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"><?php echo ERROR_TEXT; ?></span>
				<?php echo ERROR_PDF; ?>
				</div>
				<?php
				$uploadOk = 0;
			}
			//=================== hibakezelés VÉGE ===================//
			if ($uploadOk == 0)
			{
				//hiba történt a feltöltéskor - sikertelen
				?>	
				<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"><?php echo ERROR_TEXT; ?></span>
				<?php echo ERROR_NOUP; ?></div>
				<?php
			}
			else
			{
				//sikeres feltöltés
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
				{
					$newname =  $target_dir.basename($_POST['fileName']).".pdf";
					$newfilename =  basename($_POST['fileName']).".pdf";
					//a feltöltött fájl átnevezése a feltöltéskor megadott névre
					rename($target_file, $newname);
					
					//feltötlés rögzítése az adatbázisba
					$this->createSQL("INSERT INTO uploads (`username`, `orig_filename`,`new_filename`) VALUES ('".$_SESSION["user"]."','".$orig_file."','".$newfilename."');");
				}
				else
				{
				//hiba történt a feltöltéskor - sikertelen
				?>	
				<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only"><?php echo ERROR_TEXT; ?></span>
				<?php echo ERROR_WHUP; ?>
				</div>
				<?php
				}
			}
		}
	}

	/**
	 *  Feltöltött állományok megjelenítése táblázatos formában
	 *
	 *  @return nincs 
	 */ 
	function elementList(){

		global $url, $filesDir, $preURL, $postURL;
		$i=1;
		
		echo "<br><br><p><b>".LIST_TITLE."</b></p>
		<table class='table table-striped '>
		<thead>
		<tr>
			<th>".LIST_RWNUM."</th>
			<th class='success'>".LIST_FLINK."</th>
			<th>".LIST_OLINK."</th>
			<th>".LIST_DATE."</th>
		</tr>
		</thead>
		<tbody>";
		$list=$this->createSQL("SELECT * FROM uploads WHERE username='".$_SESSION["user"]."' ORDER BY timestamp DESC LIMIT 15");
		while($record=$this->getSQL($list))
		{
			$recordNumber  = preg_replace("#(.pdf)#","",$record['new_filename']);
			
			echo "
			<tr>
				<td>".$i."</td>
				<td class='success'>

				<div class='input-group'>
				<input type='text' id='copytext' class='form-control' value='".$url."?".$recordNumber."+".$filesDir."' size='10'/>
				<span class='input-group-btn'>
				<button title='".UPLOAD_CPTOOLT."' onClick='copyToClipboard();' class='btn btn-default'><span class='glyphicon glyphicon-copy'></span></button>
				<a type='button' title='".UPLOAD_SHTOOLT."' class='btn btn-default' target='_blank' href='".$url."?".$recordNumber."+".$filesDir."'>
				<span class='glyphicon glyphicon-search'></span>
				</a>
				</span>
				</div>
				</td>
				<td><a target='_blank'href='".$preURL.$recordNumber.$postURL."'>".LIST_TEXT." (".$recordNumber.")</a></td>
				<td>".$record['timestamp']."</td>
			</tr>";
			
			$i++;
		}
		echo "</tbody>
		</table>";
		
				?>
<script>
	function copyToClipboard()
	{
		var text = document.getElementById('copytext').select();
		var successful = document.execCommand('copy');
	}


</script>
		<?php
	}

	/**
	 *  Bejelentkezési form megjelenítése
	 *
	 *  @return nincs 
	 */ 
	function loginForm(){
		?>
		<form action="" method="POST">
		<label for="username"><?php echo LOGIN_USER; ?></label><input class="form-control" id="username" type="text" name="username" autofocus/> <br>
		<label for="password"><?php echo LOGIN_PASW; ?></label><input  class="form-control" id="password" type="password" name="password" /><br>
		<input type="submit" class="btn btn-primary btn-block" name="login" value="<?php echo LOGIN_BUTN; ?>" />
		</form>
		<?php
	}

	/**
	 *  SQL lekérdezés készítése  - wrapper
	 *
	 *  @return az SQl lekérdezés eredménye
	 */ 
	function createSQL($query){
		
		$result = mysqli_query($this->getDatabaseConnect(),$query);
		
		return $result;
	}

	/**
	 *  a lekérdezett SQL-ből megjeleníthető eredményt készít  - wrapper
	 *
	 *  @return az SQl lekérdezés eredménye tömbként
	 */ 
	function getSQL($list){
		
		$result = mysqli_fetch_array($list);
		
		return $result;
	}
	
	/**
	 *  Adatbáziskapcsolat adatai és lokalizáci
	 *
	 *  @return sikeres | sikertelen kapcsolat
	 */ 
	function getDatabaseConnect()
	{
		global $dbHost, $dbName, $dbUser, $dbPassword;
		
		//adatbázis és tábla ellenőrzése
		$this->createDbAndTableIfNotExist();

		$connect_db=mysqli_connect($dbHost,$dbUser,$dbPassword,$dbName)or die("Connect error");
		mysqli_set_charset($connect_db,"utf8");
		
		return $connect_db;
	}
	/**
	 *  Adatbázis és tábla létrehozáas ha nem létezik
	 *
	 *  @return nincs 
	 */ 
	function createDbAndTableIfNotExist(){
	
		global $dbHost, $dbName, $dbUser, $dbPassword;
		
		$mysqli = new mysqli($dbHost,$dbUser,$dbPassword);
		if($mysqli->select_db($dbName)==false)
		{
			$mysqli->query("CREATE DATABASE ".$dbName."");
		}
		$mysqli->close();
		
		$mysqli = new mysqli($dbHost,$dbUser,$dbPassword,$dbName);
		$exist = $mysqli->query("SELECT * FROM uploads ");
		if(empty($exist))
		{
			$mysqli->query("CREATE TABLE `uploads` (`id` int(11) NOT NULL AUTO_INCREMENT,`username` text NOT NULL,`orig_filename` text NOT NULL,`new_filename` text NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		}
		$mysqli->close();
	}
	
}
?>