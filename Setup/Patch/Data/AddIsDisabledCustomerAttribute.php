<?php declare(strict_types = 1);

/**
 * @author    Aurélien Jourquin <aurelien@growzup.com>
 * @link      http://www.ajourquin.com
 */

namespace Ajourquin\DisableCustomer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddIsDisabledCustomerAttribute implements DataPatchInterface
{
    private const ATTRIBUTE_CODE = 'is_disabled';

    /** @var EavSetupFactory */
    private $customerSetupFactory;

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var AttributeResource */
    private $attributeResource;

    /**
     * AddIsDisabledCustomerAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeResource $attributeResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeResource = $attributeResource;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'static',
                'label' => 'Account disabled',
                'input' => 'boolean',
                'required' => false,
                'visible' => true,
                'system' => false,
                'source' => Boolean::class,
                'default' => 0,
                'note' => 'Determine if customer can log in on storefront'
            ]
        );

        $isBlockedAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CODE
        );

        $isBlockedAttribute->addData([
            'used_in_forms' => ['adminhtml_customer'],
        ]);

        $this->attributeResource->save($isBlockedAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
