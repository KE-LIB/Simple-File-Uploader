# Simple-File-Uploader
	Simple PHP based PDF Uploader with LDAP authentication (using adLDAP)

#Install:

============ If LDAP is already enabled, start at 3. ==================

1.	php.ini: enable ldap 
	uncomment this line ;extension=php_ldap.so 
	
2.	apache restart 
	example: sudo /etc/init.d/apache2 restart
	
3.	~Simple-File-Uploader/lib/config.php 
	set uploaded file directory path: setSaveDir('./files/')
	db connection: setDBConnection("host","db","mysql_user","mysql_psw");)
	redirect url path: setURLSkip("http://localhost/analitika/handle/","files_mappa_helye");
	
4.	~Simple-File-Uploader/handle/ copy to setURLScript path

	
	