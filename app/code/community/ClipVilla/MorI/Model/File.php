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
class ClipVilla_MorI_Model_File extends Mage_Core_Model_Abstract
{

    /**
     * downloads the video
     *
     * @param $moriVideoFile
     * @param $shopVideoFile
     *
     * @return bool
     */
    public function downloadMovieFile($fullServerFilePath, $localFile)
    {
        try {
            preg_match("/Projects.*/", $fullServerFilePath, $serverFile);
            $filew = "ftp://Mgnt-Extension:CVMgnt8588@85.88.11.170:21/$serverFile[0]";
            $openFile = fopen($filew , 'r');
            $strwri = file_put_contents($localFile, $openFile);
			
        } catch (Exception $e) {
            return $e;
        }
    }


}