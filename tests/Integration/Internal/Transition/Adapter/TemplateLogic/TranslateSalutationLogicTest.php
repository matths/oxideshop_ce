<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateSalutationLogic;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\LegacyTemplateTranslator;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;

/**
 * Class TranslateSalutationLogic
 */
class TranslateSalutationLogicTest extends IntegrationTestCase
{
    /**
     * Provides data for testTranslateSalutation
     *
     * @return array
     */
    public function translateSalutationProvider(): array
    {
        return [
            ['MR', 0, 'Herr'],
            ['MRS', 0, 'Frau'],
            ['MR', 1, 'Mr'],
            ['MRS', 1, 'Mrs']
        ];
    }

    /**
     * @param string $ident
     * @param int    $languageId
     * @param string $expected
     *
     * @dataProvider translateSalutationProvider
     */
    public function testTranslateSalutation(string $ident, int $languageId, string $expected): void
    {
        $translateSalutationLogic = new TranslateSalutationLogic($this->getTranslator($languageId));
        $this->assertEquals($expected, $translateSalutationLogic->translateSalutation($ident));
    }

    /**
     * @param $languageId
     * @return LegacyTemplateTranslator
     */
    private function getTranslator($languageId)
    {
        $language = Registry::getLang();
        $language->setTplLanguage($languageId);
        $language->setAdminMode(false);
        Registry::set(Language::class, $language);
        return new LegacyTemplateTranslator();
    }
}
