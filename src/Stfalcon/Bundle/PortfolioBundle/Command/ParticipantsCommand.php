<?php

namespace Stfalcon\Bundle\PortfolioBundle\Command;

use Application\Bundle\UserBundle\Entity\User;
use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\UserWithPosition;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ParticipantsCommand class
 *
 * @author Oleg Kachinsky <logansoleg@gmail.com>
 */
class ParticipantsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('portfolio:project:participants')
            ->setDescription('Move project participants to another table')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> move project participants to table where users have custom positions.
HELP
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $projects = $em->getRepository('StfalconPortfolioBundle:Project')->findAllWithoutUserWithPosition();
        if (empty($project)) {
            foreach ($projects as $project) {
                $participants = $project->getParticipants()->getValues();

                if (!empty($participants)) {
                    /** @var User $participant */
                    foreach ($participants as $participant) {
                        /** @var UserWithPosition $userWithPosition */
                        $userWithPosition = (new UserWithPosition())
                            ->setProject($project)
                            ->setUser($participant)
                            ->setPositions($participant->getPosition());

                        $em->persist($userWithPosition);

                        $output->writeln(sprintf(
                            '<comment>User with position for Proejct#<info>%s</info> <info>successfully</info> created</comment>',
                            $project->getId()
                        ));
                    }

                    $em->flush();
                } else {
                    $output->writeln(sprintf(
                        '<comment>Project #<info>%s</info> without participants</comment>',
                        $project->getId()
                    ));
                }
            }
        } else {
            $output->writeln('<comment>There\'s not projects without participants</comment>');
        }
    }
}