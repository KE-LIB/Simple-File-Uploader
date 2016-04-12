# Simple-File-Uploader
Simple PHP based PDF Uploader with LDAP authentication (using adLDAP)

#Install:

============ Ha engedélyezve van az LDAP akkor elég a 3. pont ==================

1.	php.ini: ldap engedélyezése bejelentkezéshez 
	ezt ;extension=php_ldap.so -> erre: extension=php_ldap.so (uncomment)
	
2.	apache restart 
	sudo /etc/init.d/apache2 restart

================================================================================
	
3.	~analitika/lib/config.php 
	mentés helyének megadása: setSaveDir('./files/')
	adatbázis adatok: setDBConnection("host","db","mysql_user","mysql_psw");)
	url feloldó szkript helye: setURLSkip("http://localhost/analitika/handle/","files_mappa_helye");
	
4.	~analitika/handle/ mappa másolása  a 3. pont setURLScript-ben megadott helyre

	
	