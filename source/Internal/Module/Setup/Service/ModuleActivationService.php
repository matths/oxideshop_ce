<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\AfterModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ModuleActivationService implements ModuleActivationServiceInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ModuleCacheServiceInterface
     */
    //private $moduleCacheService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ModuleSettingsHandlingServiceInterface
     */
    private $moduleSettingsHandlingService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $stateService;

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationDaoInterface        $ModuleConfigurationDao
     * @param EventDispatcherInterface               $eventDispatcher
     * @param ModuleSettingsHandlingServiceInterface $moduleSettingsHandlingService
     * @param ModuleStateServiceInterface            $stateService
     */
    public function __construct(
        ModuleConfigurationDaoInterface         $ModuleConfigurationDao,
        EventDispatcherInterface                $eventDispatcher,
        //ModuleCacheServiceInterface             $moduleCacheService,
        ModuleSettingsHandlingServiceInterface  $moduleSettingsHandlingService,
        ModuleStateServiceInterface             $stateService
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
        //$this->moduleCacheService = $moduleCacheService;
        $this->moduleSettingsHandlingService = $moduleSettingsHandlingService;
        $this->stateService = $stateService;
        //$this->moduleCacheService = $moduleCacheService;
        //updateChain
        //handle module yml services / ShopActivationService
        // ACTIVE_MODULES: add to, delete from
        //autoActivate - projectConfigurationDao
        // State service
        // transaction service
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        if ($this->stateService->isActive($moduleId, $shopId) === true) {
            throw new ModuleSetupException('Module with id "'. $moduleId . '" is already active.');
        }

        $this->stateService->setActive($moduleId, $shopId);

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            AfterModuleActivationEvent::NAME,
            new AfterModuleActivationEvent($shopId, $moduleId)
        );

        //$this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        if ($this->stateService->isActive($moduleId, $shopId) === false) {
            throw new ModuleSetupException('Module with id "'. $moduleId . '" is not active.');
        }

        $this->stateService->setDeactivated($moduleId, $shopId);

        $this->eventDispatcher->dispatch(
            BeforeModuleDeactivationEvent::NAME,
            new BeforeModuleDeactivationEvent($shopId, $moduleId)
        );

        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        $this->moduleSettingsHandlingService->handleOnDeactivation($moduleConfiguration, $shopId);

        //$this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }
}
