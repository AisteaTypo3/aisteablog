-- ============================================================
-- Aistea Blog – Beispiel-Daten
-- Import: ddev mysql db < packages/aisteablog/Documentation/example-data.sql
--
-- Erstellt:
--   Seite  9001  – Blog (Standard-Seite, pid=1)
--   Seite  9002  – Blog Datensätze (Systemordner, pid=1)
--   Post   9001  – "Willkommen im Aistea Blog"
--   Kat.   9001  – "TYPO3"
--   Tag    9001  – "Extension"
--   Plugin       – tt_content auf Seite 9001
--
-- UIDs beginnen bei 9000, um Konflikte zu vermeiden.
-- Prüfe vorher: SELECT uid FROM pages WHERE uid IN (9001, 9002);
-- ============================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- ============================================================
-- 1. Seiten
-- ============================================================

INSERT INTO `pages`
    (uid, pid, sorting, crdate, tstamp, hidden, deleted, doktype, title, slug, nav_hide, fe_group, starttime, endtime)
VALUES
    -- Blog-Listenseite (normale Seite)
    (9001, 1, 4096, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 1, 'Blog', '/blog', 0, '', 0, 0),
    -- Systemordner für Blog-Datensätze
    (9002, 1, 4352, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 254, 'Blog Datensätze', '/blog-data', 1, '', 0, 0)
ON DUPLICATE KEY UPDATE uid = uid;

-- ============================================================
-- 2. Kategorie
-- ============================================================

INSERT INTO `tx_aisteablog_domain_model_category`
    (uid, pid, crdate, tstamp, hidden, deleted, title, slug, description)
VALUES
    (9001, 9002, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 'TYPO3', 'typo3', 'Alles rund um TYPO3 CMS, Extensions und Best Practices.')
ON DUPLICATE KEY UPDATE uid = uid;

-- ============================================================
-- 3. Tag
-- ============================================================

INSERT INTO `tx_aisteablog_domain_model_tag`
    (uid, pid, crdate, tstamp, hidden, deleted, title)
VALUES
    (9001, 9002, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 'Extension')
ON DUPLICATE KEY UPDATE uid = uid;

-- ============================================================
-- 4. Blog Post
-- ============================================================

INSERT INTO `tx_aisteablog_domain_model_post`
    (uid, pid, crdate, tstamp, hidden, deleted,
     title, slug, teaser, bodytext, author, publish_date, cover_image, categories, tags)
VALUES
    (9001, 9002, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0,
     'Willkommen im Aistea Blog',
     'willkommen-im-aistea-blog',
     'Der erste Beitrag des Aistea Blogs – Gedanken zu TYPO3, Webentwicklung und dem Bauen schlanker Extensions.',
     '<p>Herzlich willkommen auf dem <strong>Aistea Blog</strong>! 🎉</p>

<p>Dieser Blog entstand als Teil meines Portfolios auf <em>aistea.me</em> und ist als schlanke TYPO3 v14 Extension
realisiert – ohne externe Blog-Extension, ohne unnötige Abhängigkeiten, nur Extbase + Fluid und ein paar sauber
definierte Datenbanktabellen.</p>

<h2>Was dich hier erwartet</h2>

<p>Ich schreibe über Themen, die mich täglich begleiten:</p>

<ul>
  <li><strong>TYPO3 Entwicklung</strong> – Extensions, TypoScript, Content Blocks, Sets</li>
  <li><strong>Frontend</strong> – SCSS, CSS-Architektur, Performance, barrierefreie Templates</li>
  <li><strong>Webdesign</strong> – UX, Typography, Layoutsysteme</li>
  <li><strong>Projekte</strong> – Einblicke in laufende und abgeschlossene Arbeiten</li>
</ul>

<h2>Aufbau dieser Extension</h2>

<p>Die Blog-Extension <code>aistea/aisteablog</code> ist bewusst schlank gehalten:</p>

<ul>
  <li>Extbase MVC mit <code>PostController</code> (list, show, category)</li>
  <li>Eigene Tabellen für Posts, Kategorien und Tags</li>
  <li>Slugbasiertes Routing über den TYPO3 RouteEnhancer</li>
  <li>Fluid Templates mit BEM-Struktur</li>
  <li>SCSS als eigenständige Datei, kompiliert via ws-scss</li>
  <li>Kein Custom Backend Module – das Standard-Listenmodul reicht vollständig aus</li>
</ul>

<p>Den Quellcode findest du wie immer auf GitHub. Feedback und Fragen sind willkommen!</p>',
     'Yannick Aister',
     UNIX_TIMESTAMP(),
     0,
     1,
     1)
ON DUPLICATE KEY UPDATE uid = uid;

-- ============================================================
-- 5. MM-Relationen
-- ============================================================

INSERT INTO `tx_aisteablog_post_category_mm`
    (uid_local, uid_foreign, sorting, sorting_foreign)
VALUES
    (9001, 9001, 1, 1)
ON DUPLICATE KEY UPDATE sorting = sorting;

INSERT INTO `tx_aisteablog_post_tag_mm`
    (uid_local, uid_foreign, sorting, sorting_foreign)
VALUES
    (9001, 9001, 1, 1)
ON DUPLICATE KEY UPDATE sorting = sorting;

-- ============================================================
-- 6. Blog Plugin als tt_content auf Seite 9001
-- ============================================================

INSERT INTO `tt_content`
    (uid, pid, sorting, crdate, tstamp, hidden, deleted,
     sys_language_uid, l18n_parent, l18n_diffsource,
     CType, header, colPos, pi_flexform, frame_class, space_before_class, space_after_class)
VALUES
    (9001, 9001, 256, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0,
     0, 0, '',
     'aisteablog_blog', 'Blog', 0, '', 'default', '', '')
ON DUPLICATE KEY UPDATE uid = uid;

SET foreign_key_checks = 1;

-- ============================================================
-- FERTIG
-- Danach TYPO3-Cache leeren:
--   ddev exec vendor/bin/typo3 cache:flush
-- ============================================================
