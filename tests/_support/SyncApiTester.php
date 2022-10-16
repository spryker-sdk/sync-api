<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class SyncApiTester extends Actor
{
    use _generated\SyncApiTesterActions;

    /**
     * @param string $yamlFixturePath
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function loadOpenApiFromYaml(string $yamlFixturePath): OpenApi
    {
        return Reader::readFromYamlFile(codecept_data_dir('api/' . $yamlFixturePath), OpenApi::class, false);
    }
}
