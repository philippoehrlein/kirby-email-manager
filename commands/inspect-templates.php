<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KirbyEmailManager\CLI\TemplateInspector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InspectTemplatesCommand
 * @package KirbyEmailManager\CLI
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class InspectTemplatesCommand extends Command
{
    protected static $defaultName = 'inspect:templates';

    protected function configure()
    {
        $this
            ->setName('inspect:templates')
            ->setDescription('Inspects email templates and returns actual values of $email and $form.')
            ->addArgument('templateId', InputArgument::REQUIRED, 'The ID of the template to inspect');
    }

    /**
     * Executes the command.
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return int The exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $templateId = $input->getArgument('templateId');
        $inspector = new TemplateInspector();
        $result = $inspector->inspect($templateId);

        if (isset($result['error'])) {
            $output->writeln('<error>' . $result['error'] . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>$form:</info>');
        print_r($result['form']);

        $output->writeln('<info>$email:</info>');
        print_r($result['content']);

        return Command::SUCCESS;
    }
}

$application = new Application();
$application->add(new InspectTemplatesCommand());
$application->run();