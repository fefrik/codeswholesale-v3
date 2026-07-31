


PHP SDK pro práci s **CodesWholesale API v3**
(produkty, objednávky, licenční klíče, synchronizace, bezpečnost).

Navrženo pro reálné e‑commerce integrace a dlouhodobě běžící procesy.

✅ PHP 8.3+
✅ Není potřeba žádný framework  
✅ Automatická OAuth autentizace  
✅ Bezpečné stránkování (pokračování pomocí continuation tokenu)  
✅ Navrženo pro dlouhé synchronizace a cron joby

---

## Požadavky

- PHP **8.3+**
- rozšíření **cURL**
- rozšíření **JSON**

---

## Instalace

```bash
composer require codeswholesale-v3/sdk
```

---

## Základní použití

### Vytvoření klienta a SDK

```php
use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Sdk\Sdk;
use CodesWholesaleApi\Storage\OAuth2\TokenSessionOAuthStorage;

$oauthStorage = new TokenSessionOAuthStorage();

$client = new Client(
    Config::live(),
    $oauthStorage,
    'CLIENT_ID',
    'CLIENT_SECRET'
);

$sdk = new Sdk($client);
```

---

## Architektura SDK

```
Client
 └── Endpoint (Products, Orders, Codes, …)
       └── Resource (ProductItem, OrderItem, …)
```

### Client
- zajišťuje HTTP komunikaci, OAuth2, retry a chybové stavy
- **vždy vrací `stdClass`**

### Endpoint
- reprezentuje skupinu REST endpointů (`/v3/products`, `/v3/orders`, …)
- převádí odpovědi na **Resource objekty**

### Resource
- immutable DTO (read‑only)
- typové gettery
- žádná business logika

---

## Přehled SDK podle API oblastí

### Produkty
- seznam produktů (stránkování, pokračování)
- detail produktu
- popisy produktů
- obrázky produktů
- bezpečná synchronizace velkých katalogů (50k+ produktů)

### Objednávky
- vytváření objednávek
- historie objednávek
- detail objednávky
- získání licenčních klíčů z objednávky

### Kódy (licenční klíče)
- získání zakoupených klíčů
- textové i obrázkové kódy
- práce s base64 obrázky

### Účet
- zůstatek účtu
- informace o účtu

### Bezpečnost
- kontrola rizik / fraud
- kontrola IP a domény
- risk skóre

### Metadata
- platformy
- regiony
- jazyky
- teritoria

---

## Produkty

### Získání jedné stránky produktů

```php
$page = $sdk->products()->getPage([
    'updatedSince' => '2024-01-01T00:00:00Z'
]);

foreach ($page['items'] as $product) {
    echo $product->getName();
}
```

---

## Iterace všech produktů

```php
$sdk->products()->getAll(
    function (array $items) {
        foreach ($items as $product) {
            saveProduct($product);
        }
    }
);
```

---

## Synchronizace produktů (doporučeno)

Bezpečná a obnovitelná synchronizace pomocí continuation tokenů.

```php
$runner->runForSeconds(
    fn(ProductItem $p) => upsertProduct($p),
    30
);
```

✔ bezpečné pro web  
✔ bezpečné pro cron  
✔ pokračuje přesně tam, kde skončilo

---

## Podpořte tento projekt ❤️

Tento projekt je **free a open‑source** a takový vždy zůstane.

Pokud vám pomohl ušetřit čas nebo dodat projekt rychleji,
můžete podpořit jeho další vývoj přes GitHub Sponsors:

➡️ https://github.com/sponsors/fefrik

Děkujeme — i malý příspěvek pomáhá projekt udržet při životě 🚀

---

## Upozornění (Disclaimer)

Toto je **komunitně udržovaná integrace** a **nejde o oficiální produkt CodesWholesale**.

Musíte použít **vlastní CodesWholesale API klíč a účet**.
Veškeré ochranné známky patří jejich vlastníkům.
