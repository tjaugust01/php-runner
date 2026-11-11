# setup-runner

Ein einfacher task-/setup-runner in PHP, gesteuert über eine YAML-Konfigurationsdatei.

> **Status:** reines Proof-of-Concept / Konzept-Projekt. Wird aktuell nicht weiterentwickelt oder gepflegt.

## Was ist das?

Das Tool liest eine Konfigurationsdatei (standardmäßig `runner.yaml`) ein und führt die darin definierten Schritte nacheinander auf dem System aus.

## Unterstützte Aktionen (Steps)

- `check`: Prüft Ausgaben von CLI-Befehlen (z. B. PHP-Version).
- `makedir`: Erstellt Verzeichnisse.
- `cleandir`: Leert Verzeichnisse.
- `createfile`: Erstellt Dateien mit vorgegebenem Inhalt.
- `appendtofile`: Hängt Text an eine bestehende Datei an.
- `copy`: Kopiert Dateien oder Ordner.
- `chmod`: Setzt Dateirechte.
- `symlink`: Erstellt Symlinks.
- `exists`: Prüft, ob Dateien oder Pfade existieren.
- `run`: Führt Shell-Befehle aus (mit Unterstützung für Stdin-Input und interaktive Abfragen).

## Verwendung

Voraussetzungen: PHP 8.2+ und Composer-Abhängigkeiten.

```bash
composer install
php bin/setup-runner [pfad/zu/runner.yaml]
```

Eine Beispielkonfiguration liegt in `runner.yaml.example`.
