<?php
/**
*ClipVilla Video Producer
*
*The ClipVilla Video Producer's code is created by ClipVilla GmbH. 
*If the code is completely or partially changed by a third party all guarantee and warranty claims against ClipVilla GmbH will be invalidated.
*
* NOTICE OF LICENSE
*
* This source file is subject to the GNU General Public License (GPLv3)
* It is available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/quick-guide-gplv3.html.en
*/
class ClipVilla_MorI_Model_Mail extends Mage_Core_Model_Abstract
{

    /**
     * sends an Email in case of an Error during download, rendering etc
     *
     * @param $productId
     * @param $errorMessage
     *
     * @return string
     */
    public function sendErrorMail($productId, $errorMessage)
    {
        if (Mage::getStoreConfig('clipvilla/rendering/allow_mail')) {
            $emailTemplate = Mage::getModel('core/email_template')
                ->loadDefault('clipvilla_mail');
            $emailTemplateVariables = array(
                'productId'    => $productId,
                'errorMessage' => $errorMessage
            );

            $emailTemplate->setSenderName('ClipVilla - Error Messenger');
            $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

            $recipient = Mage::getStoreConfig('clipvilla/rendering/mail');
            $emailTemplate->send($recipient, 'Backend-User', $emailTemplateVariables);
        }
    }

}