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
class ClipVilla_MorI_Model_Stack extends Mage_Core_Model_Abstract
{

    const STATUS_QUEUE      = 0;
    const STATUS_DOWNLOAD   = 1;
    const STATUS_RENDERING  = 2;

    protected function _construct()
    {
        $this->_init('clipvilla_mori/stack');
    }

    /**
     * inserts entry into clipvilla_stack with priority 1 and deletes
     * the same entry if already in the table with (status must be 0)
     *
     * @param $videoId
     */
    public function prioritizeVideo($videoId)
    {
        $existingStackItem = Mage::getModel('clipvilla_mori/stack')->getCollection()
            ->addFieldToFilter('video_id', $videoId)
            ->getFirstItem();
        $stackData = $existingStackItem->getData();
        if (!empty($stackData)) {
            if ($existingStackItem->getStatus() == self::STATUS_QUEUE) {
                $existingStackItem->delete();
            }
        }

        $insertData = array(
            'video_id' => $videoId,
            'priority' => 1,
            'status'   => self::STATUS_QUEUE,
        );
        $this->setData($insertData);
        $this->save();

    }

}