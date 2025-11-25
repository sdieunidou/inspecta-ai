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

# 📘 Demo Use Case — SOLID Analysis in CI (GitHub Actions)

This example demonstrates how to integrate **Inspecta AI** into a real-world GitHub Actions workflow.
It automatically:

* detects modified PHP files (`src/` and `tests/`)
* runs an AI-powered SOLID analysis using Ollama
* captures raw LLM output
* converts it into valid JSON
* emits GitHub annotations (`::error`, `::warning`)
* generates a Markdown report added to the GitHub Actions summary
- posts a **CheckRun** that is:
  - 🟢 green if no issues
  - ⚪ neutral if issues are detected

All scripts shown here are reusable across projects.

## 🧠 About the AI model used in this example

This example uses **Ollama** with the lightweight **llama3.2** model.

We chose it because:

* ⚡ **Runs locally** (no external API calls)
* 🛡️ **No data leaves your machine or CI environment**
* 💸 **Free**
* 🧩 **Simple to integrate with Inspecta AI**
* 🪶 Very lightweight compared to larger foundation models

**Downside:**
Ollama’s smaller local models are typically **slower and less accurate** than hosted LLMs (OpenAI, Gemini, Claude…).

However, Inspecta AI is fully configurable:

* You can switch to **another Ollama model** in `inspecta-ai.yaml`
* Or define a new runner using OpenAI, Gemini, Claude, Mistral API or any LLM provider you prefer

Inspecta AI does not depend on Ollama — it only uses the runner you configure.

## 1. Install Inspecta AI

```sh
composer require --dev sdieunidou/inspecta-ai
```

## 2. Add `inspecta-ai.yaml` at your project root

```yaml
runners:
  llama3.2:
    type: ollama
    binary: ollama
    model: llama3.2
    timeout: 300

prompts:
  solid_violations:
    template: .github/prompt/solid.prompt
    runner: llama3.2
```

## 3. Create a prompt: `.github/prompt/solid.prompt`

You can reuse the prompt shipped with this repo: [`.github/prompt/solid.prompt`](.github/prompt/solid.prompt).

```
You are an expert in PHP 8.4 / Symfony and SOLID principles.

Your role:
- analyze the following file
- detect SOLID principle violations
- propose CONCRETE and ACTIONABLE refactorings for a Symfony developer.

Context:
- The code lives in a modern Symfony project (autowiring, services, thin controllers).
- Controllers should primarily orchestrate services / use cases.
- Business logic, validation, caching, logging, and email sending should ideally live in dedicated services.

IMPORTANT: GRANULARITY OF PROBLEMS

For the "problems" array:

1. Each entry in "problems" must represent **ONE specific issue**:
   - one location in the code
   - one violated principle (SRP OR OCP OR LSP OR ISP OR DIP)
   - a clearly targeted summary (not a global diagnosis of the entire class).

2. The "principle" field must contain **exactly ONE value**, chosen from:
   - "SRP"
   - "OCP"
   - "LSP"
   - "ISP"
   - "DIP"

   You MUST NOT write composite text like "SRP | OCP | LSP | ISP | DIP" or list multiple principles in the same field.

3. If you detect multiple issues for the same principle in different places:
   - you must create **multiple entries** in "problems"
   - for example, 3 SRP violations → 3 separate objects in "problems" (with different line numbers).

4. Never group several issues into a single "problems" object.
   It is better to create several short, precise entries than one general entry.

For every problem detected:

1. **Summary**
   - Summarize the problem in one clear sentence focused on a specific case.

2. **Suggestion**
   - Provide a concrete refactoring recommendation in continuous text.
   - DO NOT settle for vague sentences ("simplify the controller", "extract a service").
   - Give precise examples:
     - class names to create (e.g. `LoginRequestValidator`, `LoginService`, `UserLoginNotifier`)
     - the EXACT responsibilities of these classes
     - which code fragments to move (e.g. "extract validation logic from `__invoke()` into `LoginRequestValidator::validate(Request $request): LoginData`")
     - how to inject these classes into the controller (constructor injection, autowiring).

3. **refactor_steps**
   - Provide a list of concrete steps as an array of strings.
   - Each step must be a simple instruction that the developer can follow.
   - Example:
     - "Create the LoginRequestValidator class with a validate(Request $request): LoginData method"
     - "Create the LoginService class with a handle(LoginData $data): User method"
     - "Inject LoginRequestValidator and LoginService into LoginController via the constructor"
     - "In __invoke(), replace the current logic with calls to these services"

Important:
- Stay compatible with Symfony (services, dependency injection).
- Prefer creating services / interfaces over adding simple comments or TODOs.
- When suggesting class/service names, keep them consistent with the domain (e.g. `LoginHandler`, `UserNotifier`, etc.).

IMPORTANT: Respond ONLY with valid JSON, with no text before or after. Start directly with { and end with }.

Required JSON format:

{
  "file": "path/to/file.php",
  "problems": [],
}

or if issues are detected:

{
  "file": "path/to/file.php",
  "problems": [
    {
      "principle": "SRP",
      "severity": "major",
      "summary": "Short summary of a single SRP issue",
      "suggestion": "Concrete refactoring recommendation with class/service/method names and logic to move",
      "refactor_steps": [
        "Refactor step 1",
        "Refactor step 2",
        "Refactor step 3"
      ],
      "line": 42
    }
  ]
}

FILE: %%file%%
```

## 4. Add helper scripts

Place these 3 scripts in `.github/scripts/`.

### ✔ `parse_response.php`

Turns Inspecta AI’s raw output (`{...}{...}{...}`) into valid JSON (`[ {...}, {...} ]`). Full script: [`.github/scripts/parse_response.php`](.github/scripts/parse_response.php).

### ✔ `emit_annotations.php`

Reads the normalized JSON and emits GitHub annotations (`::error`, `::warning`). Full script: [`.github/scripts/emit_annotations.php`](.github/scripts/emit_annotations.php).

### ✔ `generate_solid_report.php`

Produces a Markdown report from the normalized JSON (no file if no violations). Full script: [`.github/scripts/generate_solid_report.php`](.github/scripts/generate_solid_report.php).

## 5. GitHub Actions Workflow

This workflow adds a SOLID analysis job to your CI pipeline, running after the standard lint stage and emitting annotations plus a Markdown summary when PHP files change. For the full configuration used in this repository, see [`.github/workflows/ci.yml`](.github/workflows/ci.yml).

```yaml
solid-ai:
  runs-on: ubuntu-latest
  continue-on-error: true
  
  if: always() && (github.event_name != 'pull_request' || github.event.pull_request.head.repo.fork == false)
    
  permissions:
    contents: read
    checks: write

  steps:
    - uses: actions/checkout@v4
      with:
        fetch-depth: 0

    - uses: shivammathur/setup-php@v2
      with:
        php-version: "8.4"

    - name: Detect modified PHP files
      id: detect
      run: |
        BASE="${{ github.event.pull_request.base.sha || github.event.before }}"
        HEAD="${{ github.event.pull_request.head.sha || github.sha }}"

        FILES=$(git diff --name-only "$BASE" "$HEAD" | grep -E '^(src|tests)/.*\.php$' || true)

        if [ -n "$FILES" ]; then
          echo "changed_php=$FILES" >> $GITHUB_OUTPUT
          echo "has_changes=true" >> $GITHUB_OUTPUT
        else
          echo "has_changes=false" >> $GITHUB_OUTPUT
        fi

    - uses: ai-action/setup-ollama@v1
      if: steps.detect.outputs.has_changes == 'true'

    - name: Pull model
      if: steps.detect.outputs.has_changes == 'true'
      run: ollama pull llama3.2

    - name: Run Inspecta AI
      id: inspecta
      if: steps.detect.outputs.has_changes == 'true'
      run: |
        RAW=".github/solid/raw.txt"
        FILES="${{ steps.detect.outputs.changed_php }}"

        ./vendor/bin/inspecta-ai analyze solid_violations $FILES \
          -c inspecta-ai.yaml \
          > "$RAW"

        echo "raw_file=$RAW" >> $GITHUB_OUTPUT

    - name: Normalize JSON
      id: parse
      if: steps.inspecta.outputs.raw_file
      run: |
        RAW="${{ steps.inspecta.outputs.raw_file }}"
        JSON=".github/solid/parsed.json"

        php .github/scripts/parse_response.php "$RAW" "$JSON"

        echo "parsed_file=$JSON" >> $GITHUB_OUTPUT

    - name: Emit annotations
      if: steps.parse.outputs.parsed_file
      run: |
        php .github/scripts/emit_annotations.php \
          "${{ steps.parse.outputs.parsed_file }}"

    - name: Generate SOLID report
      id: report
      if: steps.parse.outputs.parsed_file
      run: |
        MD=".github/solid/report.md"

        php .github/scripts/generate_solid_report.php \
          "${{ steps.parse.outputs.parsed_file }}" \
          "$MD"

        if [ -f "$MD" ]; then
          echo "report=$MD" >> $GITHUB_OUTPUT
        fi

    - name: Add report to summary
      if: steps.report.outputs.report
      run: |
        echo "## 🔍 SOLID Analysis (AI)" >> $GITHUB_STEP_SUMMARY
        echo "" >> $GITHUB_STEP_SUMMARY
        cat "${{ steps.report.outputs.report }}" >> $GITHUB_STEP_SUMMARY

    - name: Create SOLID / IA Check Run
      if: always()
      uses: actions/github-script@v7
      env:
        REPORT_EXISTS: ${{ steps.generate-report.outputs.report_exists }}
      with:
        github-token: ${{ secrets.GITHUB_TOKEN }}
        script: |
          const { owner, repo } = context.repo;
          const hasIssues = process.env.REPORT_EXISTS === 'true';

          // 🟢 No SOLID issues → success
          // ⚪ Issues detected → neutral (not blocking)
          const conclusion = hasIssues ? 'neutral' : 'success';

          const summary = hasIssues
            ? 'SOLID issues detected. See annotations and the solid-ai job logs for details.'
            : 'No SOLID issues detected.';

          await github.rest.checks.create({
            owner,
            repo,
            name: 'SOLID / IA',
            head_sha: context.sha,
            status: 'completed',
            conclusion,
            output: {
              title: 'SOLID / IA',
              summary,
            },
          });
```
