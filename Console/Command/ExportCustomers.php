<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Console\Command;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wojtekn\Channels\Logger\Logger;
use Wojtekn\Channels\Model\Config;
use Wojtekn\Channels\Model\CustomerAddressExtractor;
use Wojtekn\Channels\Model\CustomerExportScheduler;

class ExportCustomers extends Command
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerAddressExtractor
     */
    private $customerAddressExtractor;

    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerExportScheduler
     */
    private $customerExportScheduler;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Iterator
     */
    private $resourceIterator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param CustomerAddressExtractor $customerAddressExtractor
     * @param CollectionFactory $customerCollectionFactory
     * @param CustomerExportScheduler $customerExportScheduler
     * @param CustomerFactory $customerFactory
     * @param Logger $logger
     * @param Iterator $resourceIterator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        CustomerAddressExtractor $customerAddressExtractor,
        CollectionFactory $customerCollectionFactory,
        CustomerExportScheduler $customerExportScheduler,
        CustomerFactory $customerFactory,
        Logger $logger,
        Iterator $resourceIterator,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->customerAddressExtractor = $customerAddressExtractor;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerExportScheduler = $customerExportScheduler;
        $this->customerFactory = $customerFactory;
        $this->logger = $logger;
        $this->resourceIterator = $resourceIterator;
        $this->storeManager = $storeManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('channels:customer:export');
        $this->setDescription('Schedules all customers for export');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     *
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $websites = $this->storeManager->getWebsites();

        $output->writeln('<info>Start scheduling customers for export.</info>');

        foreach ($websites as $website) {
            $websiteId = (int) $website->getId();

            if (!$this->config->isEnabled($websiteId)) {
                continue;
            }

            $collection = $this->customerCollectionFactory->create();
            $collection->addAttributeToFilter('website_id', $websiteId);

            $this->resourceIterator->walk(
                $collection->getSelect(),
                [[$this, 'callbackScheduleCustomer']],
                [
                    'customer' => $this->customerFactory->create()
                ]
            );

            $output->writeln(sprintf(
                '<info>Finished scheduling customers from website #%d for export.</info>',
                $websiteId
            ));
        }

        $output->writeln('<info>All customers have been scheduled for export.</info>');
    }

    /**
     * Callback function for customer export scheduling
     *
     * @param array $args
     * @return void
     */
    public function callbackScheduleCustomer($args)
    {
        $customer = clone $args['customer'];
        $customer->setData($args['row']);
        $customerDataModel = $customer->getDataModel();

        $address = $this->customerAddressExtractor->getAddress($customerDataModel);

        if (!($address instanceof AddressInterface)) {
            return;
        }

        $this->logger->debug(sprintf(
            'Scheduling customer #%d from website #%d for export (CLI)',
            (int) $customer->getId(),
            (int) $customer->getWebsiteId()
        ));

        $this->customerExportScheduler->schedule($customerDataModel, $address);
    }
}
