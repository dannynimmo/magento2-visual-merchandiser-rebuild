# Rebuild Visual Merchandiser Categories for Magento 2

Rebuild Magento 2 Visual Merchandiser categories using the CLI tool, or via scheduled cron job

## Installation

```bash
composer require dannynimmo/magento2-visual-merchandiser-rebuild
```

## Usage

```bash
bin/magento catalog:visual-merchandiser:rebuild
```

## Changelog

### Unreleased
* Fixed code style & Magento 2 code quality warnings

### [0.1.2](https://github.com/dannynimmo/magento2-visual-merchandiser-rebuild/releases/tag/0.1.2) — 2017-06-27
* Fixed "Area code not set" bug in later versions of Magento _(thanks [@dakzilla](https://github.com/dakzilla))_
* Added dependencies explicitly in `composer.json`

### [0.1.1](https://github.com/dannynimmo/magento2-visual-merchandiser-rebuild/releases/tag/0.1.1) — 2017-03-17
* Fixed major issue where URL keys were missing from collection

### [0.1.0](https://github.com/dannynimmo/magento2-visual-merchandiser-rebuild/releases/tag/0.1.0) — 2017-02-28
* Released!
