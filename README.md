# Inspecta AI

Inspecta AI is a PHP CLI tool that runs configurable AI analyses on selected files.

## Requirements

- PHP 8.4

## Installation

```sh
composer install
```

## Compilation

To build the PHAR using Box:

```sh
vendor/bin/box compile
```

## How to use

### Configuration

- Project-level settings live in `inspecta-ai.yaml`. See [`tests/data/config/inspecta-ai.yaml`](tests/data/config/inspecta-ai.yaml) for a sample describing providers (`ollama`, model, binary path, timeout) and which prompt to use.
- Prompt templates are plain text files referenced by name in the configuration. [`tests/data/prompt/solid.prompt`](tests/data/prompt/solid.prompt) shows how to describe expectations for the `solid_violations` analysis (structure of the response, precision required, JSON format, etc.). Update or duplicate it to create new prompts tailored to your use cases.


## Usage

```sh
./inspecta-ai analyze solid_violations tests/data/scripts/solid_violations.php -c tests/data/config/inspecta-ai.yaml
```
