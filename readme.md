# Vscode bővítmények
- Rest client

# Composer
## Telepítés
A **Composer** egy **függőségkezelő eszköz** a PHP programozási nyelvhez. Fő feladata, hogy segítsen a PHP projekteknek kezelni azokat a külső könyvtárakat és csomagokat, amelyektől függenek (ezek a "függőségek").

[telepítés](https://getcomposer.org/doc/00-intro.md#installation-windows)

- A telepítés során meg kell adni azt a php.exe fájlt, amit a composer használ.
- A Composer telepítésekor azért kell megadni a php.exe fájl helyét, mert a Composer maga is egy PHP alkalmazás, és szüksége van a PHP értelmezőre a futáshoz.

## Egy csomag telepítése
`composer require nikic/fast-route`

Ha letelepítünk egy csomagot, három új elem jön létre:
- **vendor mappa**: ide kerülnek a telepített csomagok
    - fontos, hogy ha verziórunk, akkor ezt a mappát ne vegyük bele

.gitignore
```gitignore
vendor
```

- **composer.json**: ide kerül, hogy milyen csomagok települtek: ezek **függőségek** a saját programunk működése függ ezektől a csomagoktól, hiszen ezeket használjuk hozzá.
composer.json:
```json
{
    "require": {
        "nikic/fast-route": "^1.3"
    }
}
```
- composer.lock: A telepítésheztelepített csomagokhoz szükséges összes csomag

## Csomagok verziószáma
[verzós cikk](https://gemini.google.com/share/3c894ec9a714)


## A vendor mappa újratelepítése
`composer install`

# nikic-fast-route
[nikic/fast-route](https://github.com/nikic/FastRoute)

# Cors policy
[AI cikk](https://g.co/gemini/share/3451db17b7f4)

# CRUD muveletek

Create: Posztolás
Read: listázás
Update: módosítás
Delete: törlés

# Cors policy
A CORS "Cross-Origin Resource Sharing" kifejezés magyar fordítása: **Eredetközi Erőforrásmegosztás**
Rövidítve gyakran: CORS (ejtsd: korsz) A "Cross-Origin" a "más eredetű" vagy "különböző forrású" tartalmakra utal.

[AI cikk](https://g.co/gemini/share/3451db17b7f4)