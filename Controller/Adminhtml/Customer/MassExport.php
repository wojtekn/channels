<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Wojtekn\CrazyCall\Logger\Logger;
use Wojtekn\CrazyCall\Model\CustomerAddressExtractor;
use Wojtekn\CrazyCall\Model\CustomerExportScheduler;

/**
 * Class MassExport allows scheduling export for selected customers
 */
class MassExport extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * @var string
     */
    protected $redirectUrl = 'customer/index/index';

    /**
     * @var CustomerAddressExtractor
     */
    private $customerAddressExtractor;

    /**
     * @var CustomerExportScheduler
     */
    private $customerExportScheduler;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerAddressExtractor $customerAddressExtractor
     * @param CustomerExportScheduler $customerExportScheduler
     * @param CustomerRepositoryInterface $customerRepository
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerAddressExtractor $customerAddressExtractor,
        CustomerExportScheduler $customerExportScheduler,
        CustomerRepositoryInterface $customerRepository,
        Logger $logger
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerAddressExtractor = $customerAddressExtractor;
        $this->customerExportScheduler = $customerExportScheduler;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * Customer mass export to Crazy Call action
     *
     * @param AbstractCollection $collection
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersUpdated = 0;

        foreach ($collection->getAllIds() as $customerId) {
            $customerDataModel = $this->customerRepository->getById($customerId);

            $address = $this->customerAddressExtractor->getAddress($customerDataModel);

            if (!($address instanceof AddressInterface)) {
                continue;
            }

            $this->logger->debug(sprintf(
                'Scheduling customer #%d from website #%d for export (mass action)',
                (int) $customerDataModel->getId(),
                (int) $customerDataModel->getWebsiteId()
            ));

            $this->customerExportScheduler->schedule($customerDataModel, $address);

            $customersUpdated++;
        }

        if ($customersUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $customersUpdated));
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);

        return $resultRedirect;
    }
}
