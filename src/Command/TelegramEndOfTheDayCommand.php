<?php

namespace App\Command;

use App\Entity\TelegramChat;
use App\Entity\Time;
use App\Entity\User;
use App\Repository\TelegramChatRepository;
use App\Repository\TimeRepository;
use App\Repository\UserRepository;
use App\Services\Telegram;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TelegramEndOfTheDayCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;
    protected static $defaultName = 'app:telegram:end-of-the-day';

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Exec me end of the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var TelegramChatRepository $telegramChatRepository */
        $telegramChatRepository = $this->em->getRepository(TelegramChat::class);


        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->getTelegramUsers();
        /** @var User $user */
        foreach ($users as $user){
            /** @var TelegramChat $telegramChat */
            $telegramChat = $telegramChatRepository->findOneBy(['chatId' => $user->getTelegramReportChatId()]);
            if(is_null($telegramChat)){
                $io->writeln("User " . $user->getUsername() . " skipped. Missing bot key for gaven chat id " . $user->getTelegramReportChatId());
                continue;
            }

            $message = $user->getUsername() . "
";
            /** @var TimeRepository $timeRepository */
            $timeRepository = $this->em->getRepository(Time::class);
            $data = $timeRepository->getDailyProjectHours($user->getWorkspaces()[0], new \DateTime('1 day ago'), new \DateTime('now'));

            $totalHours = 0;
            $totalProject = 0;
            if(count($data) > 0){
                foreach($data as $project){
                    if($project['project_name'] == 'ige'){
                        continue;
                    }
                    $totalHours += $project['hours'];
                    $totalProject++;
                    $message .= $project['project_name'] . ": " . number_format($project['hours'], 1)  . '
';
                }
            }else{
                $message .= ' sifir SIFIR sifir!! O 000
';
            }

            if($totalProject > 1){
                $message .= ' total ' . number_format($totalHours, 1);
            }
            if($totalHours > 9){
                $message .= ' CHEATERS DETECTED!!! ???
';
            }else if($totalHours > 5){
                $message .= ' nice job!
';
            }else if($totalHours < 2){
                $message .= ' better than nothing
';
            }else {
                $message .= ' 
';
            }

            $telegram = new Telegram();
            $telegram->sendMessage($message, $telegramChat->getChatId(), $telegramChat->getBotKey());
            $io->success($message);
        }


    }
}
