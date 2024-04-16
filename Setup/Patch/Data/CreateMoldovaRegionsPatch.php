<?php

namespace BurlacuWeb\MoldovaRegions\Setup\Patch\Data;

use BurlacuWeb\MoldovaRegions\Service\GetRegionsService;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateMoldovaRegionsPatch implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private GetRegionsService $getRegionsService
    ) {

    }

    /**
     * Do Upgrade.
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $regions = $this->getRegionsService->execute();

        // get last region ID
        $lastRegionId = $this->moduleDataSetup->getConnection()->fetchOne(
            $this->moduleDataSetup->getConnection()->select()
                ->from($this->moduleDataSetup->getTable('directory_country_region'), 'MAX(region_id)')
        );

        foreach ($regions as $region) {
            $lastRegionId++;
            // insert the regions into the directory_country_region_name table
            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region_name'),
                [
                    'locale' => 'ro_RO',
                    'region_id' => $lastRegionId,
                    'name' => $region[0]
                ]
            );

            // add regions to the database for the country with code MD
            $this->moduleDataSetup->getConnection()->insert(
                $this->moduleDataSetup->getTable('directory_country_region'),
                [
                    'country_id' => 'MD',
                    'code' => $region[1],
                    'default_name' => $region[0],
                    'region_id' => $lastRegionId
                ]
            );

        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }
}
