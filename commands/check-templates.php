<?php
require __DIR__ . '/../vendor/autoload.php';

use KirbyEmailManager\CLI\TemplateChecker;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;

$kirby = kirby();

$output = new ConsoleOutput();
$output->setFormatter(new OutputFormatter(true));

$checker = new TemplateChecker();
$result = $checker->check();

$output->writeln("\n<info>🔦 E-Mail Template Check</info>\n");

if (!empty($result['errors'])) {
    $output->writeln("<error>❌ Errors detected:</error>");
    foreach ($result['errors'] as $error) {
        $output->writeln("  • {$error}");
    }
    $output->writeln('');
}

if (!empty($result['warnings'])) {
    $output->writeln("<comment>⚠️  Warnings:</comment>");
    foreach ($result['warnings'] as $warning) {
        $output->writeln("  • {$warning}");
    }
    $output->writeln('');
}

if (empty($result['errors']) && empty($result['warnings'])) {
    $output->writeln("<info>✅ All templates are correctly configured!</info>\n");
} else {
    $summary = sprintf(
        "\nSummary: %d errors, %d warnings\n",
        count($result['errors']),
        count($result['warnings'])
    );
    $output->writeln($summary);
}

exit(0);