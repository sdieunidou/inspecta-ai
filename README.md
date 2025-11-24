# Inspecta AI

Inspecta AI is a PHP CLI tool that runs configurable AI analyses on selected files.

## Requirements

- PHP 8.4

## Installation

```sh
composer require --dev sdieunidou/inspecta-ai
```

## Configuration

- Project-level settings live in `inspecta-ai.yaml`. See [`tests/data/config/inspecta-ai.yaml`](tests/data/config/inspecta-ai.yaml) for a sample describing runners (`ollama`, model, binary path, timeout) and which prompt to use.
- Prompt templates are plain text files referenced by name in the configuration. [`tests/data/prompt/solid.prompt`](tests/data/prompt/solid.prompt) shows how to describe expectations for the `solid_violations` analysis (structure of the response, precision required, JSON format, etc.). Update or duplicate it to create new prompts tailored to your use cases.

### Available Template Variables

Prompt templates support variables that are automatically replaced during processing:

- `%%file%%` - The file path of the file being analyzed

## Runners

Runners are responsible for executing AI analyses by interfacing with different AI providers or local tools.

### Available Runners

- **Ollama** - For running analyses locally using Ollama. Configured in `inspecta-ai.yaml` with `binary` (path to Ollama CLI), `model`, and `timeout` settings.

### Creating a New Runner

To add support for other AI providers (e.g., OpenAI, Gemini), create a new class implementing `RunnerInterface`:

- Implement the `public function analyze(AnalysisRequest $request): string` method that executes the analysis and returns the raw result
- Implement the `supports(string $runnerType): bool` method that returns `true` for the runner type identifier
- Register your runner in the `RunnerRegistry` when creating the `Analyzer` instance

You can use [`src/Runner/OllamaRunner.php`](src/Runner/OllamaRunner.php) as a reference implementation for creating a new runner.

## Usage

```sh
./vendor/bin/inspecta-ai analyze solid_violations tests/data/scripts/solid_violations.php -c tests/data/config/inspecta-ai.yaml
```

Example output:

```json
{
  "file": "tests/data/scripts/solid_violations.php",
  "solid_ok": true,
  "problems": [
    {
      "principle": "SRP",
      "severity": "major",
      "summary": "Résumé court d'un seul problème SRP précis",
      "suggestion": "Créer un service de traitement des commandes, et déplacer la logique de traitement des différents types de commandes, ainsi que les calculs d'impôts. Le contrôleur est déchargé uniquement de passer à l'étape suivante.",
      "refactor_steps": [
        "Créer une interface CommandProcessor avec une méthode processCommand(array $command): void",
        "Déplacer la logique de traitement des différents types de commandes vers un service, créant des classes par exemple OrderProcessingService et TaxCalculationService",
        "Injecter le service de traitement des commandes dans le contrôleur pour la passer à l'étape suivante"
      ],
      "line": 42
    },
    {
      "principle": "OCP",
      "severity": "major",
      "summary": "Résumé court d'un seul problème OCP précis",
      "suggestion": "Enregistrer les types de commandes dans une liste fixe, et déplacer le code qui appelle ces methods dans un service ou un contrôleur.",
      "refactor_steps": [
        "Créer une classe CommandProcessor avec une méthode processCommand(array $command): void",
        "Ajouter chaque type de commande à la liste des commandes acceptables pour le traitement de commande, et déplacer les appels aux methodes de traitement dans un service ou un contrôleur",
        "Ajouter une fonction checkCommandType() pour vérifier si le type de commande est bien accepté"
      ],
      "line": 0
    },
    {
      "principle": "ISP",
      "severity": "minor",
      "summary": "Résumé court d'un seul problème ISP précis",
      "suggestion": "En ajouter une méthode work() à la classe RobotWorker qui fait appel au contrôle de robot sans utiliser le contract.",
      "refactor_steps": [
        "Ajouter une méthode work() dans RobotWorker",
        "Faire appel à cette méthode par l'interface WorkerContract"
      ],
      "line": 32
    },
    {
      "principle": "ISP",
      "severity": "minor",
      "summary": "Résumé court d'un seul problème ISP précis",
      "suggestion": "En changer l'état de la classe Square pour faire une extension de Rectangle.",
      "refactor_steps": [
        "Ajouter une méthode area() qui utilise toujours les propriétés width et height pour calculer son aire, sans utiliser la variable $x.",
        "Faire appel à cette méthode dans la classe Rectangle"
      ],
      "line": 68
    },
    {
      "principle": "DIP",
      "severity": "minor",
      "summary": "Résumé court d'un seul problème DIP précis",
      "suggestion": "Enregistrer l'instance du logger dans le contrôleur, plutôt que de faire appel à une méthode statique.",
      "refactor_steps": [
        "Ajouter un champ private $logger au contrôleur",
        "Passer cet objet au constructeur du NotificationService"
      ],
      "line": 0
    }
  ],
  "score": 80
}
```
