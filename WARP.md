# WARP.md - Working AI Reference for AbraFlexi-InvoiceEnhancer

## Project Overview
**Type**: PHP project (Composer `type: project`), packaged as `abraflexi-enhancer` Debian package
**Purpose**: External web application for AbraFlexi that converts text invoice line items into pricelist entries and stores supplier prices. Adds a trigger button to Received Invoices in AbraFlexi.
**Status**: Active
**Repository**: git@github.com:Vitexus/AbraFlexi-InvoiceEnhancer.git
**Live demo**: https://enhancer.vitexsoftware.com/

## Key Technologies
- PHP 8 (PSR-4 autoloading under `AbraFlexi\Enhancer\`)
- Ease Framework (Bootstrap 5 widgets, `Ease\TWB5`, `Ease\Shared`)
- AbraFlexi PHP library (`php-spojenet-abraflexi`, `AbraFlexi\FakturaPrijata`, `AbraFlexi\Cenik`)
- Debian packaging (`debhelper-compat = 12`, `jq`, static `debian/autoload.php`)
- PHPUnit 13, PHPStan, PHP-CS-Fixer

## Architecture & Structure
```
AbraFlexi-InvoiceEnhancer/
├── src/
│   ├── init.php          # Bootstrap: loads autoloader, config, starts session
│   ├── index.php         # Main entry point: renders invoice form
│   ├── install.php       # One-time setup: creates AbraFlexi trigger button
│   └── Enhancer/
│       ├── InvoiceEnhancer.php          # Core logic: extends FakturaPrijata
│       └── ui/
│           ├── AppLogo.php              # Inline SVG logo (Ease\Html\ImgTag)
│           ├── InvoiceForm.php          # Invoice item selection form (Ease\TWB5\Panel)
│           └── PageBottom.php          # Footer with status messages and social links
├── tests/
│   ├── bootstrap.php     # loads vendor/autoload.php + tests/.env
│   ├── .env              # AbraFlexi demo credentials (demo.flexibee.eu:5434)
│   └── src/Enhancer/ui/
│       └── OptionsFormTest.php         # AppLogoTest (class name is AppLogoTest)
├── debian/
│   ├── autoload.php      # Static autoloader: system deps + PSR-4 + InstalledVersions
│   ├── rules             # PKG_VERSION/PKG_SOURCE/PKG_TYPE + APP_NAME/APP_VERSION injection
│   ├── install           # File install map (no composer.json, metainfo installed here)
│   ├── control           # Depends: composer (not composer-debian)
│   └── io.github.vitexsoftware.abraflexi_enhancer.metainfo.xml
├── project-logo.svg      # Installed as AppStream stock icon
└── phpunit.xml           # bootstrap: tests/bootstrap.php, suite: tests/src
```

## Core Class: InvoiceEnhancer
`InvoiceEnhancer extends FakturaPrijata` — key methods:
- `convertSelected(array $requestData)` — iterates `$requestData['convert']` item IDs, looks up or creates a `Cenik` pricelist entry (by EAN or `kod`), then calls `updateSupplierPrice`
- `createPricelistItem(array $subitemData)` — uses `AbraFlexi\Bricks\Convertor` to convert a `FakturaPrijataPolozka` into a new `Cenik` entry
- `updateSupplierPrice(array $activeItemData)` — upserts a `Dodavatel` (supplier price) record

## Debian Packaging Notes
- Uses **static `debian/autoload.php`** (no `composer-debian`, no postinst composer run)
- `debian/rules` injects `APP_NAME` and `APP_VERSION` constants and `InstalledVersions` data at build time via `sed`
- PHP files installed to: `usr/share/abraflexi-enhancer/` (web entry points), `usr/lib/abraflexi-enhancer/` (library classes + autoload.php)
- Apache config: `etc/apache2/conf-available/abraflexi-enhancer.conf`
- AppStream metainfo: `usr/share/metainfo/`, stock icon from `project-logo.svg`

## Development Workflow

### Setup
```bash
git clone git@github.com:Vitexus/AbraFlexi-InvoiceEnhancer.git
cd AbraFlexi-InvoiceEnhancer
composer install
```

### Build Debian package
```bash
dpkg-buildpackage -b -uc
```

### Testing
```bash
vendor/bin/phpunit
```

AbraFlexi demo credentials are pre-configured in `tests/.env` (`demo.flexibee.eu:5434`).

### Code Quality
```bash
vendor/bin/php-cs-fixer fix
vendor/bin/phpstan analyse
```

## Common Tasks
- **Add a new UI component**: extend `Ease\TWB5\*`, place in `src/Enhancer/ui/`, add PSR-4 entry if needed
- **Change installed paths**: update `debian/install` and the corresponding `sed` lines in `debian/rules`
- **Update Debian dependencies**: edit `debian/control` Depends; if a new dep ships an `autoload.php`, add it to `debian/autoload.php`
