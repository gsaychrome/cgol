A Game of Life egy implementációja. 

A portál elérhető fejlesztői konfigurációval, ahol a kapcsolódó saját 
fejlesztésü modulok közvetlen a develop branchben módosíthatóak, és
production módban, ahol az összes modul a vendor mappába kerül 
telepítésre.


A fejlesztőkörnyezet élesztése:

1. A kapcsolódó backend modulok szerkesztése workbench környezetben: 

php workbench.php

2. environment.php file másolása és lokális beállításai

copy config/environment.example.php environment.php

3. Backend szerver élesztése

A belépési pontot a /public könyvtássa kell állítani, a 
DirectoryIndex: index.php. Az apache konfiguráció megtalálható a 
public/.htaccess fileban, ezt környezettől függően át kell alakítani 