# Inspecta AI

Inspecta AI is a PHP CLI tool that runs configurable AI analyses on selected files.

Inspecta AI started as a CI tool for analyzing committed files, but its design makes it suitable for a wide range of analysis scenarios. At its core, it's simply a prompt-driven LLM runner that processes files and returns structured results.

**Use cases include:**
- Code quality analysis (SOLID principles, design patterns)
- Security rule validation
- Content style checks (Markdown, documentation)
- Code review automation
- Custom business rule validation

The output format is determined by your prompt. Request JSON, Markdown, plain text, or any other format—the LLM will follow your instructions.

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

- Implement the `analyze(AnalysisRequest $request): string` method that executes the analysis and returns the raw result
- Implement the `supports(string $runnerType): bool` method that returns `true` for the runner type identifier
- Register your runner in the `RunnerRegistry` when creating the `Analyzer` instance

You can use [`src/Runner/OllamaRunner.php`](src/Runner/OllamaRunner.php) as a reference implementation for creating a new runner.

## Usage

Analyze a single file:

```sh
./vendor/bin/inspecta-ai analyze solid_violations tests/data/scripts/solid_violations.php -c tests/data/config/inspecta-ai.yaml
```

You can also analyze multiple files in one command:

```sh
./vendor/bin/inspecta-ai analyze solid_violations file1.php file2.php file3.php -c tests/data/config/inspecta-ai.yaml
```

> **Note:** The `-c` option is optional and defaults to `inspecta-ai.yaml` if not specified.

Example output:

```json
{
  "file": "tests/data/scripts/solid_violations.php",
  "solid_ok": false,
  "problems": [
    {
      "principle": "SRP",
      "severity": "major",
      "summary": "MegaOrderProcessor class has multiple unrelated responsibilities (fetching, billing, emailing, reporting).",
      "suggestion": "Extract separate services for fetching, billing, emailing and reporting. For example: `FetchService`, `BillingService`, `EmailNotifierService`. Move the corresponding methods to these new classes.",
      "refactor_steps": [
        "Create FetchService class with a fetch method that processes orders",
        "Create BillingService class with a bill method that calculates order amounts",
        "Create EmailNotifierService class with a notify method that sends emails"
      ],
      "line": 10
    },
    {
      "principle": "LSP",
      "severity": "major",
      "summary": "Square class breaks width/height assumptions inherited from Rectangle.",
      "suggestion": "Renamed Square class to `RectangularShape` and corrected setWidth/setHeight methods. Also, added a new method that calculates area correctly based on shape type.",
      "refactor_steps": [
        "Rename Square to RectangularShape",
        "Update setWidth/setHeight methods in Rectangle class to make them more general",
        "Add isSquare method in RectangularShape class to determine if the object is square or not"
      ],
      "line": 48
    },
    {
      "principle": "OCP",
      "severity": "minor",
      "summary": "NotificationService uses a specific logger implementation (FileLogger).",
      "suggestion": "Make NotificationService more flexible by injecting an interface for logging, rather than a concrete class. For example, use `LoggingInterface` instead of `FileLogger`. This will make the service more testable and easier to switch between different logging implementations.",
      "refactor_steps": [
        "Create LoggingInterface with a write method",
        "Update NotificationService constructor to accept an instance of LoggingInterface"
      ],
      "line": 41
    },
    {
      "principle": "DIP",
      "severity": "major",
      "summary": "RobotWorker class is forced to implement the eat() method, which is not applicable.",
      "suggestion": "Remove the `eat()` method from RobotWorker and instead provide a different implementation that doesn't involve eating. Alternatively, refactor WorkerContract to allow for more flexibility in implementing work-related tasks.",
      "refactor_steps": [
        "Delete eat() method from RobotWorker class",
        "Update RobotWorker class to not implement eat()"
      ],
      "line": 49
    }
  ],
  "score": 80
}
```
