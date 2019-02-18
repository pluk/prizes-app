<?php

namespace App\ConsoleCommand;


use App\Entity\Prize;
use App\Service\BankClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreditMoneyToBankCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var BankClient
     */
    private $bankClient;

    public function __construct(
        EntityManagerInterface $em,
        BankClient $bankClient
    ) {
        $this->em = $em;
        parent::__construct();
        $this->bankClient = $bankClient;
    }

    protected function configure()
    {
        $this->setName('prizes:credit-money-to-bank-account')
            ->setDescription('Консольная команда, которая зачисляет денежные средства на счета пользователей')
            ->addOption('batch-size', 'b', InputArgument::OPTIONAL, 'Размер пачки', 20);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $notFinishedPrizes = $this->em->getRepository(Prize::class)
            ->findNotFinishedMoneyPrizes();

        $batch = [];
        /** @var Prize $prize */
        foreach ($notFinishedPrizes as $prize) {
            $batch[] = [
                'prize' => $prize
            ];

            if (count($batch) == $input->getOption('batch-size')) {
                $this->sendMoney($batch);
            }
        }

        $this->sendMoney($batch);
    }

    protected function sendMoney(array $batch): void
    {
        $responses = $this->bankClient
            ->creditMoneyToBankAccountBatch($batch);

        foreach ($responses as $response) {
            if ($response['status'] === 'ok') {
                /** @var Prize */
                $response['prize']->markAsFinished();
            }
        }

        $this->em->flush();
    }
}