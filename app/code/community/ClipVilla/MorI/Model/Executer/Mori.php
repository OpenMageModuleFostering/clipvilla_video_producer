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
class ClipVilla_MorI_Model_Executer_Mori
{

    const WSDL = 'http://85.88.11.170:8080/MoRI/mori?wsdl';

    /**
     * starts the rendering process via soap for a single entry
     *
     * @param $soapVar
     *
     * @return mixed
     */
    public function startRendering($soapVar)
    {
        $options = array();
        $wsdl = self::WSDL;

        $soapclient = new SoapClient($wsdl, $options);
        $response = $soapclient->renderProjectMtom($soapVar);
        $responseObj = $response->RenderResponse;

        if (property_exists($responseObj, 'error')) {
            return $responseObj->error->message;
        } else {
            return $responseObj->renderJobId;
        }
    }

    /**
     * returns the Status of the Video currently in rendering
     * returns the file path, if the video rendering is complete
     *
     * @param $id
     *
     * @return mixed
     */
    public function getStatus($id)
    {
        $wsdl = self::WSDL;;
        $options = array();

        $soapVar = array(
            'StatusRequest' => array(
                'renderJobId' => $id
            )
        );

        $soapclient = new SoapClient($wsdl, $options);
        $response = $soapclient->getStatus($soapVar);

        $responseObj = $response->StatusResponse;

        return $responseObj;
    }
}