<?php

namespace App\Command;

use App\Entity\Job\ImportFile;
use App\Repository\ImportFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'app:import-files:delete-old', description: "Remove old import files from both database and the import directory")]
class DeleteOldImportFileCommand extends Command
{
    /** @var ImportFileRepository $repository */
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager, ?string $name = null)
    {
        parent::__construct($name);
        $this->repository = $entityManager->getRepository(ImportFile::class);
    }

    public function configure(): void
    {
        $this->addOption(
            'relative-date-time',
            '-r',
            InputOption::VALUE_REQUIRED,
            'Relative date string: only negative basic relative adjustment [ex. -1 day] are allowed',
            '-1 day'
        )->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Only list the delete candidates'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (
            $input->isInteractive()
            && !$input->hasOption('dry-run')
            && !$this->askConfirmation(
                $input,
                $output,
                sprintf(
                    '<question>Careful, all import files with relative date "%s" will be delete. Do you want to continue y/N ?</question>',
                    $input->getOption('relative-date-time'),
                )
            )
        ) {
            return self::FAILURE;
        }

        if ($input->hasOption('dry-run') && $input->getOption('dry-run') === true) {
            $output->writeln('<info>Delete candidates</info>');
            $deleted = $this->repository->findRecordOlderThan($input->getOption('relative-date-time'));
        } else {
            $output->writeln('<info>Deleted files</info>');
            $deleted = $this->repository->deleteRecordOlderThan($input->getOption('relative-date-time'));
        }

        foreach ($deleted as $record) {
            $output->writeln(sprintf('<info>%s</info>', $record->getFilePath()));
        }

        return self::SUCCESS;
    }

    private function askConfirmation(
        InputInterface $input,
        OutputInterface $output,
        string $question,
    ): bool {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, false);

        return (bool)$questionHelper->ask($input, $output, $question);
    }
}
