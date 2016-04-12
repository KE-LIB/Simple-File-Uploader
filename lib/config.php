<?php
/** 
 * használat előtt példányosítani kell!!! a fájlban 
 * a functions.php Functions osztályát $page->new Functions();
 *
 * @copyright   Copyright (C) 2016 Kaposvári Egyetem Egyetemi Könyvtár
 */ 
 
//mentés helyének beállítása
$page->setSaveDir("path_to_save_dir");

//adatbáziskapcsolat adatai - host - db - user - password
$page->setDBConnection("host","db","usr","psw");

//feloldó script címe ami az átiránytást végzi url+mappanév a files mappát tartalmazó mappa
$page->setURLSkip("../handle", "handle_dir_host_name");

//$page->setLanguage(); lang mappa xml név (nyelvkód) en_GB.xml, hu_HU.xml etc
$page->setLanguage("hu_HU.xml");

//$page->setListLink();  listaelem linkje aleph webopac link
$page->setListLinks("link_1_part","link_2_part");
?>