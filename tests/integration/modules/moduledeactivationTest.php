<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath(dirname(__FILE__).'/../../') . '/unit/OxidTestCase.php';
require_once realpath( dirname(__FILE__) ) . '/environmentvalidator.php';
require_once realpath( dirname(__FILE__) ) . '/environment.php';

class Integration_Modules_ModuleDeactivationTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->clean();
        parent::tearDown();
    }

    public function providerModuleDeactivation()
    {
        return array(
            $this->_caseSevenModulesPrepared_Deactivated_with_everything(),
            $this->_caseTwoModulesPrepared_Deactivated_with_everything(),
            $this->_caseFourModulesPrepared_Deactivated_extending_3_classes_with_1_extension(),
            $this->_caseEightModulesPrepared_Deactivated_no_extending(),
            $this->_caseTwoModulesPrepared_Deactivated_with_2_files(),
            $this->_caseTwoModulesPrepared_Deactivated_with_2_templates(),
            $this->_caseTwoModulesPrepared_Deactivated_with_2_settings(),
        );
    }

    /**
     * Test check shop environment after module deactivation
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleDeactivation( $aInstallModules, $sModuleId, $aResultToAssert )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        $oModule = new oxModule();
        $oModule->load( $sModuleId );
        $oModule->deactivate();

        $this->_runAsserts( $aResultToAssert );
    }

    /**
     * Data provider case with 7 modules prepared and with_everything module deactivated
     *
     * @return array
     */
    private function _caseSevenModulesPrepared_Deactivated_with_everything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events'
            ),

            // module that will be deactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                ),
                'disabledModules' => array(
                    'with_everything'
                ),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                ),
                'events'          => array(
                    'extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_settings'    => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_everything module deactivated
     *
     * @return array
     */
    private function _caseTwoModulesPrepared_Deactivated_with_everything()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_everything', 'no_extending'
            ),

            // module that will be deactivated
            'with_everything',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxorder'   => 'with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(),
                'settings'        => array(
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                ),
                'disabledModules' => array(
                    'with_everything'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'  => '1.0',
                ),
                'events'          => array(
                    'no_extending'  => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 4 modules prepared and extending_3_classes_with_1_extension module deactivated
     *
     * @return array
     */
    private function _caseFourModulesPrepared_Deactivated_extending_3_classes_with_1_extension()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class_3_extensions', 'extending_1_class',
                'extending_3_classes_with_1_extension', 'extending_3_classes'
            ),

            // module that will be deactivated
            'extending_1_class_3_extensions',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class_3_extensions/myorder1&extending_1_class_3_extensions/myorder2&'.
                                   'extending_1_class_3_extensions/myorder3&extending_1_class/myorder&'.
                                   'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myorder',
                    'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myarticle',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass&extending_3_classes/myuser',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'extending_1_class_3_extensions'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'extending_3_classes_with_1_extension'  => '1.0',
                    'extending_1_class'  => '1.0',
                    'extending_3_classes'  => '1.0',
                ),
                'events'          => array(
                    'extending_3_classes_with_1_extension'  => null,
                    'extending_1_class'  => null,
                    'extending_3_classes'  => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 8 modules prepared and no_extending module deactivated
     *
     * @return array
     */
    private function _caseEightModulesPrepared_Deactivated_no_extending()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'extending_1_class', 'with_2_templates', 'with_2_files', 'with_2_settings',
                'extending_3_blocks', 'with_everything', 'with_events', 'no_extending'
            ),

            // module that will be deactivated
            'no_extending',

            // environment asserts
            array(
                'blocks'          => array(
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_top', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/basket.tpl', 'block' => 'basket_btn_next_bottom', 'file' => '/views/blocks/page/checkout/myexpresscheckout.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                    array( 'template' => 'page/checkout/payment.tpl', 'block' => 'select_payment', 'file' => '/views/blocks/page/checkout/mypaymentselector.tpl' ),
                ),
                'extend'          => array(
                    'oxorder'   => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                    'oxarticle' => 'with_everything/myarticle',
                    'oxuser'    => 'with_everything/myuser',
                ),
                'files'           => array(
                    'with_2_files' => array(
                        'myexception'  => 'with_2_files/core/exception/myexception.php',
                        'myconnection' => 'with_2_files/core/exception/myconnection.php',
                    ),
                    'with_everything' => array(
                        'myexception'  => 'with_everything/core/exception/myexception.php',
                        'myconnection' => 'with_everything/core/exception/myconnection.php',
                    ),
                ),
                'settings'        => array(
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                    array( 'group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true' ),
                    array( 'group' => 'my_displayname', 'name' => 'sDisplayName', 'type' => 'str', 'value' => 'Some name' ),
                ),
                'disabledModules' => array(
                    'no_extending'
                ),
                'templates'       => array(
                    'with_2_templates' => array(
                        'order_special.tpl'    => 'with_2_templates/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_2_templates/views/tpl/user_connections.tpl',
                    ),
                    'with_everything' => array(
                        'order_special.tpl'    => 'with_everything/views/admin/tpl/order_special.tpl',
                        'user_connections.tpl' => 'with_everything/views/tpl/user_connections.tpl',
                    ),
                ),
                'versions'        => array(
                    'extending_1_class'  => '1.0',
                    'with_2_templates'   => '1.0',
                    'with_2_settings'    => '1.0',
                    'with_2_files'       => '1.0',
                    'extending_3_blocks' => '1.0',
                    'with_events'        => '1.0',
                    'with_everything'    => '1.0',
                ),
                'events'          => array(
                    'extending_1_class'  => null,
                    'with_2_templates'   => null,
                    'with_2_settings'    => null,
                    'with_2_files'       => null,
                    'extending_3_blocks' => null,
                    'with_events'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                    'with_everything'        => array(
                        'onActivate'   => 'MyEvents::onActivate',
                        'onDeactivate' => 'MyEvents::onDeactivate'
                    ),
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_files module deactivated
     *
     * @return array
     */
    private function _caseTwoModulesPrepared_Deactivated_with_2_files()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_files', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_files',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_2_files'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'  => '1.0',
                ),
                'events'          => array(
                    'no_extending'  => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_templates module deactivated
     *
     * @return array
     */
    private function _caseTwoModulesPrepared_Deactivated_with_2_templates()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_templates', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_templates',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'with_2_templates'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'  => '1.0',
                ),
                'events'          => array(
                    'no_extending'  => null,
                ),
            )
        );
    }

    /**
     * Data provider case with 2 modules prepared and with_2_settings module deactivated
     *
     * @return array
     */
    private function _caseTwoModulesPrepared_Deactivated_with_2_settings()
    {
        return array(

            // modules to be activated during test preparation
            array(
                'with_2_settings', 'no_extending'
            ),

            // module that will be deactivated
            'with_2_settings',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(),
                'files'           => array(),
                'settings'        => array(
                    array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                    array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
                ),
                'disabledModules' => array(
                    'with_2_settings'
                ),
                'templates'       => array(),
                'versions'        => array(
                    'no_extending'  => '1.0',
                ),
                'events'          => array(
                    'no_extending'  => null,
                ),
            )
        );
    }


    /**
     * Runs all asserts
     *
     * @param $aExpectedResult
     */
    private function _runAsserts( $aExpectedResult )
    {
        $oValidator = new EnvironmentValidator();
        $oValidator->setConfig( $this->getConfig() );

        if( isset( $aExpectedResult['blocks'] ) ){
            $this->assertTrue( $oValidator->checkBlocks( $aExpectedResult['blocks'] ), 'Blocks do not match expectations' );
        }

        if( isset( $aExpectedResult['extend'] ) ){
            $this->assertTrue( $oValidator->checkExtensions( $aExpectedResult['extend'] ), 'Extensions do not match expectations' );
        }

        if( isset( $aExpectedResult['files'] ) ){
            $this->assertTrue( $oValidator->checkFiles( $aExpectedResult['files'] ), 'Files do not match expectations' );
        }

        if( isset( $aExpectedResult['events'] ) ){
            $this->assertTrue( $oValidator->checkEvents( $aExpectedResult['events'] ), 'Events do not match expectations' );
        }

        if( isset( $aExpectedResult['settings'] ) ){
            $this->assertTrue( $oValidator->checkConfigs( $aExpectedResult['settings'] ), 'Configs do not match expectations' );
        }

        if( isset( $aExpectedResult['versions'] ) ){
            $this->assertTrue( $oValidator->checkVersions( $aExpectedResult['versions'] ), 'Versions do not match expectations' );
        }

        if( isset( $aExpectedResult['templates'] ) ){
            $this->assertTrue( $oValidator->checkTemplates( $aExpectedResult['templates'] ), 'Templates do not match expectations' );
        }
    }


}
 