<?php
/**
 * Payment API helper
 *
 * @category  API helper
 * @package   Payment
 * @author    support@t2mgroup.com
 * @copyright Copyright (c) 2014
 * @version   1.0

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 **/

class Payment {
    protected static $instance;
    private $UserName;
    private $Password;
    private $url = "https://secure.suregate.net/ws/transact.asmx/ProcessCreditCard";
    private $defFileds = array('TransType', 'NameOnCard','CardNum','ExpDate','Amount','CVNum','InvNum',
                               'Zip','Street', 'MagData', 'Amount','PNRef');
    private $amountFields = Array ('Amount', 'TipAmt', 'TaxAmt');
    private $formData = Array ();

    public function __construct ($user, $pass) {
        libxml_use_internal_errors (true);
        $this->UserName = $user;
        $this->Password = $pass;

        self::$instance = $this;
    }

    public function getInstance() {
        return $this->instance;
    }

    public function __get ($attr) {
        return $this->formData[$attr];
    }

    public function __set ($attr, $value) {
        $this->formData[$attr] = $value;
    }

    function stringStartsWith($haystack, $needle) {
        return strpos ($haystack, $needle) === 0;
    }

    public function setGatewayUrl ($url) {
        $this->url = $url;
    }

    public function getGatewayUrl () {
        return $this->url;
    }

    function sale ($opt = null) {
        if ($opt)
            $this->formData = $opt;
        $this->formData["TransType"] = "Sale";

        return $this->post();
    }

    function auth ($opt = null) {
        if ($opt)
            $this->formData = $opt;
        $this->formData["TransType"] = "Auth";

        return $this->post();
    }

    function returnTrans ($opt = null) {
        if ($opt)
            $this->formData = $opt;
        $this->formData["TransType"] = "Return";

        return $this->post();
    }

    function void ($opt = null) {
        if (count ($opt) > 0)
            $this->formData = $opt;
        $this->formData["TransType"] = "Void";

        return $this->post();
    }

    function capture ($opt = null) {
        if ($opt)
            $this->formData = $opt;
        $this->formData["TransType"] = "Force";

        return $this->post();
    }

    function prepare () {
        $data = Array (
            "UserName" => $this->UserName,
            "Password" => $this->Password,
            "ExtData" => ''
        );

        // fix amounts
        foreach ($this->amountFields as $f) {
            if ($this->formData [$f])
                $this->formData [$f] =  number_format($this->formData [$f], 2, ".","");
        }
 
        // Construct ExtData
        foreach ($this->formData as $f => $val) {
            if (!in_array ($f, $this->defFileds))
                $data['ExtData'] .= "<{$f}>{$val}</{$f}>";
            else 
                $data[$f] = $val;
        }

        // set defaults fields
        foreach ($this->defFileds as $f) {
            if (empty ($data[$f]))
                $data[$f] = '';
        }
        return $data;
    }

    private function convertResponse ($xml) {
        $resp = new stdClass;
        $resp->Approved = false;

        $data = json_decode (json_encode (simplexml_load_string ($xml)));
        // Cleanup array
        foreach ($data as $key => $value) {
            if (!is_object ($value) && $key != 'ExtData')
                $resp->$key = $value;
        }

        // Add Approved value
        if (!empty ($resp->Result) && $resp->Result == '0')
            $resp->Approved = true;

        /// Convert ExtData
        // Split plain data and XML into $matches array
        preg_match ('/([,=0-9a-zA-Z]*)(\<.*\>)?/', $data->ExtData, $matches);
        if (count ($matches) == 1)
            return $resp;

        // Process plain text coma separated keypairs
        $plainData = explode (",", $matches[1]);
        if ($plainData) {
            foreach ($plainData as $keypair) {
                list ($key, $val) = explode ("=", $keypair);
                $resp->$key = $val;
            }
        }
        // Process XML part
        $xmlData = json_decode (json_encode (simplexml_load_string ("<d>" . $matches[2] . '</d>')));
        if ($xmlData) {
            foreach ($xmlData as $key => $val)
                $resp->$key = $val;
        }

        return $resp;
    }

    function post () {
        $data = $this->prepare ();
        try {
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $this->url);
            curl_setopt ($ch, CURLOPT_POST, 1);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query ($data));
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
            $xmlRaw = curl_exec ($ch);
            curl_close ($ch);
            $this->formData = Array();

            if ($this->stringStartsWith ($xmlRaw, "<?xml")) {
                return $this->convertResponse ($xmlRaw);
            } else {
                $resp = new stdClass;
                $resp->Result = -999;
                $resp->Message = "Gateway error: " . $xmlRaw;
                return $resp;
            }
        } catch (Exception $e) {
            $resp = new stdClass;
            $resp->Result = -999;
            $resp->Message = "Gateway error: " . $xmlRaw;
            return $resp;
        }
    }
}
?>
