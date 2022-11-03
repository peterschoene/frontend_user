<?php

declare(strict_types=1);

/**
 * Frontend User Extension
 *
 * @copyright   Copyright (c) 2022 Your Dev Team Global (https://ydt-global.com/)
 * @author      YDT Global Team <typo3@ydt-global.com>
 */

namespace Ydt\FrontendUser\Tests\Unit\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Ydt\FrontendUser\Domain\Model\FrontendUser;
use Ydt\FrontendUser\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class FrontendUserRepositoryTest
 * Testcase for class \Ydt\FrontendUser\Domain\Repository\FrontendUserRepository
 */
class FrontendUserRepositoryTest extends UnitTestCase
{
    /**
     * Frontend User Repository
     *
     * @var FrontendUserRepository
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $querySettingsMock = $this->createMock(QuerySettingsInterface::class);

        $builder = $querySettingsMock->method('getStoragePageIds');
        $builder->willReturn([0]);
        $builder = $querySettingsMock->method('setStoragePageIds');
        $builder->willReturnSelf();

        $frontendUserMock = $this->createMock(FrontendUser::class);
        $queryResultMock = $this->createMock(QueryResultInterface::class);
        $builder = $queryResultMock->method('getFirst');
        $builder->willReturn($frontendUserMock);

        $queryMock = $this->createMock(QueryInterface::class);

        $builder = $queryMock->method('getQuerySettings');
        $builder->willReturn($querySettingsMock);
        $builder = $queryMock->method('execute');
        $builder->willReturn($queryResultMock);

        $persistenceManagerMock = $this->createMock(PersistenceManagerInterface::class);
        $builder = $persistenceManagerMock->method('createQueryForType');
        $builder->willReturn($queryMock);

        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->subject = new FrontendUserRepository($objectManagerMock);

        $this->subject->injectPersistenceManager($persistenceManagerMock);
    }

    /**
     * Test findByUsername
     *
     * @return void
     */
    public function testFindByUsername(): void
    {
        $result = $this->subject->findByUsername('test', 1);

        $this->assertInstanceOf(FrontendUser::class, $result);
    }
}
