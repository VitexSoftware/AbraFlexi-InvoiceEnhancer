![project logo](project-logo.png?raw=true)

Invoice Enhancer for AbraFlexi
==============================

[![wakatime](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/0200f761-5082-47b3-abf9-3f393a268050.svg)](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/0200f761-5082-47b3-abf9-3f393a268050)

Použití
-------

Do Přijatých Faktur ve AbraFlexi přidá tlačítko "Vylepšovač" kterým se aplikace volá.

![Trigger](trigger.png?raw=true)


![Pricelist Item](pricelist-item.png?raw=true)

![Conversion Done](conversion-done.png?raw=true)


Instalace
---------

V browseru je třeba ručně otevřít stránku [install.php](src/install.php)

Do formuláře se vyplní přístupové údaje do AbraFlexi. 
Pokud jsou tyto správné, vytvoří se ve AbraFlexi v evidenci přijaté faktury spouštěcí tlačítko.

(Pokud se nepovede autodetekce serveru a portu, zkopírujte prosím tuto hodnotu z adresního řádku do příslušného políčka)

Aplikace v chodu je k vyzkoušení [abraflexi-enhancer.vitexsoftware.com](https://abraflexi-enhancer.vitexsoftware.com/)

Testování
---------

Pokud není stránka volána s parametry $authSessionId && $companyUrl pokusí se načíst konfigurák ../testing/.env

Nasazení
--------

K dispozici je Docker image: https://hub.docker.com/r/vitexsoftware/abraflexi-enhancer-overview/tags

```
docker run -d -p ${OUTPORT}:${INPORT} --name ${CONTNAME} vitexsoftware/abraflexi-enhancer
```

Nebo debianí balíček k instalaci na server se sytémem Debian či Ubuntu:

```
sudo apt install lsb-release wget apt-transport-https bzip2

sudo wget -O /usr/share/keyrings/vitexsoftware.gpg https://repo.vitexsoftware.cz/keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/vitexsoftware.gpg]  https://repo.vitexsoftware.com  $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo apt update

sudo apt install abraflexi-enhancer
```


```shell
vitex@vyvojar ~ % sudo apt install abraflexi-enhancer 
Načítají se seznamy balíků… Hotovo
Vytváří se strom závislostí… Hotovo
Načítají se stavové informace… Hotovo
Následující balíky byly nainstalovány automaticky a již nejsou potřeba:
  libabsl20200923 libavif9 libdav1d4 libgav1-0 linux-image-5.10.0-28-amd64 nginx-common
Pro jejich odstranění použijte „sudo apt autoremove“.
Následující dodatečné balíky budou instalovány:
  php-vitexsoftware-ease-bootstrap5 php-vitexsoftware-ease-bootstrap5-widgets php-vitexsoftware-ease-bootstrap5-widgets-abraflexi
Navrhované balíky:
  php-spojenet-abraflexi-doc php-vitexsoftware-abraflexi-bricks-doc
Následující NOVÉ balíky budou nainstalovány:
  abraflexi-enhancer php-vitexsoftware-ease-bootstrap5 php-vitexsoftware-ease-bootstrap5-widgets
  php-vitexsoftware-ease-bootstrap5-widgets-abraflexi
0 aktualizováno, 4 nově instalováno, 0 k odstranění a 9 neaktualizováno.
Nutno stáhnout 41,2 kB archivů.
Po této operaci bude na disku použito dalších 236 kB.
Chcete pokračovat? [Y/n] 
Stahuje se:1 http://repo.vitexsoftware.com bookworm/main amd64 php-vitexsoftware-ease-bootstrap5 all 0.1~bookworm~35 [9 680 B]
Stahuje se:2 http://repo.vitexsoftware.com bookworm/main amd64 php-vitexsoftware-ease-bootstrap5-widgets all 1.3~bookworm~2 [19,4 kB]
Stahuje se:3 http://repo.vitexsoftware.com bookworm/main amd64 php-vitexsoftware-ease-bootstrap5-widgets-abraflexi all 0.1.1~bookworm~6 [4 388 B]
Stahuje se:4 http://repo.vitexsoftware.com bookworm/main amd64 abraflexi-enhancer all 0.1.0.3~bookworm [7 772 B]
Staženo 41,2 kB za 0s (1 080 kB/s)                 
Auto packing the repository in background for optimum performance.
See "git help gc" for manual housekeeping.
[master 102a4e0] saving uncommitted changes in /etc prior to apt run
 Author: vitex <vitex@vyvojar.spojenet.cz>
 1 file changed, 2036 insertions(+)
Vybírá se dosud nevybraný balík php-vitexsoftware-ease-bootstrap5.
(Načítá se databáze … nyní je nainstalováno 219709 souborů a adresářů.)
Připravuje se nahrazení …/php-vitexsoftware-ease-bootstrap5_0.1~bookworm~35_all.deb …
Rozbaluje se php-vitexsoftware-ease-bootstrap5 (0.1~bookworm~35) …
Vybírá se dosud nevybraný balík php-vitexsoftware-ease-bootstrap5-widgets.
Připravuje se nahrazení …/php-vitexsoftware-ease-bootstrap5-widgets_1.3~bookworm~2_all.deb …
Rozbaluje se php-vitexsoftware-ease-bootstrap5-widgets (1.3~bookworm~2) …
Vybírá se dosud nevybraný balík php-vitexsoftware-ease-bootstrap5-widgets-abraflexi.
Připravuje se nahrazení …/php-vitexsoftware-ease-bootstrap5-widgets-abraflexi_0.1.1~bookworm~6_all.deb …
Rozbaluje se php-vitexsoftware-ease-bootstrap5-widgets-abraflexi (0.1.1~bookworm~6) …
Vybírá se dosud nevybraný balík abraflexi-enhancer.
Připravuje se nahrazení …/abraflexi-enhancer_0.1.0.3~bookworm_all.deb …
Rozbaluje se abraflexi-enhancer (0.1.0.3~bookworm) …
Nastavuje se balík php-vitexsoftware-ease-bootstrap5 (0.1~bookworm~35) …
Conf javascript-common already enabled
Nastavuje se balík php-vitexsoftware-ease-bootstrap5-widgets (1.3~bookworm~2) …
composer-global-update found: /usr/lib/abraflexi-enhancer/composer.json deb/ease-bootstrap5-widgets
ProjectDir: /usr/lib/abraflexi-enhancer VendorDir: /var/lib/composer/abraflexi-enhancer
/var/lib/composer/abraflexi-enhancer/autoload.php
Nastavuje se balík php-vitexsoftware-ease-bootstrap5-widgets-abraflexi (0.1.1~bookworm~6) …
composer-global-update found: /usr/lib/abraflexi-enhancer/composer.json deb/ease-bootstrap5-widgets-abraflexi
ProjectDir: /usr/lib/abraflexi-enhancer VendorDir: /var/lib/composer/abraflexi-enhancer
/var/lib/composer/abraflexi-enhancer/autoload.php
Nastavuje se balík abraflexi-enhancer (0.1.0.3~bookworm) …
ProjectDir: /usr/lib/abraflexi-enhancer VendorDir: /var/lib/composer/abraflexi-enhancer
/var/lib/composer/abraflexi-enhancer/autoload.php
```

Pokud používáte apache, je třeba aktivovat jeho konfiguraci:

```
a2enconf abraflexi-enhancer
apache2ctl restart
```

Poté je aplikace dostupná bez další konfigurace na http://0.0.0.0/abraflexi-enhancer/

![Installer](installer.png?raw=true)
