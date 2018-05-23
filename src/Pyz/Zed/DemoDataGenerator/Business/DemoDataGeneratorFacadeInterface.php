<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\DemoDataGenerator\Business;

interface DemoDataGeneratorFacadeInterface
{
    /**
     * @param int $rowsNumber
     *
     * @return void
     */
    public function createProductAbstractCsvDemoData(int $rowsNumber): void;

    /**
     * @param int $rowsNumber
     *
     * @return void
     */
    public function createProductConcreteCsvDemoData(int $rowsNumber): void;

}
