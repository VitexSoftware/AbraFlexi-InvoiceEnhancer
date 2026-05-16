![project logo](project-logo.svg?raw=true)

Vylepšovač přijatých faktur pro AbraFlexi
=========================================

[![wakatime](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/0200f761-5082-47b3-abf9-3f393a268050.svg)](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/0200f761-5082-47b3-abf9-3f393a268050)


Konvertuje textové položky přijatých faktur na ceníkové. Zároveň jsou ukládány dodavatelské ceny.

Použití
-------

Do Přijatých Faktur ve AbraFlexi přidá tlačítko "Vylepšovač" kterým se aplikace volá.

![Trigger](trigger.png?raw=true)

Po volbě, které položky se mají zpracovat a odeslání formuláře jsou zpracované položky označeny zaškrtávátkem:

![Conversion Done](conversion-done.png?raw=true)

Takto vypadá importovaná položka v ceníku

![Pricelist Item](pricelist-item.png?raw=true)


Instalace
---------

V browseru je třeba ručně otevřít stránku [install.php](src/install.php)

Do formuláře se vyplní přístupové údaje pro vaše AbraFlexi. 
Pokud jsou tyto správné, vytvoří se ve AbraFlexi v evidenci přijaté faktury spouštěcí tlačítko.

(Pokud se nepovede autodetekce serveru a portu, zkopírujte prosím tuto hodnotu z adresního řádku do příslušného políčka)

Aplikace v chodu je k vyzkoušení [enhancer.vitexsoftware.com](https://enhancer.vitexsoftware.com/)

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



Pokud používáte apache, je třeba aktivovat jeho konfiguraci:

```
a2enconf abraflexi-enhancer
apache2ctl restart
```

Poté je aplikace dostupná bez další konfigurace na http://0.0.0.0/abraflexi-enhancer/

![Installer](installer.png?raw=true)
