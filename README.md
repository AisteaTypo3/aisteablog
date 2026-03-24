# aistea/aisteablog

Schlanke TYPO3 v14 Blog Extension für [aistea.me](https://aistea.me).
Gebaut mit Extbase + Fluid, ohne externe Abhängigkeiten.

---

## Inhaltsverzeichnis

1. [Voraussetzungen](#voraussetzungen)
2. [Installation](#installation)
3. [Erste Schritte](#erste-schritte)
4. [Backend-Bedienung](#backend-bedienung)
5. [Datensätze anlegen](#datensätze-anlegen)
6. [Beispieldaten importieren](#beispieldaten-importieren)
7. [URL-Struktur](#url-struktur)
8. [TypoScript-Konfiguration](#typoscript-konfiguration)
9. [Templates anpassen](#templates-anpassen)
10. [SCSS anpassen](#scss-anpassen)
11. [Erweiterungsmöglichkeiten](#erweiterungsmöglichkeiten)

---

## Voraussetzungen

| Komponente | Version |
|---|---|
| TYPO3 | ^14.0 |
| PHP | ^8.2 |
| `aistea/aisteacorp` | @dev (Sitepackage) |
| `wapplersystems/ws-scss` | ^14.0 (SCSS-Kompilierung) |

---

## Installation

```bash
# 1. Composer
ddev composer require aistea/aisteablog:@dev

# 2. TYPO3 Datenbank aktualisieren
#    Backend → Admin Tools → Maintenance → Analyze Database Structure
#    Alle aisteablog-Tabellen anlegen

# 3. Cache leeren
ddev exec vendor/bin/typo3 cache:flush
```

---

## Erste Schritte

### Seitenstruktur anlegen

Empfohlene Struktur im TYPO3 Seitenbaum:

```
/ (Root, uid=1)
├── Blog              (Seite, doktype=1)  ← Hier wird der Blog angezeigt
│   └── ...
└── Blog Datensätze   (Systemordner, doktype=254)  ← Hier werden Posts etc. gespeichert
```

1. **Systemordner** anlegen: Seite → Neu → Typ: Ordner → Titel „Blog Datensätze"
2. **Blog-Seite** anlegen: Seite → Neu → Typ: Standard → Titel „Blog"
3. Auf der Blog-Seite Inhaltselement hinzufügen: **Blog** → **Aistea Blog**

---

## Backend-Bedienung

Die Extension nutzt das Standard-**Listenmodul** von TYPO3 (keine eigene Backend-Applikation).

### Datensätze verwalten

1. Im Backend-Menü: **Web → Liste**
2. Den Systemordner „Blog Datensätze" im Seitenbaum anklicken
3. Hier erscheinen alle drei Datensatztypen:

| Tabelle | Zweck |
|---|---|
| `Blog Post` | Eigentliche Artikel |
| `Blog Kategorie` | Gruppierung der Artikel |
| `Blog Tag` | Schlagwörter für Artikel |

### Empfohlene Reihenfolge beim Befüllen

```
1. Kategorien anlegen   (z.B. TYPO3, Frontend, Projekte)
2. Tags anlegen         (z.B. Extension, SCSS, Fluid)
3. Posts anlegen        (Kategorien + Tags zuweisen)
```

---

## Datensätze anlegen

### Blog Post

| Feld | Beschreibung |
|---|---|
| **Titel** | Überschrift des Beitrags (Pflichtfeld) |
| **URL Slug** | Wird automatisch aus dem Titel generiert. Manuell anpassbar. |
| **Teaser** | Kurze Zusammenfassung (erscheint in der Listenansicht) |
| **Inhalt** | RTE-Volltext-Editor (nutzt das `aisteacorp` Preset) |
| **Titelbild** | FAL-Bild (max. 1 Datei) |
| **Veröffentlichungsdatum** | Steuert Sortierung in der Liste |
| **Autor** | Freitextfeld |
| **Kategorien** | Mehrfachauswahl aus bestehenden Kategorien |
| **Tags** | Mehrfachauswahl aus bestehenden Tags |

### Blog Kategorie

| Feld | Beschreibung |
|---|---|
| **Bezeichnung** | Name der Kategorie |
| **URL Slug** | Für Kategorie-URLs (`/blog/kategorie/typo3`) |
| **Beschreibung** | Wird in der Kategorieansicht angezeigt |

### Blog Tag

Nur ein Feld: **Tag** (der Name des Schlagworts).

---

## Beispieldaten importieren

Ein fertig vorbereitetes SQL-Script mit einer Beispielseite, einem Post, einer Kategorie und einem Tag liegt unter:

```
packages/aisteablog/Documentation/example-data.sql
```

Import via DDEV:

```bash
ddev mysql db < packages/aisteablog/Documentation/example-data.sql
ddev exec vendor/bin/typo3 cache:flush
```

**Was wird angelegt:**

| UID | Typ | Beschreibung |
|---|---|---|
| 9001 | Seite (Standard) | Blog-Listenseite unter `/blog` |
| 9002 | Seite (Ordner) | Systemordner für Blog-Datensätze |
| 9001 | Kategorie | „TYPO3" |
| 9001 | Tag | „Extension" |
| 9001 | Post | „Willkommen im Aistea Blog" |
| 9001 | tt_content | Blog-Plugin auf der Blog-Seite |

> **Hinweis:** UIDs ab 9000 gewählt, um Konflikte mit bestehenden Seiten zu vermeiden.
> Prüfe vorher: `ddev mysql typo3 -e "SELECT uid FROM pages WHERE uid IN (9001, 9002);"`

---

## URL-Struktur

Die Extension nutzt den TYPO3 RouteEnhancer (`Extbase`-Typ) in der Site-Konfiguration.

| URL | Aktion | Beschreibung |
|---|---|---|
| `/blog/` | `Post::list` | Alle Beiträge (paginiert) |
| `/blog/seite/2` | `Post::list` | Seite 2 der Übersicht |
| `/blog/mein-erster-post` | `Post::show` | Einzelansicht per Slug |
| `/blog/kategorie/typo3` | `Post::category` | Gefiltert nach Kategorie |
| `/blog/kategorie/typo3/seite/2` | `Post::category` | Kategorie, Seite 2 |

Der Slug eines Posts wird automatisch aus dem Titel generiert und ist im TCA-Feld „URL Slug" editierbar.

---

## TypoScript-Konfiguration

Die Konfiguration liegt im Set `aistea/aisteablog`:

```
packages/aisteablog/Configuration/Sets/Blog/
├── config.yaml          # Set-Definition, Abhängigkeit von aistea/aisteacorp
├── setup.typoscript     # Plugin-View + CSS-Include
└── page.tsconfig        # Backend Wizard-Eintrag
```

### Einstellungen überschreiben

In der eigenen TypoScript-Konfiguration (z.B. im Aisteacorp Set):

```typoscript
# Anzahl Posts pro Seite anpassen
plugin.tx_aisteablog_blog.settings.postsPerPage = 9
```

---

## Templates anpassen

Templates liegen unter:

```
packages/aisteablog/Resources/Private/
├── Layouts/
│   └── Blog.html              # Basis-Layout (derzeit minimal)
├── Templates/Post/
│   ├── List.html              # Übersichtsseite
│   ├── Show.html              # Einzelansicht
│   └── Category.html         # Kategorie-Filteransicht
└── Partials/
    ├── Post/
    │   ├── Card.html          # Beitragskarte in der Liste
    │   └── Meta.html          # Datum + Autor
    └── Pagination.html        # Seitennavigation
```

### Template-Pfade überschreiben

Eigene Templates können über TypoScript eingehängt werden (höhere Index-Nummer = höhere Priorität):

```typoscript
plugin.tx_aisteablog_blog {
    view {
        templateRootPaths.10 = EXT:aisteacorp/Resources/Private/Blog/Templates/
        partialRootPaths.10  = EXT:aisteacorp/Resources/Private/Blog/Partials/
        layoutRootPaths.10   = EXT:aisteacorp/Resources/Private/Blog/Layouts/
    }
}
```

---

## SCSS anpassen

Die Blog-Styles liegen in:

```
packages/aisteablog/Resources/Public/Assets/SCSS/blog.scss
```

Kompiliert wird via `wapplersystems/ws-scss` automatisch nach:

```
packages/aisteablog/Resources/Public/Css/blog.css
```

Die SCSS-Datei verwendet **BEM-Klassen** und **CSS-Custom-Properties-kompatible Variablen**.
Alle Farben nutzen `currentColor` – damit erbt der Blog automatisch die Textfarbe deines Themes.

### Wichtige SCSS-Variablen

```scss
$blog-gap:         2rem;     // Abstand zwischen den Cards
$blog-card-radius: 0.5rem;   // Abrundung der Cards
$blog-meta-color:  #888;     // Farbe von Datum / Autor
```

Um diese zu überschreiben, ohne die Extension-Dateien zu ändern, füge vor dem Include in deiner `main.scss` eigene Werte ein:

```scss
// In aisteacorp/Resources/Private/Assets/SCSS/main.scss
$blog-gap: 1.5rem;
$blog-card-radius: 0.25rem;

// Dann ws-scss übernimmt den @import automatisch via TypoScript
```

---

## Erweiterungsmöglichkeiten

| Feature | Umsetzungshinweis |
|---|---|
| **SEO Meta** | `tx_seo_*` Felder zu Post-TCA hinzufügen |
| **RSS Feed** | `typeNum=100` PAGE-Objekt + eigener Controller |
| **Kommentare** | Externes Tool (giscus, utterances) via JS einbinden |
| **Suche** | `PostRepository::findBySearch()` mit `LIKE`-Query ergänzen |
| **Related Posts** | `findByCategories()` in `ShowAction` injizieren |
| **EN-Übersetzung** | L10n via Standard-TYPO3-Übersetzungsworkflow (Datensatz übersetzen) |
| **Ladezeit** | Titelbild mit `f:image` und `width="640c"` (c = crop) korrekt dimensionieren |

---

## Dateiübersicht

```
packages/aisteablog/
├── Classes/
│   ├── Controller/PostController.php
│   ├── Domain/Model/Post.php
│   ├── Domain/Model/Category.php
│   ├── Domain/Model/Tag.php
│   └── Domain/Repository/
│       ├── PostRepository.php
│       ├── CategoryRepository.php
│       └── TagRepository.php
├── Configuration/
│   ├── Extbase/Persistence/Classes.php
│   ├── Sets/Blog/
│   │   ├── config.yaml
│   │   ├── setup.typoscript
│   │   └── page.tsconfig
│   └── TCA/
│       ├── tx_aisteablog_domain_model_post.php
│       ├── tx_aisteablog_domain_model_category.php
│       ├── tx_aisteablog_domain_model_tag.php
│       └── Overrides/tt_content.php
├── Documentation/
│   └── example-data.sql
├── Resources/
│   ├── Private/
│   │   ├── Layouts/Blog.html
│   │   ├── Partials/
│   │   │   ├── Pagination.html
│   │   │   └── Post/Card.html
│   │   │   └── Post/Meta.html
│   │   └── Templates/Post/
│   │       ├── List.html
│   │       ├── Show.html
│   │       └── Category.html
│   └── Public/
│       ├── Assets/SCSS/blog.scss
│       ├── Css/blog.css              (generiert)
│       └── Icons/
│           ├── post.svg
│           ├── category.svg
│           ├── tag.svg
│           └── plugin.svg
├── composer.json
├── ext_emconf.php
├── ext_localconf.php
├── ext_tables.php
└── ext_tables.sql
```

---

## Lizenz

GPL-2.0-or-later — © Yannick Aister / [aistea.me](https://aistea.me)
