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

- `vendor/bin/syncapi schema:openapi:create`
- `vendor/bin/syncapi schema:openapi:validate`
- `vendor/bin/syncapi code:openapi:generate`
- `vendor/bin/syncapi code:openapi:update`

## Adding an OpenAPI file

The `vendor/bin/syncapi schema:openapi:create` adds a minimal OpenAPI file.

## Validating an OpenAPI file

The `vendor/bin/syncapi schema:openapi:validate` validates an OpenAPI file.

## Create code from an existing OpenAPI

The `vendor/bin/syncapi code:openapi:generate` reads an existing OpenAPI file and creates code out of it.

## Update an OpenApi file with provided OpenApi schema

The `vendor/bin/syncapi code:openapi:update` updates an existing OpenAPI file (or creates a new one if the file doesn't exist) with the provided JSON.


