<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2012 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sub1_Nps_Block_Form extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()  
    {  
        parent::_construct();  
        
        //Where do you think the association with phtml and classes were made?  
        $this->setTemplate('nps/form.phtml'); //This time "/" does not define the namespace but file system!  
    }      
    
    public function getConfiguredProducts() 
    {
    
    }
}
