<?php

/**
 * AcySMS SMS.ir Gateway Main Class File
 * 
 * PHP version 5.6.x | 7.x | 8.x
 * 
 * @category  Library
 * @package   Joomla AcySMS
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 * @version   v1.0.1
 * @link      https://github.com/pejmankheyri/SMSIR-JoomlaAcy
 */

defined('_JEXEC') or die('Restricted access');

/**
 * AcySMS SMS.ir Gateway Main Class
 * 
 * @category  Library
 * @package   Joomla AcySMS
 * @author    Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 * @version   v1.0.1
 * @link      https://github.com/pejmankheyri/SMSIR-JoomlaAcy
 */
class ACYSMSGateway_smsir_gateway extends ACYSMSGateway_default_gateway
{
    /**
     * Gets API Customer Club Send To Categories Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPICustomerClubSendToCategoriesUrl()
    {
        return "api/CustomerClub/SendToCategories";
    }

    /**
     * Gets API Message Send Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPIMessageSendUrl()
    {
        return "api/MessageSend";
    }

    /**
     * Gets API Customer Club Add Contact And Send Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPICustomerClubAddAndSendUrl()
    {
        return "api/CustomerClub/AddContactAndSend";
    }

    /**
     * Gets API credit Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPIcreditUrl()
    {
        return "api/credit";
    }

    /**
     * Gets Api Token Url.
     *
     * @return string Indicates the Url
     */
    protected function getApiTokenUrl()
    {
        return "api/Token";
    }

    public $username;
    public $senderName;
    public $waittosend = 0;
    public $indexMessage = 0;
    public $messageToSend = array();
    public $messageResults = array();

    public $sendMessage = true;
    public $deliveryReport = true;
    public $answerManagement = true;

    public $domain = 'sms.ir';
    public $port = 80;

    public $name = 'sms.ir';
    public $creditsUrl = 'https://www.acyba.com/acysms/purchase-sms.html';

    public $isclubnumber = false;

    /**
     * Open Send method for controller
     *
     * @param string  $message sms message
     * @param integer $phone   sms number
     *
     * @return integer Message index
     */
    public function openSend($message, $phone)
    {
        $this->indexMessage += 1;

        $oneMessage = new stdClass();
        $oneMessage->receiver = $this->checkNum($phone);
        $oneMessage->body = $message;
        $this->messageToSend[$this->indexMessage] = $oneMessage;

        return $this->indexMessage;
    }

    /**
     * Get Credit.
     *
     * @return string Indicates the sent sms result
     */
    public function getCredit()
    {
        $token = $this->_getToken($this->username, $this->password);
        $result = false;
        if ($token != false) {
            $url = $this->apidomain.$this->getAPIcreditUrl();
            $GetCredit = $this->_executeCredit($url, $token);
            $object = json_decode($GetCredit);

            if (is_object($object)) {
                if ($object->IsSuccessful == true) {
                    $result = $object->Credit;
                } else {
                    $result = $object->Message;
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Customer Club Send To Categories.
     *
     * @param Messages[] $Messages array structure of messages
     *
     * @return boolean Indicates the sent sms result
     */
    public function sendSMStoCustomerclubContacts($Messages)
    {
        $contactsCustomerClubCategoryIds = array();
        $token = $this->_getToken($this->username, $this->password);
        if ($token != false) {
            $postData = array(
                'Messages' => $Messages,
                'contactsCustomerClubCategoryIds' => $contactsCustomerClubCategoryIds,
                'SendDateTime' => '',
                'CanContinueInCaseOfError' => 'false'
            );

            $url = $this->apidomain.$this->getAPICustomerClubSendToCategoriesUrl();
            $CustomerClubSendToCategories = $this->_execute($postData, $url, $token);
            $object = json_decode($CustomerClubSendToCategories);

            if (is_object($object)) {
                if ($object->IsSuccessful == true) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Display Config
     *
     * @return void
     */
    public function displayConfig()
    {
        $isclubnumberData[] = JHTML::_('select.option', '1', JText::_('SMS_YES'));
        $isclubnumberData[] = JHTML::_('select.option', '0', JText::_('SMS_NO'));
        $isclubnumber = empty($this->isclubnumber) ? 0 : 1;
        $isclubnumberOption = JHTML::_('acysmsselect.radiolist', $isclubnumberData, 'data[senderprofile][senderprofile_params][isclubnumber]', '', 'value', 'text', $isclubnumber);

        ?>

        <table>
            <tr>
                <td class="key">
                    <label for="senderprofile_apidomain"><?php echo ACYSMS::tooltip('کلید وب سرویس شما در sms.ir', JText::_('SMS_APIDOMAIN'), '', JText::_('SMS_APIDOMAIN')); ?></label>
                </td>
                <td>
                    <input type="text" name="data[senderprofile][senderprofile_params][apidomain]" id="senderprofile_apidomain" class="inputbox" value="<?php echo htmlspecialchars(@$this->apidomain, ENT_COMPAT, 'UTF-8'); ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <label for="senderprofile_username"><a href="http://ip.sms.ir/#/UserApiKey" target="_blank"><?php echo ACYSMS::tooltip('کلید وب سرویس شما در sms.ir', JText::_('SMS_USERNAME'), '', JText::_('SMS_USERNAME')); ?></a></label>
                </td>
                <td>
                    <input type="text" name="data[senderprofile][senderprofile_params][username]" id="senderprofile_username" class="inputbox" value="<?php echo htmlspecialchars(@$this->username, ENT_COMPAT, 'UTF-8'); ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <label for="senderprofile_password"><a href="http://ip.sms.ir/#/UserApiKey" target="_blank"><?php echo ACYSMS::tooltip('کد امنیتی شما در sms.ir', JText::_('SMS_PASSWORD'), '', JText::_('SMS_PASSWORD')); ?></a></label>
                </td>
                <td>
                    <input type="password" name="data[senderprofile][senderprofile_params][password]" id="senderprofile_password" class="inputbox" value="<?php echo htmlspecialchars(@$this->password, ENT_COMPAT, 'UTF-8'); ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <label for="senderprofile_linenumber"><a href="http://ip.sms.ir/#/UserSetting" target="_blank"><?php echo ACYSMS::tooltip('شماره ارسالی پیامک در sms.ir', JText::_('SMS_FROM'), '', JText::_('SMS_FROM')); ?></a></label>
                </td>
                <td>
                    <input type="text" name="data[senderprofile][senderprofile_params][linenumber]" id="senderprofile_linenumber" class="inputbox" value="<?php echo htmlspecialchars(@$this->linenumber, ENT_COMPAT, 'UTF-8'); ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <label for="senderprofile_isclubnumber"><?php echo ACYSMS::tooltip(JText::_('SMS_IS_CUSTOMER_CLUB_NUMBER'), JText::_('SMS_IS_CUSTOMER_CLUB_NUMBER'), '', JText::_('SMS_IS_CUSTOMER_CLUB_NUMBER')); ?></label>
                </td>
                <td>
                    <?php echo $isclubnumberOption; ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <label for="senderprofile_getcredit"><?php echo ACYSMS::tooltip(JText::_('SMS_IS_GET_CREDIT'), JText::_('SMS_IS_GET_CREDIT'), '', JText::_('SMS_IS_GET_CREDIT')); ?></label>
                </td>
                <td>
                    <?php echo $this->getCredit()." پیامک "; ?>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Close Send method
     *
     * @param integer $idMessage sent message id
     * 
     * @return boolean send status
     */
    public function closeSend($idMessage)
    {
        if (!empty($this->messageToSend)) {
            $this->_sendMessages();
            $this->messageToSend = array();
        }
        if (empty($this->messageResults[$idMessage])) {
            $this->errors[] = 'Status not found for the message ID : '.$idMessage;
            $this->errors[] = print_r($this->messageResults, true);
            return false;
        }

        if (!empty($this->messageResults[$idMessage]->smsid)) $this->smsid = $this->messageResults[$idMessage]->smsid;
        if (!empty($this->messageResults[$idMessage]->info)) $this->errors[] = $this->messageResults[$idMessage]->info;
        return $this->messageResults[$idMessage]->status;
    }

    /**
     * Send SMS.
     *
     * @return boolean
     */
    private function _sendMessages()
    {
        $isclubnumber = $this->isclubnumber;
        $messagearray = $this->messageToSend;
        $messagevars = get_object_vars($messagearray[1]);
        $messagereceiver = $messagevars['receiver'];
        $numberr = substr($messagereceiver, -10);
        $messagebody = $messagevars['body'];
        $numberss[] = doubleval($numberr);

        foreach ($numberss as $nkey => $nvalue) {
            if (($this->isMobile($nvalue)) || ($this->isMobileWithz($nvalue))) {
                $number[] = $nvalue;
            }
        }
        @$numbers = array_unique($number);

        if (is_array($numbers) && $numbers) {
            foreach ($numbers as $key => $value) {
                $Messages[] = $messagebody;
            }
        }

        date_default_timezone_set('Asia/Tehran');
        $SendDateTime = date("Y-m-d")."T".date("H:i:s");

        if ($isclubnumber == 1) {
            foreach ($numbers as $num_keys => $num_vals) {
                $contacts[] = array(
                    "Prefix" => "",
                    "FirstName" => "" ,
                    "LastName" => "",
                    "Mobile" => $num_vals,
                    "BirthDay" => "",
                    "CategoryId" => "",
                    "MessageText" => $messagebody
                );
            }
            $CustomerClubInsertAndSendMessage = $this->customerClubInsertAndSendMessage($contacts);
        } else {
            $SendMessage = $this->sendMessage($numbers, $Messages, $SendDateTime);
        }

        if ((@$CustomerClubInsertAndSendMessage == true) || ($SendMessage == true)) {
            $answer = new stdClass();
            @$answer->status = 1;
            @$answer->info = nl2br($result->status_message);
            foreach ($this->messageToSend as $oneIndex => $oneMessageSent) {
                $this->messageResults[$oneIndex] = $answer;
            }
        } else {
            @$resmessage = $result->messages;
            if ($resmessage) {
                foreach ($result->messages as $oneId => $oneMessage) {
                    $answer = new stdClass();
                    $answer->smsid = empty($oneMessage->message_id) ? '' : $oneMessage->message_id;
                    $answer->status = $oneMessage->status;
                    if (!empty($oneMessage->status_message)) $answer->info = $oneMessage->status_message;
                    $this->messageResults[$oneId] = $answer;
                }
            }
        }
        $this->indexMessage = 0;
    }

    /**
     * Send sms.
     *
     * @param MobileNumbers[] $MobileNumbers array structure of mobile numbers
     * @param Messages[]      $Messages      array structure of messages
     * @param string          $SendDateTime  Send Date Time
     *
     * @return boolean Indicates the sent sms result
     */
    public function sendMessage($MobileNumbers, $Messages, $SendDateTime = '')
    {
        $token = $this->_getToken($this->username, $this->password);
        $result = false;
        if ($token != false) {
            $postData = array(
                'Messages' => $Messages,
                'MobileNumbers' => $MobileNumbers,
                'LineNumber' => $this->linenumber,
                'SendDateTime' => $SendDateTime,
                'CanContinueInCaseOfError' => 'false'
            );

            $url = $this->apidomain.$this->getAPIMessageSendUrl();
            $SendMessage = $this->_execute($postData, $url, $token);
            $object = json_decode($SendMessage);

            if (is_object($object)) {
                if ($object->IsSuccessful == true) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Customer Club Insert And Send Message.
     *
     * @param data[] $data array structure of contacts data
     *
     * @return string Indicates the sent sms result
     */
    public function customerClubInsertAndSendMessage($data)
    {
        $token = $this->_getToken($this->username, $this->password);
        $result = false;
        if ($token != false) {
            $postData = $data;

            $url = $this->apidomain.$this->getAPICustomerClubAddAndSendUrl();
            $CustomerClubInsertAndSendMessage = $this->_execute($postData, $url, $token);
            $object = json_decode($CustomerClubInsertAndSendMessage);

            if (is_object($object)) {
                if ($object->IsSuccessful == true) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Gets token key for all web service requests.
     *
     * @return string Indicates the token key
     */
    private function _getToken()
    {
        $postData = array(
            'UserApiKey' => $this->username,
            'SecretKey' => $this->password,
            'System' => 'joomla_acy_3_5_v_3_0'
        );
        $postString = json_encode($postData);

        $ch = curl_init($this->apidomain.$this->getApiTokenUrl());
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);
        $resp = false;
        if (is_object($response)) {
            @$IsSuccessful = $response->IsSuccessful;
            if ($IsSuccessful == true) {
                @$TokenKey = $response->TokenKey;
                $resp = $TokenKey;
            } else {
                $resp = false;
            }
        }
        return $resp;
    }

    /**
     * Executes the main method.
     *
     * @param postData[] $postData array of json data
     * @param string     $url      url
     * @param string     $token    token string
     *
     * @return string Indicates the curl execute result
     */
    private function _execute($postData, $url, $token)
    {
        $postString = json_encode($postData);

        $ch = curl_init($url);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-sms-ir-secure-token: '.$token
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Executes the main method.
     *
     * @param string $url   url
     * @param string $token token string
     *
     * @return string Indicates the curl execute result
     */
    private function _executeCredit($url, $token)
    {
        $ch = curl_init($url);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'x-sms-ir-secure-token: '.$token
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Check if mobile number is valid.
     *
     * @param string $mobile mobile number
     *
     * @return boolean Indicates the mobile validation
     */
    public function isMobile($mobile)
    {
        if (preg_match('/^09(0[1-3]|1[0-9]|3[0-9]|2[0-2]|9[0])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if mobile with zero number is valid.
     *
     * @param string $mobile mobile with zero number
     *
     * @return boolean Indicates the mobile with zero validation
     */
    public function isMobileWithz($mobile)
    {
        if (preg_match('/^9(0[1-3]|1[0-9]|3[0-9]|2[0-2]|9[0])-?[0-9]{3}-?[0-9]{4}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * After Save Config
     *
     * @param string $senderProfile sender Profile
     *
     * @return void
     */
    public function afterSaveConfig($senderProfile)
    {
        if (in_array(JRequest::getCmd('task'), array('save', 'apply'))) $this->_displayBalance();
    }

    /**
     * Get Balance
     *
     * @return string user balance
     */
    public function getBalance()
    {
        $fsockParameter = "GET /api/getBalance?apiKey=".urlencode($this->username)." HTTP/1.1\r\n";
        $fsockParameter .= "Host: www.acygateway.com \r\n";
        $fsockParameter .= "Content-type: application/x-www-form-urlencoded\r\n\r\n";

        $idConnection = $this->sendRequest($fsockParameter);
        $result = $this->readResult($idConnection);

        if ($result === false) {
            ACYSMS::enqueueMessage(implode('\n', $this->errors), 'error');
            return false;
        }
        if (!strpos($result, '200 OK')) {
            $this->errors[] = 'Error 200 KO => '.$result;
            return false;
        }
        $res = json_decode(trim(substr($result, strpos($result, "\r\n\r\n"))));

        if (!empty($res->status)) {
            $res->user_nb_credits = substr($res->user_nb_credits, 0, strpos($res->user_nb_credits, '.') + 3);
            return array("default" => $res->user_nb_credits);
        } else {
            ACYSMS::enqueueMessage($res->status_message, 'error');
            return false;
        }
    }

    /**
     * Display Balance
     *
     * @return boolean Display Balance
     */
    private function _displayBalance()
    {
        $balance = $this->getBalance();
        if ($balance === false) {
            ACYSMS::enqueueMessage(implode('<br />', $this->errors), 'error');
            return false;
        }
        ACYSMS::enqueueMessage(JText::sprintf('SMS_CREDIT_LEFT_ACCOUNT', $balance["default"]), 'message');
    }

    /**
     * Delivery Report
     *
     * @return array Delivery Report informations
     */
    public function deliveryReport()
    {
        $callbackInformationsEncoded = JRequest::getVar("callbackInformations", '');
        $callbackInfoDecoded = json_decode($callbackInformationsEncoded);
        if (empty($callbackInfoDecoded)) return;

        $status = array();
        $status[0] = "Not sent";
        $status[1] = "Sent";
        $status[2] = "Accepted by the gateway";
        $status[3] = "Sent to the operator";
        $status[4] = "Buffered";
        $status[5] = "Delivered";
        $status[-1] = "Not delivered";
        $status[-2] = "Timed out";
        $status[-99] = "Error unknown status";

        $deliveryInformations = array();

        foreach ($callbackInfoDecoded as $oneCallback) {
            $oneInformation = new stdClass();
            $oneInformation->statsdetails_error = array();
            $messageStatus = empty($oneCallback->status) ? '' : $oneCallback->status;
            $completed_time = empty($oneCallback->completed_time) ? '' : $oneCallback->completed_time;

            if (empty($messageStatus)) $oneInformation->statsdetails_error[] = 'Empty status received';
            if ($messageStatus == 5) {
                if (empty($completed_time)) {
                    $oneInformation->statsdetails_received_date = time();
                } else $oneInformation->statsdetails_received_date = $completed_time;
            }

            $smsId = empty($oneCallback->message_id) ? '' : $oneCallback->message_id;
            if (empty($smsId)) $oneInformation->statsdetails_error[] = 'Can t find the message_id';

            if (!isset($status[$messageStatus])) {
                $oneInformation->statsdetails_error[] = 'Unknow status : '.$messageStatus;
                $oneInformation->statsdetails_status = -99;
            } else {
                $oneInformation->statsdetails_status = $messageStatus;
                $oneInformation->statsdetails_error[] = $status[$messageStatus];
            }
            $oneInformation->statsdetails_sms_id = $smsId;
            $deliveryInformations[] = $oneInformation;
        }
        return $deliveryInformations;
    }

    /**
     * Answer method
     *
     * @return array answer Informations
     */
    public function answer()
    {
        $callbackInformationsEncoded = JRequest::getVar("callbackInformations", '');
        $callbackInfoDecoded = json_decode($callbackInformationsEncoded);
        if (empty($callbackInfoDecoded)) return;

        $answerInformations = array();

        foreach ($callbackInfoDecoded as $oneAnswerInformation) {

            $oneInformation = new stdClass();
            $oneInformation->statsdetails_error = array();

            $oneInformation->answer_date = empty($oneAnswerInformation->received_time) ? time() : $oneAnswerInformation->received_time;
            $oneInformation->answer_body = empty($oneAnswerInformation->answer_body) ? '' : $oneAnswerInformation->answer_body;

            $answerSender = empty($oneAnswerInformation->answer_sender) ? '' : $oneAnswerInformation->answer_sender;
            $answerReceiver = empty($oneAnswerInformation->answer_receiver) ? '' : $oneAnswerInformation->answer_receiver;

            if (!empty($answerSender)) $oneInformation->answer_from = '+'.$answerSender;
            if (!empty($answerReceiver)) $oneInformation->answer_to = '+'.$answerReceiver;

            $oneInformation->answer_sms_id = empty($oneAnswerInformation->message_id) ? '' : $oneAnswerInformation->message_id;
            $answerInformations[] = $oneInformation;
        }
        return $answerInformations;
    }
}