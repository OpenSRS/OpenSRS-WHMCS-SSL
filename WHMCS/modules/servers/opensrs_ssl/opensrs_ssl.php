<?php
/**********************************************************************
 *  OpenSRS - SSL WHMCS module
 *
 *
 *  CREATED BY Tucows Co       ->    http://www.opensrs.com
 *  CONTACT                    ->	 help@tucows.com
 *  Version                    -> 	 2.0.1
 *  Release Date               -> 	 07/10/14
 *
 *
 * Copyright (C) 2014 by Tucows Co/OpenSRS.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 **********************************************************************/

defined('DS') ? null : define('DS',DIRECTORY_SEPARATOR);
if(!defined('PS')) define('PS', PATH_SEPARATOR);
if(!defined('CRLF')) define('CRLF', "\r\n");
require_once dirname(__FILE__).DS.'core'.DS.'openSRS.php';
 
//GLOBAL
$opensrs_ssl_language = array();

//A LITTLE HELPER
if(function_exists('mysql_safequery') == false) {
    function mysql_safequery($query,$params=false) {
        if ($params) {
            foreach ($params as &$v) { $v = mysql_real_escape_string($v); }
            $sql_query = vsprintf( str_replace("?","'%s'",$query), $params );
            $sql_query = mysql_query($sql_query);
        } else {
            $sql_query = mysql_query($query);
        }
        return ($sql_query);
    }
}

function opensrs_ssl_whmcsServerTypes()
{
    return array
    (
        //WHMCS Type
        "1001"  =>  "AOL",
        "1002"  =>  "Apache +ModSSL",
        "1003"  =>  "Apache-SSL (Ben-SSL, not Stronghold)",
        "1004"  =>  "C2Net Stronghold",
        "1005"  =>  "Cobalt Raq",
        "1006"  =>  "Covalent Server Software",
        "1031"  =>  "cPanel / WHM",
        "1029"  =>  "Ensim",
        "1032"  =>  "H-Sphere",
        "1007"  =>  "IBM HTTP Server",
        "1008"  =>  "IBM Internet Connection Server",
        "1009"  =>  "iPlanet",
        "1010"  =>  "Java Web Server (Javasoft / Sun)",
        "1011"  =>  "Lotus Domino",
        "1012"  =>  "Lotus Domino Go!",
        "1013"  =>  "Microsoft IIS 1.x to 4.x",
        "1014"  =>  "Microsoft IIS 5.x and later",
        "1015"  =>  "Netscape Enterprise Server",
        "1016"  =>  "Netscape FastTrack",
        "1017"  =>  "Novell Web Server",
        "1018"  =>  "Oracle",
        "1030"  =>  "Plesk",
        "1019"  =>  "Quid Pro Quo",
        "1020"  =>  "R3 SSL Server",
        "1021"  =>  "Raven SSL",
        "1022"  =>  "RedHat Linux",
        "1023"  =>  "SAP Web Application Server",
        "1024"  =>  "Tomcat",
        "1025"  =>  "Website Professional",
        "1026"  =>  "WebStar 4.x and later",
        "1027"  =>  "WebTen (from Tenon)",
        "1028"  =>  "Zeus Web Server",
        //Custom Types
        "2001"  =>  "Apache 2",
        "2002"  =>  "Apache + OpenSSL",
        "2003"  =>  "C2NET",
        "2004"  =>  "Cobalt Series",
        "2005"  =>  "H-Sphere",
        "2006"  =>  "Citrix",
        "2007"  =>  'IPSwitch',
        "2008"  =>  'Web Logic',
        //Not Listed
        "1000"  =>  "Other (not listed)"
    );
}
function opensrs_ssl_getServerType($server, $type)
{
    $comodo = array
    (
        "1001"  =>  "other",           // AOL
        "1002"  =>  "apachessl",          // Apache +ModSSL
        "1003"  =>  "other",    // Apache-SSL (Ben-SSL, not Stronghold)
        "1004"  =>  "other",              // C2Net Stronghold
        "1005"  =>  "other",       // Cobalt Raq
        "1006"  =>  "other",              // Covalent Server Software
        "1031"  =>  "whmcpanel",             // cPanel / WHM
        "1029"  =>  "ensim",              // Ensim
        "2005"  =>  "hsphere",            // H-Sphere
        "1007"  =>  "ibmhttp",            // IBM HTTP Server
        "1008"  =>  "ibmhttp",            // IBM Internet Connection Server
        "1009"  =>  "other",            // iPlanet
        "1010"  =>  "javawebserver",              // Java Web Server (Javasoft / Sun)
        "1011"  =>  "domino",             // Lotus Domino
        "1012"  =>  "other",       // Lotus Domino Go!
        "1013"  =>  "iis4",               // Microsoft IIS 1.x to 4.x
        "1014"  =>  "iis5",               // Microsoft IIS 5.x and later
        "1015"  =>  "netscape",           // Netscape Enterprise Server
        "1016"  =>  "netscape",           // Netscape FastTrack
        "1017"  =>  "novell",              // Novell Web Server
        "1018"  =>  "oracle",              // Oracle
        "1000"  =>  "other",              // Other (not listed)
        "1030"  =>  "plesk",              // Plesk
        "1019"  =>  "other",              // Quid Pro Quo
        "1020"  =>  "other",              // R3 SSL Server
        "1021"  =>  "other",              // Raven SSL
        "1022"  =>  "redhat",              // RedHat Linux
        "1023"  =>  "sap",              // SAP Web Application Server
        "1024"  =>  "tomcat",             // Tomcat
        "1025"  =>  "other",            // Website Professional
        "1026"  =>  "webstar",            // WebStar 4.x and later
        "1027"  =>  "other",              // WebTen (from Tenon)
        "1028"  =>  "other",             // Zeus Web Server
        //CUSTOM
        "2001"  =>  "other",                //Apache 2
        "2002"  =>  "other",                //Apache + OpenSSL
        "2003"  =>  "other",                //C2NET
        "2004"  =>  "other",                //Cobalt Series
        "2005"  =>  "other",                //H-Sphere
        "2006"  =>  "citrix",               //Citrix
        "2007"  =>  "other",                //IP Switch
        "2008"  =>  "webstart"              //WebLogic
    );
    
    $other = array
    (
        "1001"  =>  'other',              // AOL
        "1002"  =>  "apacheapachessl",          // Apache +ModSSL
        "1003"  =>  "other",    // Apache-SSL (Ben-SSL, not Stronghold)
        "1004"  =>  "c2net",              // C2Net Stronghold
        "1005"  =>  "cobaltseries",       // Cobalt Raq
        "1006"  =>  'other',              // Covalent Server Software
        "1031"  =>  "cpanel",             // cPanel / WHM
        "1029"  =>  "ensim",              // Ensim
        "2005"  =>  "hsphere",            // H-Sphere
        "1007"  =>  "ibmhttp",            // IBM HTTP Server
        "1008"  =>  "ibmhttp",            // IBM Internet Connection Server
        "1009"  =>  "iplanet",            // iPlanet
        "1010"  =>  'other',              // Java Web Server (Javasoft / Sun)
        "1011"  =>  "domino",             // Lotus Domino
        "1012"  =>  "dominogo4626",       // Lotus Domino Go!
        "1013"  =>  "iis4",               // Microsoft IIS 1.x to 4.x
        "1014"  =>  "iis5",               // Microsoft IIS 5.x and later
        "1015"  =>  "netscape",           // Netscape Enterprise Server
        "1016"  =>  "netscape",           // Netscape FastTrack
        "1017"  =>  'other',              // Novell Web Server
        "1018"  =>  "other",              // Oracle
        "1000"  =>  "other",              // Other (not listed)
        "1030"  =>  "plesk",              // Plesk
        "1019"  =>  "other",              // Quid Pro Quo
        "1020"  =>  "other",              // R3 SSL Server
        "1021"  =>  "other",              // Raven SSL
        "1022"  =>  "other",              // RedHat Linux
        "1023"  =>  "other",              // SAP Web Application Server
        "1024"  =>  "tomcat",             // Tomcat
        "1025"  =>  "website",            // Website Professional
        "1026"  =>  "webstar",            // WebStar 4.x and later
        "1027"  =>  "other",              // WebTen (from Tenon)
        "1028"  =>  "zeusv3",             // Zeus Web Server
        //CUSTOM
        "2001"  =>  "apache2",              //Apache 2
        "2002"  =>  "apacheopenssl",        //Apache + OpenSSL
        "2003"  =>  "c2net",                //C2NET
        "2004"  =>  "cobaltseries",         //Cobalt Series
        "2005"  =>  "hsphere",              //H-Sphere
        "2006"  =>  "other",                //Citrix
        "2007"  =>  "ipswitch",             //IP Switch
        "2008"  =>  "weblogic"              //WebLogic
    );
    
    $comodo_types = array('comodo_ev','comodo_instantssl','comodo_premiumssl','comodo_premiumssl_wildcard','comodo_ssl','comodo_wildcard');
    if(in_array($type, $comodo_types))
        return $comodo[$server];
    
    return $other[$server];
}

function opensrs_ssl_getRequiredContacts($t)
{
    $contacts = array
    (
        'admin'         =>  array
        (
            //COMODO
            'comodo_ev',
            //GEOTRUST
            'quickssl',
            'quickssl_premium',
            'truebizid',
            'truebizid_wildcard',
            'truebizid_ev',
            'rapidssl',
            'rapidssl_wildcard',
            //SYMANTEC
            'securesite',
            'securesite_pro',
            'securesite_ev',
            'securesite_pro_ev',
            //TRUSTWAVE
            'trustwave_dv',
            'trustwave_ev',
            'trustwave_premiumssl',
            'trustwave_premiumssl_wildcard',
            //THAWTE
            'ssl123',
            'sgcsuper_certs',
            'sslwebserver',
            'sslwebserver_ev',
            'sslwebserver_wildcard',
        ),
        'billing'       =>  array
        (
            //GEOTRUST
            'quickssl',
            'quickssl_premium',
            'truebizid',
            'truebizid_wildcard',
            'truebizid_ev',
            'rapidssl',
            'rapidssl_wildcard',
            //SYMANTEC
            'securesite',
            'securesite_pro',
            'securesite_ev',
            'securesite_pro_ev',
            //THAWTE
            'ssl123',
            'sgcsuper_certs',
            'sslwebserver',
            'sslwebserver_ev',
            'sslwebserver_wildcard',
            
        ),
        'tech'          =>  array
        (
            //GEOTRUST
            'quickssl',
            'quickssl_premium',
            'truebizid',
            'truebizid_wildcard',
            'truebizid_ev',
            'rapidssl',
            'rapidssl_wildcard',
            //SYMANTEC
            'securesite',
            'securesite_pro',
            'securesite_ev',
            'securesite_pro_ev',
            //THAWTE
            'ssl123',
            'sgcsuper_certs',
            'sslwebserver',
            'sslwebserver_ev',
            'sslwebserver_wildcard',
        ),
        'organization'  =>  array
        (
            //COMODO
            'comodo_ev',
            'comodo_instantssl',
            'comodo_premiumssl',
            'comodo_premiumssl_wildcard',
            'comodo_ssl',
            'comodo_wildcard',
            'essentialssl',
            'essentialssl_wildcard',
            //SYMANTEC
            'securesite',
            'securesite_pro',
            'securesite_ev',
            'securesite_pro_ev',
            //GEOTRUST
            'truebizid',
            'truebizid_wildcard',
            'truebizid_ev',
            //THAWTE
            'ssl123',
            'sgcsuper_certs',
            'sslwebserver',
            'sslwebserver_ev',
            'sslwebserver_wildcard',
        ),
        'signer'        =>  array
        (
            'comodo_ev',
        )
    );
    
    $types = array();
    foreach($contacts as $type => $values)
    {
        if(in_array($t, $values))
            $types[] = $type;
    }
    
    return $types;
}

function opensrs_ssl_getCertType($type = null)
{
    $certs = array
    (
        //COMODO
        'Comodo - EV SSL'                               =>  'comodo_ev',
        'Comodo - InstantSSL'                           =>  'comodo_instantssl',
        'Comodo - PremiumSSL'                           =>  'comodo_premiumssl',
        'Comodo - PremiumSSL Wildcard'                  =>  'comodo_premiumssl_wildcard',
        'Comodo - SSL'                                  =>  'comodo_ssl', //
        'Comodo - SSL Wildcard'                         =>  'comodo_wildcard', //
        'Comodo - EssentialSSL'                         =>  'essentialssl',
        'Comodo - EssentialSSL Wildcard'                =>  'essentialssl_wildcard',
        //GEO TRUST
        'GeoTrust - QuickSSL'                           =>  'quickssl',
        'GeoTrust - QuickSSL Premium'                   =>  'quickssl_premium',
        'GeoTrust - RapidSSL'                           =>  'rapidssl',
        'GeoTrust - RapidSSL Wildcard'                  =>  'rapidssl_wildcard',
        'GeoTrust - True BusinessID'                    =>  'truebizid',
        'GeoTrust - True BusinessID Wildcard'           =>  'truebizid_wildcard',
        'GeoTrust - True BusinessID with EV'            =>  'truebizid_ev',
        //SYMANTEC
        'Symantec - SecureSite'                         =>  'securesite',
        'Symantec - SecureSite Pro'                     =>  'securesite_pro',
        'Symantec - SecureSite with EV'                 =>  'securesite_ev',
        'Symantec - SecureSite Pro With EV'             =>  'securesite_pro_ev',
        //THAWTE
        'Thawte - SSL123'                               =>  'ssl123',
        'Thawte - SGC SuperCerts'                       =>  'sgcsuper_certs',
        'Thawte - SSL Webserver Certificate'            =>  'sslwebserver',
        'Thawte - SSL Webserver Certificate with EV'    =>  'sslwebserver_ev',
        'Thawte - SSL Webserver Certificate Wildcard'   =>  'sslwebserver_wildcard',
        //TRUSTWAVE
        'Trustwave - EasyTrust SSL'                     =>  'trustwave_dv',
        'Trustwave - Premium EV SSL'                    =>  'trustwave_ev',
        'Trustwave - Premium SSL'                       =>  'trustwave_premiumssl',
        'Trustwave - Premium SSL with Wildcard'         =>  'trustwave_premiumssl_wildcard'
    );
    
    if(isset($type))
        return $certs[$type];
    
    return $certs;
}
function opensrs_ssl_configoptions()
{
    //CREATE TABLE
    mysql_safequery('CREATE TABLE IF NOT EXISTS `opensrs_ssl`
    (
        `account_id` INT(11) NOT NULL,
        `data` TEXT,
        UNIQUE KEY(`account_id`)
    ) DEFAULT CHARACTER SET UTF8 ENGINE = MyISAM');
    
    //EMAIL FOR NEW CERTS
    $q = mysql_safequery('SELECT COUNT(*) as `count` FROM tblemailtemplates WHERE name = "OpenSRS - SSL Certificate Configuration Required"');
    $row = mysql_fetch_assoc($q);
    if(!mysql_num_rows($q) || !$row['count'])
    {
        mysql_safequery("INSERT INTO `tblemailtemplates` (`type` ,`name` ,`subject` ,`message` ,`fromname` ,`fromemail` ,`disabled` ,`custom` ,`language` ,`copyto` ,`plaintext` )VALUES ('product', 'OpenSRS - SSL Certificate Configuration Required', 'SSL Certificate Configuration Required', '<p>Dear {\$client_name},</p><p>Thank you for your order for an SSL Certificate. Before you can use your certificate, it requires configuration which can be done at the URL below.</p><p>{\$ssl_configuration_link}</p><p>Instructions are provided throughout the process but if you experience any problems or have any questions, please open a ticket for assistance.</p><p>{\$signature}</p>', '', '', '', '', '', '', '0')");
    }
    
    //CONFIG
    return array
    (
        'username'          =>  array // 1
        (
            'FriendlyName'  =>  'Username',
            'Type'          =>  'text',
            'Size'          =>  '25'
        ),
        'apikey'            =>  array // 2
        (
            'FriendlyName'  =>  'API Key',
            'Type'          =>  'text',
            'Size'          =>  '25'
        ),
        'test'              =>  array // 3
        (
            'FriendlyName'  =>  'Test Mode',
            'Type'          =>  'yesno',
        ),
        'Certificate Type'  =>  array // 4
        (
            'FriendlyName'  =>  'Certificate Type',
            'Type'          =>  'dropdown',
            'Options'       =>  implode(',', array_keys(opensrs_ssl_getCertType()))
        ),
        'period'            =>  array // 5
        (
            'FriendlyName'  =>  'Default Registration Period',
            'Type'          =>  'text',
            'Size'          =>  '10'
        ), 
        'Search in seal'    =>  array // 6
        (
            'FriendlyName'  =>  'Show seal during search by default',
            'Description'   =>  'Applicable only to Symantec certificates',
            'Type'          =>  'yesno'
        ),
        'Server count'      =>  array // 7
        (
            'FriendlyName'  =>  'Server Count',
            'Type'          =>  'text'
        )
    );
}

/**
 * CREATE NEW RECORD IN DATABASE AND SEND EMAIL WITH CONFIGURATION LINK TO CLIENT
 * @param type $params
 * @return type 
 */
function opensrs_ssl_CreateAccount($params)
{
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);
    if($row['remoteid'])
    {
        return opensrs_ssl_translate('certificate_already_exists');
    }
    
    GLOBAL $CONFIG;
    
    $period = $params['configoptions']['Period'] ? $params['configoptions']['Period'] : $params['configoption5'];
    $seal_in_search = $params['customfields']['Search in seal'] ? $params['customfields']['Search in seal'] : $params['configoption6'];
    $server_count = $params['customfields']['Server Count'] ? $params['customfields']['Server Count'] : $params['configoption7'];
    $type = $params['configoption4'];
    
    $details = array
    (
        'type'              =>  $type,
        'period'            =>  $period,
        'seal_in_search'    =>  $seal_in_search,
        'server_count'      =>  $server_count
    );
    
    mysql_safequery("UPDATE tblhosting SET username = '', password = '' WHERE id = ?", array($params['serviceid']));
    
    mysql_safequery('REPLACE INTO opensrs_ssl SET data = ?, account_id = ?',array(serialize($details), $params['serviceid']));
    
    mysql_safequery('DELETE FROM tblsslorders WHERE serviceid = ? AND userid = ?', array($params['serviceid'], $params['clientsdetails']['userid']));
    mysql_safequery('INSERT INTO `tblsslorders` (`id`, `userid`, `serviceid`, `remoteid`, `module`, `certtype`, `configdata`, `completiondate`, `status`)
        VALUES ("", ?, ?, ?, ?, ?, ?, ?, ?)', array
        (
            $params['clientsdetails']['userid'],
            $params['serviceid'],
            '',
            'opensrs_ssl',
            $type,
            '',
            '',
            'Awaiting Configuration'
        )
    );
    
    $id = mysql_insert_id();
    $url = '<a href="'.$CONFIG["SystemURL"].'/configuressl.php?cert='.md5($id).'">'.$CONFIG["SystemURL"].'/configuressl.php?cert='.md5($id).'</a>';
    $r = sendMessage("OpenSRS - SSL Certificate Configuration Required",
        $params["serviceid"],array(
            "ssl_configuration_link"    =>  $url
        )
    );
    
    return 'success';
}

function opensrs_ssl_terminateaccount($params)
{
    //get cert details
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);
    if($row['remoteid'])
    {
        $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
        $send = array
        (
            'action'        =>  'cancel_order',
            'object'        =>  'trust_service',
            'attributes'    =>  array
            (
                'order_id'  =>  $row['remoteid']
            )
        );
        
        $res = $openSRS->send($send);
        if(!$openSRS->isSuccess())
        {
            return opensrs_ssl_translate($openSRS->getError());
        }
    }
    
    
    $q = mysql_safequery("DELETE FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    return 'success';
}

function opensrs_ssl_admincustombuttonarray()
{
    return array
    (
        opensrs_ssl_translate('resend_cert_email')      =>  'resend',
        opensrs_ssl_translate('resend_approve_email')   =>  'resendApproveEmail',
        opensrs_ssl_translate('renew')                  =>  'Renew',
    );
}

function opensrs_ssl_clientareacustombuttonarray()
{
    return array
    (
        opensrs_ssl_translate('get_certificate')   =>  'getcert'
    );
}


/**
 * Display certificate in client area
 * @param type $params
 * @return type 
 */
function opensrs_ssl_getcert($params)
{
    $_LANG = opensrs_ssl_loadLanguage();
    $q = mysql_safequery("SELECT id, status, remoteid FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);
    $cert_id = $row['id'];
    $status = $row['status'];
    $cert = $row['remoteid'];
    
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    $send = array
    (
        'action'        =>  'get_cert',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'order_id'  =>  $cert,
        )
    );
    
    $r = $openSRS->send($send);
    
    if($openSRS->isSuccess())
    {
        return array
        (
            'templatefile'  =>  'getcert',
            'breadcrumb'    =>  '> <a href="#">Get cert</a>',
            'vars'          =>  array
            (
                'cert'      =>  $r['attributes']['cert_data']['certificate'],
                'lang'      =>  $_LANG,
                'serviceid' =>  $params['serviceid']
            )
        );
    }
    
    return opensrs_ssl_translate($openSRS->getError());
}


/**
 * Resend cert email to client
 * @param type $params
 * @return type 
 */
function opensrs_ssl_resend($params)
{
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);
    
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    $send = array
    (
        'action'        =>  'resend_cert_email',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'order_id'  =>  $row['remoteid']
        )
    );

    $res = $openSRS->send($send);
    if(!$openSRS->isSuccess())
    {
        return opensrs_ssl_translate($openSRS->getError());
    }
    
    return 'success';
}


/**
 * Renew certificate. This function should be called automatically
 * @param type $params
 * @return type 
 */
function opensrs_ssl_Renew($params)
{
    $csr = trim($params['configdata']['csr']);
    $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
    $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
    $csr = trim($csr);
    
    $subject  = openssl_csr_get_subject($csr);
    $domain = $subject['CN'];
    $period = $params['configoptions']['Period'] ? $params['configoptions']['Period'] : $params['configoption5'];
    $seal_in_search = $params['customfields']['Search in seal'] ? $params['customfields']['Search in seal'] : $params['configoption6'];
    $server_count = $params['customfields']['Server Count'] ? $params['customfields']['Server Count'] : $params['configoption7'];
    
    $product_type = opensrs_ssl_getCertType($params['configoption4']);
    $types = opensrs_ssl_getRequiredContacts($product_type);
    
    //ORDER
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $cert = mysql_fetch_assoc($q);
    $order_id = $cert['remoteid'];
    $cert['configdata'] = unserialize($cert['configdata']);
    $details = $cert['configdata']['fields'];
    
    //GET INFO
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    $send = array();
    $send = array
    (
        'action'        =>  'sw_register',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'order_id'          =>  $order_id,
            'approver_email'    =>  $cert['configdata']['approveremail'],
            'product_type'      =>  $product_type,
            'contact_set'       =>  array(),
            'csr'               =>  $csr,
            'domain'            =>  $domain,
            'handle'            =>  'process',
            'period'            =>  $period,
            'reg_type'          =>  'renew',
            'server_count'      =>  $server_count,
            'server_type'       =>  opensrs_ssl_getServerType($cert['configdata']['servertype'], $product_type)
         )
    );
    
    $contact = array();
    $contact_types = array('admin', 'billing', 'tech', 'organization', 'signer');
    foreach($contact_types as $type)
    {
        $t = ucfirst($type);
        if(in_array($type, $types))
        {
            $contact[$type]['first_name'] = $details[$t.'FirstName'];
            $contact[$type]['last_name'] = $details[$t.'LastName'];
            if($type != 'organization')
                $contact[$type]['title'] = $details[$t.'Title'];
            $contact[$type]['org_name'] = $details[$t.'Name'];
            $contact[$type]['address1'] = $details[$t.'Address1'];
            $contact[$type]['address2'] = $details[$t.'Address2'];
            $contact[$type]['address3'] = $details[$t.'Address3'];
            $contact[$type]['city'] = $details[$t.'City'];
            $contact[$type]['state'] = $details[$t.'State'];
            $contact[$type]['postal_code'] = $details[$t.'PostalCode'];
            $contact[$type]['state'] = $details[$t.'State'];
            $contact[$type]['country'] = $details[$t.'Country'];
            if($type != 'organization')
                $contact[$type]['email'] = $details[$t.'Email'];
            $contact[$type]['phone'] = $details[$t.'Phone'];
            $contact[$type]['fax'] = $details[$t.'Fax'];
        }
    }
    
    
    $send['attributes']['contact_set'] = $contact;
    
    if($seal_in_search)
    {
        $send['attributes']['seal_in_search'] = '1';
        $send['attributes']['trust_seal'] = '1';
    }

    $res = $openSRS->send($send);
    
    if($openSRS->isSuccess())
    {
        $order_id = $res['attributes']['order_id'];
        $q = mysql_safequery("UPDATE tblsslorders SET remoteid = ? WHERE serviceid = ?", array($order_id, $params['serviceid']));
        return 'success';
    }
    
    
    return opensrs_ssl_translate($openSRS->getError());
}

function opensrs_ssl_resendApproveEmail($params)
{
    //ORDER
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $cert = mysql_fetch_assoc($q);
    $order_id = $cert['remoteid'];
    
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    $send = array
    (
        'action'        =>  'resend_approve_email',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'order_id'  =>  $order_id,
        )
    );

    $r = $openSRS->send($send);
    if(!$openSRS->isSuccess())
    {
        return $openSRS->getError();
    }
    
    return 'success';
}

/**
 * Display details about cert in client area
 * @param type $params
 * @return string 
 */
function opensrs_ssl_ClientArea($params)
{
    $q = mysql_safequery("SELECT id, status, remoteid FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);
    $cert_id = $row['id'];
    $status = $row['status'];
    $cert = $row['remoteid'];
    
    $code = '';
    $code .= '<table style="width: 100%">';
    

    if($status == 'Completed')
    {
        $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
        $send = array
        (
            'action'        =>  'get_order_info',
            'object'        =>  'trust_service',
            'attributes'    =>  array
            (
                'order_id'  =>  $cert,
            )
        );
        $r = $openSRS->send($send);
        
        $code .= '<tr><td>'.opensrs_ssl_translate('certificate_details').'</td><td><button class="btn" onclick="document.location=\'clientarea.php?action=productdetails&id='.$params['serviceid'].'&modop=custom&a=getcert\'">'.opensrs_ssl_translate('get_certificate').'</button></td></tr>';
        $code .= '<tr><td>'.opensrs_ssl_translate('domain').'</td><td>'.$r['attributes']['domain'].'</td></tr>';
        $code .= '<tr><td>'.opensrs_ssl_translate('period').'</td><td>'.$r['attributes']['period'].'</td></tr>';
        if($r['attributes']['approver_email'])
        {
            $code .= '<tr><td>'.opensrs_ssl_translate('approver_email').'</td><td>'.$r['attributes']['approver_email'].'</td></tr>';
        }
        $code .= '<tr><td>'.opensrs_ssl_translate('contact_email').'</td><td>'.$r['attributes']['contact_email'].'</td></tr>';
        $code .= '<tr><td>'.opensrs_ssl_translate('server_count').'</td><td>'.$r['attributes']['server_count'].'</td></tr>';
        $code .= '<tr><td>'.opensrs_ssl_translate('configuration_status').'</td><td>'.opensrs_ssl_translate('completed').'</td></tr>';
    }
    else 
    {
        $code .= '<tr><td>'.opensrs_ssl_translate('configuration_status').'</td>';
        $code .= '<td><a href="configuressl.php?cert='.md5($cert_id).'">'.opensrs_ssl_translate('configure_now').'</a></td></tr>';
    }
    $code .= '</table>';
    
    return $code;
}

/**
 * Display cert details in admin area
 * @param type $params
 * @return string 
 */
function opensrs_ssl_adminservicestabfields($params)
{
    $q = mysql_safequery("SELECT * FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    if(!mysql_num_rows($q))
        return false;
    
    $row = mysql_fetch_assoc($q);
    $row['configdata'] = unserialize($row['configdata']);
    
    $cert_id = $row['remoteid'];
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    $send = array
    (
        'action'        =>  'get_order_info',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'order_id'  =>  $cert_id,
        )
    );
    
    $r = $openSRS->send($send);
    if($openSRS->isSuccess())
    {
        $fieldsarray = array
        (
            '<b>Service details</b>'    =>  '
                                            <div id="modrenew" title="'.opensrs_ssl_translate('renew_title').'" style="display:none;">
                                                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>'.opensrs_ssl_translate('renew_question').'</p>
                                            </div>
                                            <script type="text/javascript">
                                                $(function(){
                                                    $(".button[value='.opensrs_ssl_translate('renew').']").attr("onclick", "");
                                                        
                                                    $(".button[value='.opensrs_ssl_translate('renew').']").click(function(event){
                                                        event.preventDefault();
                                                        $("#modrenew").dialog({
                                                        autoOpen: true,
                                                        resizable: false,
                                                        width: 450,
                                                        modal: true,
                                                            buttons: {"Yes": function() {
                                                                       window.location="clientshosting.php?userid='.$_REQUEST['userid'].'&id='.$_REQUEST['id'].'&modop=custom&ac=Renew";
                                                                    },"No": function() {
                                                                        $(this).dialog("close");
                                                                    }}
                                                        });
                                                    });
                                                });
                                            </script>
                                             <div style="background-color: #fff">
                                              <table>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('status').'</b></td>
                                                    <td>'.$r['attributes']['state'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('server_count').'</b></td>
                                                    <td>'.$r['attributes']['server_count'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('product_type').'</b></td>
                                                    <td>'.$r['attributes']['product_type'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('contact_email').'</b></td>
                                                    <td>'.$r['attributes']['contact_email'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('approver_email').'</b></td>
                                                    <td>'.($r['attributes']['approver_email'] ? $r['attributes']['approver_email'] : opensrs_ssl_translate('not_supported_by_certificate')).'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('order_id').'</b></td>
                                                    <td>'.$r['attributes']['order_id'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('period').'</b></td>
                                                    <td>'.$r['attributes']['period'].'</td>
                                                </tr>
                                              </table>
                                          </div>', 
        );
    }
    
    $whmcs_severs = opensrs_ssl_whmcsServerTypes();
    $select_server = '';
    foreach($whmcs_severs as $key => $s)
    {
        $select_server .= '<option '.($key == $row['configdata']['servertype'] ? 'selected="selected"' : '' ).' value="'.$key.'">'.$s.'</option>';
    }

    $fieldsarray['<b>Edit Certificate</b>'] = 
        '<div style="background-color: #fff">
          <table style="width: 100%">
            <tr>
                <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('order_id').'</b></td>
                <td><input style="width: 200px" type="text" name="opensrs[remote_id]" value="'.$row['remoteid'].'" /></td>
            </tr>
            <tr>
                <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('approver_email').'</b></td>
                <td><input style="width: 200px" type="text" name="opensrs[approveremail]" value="'.$row['configdata']['approveremail'].'" /></td>
            </tr>
            <tr>
                <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('certificate_request').'</b></td>
                <td><textarea style="width: 100%; height: 200px" name="opensrs[csr]">'.$row['configdata']['csr'].'</textarea></td>
            </tr>
            <tr>
                <td style="width: 150px; padding: 3px 10px 3px 0; text-align: right;"><b>'.opensrs_ssl_translate('server_type').'</b></td>
                <td>
                    <select name="opensrs[servertype]">'.$select_server.'</select>    
                    </td>
            </tr>
          </table>
      </div>';
    return $fieldsarray;
}

/**
 * Updater Certificate data
 * @param type $params 
 */
function opensrs_ssl_AdminServicesTabFieldsSave($params)
{
    $q = mysql_safequery("SELECT configdata FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    if(!mysql_num_rows($q))
    {
        $row['approveremail'] = $_POST['opensrs']['approveremail'];
        $row['servertype'] = $_POST['opensts']['servertype'];
        //Prepare CSR
        $csr = trim($_POST['opensrs']['csr']);
        $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
        $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
        $csr = trim($csr);
        $row['csr'] = $csr;
        
        mysql_safequery('INSERT INTO `tblsslorders` (`id`, `userid`, `serviceid`, `remoteid`, `module`, `certtype`, `configdata`, `completiondate`, `status`)
            VALUES ("", ?, ?, ?, ?, ?, ?, ?, ?)', array
            (
                $params['clientsdetails']['userid'],
                $params['serviceid'],
                $_POST['opensrs']['remote_id'],
                'opensrs_ssl',
                $params['configoption4'],
                serialize($row),
                'NOW()',
                'Completed'
            )
        );
    }
    else
    {
        $row = mysql_fetch_assoc($q);
        $row = unserialize($row['configdata']);
        $row['approveremail'] = $_POST['opensrs']['approveremail'];
        $row['servertype'] = $_POST['opensrs']['servertype'];
        //Prepare CSR
        $csr = trim($_POST['opensrs']['csr']);
        $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
        $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
        $csr = trim($csr);
        $row['csr'] = $csr;
        
        mysql_safequery("UPDATE tblsslorders SET configdata = ?, remoteid = ?, certtype = ? WHERE serviceid = ?", array(serialize($row), $_POST['opensrs']['remote_id'],$params['configoption4'], $params['serviceid']));
    }
    
    $subject  = openssl_csr_get_subject($csr);
    $domain = $subject['CN'];
    mysql_safequery("UPDATE tblhosting SET domain = ? WHERE id = ?", array($domain, $params['serviceid']));
}


/**
 * First step
 * @param type $params
 * @return boolean 
 */
function opensrs_ssl_SSLStepOne($params)
{
    $_LANG = opensrs_ssl_loadLanguage();
    
    $product_type = opensrs_ssl_getCertType($params['certtype']);
    $types = opensrs_ssl_getRequiredContacts($product_type);
    $fields = array();
    
    $previous_type = null;
    $contact_types = array('admin', 'billing', 'tech', 'organization', 'signer');
    foreach($contact_types as $t)
    {
        if(in_array($t, $types))
        {
            $field = ucfirst($t);
            $tr = opensrs_ssl_translate($t);
            
            if(in_array($previous_type, array('admin' ,'billing', 'tech', 'organization')))
            {
                $fields['additionalfields'][$tr]['CopyDataFrom'.$previous_type] = array
                (
                    'FriendlyName'  =>  opensrs_ssl_translate('use_'.$previous_type.'_contact_info'),
                    'Type'          =>  'yesno', 
                    'Description'   =>  '<script type="text/javascript">$(function(){$("input[type=checkbox][name=\'fields[CopyDataFrom'.$previous_type.']\']").click(function(){
                            if($(this).is(":checked"))
                            {
                                /* INPUT TYPE TEXT */
                                $("input[type=text][name^=\'fields['.ucfirst($previous_type).'\']").each(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("'.ucFirst($previous_type).'", "'.$field.'");
                                    $("input[type=text][name=\'"+new_name+"\']").val(val);
                                    $("input[type=text][name=\'"+new_name+"\']").attr("readonly", "readonly");
                                });
                                
                                $("input[type=text][name^=\'fields['.ucfirst($previous_type).'\']").change(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("'.ucFirst($previous_type).'", "'.$field.'");
                                    $("input[type=text][name=\'"+new_name+"\']").val(val);
                                    $("input[type=text][name=\'"+new_name+"\']").change();
                                });
                                
                                /* SELECT */
                                $("select[name^=\'fields['.ucfirst($previous_type).'\']").each(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("'.ucFirst($previous_type).'", "'.$field.'");
                                    $("select[name=\'"+new_name+"\']").val(val);
                                    $("select[name=\'"+new_name+"\']").attr("readonly", "readonly");
                                });
                                
                                $("select[name^=\'fields['.ucfirst($previous_type).'\']").change(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("'.ucFirst($previous_type).'", "'.$field.'");
                                    $("select[name=\'"+new_name+"\']").val(val);
                                    $("select[name=\'"+new_name+"\']").change();
                                });
                            }
                            else
                            {
                                /* INPUT TYPE TEXT */ 
                                $("input[type=text][name^=\'fields['.ucfirst($previous_type).'\']").unbind("change");
                                $("input[type=text][name^=\'fields['.$field.'\']").removeAttr("readonly");
                                
                                /* SELECT  */ 
                                $("select[name^=\'fields['.ucfirst($previous_type).'\']").unbind("change");
                                $("select[name^=\'fields['.$field.'\']").removeAttr("readonly");
                            }
                        })})</script>',
                ); 
            }
            
            $fields['additionalfields'][$tr][$field.'FirstName'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('first_name'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  $t != 'organization' ? true : false,
            );
            $fields['additionalfields'][$tr][$field.'LastName'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('last_name'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  $t != 'organization' ? true : false,
            );
            $fields['additionalfields'][$tr][$field.'Title'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('title'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  in_array($t, array('sss123', 'sslwebserver', 'sslwebserv_wildcard', 'sslwebserver_ev', 'securesite', 'securesite_ev', 'securesite_pro', 'securite_site_pro_ev', 'truebizd_ev')) ? true : false,
            );
            $fields['additionalfields'][$tr][$field.'Name'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('organization'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  false,
            );
            $fields['additionalfields'][$tr][$field.'Address1'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('address1'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  true,
            );
            $fields['additionalfields'][$tr][$field.'Address2'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('address2'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  false,
            );
            $fields['additionalfields'][$tr][$field.'Address3'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('address3'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  false,
            );
            $fields['additionalfields'][$tr][$field.'City'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('city'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  true,
            );
            $fields['additionalfields'][$tr][$field.'State'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('state'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  true,
            );
            $fields['additionalfields'][$tr][$field.'PostalCode'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('postal_code'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  false, 
            );
            $fields['additionalfields'][$tr][$field.'Country'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('country'),
                'Type'          =>  'country',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  true, 
            );
            $fields['additionalfields'][$tr][$field.'Phone'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('phone'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  opensrs_ssl_translate('phone_description'),
                'Required'      =>  true, 
            );
            $fields['additionalfields'][$tr][$field.'Fax'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('fax'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  false, 
            );
            $fields['additionalfields'][$tr][$field.'Email'] = array
            (
                'FriendlyName'  =>  opensrs_ssl_translate('email'),
                'Type'          =>  'text',
                'Size'          =>  '30',
                'Description'   =>  '',
                'Required'      =>  true, 
            );
            $previous_type = $t;
        }
    }
    
    return $fields;
}


function opensrs_ssl_SSLStepTwo($params)
{
    //LOAD LANG
    $_LANG = opensrs_ssl_loadLanguage();

    //CHECK TYPES AND PHONES
    $product_type = opensrs_ssl_getCertType($params['certtype']);
    $types = opensrs_ssl_getRequiredContacts($product_type);
    foreach($types as $type)
    {
        $n = $type.'Phone';
        if(isset($params['configdata']['fields'][$n]))
        {
            $phone = $params['configdata']['fields'][$n];
            $preg = preg_match('/(\+{1})([0-9]{2,3})(\.{1})([0-9]+)/', $phone);
            if(!$preg)
            {
                return array('error'    =>  opensrs_ssl_translate('invalid_phone_number'));
            }
        }
    }

    //For trustwave certs
    $q = mysql_safequery("SELECT certtype FROM tblsslorders WHERE MD5(id) = ?", array($_REQUEST['cert']));
    $row = mysql_fetch_assoc($q);

    if(in_array(opensrs_ssl_getCertType($row['certtype']), array('trustwave_dv', 'trustwave_ev', 'trustwave_premiumssl', 'trustwave_premiumssl_wildcard')))
    {
        return array
        (
            'approveremails'   =>   array
            (
                $params['clientsdetails']['email'],
            )
        );
    }
    
    //Prepare CSR
    $csr = trim($params['configdata']['csr']);
    $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
    $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
    $csr = trim($csr);
    
    $subject  = openssl_csr_get_subject($csr);
    $domain = $subject['CN'];
    $product_type = opensrs_ssl_getCertType($params['configoption4']);
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);

    //CHECK CSR
    $send = array
    (
        'action'        =>  'parse_csr',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'csr'           =>  $csr,
            'product_type'  =>  $product_type
         )
    );

    $res = $openSRS->send($send);
    if(!$openSRS->isSuccess())
    {
        return array('error'    =>  opensrs_ssl_translate($openSRS->getError()));
    }

    //GET APPROVED EMAILS
    $send = array
    (
        'action'        =>  'query_approver_list',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'domain'        =>  $domain,
            'product_type'  =>  $product_type
        )
    );

    $res = $openSRS->send($send);
    if(!$openSRS->isSuccess())
    {
        return array('error'    =>  opensrs_ssl_translate($openSRS->getError()));
    }

    $emails = array();
    foreach($res['attributes']['approver_list'] as $email)
    {
        $emails[] = $email['email'];
    }

    return array
    (
        'approveremails'   =>   $emails
    );
}

function opensrs_ssl_sslstepthree($params)
{
    $_LANG = opensrs_ssl_loadLanguage();
    //Prepare CSR
    $csr = $params['configdata']['csr'];
    $csr = trim($params['configdata']['csr']);
    $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/','',$csr));
    $csr = "-----BEGIN CERTIFICATE REQUEST-----\n".$csr."\n-----END CERTIFICATE REQUEST-----";
    $csr = trim($csr);
    
    //Domain
    $subject  = openssl_csr_get_subject($csr);
    $domain = $subject['CN'];
    //Period
    $period = $params['configoptions']['Period'] ? $params['configoptions']['Period'] : $params['configoption5'];
    //Search in seal
    $seal_in_search = $params['customfields']['Search in seal'] ? $params['customfields']['Search in seal'] : $params['configoption6'];
    //Server Count
    $server_count = $params['customfields']['Server Count'] ? $params['customfields']['Server Count'] : $params['configoption7']; 
    //Cert Type
    $product_type = opensrs_ssl_getCertType($params['configoption4']);
    
    $openSRS = new OpenSRS($params['configoption1'], 0, $params['configoption2'], $params['configoption3'] == 'on' ? 0 : 1);
    
    $types = opensrs_ssl_getRequiredContacts($product_type);
    $fields = array();
    
    $contact_types = array('admin', 'billing', 'tech', 'organization', 'signer');
    
    $send = array();
    $q = mysql_safequery("SELECT remoteid FROM tblsslorders WHERE serviceid = ?", array($params['serviceid']));
    $row = mysql_fetch_assoc($q);

    $send = array
    (
        'action'        =>  'sw_register',
        'object'        =>  'trust_service',
        'attributes'    =>  array
        (
            'approver_email'    =>  $params['approveremail'] ? $params['approveremail'] : $params['clientsdetails']['email'],
            'product_type'      =>  $product_type,
            'contact_set'       =>  array(),
            'csr'               =>  $csr,
            'domain'            =>  $domain,
            'handle'            =>  'process',
            'period'            =>  $period,
            'reg_type'          =>  'new',
            'server_count'      =>  $server_count,
            'server_type'       =>  opensrs_ssl_getServerType($params['configdata']['servertype'], $product_type)
         )
    );
    
    $contact = array();
    $details = $params['configdata']['fields'];
    foreach($contact_types as $type)
    {
        $t = ucfirst($type);
        if(in_array($type, $types))
        {
            $contact[$type]['first_name'] = $details[$t.'FirstName'];
            $contact[$type]['last_name'] = $details[$t.'LastName'];
            $contact[$type]['title'] = $details[$t.'Title'];
            $contact[$type]['org_name'] = $details[$t.'Name'];
            $contact[$type]['address1'] = $details[$t.'Address1'];
            $contact[$type]['address2'] = $details[$t.'Address2'];
            $contact[$type]['address3'] = $details[$t.'Address3'];
            $contact[$type]['city'] = $details[$t.'City'];
            $contact[$type]['state'] = $details[$t.'State'];
            $contact[$type]['postal_code'] = $details[$t.'PostalCode'];
            $contact[$type]['state'] = $details[$t.'State'];
            $contact[$type]['country'] = $details[$t.'Country'];
            $contact[$type]['email'] = $details[$t.'Email'];
            $contact[$type]['phone'] = $details[$t.'Phone'];
            $contact[$type]['fax'] = $details[$t.'Fax'];
        }
    }
    
    $send['attributes']['contact_set'] = $contact;
    
    if($seal_in_search)
    {
        $send['attributes']['seal_in_search'] = '1';
        $send['attributes']['trust_seal'] = '1';
    }
   
    $res = $openSRS->send($send);
    
    if(!$openSRS->isSuccess())
    {
        return array('error' => opensrs_ssl_translate($openSRS->getError()));
    }
    
    $order_id = $res['attributes']['order_id'];
    mysql_safequery("UPDATE tblsslorders SET remoteid = ? WHERE serviceid = ?", array($order_id, $params['serviceid']));
    mysql_safequery("UPDATE tblhosting SET domain = ? WHERE id = ?", array($domain, $params['serviceid'])); 
}

//LOAD LANGUAGE
function opensrs_ssl_loadLanguage()
{
    GLOBAL $opensrs_ssl_language;
    if($opensrs_ssl_language)
        return $opensrs_ssl_language;
    
    $language = null;
    if(isset($_SESSION['Language'])) // GET LANG FROM SESSION
    { 
        $language = strtolower($_SESSION['Language']);
    }
    else
    {
        $q = mysql_safequery("SELECT language FROM tblclients WHERE id = ?", array($_SESSION['uid']));
        $row = mysql_fetch_assoc($q); 
        if($row['language'])
            $language = $row['language'];
    }
    
    if(!$language) //Ouuuh?
    {
        $q = mysql_safequery("SELECT value FROM tblconfiguration WHERE setting = 'Language' LIMIT 1");
        $row = mysql_fetch_assoc($q);
        $language = $row['language'];
    }
    $langfilename = dirname(__FILE__).DS.'lang'.DS.$language.'.php';
    $deflangfilename = dirname(__FILE__).DS.'lang'.DS.'english.php';
    if(file_exists($langfilename)) 
        include($langfilename);
    else
        include($deflangfilename);
    
    $opensrs_ssl_language = $_LANG;
    return $_LANG;
}

function opensrs_ssl_translate($key)
{
    $_LANG = opensrs_ssl_loadLanguage();
    
    if(isset($_LANG[$key]))
        return $_LANG[$key];
    
    return $key;
}
