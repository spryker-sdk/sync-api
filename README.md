# SyncApi

[![Build Status](https://github.com/spryker-sdk/sync-api/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/sync-api/actions?query=workflow%3ACI+branch%3Amaster)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)

This library provides console commands to work with synchronous APIs.

## Installation

- `composer require --dev spryker-sdk/sync-api`

## Usage

### Run tests/checks

- `composer test` - This will execute the tests.
- `composer cs-check` - This will run CodeStyle checks.
- `composer cs-fix` - This will fix fixable CodeStyles.
- `composer stan` - This will run PHPStan checks.

Documentation

The following console commands are available:

- `vendor/bin/asyncapi openapi:schema:create`
- `vendor/bin/asyncapi openapi:schema:validate`
- `vendor/bin/asyncapi openapi:code:generate`

## Adding an OpenAPI file

The `vendor/bin/syncapi openapi:schema:create` adds a minimal OpenAPI file.

## Validating an OpenAPI file

The `vendor/bin/syncapi openapi:schema:validate` validates an OpenAPI file.


## Create code from an existing OpenAPI

The `vendor/bin/asyncapi openapi:code:generate` reads an existing OpenAPI file and creates code out of it.


