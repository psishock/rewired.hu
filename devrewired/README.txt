RW Fejlesztői elérhetőségek:

Githubon végre fent van a legfrissebb lájtosított (2+giga helyett felhasználó és topik anyag nélküli), teljesen tiszta verzió, ami kb 50mega így:
https://github.com/psishock/rewired.hu

---------------------------------------------------------------------------------------

Telepítési lépések:
1) A "devrewired" mappa anyagát komplett másoljátok át egy sima PHP barát (lokális) host alá.
7.2 verzió alatt legyetek valahol.
7.2-n teszteltem és nem fér össze a friss limitációkkal.
Most itthon 7.1.x-en vagyok és minden okésan fest.

2) a devrewired.sql-t meg töltsétek fel egy sima MySQL/MariaDB barát adatbázisra.
Itthon 10.1.29-MariaDB-n teszteltem legfrissebben szóval szinte bármelyik MySQL fork verzsnnel mennie kellene.

3) állítsátok be az adatbázis hozzáférhetőség infókat a Drupálnak hogy tudjanak kommunikálni, a devrewired/sites/default/settings.php fájlban.
Keressétek a database, username, password, stb opciókat a 213 sornál.
Ez a file alapból írásvédettre van állítva, így azt kapcsoljátok át ha editálni akarjátok.

4) ennyi, innentől kóser kellene legyen. :)

én a XAMPP rendszert használom a lokális hostingra (ha valaki nem tudná honnan is kezdje és tippre van szüksége) a flexibilitásánál és kényelmességénél fogva, de nyomjátok amin akarjátok és megszoktátok.
-------------------------------------------------------------------

az általános és mindenható admin amivel be tudtok erre a fejlesztői RW-re lépni:
username: admin
password: password

aztán tetszés szerint változtathatjátok vagy hagyjátok így ahogy van.

-------------------------------------------------------------------

További hasznos útvonalak tájékozódni:
devrewired/sites/default/files/hiriro <- hiriro FTP tárhely
devrewired/sites/default/files/hiriro/gifthumbs <- a GIF animációk indítása előtti bélyegképek
devrewired/sites/all/files <- RW ikonok, logók, stb
devrewired/sites/all/modules <- minden core és custom Drupal modul amit használunk
devrewired/sites/all/themes/responsive_bartik <- az RW komplett külalakjáért felelős cuccok itt vannak.
devrewired/sites/all/themes/responsive_bartik/css <- azon belül a CSS fájlok
devrewired/sites/all/themes/responsive_bartik/js <- javascript cuccok
devrewired/sites/all/themes/responsive_bartik/templates <- a kulönféle generált PHP oldalak template-jei itt vannak.
devrewired/sites/all/themes/responsive_bartik/php <- custom PHP funkciók (media beágyazás, stb)