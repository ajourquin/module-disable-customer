<?php declare(strict_types = 1);

/**
 * @author    Aurélien Jourquin <aurelien@growzup.com>
 * @link      http://www.ajourquin.com
 */

namespace Ajourquin\DisableCustomer\Plugin\Model\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement as MagentoAccountManagement;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AccountManagement
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /**
     * AccountManagement constructor.
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param MagentoAccountManagement $subject
     * @param string $username
     * @param string $password
     * @return array
     * @throws InvalidEmailOrPasswordException|LocalizedException
     */
    public function beforeAuthenticate(MagentoAccountManagement $subject, string $username, string $password): array
    {
        try {
            $customer = $this->customerRepository->get($username);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        if ($customer->getCustomAttribute('is_disabled')->getValue() === '1') {
            throw new LocalizedException(__('Your account is disabled. Please contact customer service.'));
        }

        return [$username, $password];
    }
}
