<?php

namespace BurlacuWeb\MoldovaRegions\Service;

use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir\Reader;
use Psr\Log\LoggerInterface;

class GetRegionsService
{
    private const FILE_PATH = 'data/regions.csv';

    /**
     * @param File $fileReader
     * @param Reader $moduleReader
     * @param LoggerInterface $logger
     * @param Csv $csvReader
     */
    public function __construct(
        private File $fileReader,
        private Reader $moduleReader,
        private LoggerInterface $logger,
        private Csv $csvReader
    ) {

    }

    /**
     * @return array
     */
    public function execute(): array
    {
        try {
            $csvFile = $this->getCsvFileFullPath();

            if ($this->fileReader->isExists($csvFile)) {
                $this->csvReader->setDelimiter(",");
                $data = $this->csvReader->getData($csvFile);

                // remove the header
                array_shift($data);

                return $data;
            } else {
                $this->logger->info('CSV file with Moldova regions does not exist');
                return [];
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            return [];
        }
    }

    /**
     * @return string
     */
    private function getCsvFileFullPath(): string
    {
        $modulePath = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_SETUP_DIR,
            'BurlacuWeb_MoldovaRegions'
        );

        return $modulePath . DIRECTORY_SEPARATOR . self::FILE_PATH;
    }
}
