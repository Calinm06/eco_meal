<?php

namespace App\Command;

use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:packages:add-discount',
    description: 'Add a short description for your command',
)]

#[AsCronTask(('* * * * *'))]
class PackagesAddDiscountCommand extends Command
{
    public function __construct(private readonly PackageRepository $packageRepository, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $package = $this->packageRepository->findOneBy(['id' => 57]);
        $price = $package->getPrice();
        $reduced_price = $price - 1;
        $package->setPrice($reduced_price);
        $this->entityManager->flush();

//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
