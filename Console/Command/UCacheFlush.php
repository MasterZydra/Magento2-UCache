<?php

declare(strict_types=1);

namespace MasterZydra\UCache\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UCacheFlush extends \Symfony\Component\Console\Command\Command
{
    private \MasterZydra\UCache\Model\ResourceModel\UCache $ucacheRes;

    private const ARGUMENT_KEY = 'key';
    private const ARGUMENT_REGEX = 'regex';

    /** @inheritdoc */
    public function __construct(
        private \MasterZydra\UCache\Helper\UCache $helper,
        \MasterZydra\UCache\Model\ResourceModel\UCacheFactory $ucacheResFactory,
        private \MasterZydra\UCache\Model\ResourceModel\UCache\CollectionFactory $ucacheCollFactory,
        ?string $name = null,
    ) {
        parent::__construct($name);
        $this->ucacheRes = $ucacheResFactory->create();
    }

    /** @inheritdoc */
    protected function configure(): void
    {
        $this->setName('ucache:flush');
        $this->setDescription('Flush the universal cache');
        $this->addOption(self::ARGUMENT_REGEX, 'r', description: 'Cache key is regex');
        $this->addArgument(self::ARGUMENT_KEY, InputArgument::OPTIONAL, 'Cache key to delete');
        parent::configure();
    }

    /** Executes the current command */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cacheKey = $input->getArgument(self::ARGUMENT_KEY);
        $isRegex = $input->getOption(self::ARGUMENT_REGEX);

        if ($cacheKey === null) {
            $this->helper->clean();
            $output->writeln('Flushed ucache');
            return 0;
        }

        if ($isRegex) {
            $regex = '/' . $cacheKey . '/';
            $matchCount = 0;
            $cacheColl = $this->ucacheCollFactory->create();
            /** @var \MasterZydra\UCache\Model\UCache $entry */
            foreach ($cacheColl as $entry) {
                if (!preg_match_all($regex, $entry->getKey(), $matches)) {
                    continue;
                }
                $matchCount++;
                $output->writeln('Removed cache key "' . $entry->getKey() . '"');
                $this->ucacheRes->delete($entry);
            }
            $output->writeln('Removed ' . $matchCount . ' cache keys');
            return 0;
        }

        $this->helper->remove($cacheKey);
        $output->writeln('Removed cache key "' . $cacheKey . '"');

        return 0;
    }
}
