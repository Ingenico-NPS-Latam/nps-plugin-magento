<?php

require_once(Mage::getBaseDir('lib') . '/Sub1/psp_client.php');

/**
* Our test CC module adapter
*/
class Sub1_Nps_Model_Nps extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'nps';

    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     *
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */


    protected $_formBlockType = 'nps/form';


    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;

    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;

    /**
     * Can capture funds online?
     */
    protected $_canCapture              = true;

    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = true;

    /**
     * Can refund online?
     */
    protected $_canRefund               = true;

    /**
     * Can refund partial amounts online?
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;

    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;

    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = false;

    /**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;

    /**
     * Here you will need to implement authorize, capture and void public methods
     *
     * @see examples of transaction specific public methods such as
     * authorize, capture and void in Mage_Paygate_Model_Authorizenet
     */


    /**
     *  Return Order Place Redirect URL
     *
     *  @return string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('nps/redirect/redirect');
    }


    /**
     * Send authorize request to gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     */

    public function authorize(Varien_Object $payment, $amount)
    {
        return $this;
    }

    /**
     * Send capture request to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     */

    public function capture(Varien_Object $payment, $amount)
    {
        if(Mage::getModel('nps/nps')->getConfigData('payment_action_nps') == 'authorize_capture') {
          return $this;
        }

        if( ! $payment->getCcTransId() ) {
          return $this->getOrderPlaceRedirectUrl();
        }

        if( $amount > $payment->getOrder()->getGrandTotal() ) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount to capture.'));
        }

        $psp_parameters = array(
            'psp_Version'            => '1',
            'psp_MerchantId'         => Mage::getModel('nps/nps')->getConfigData('merchant_id'),
            'psp_TxSource'           => 'WEB',
            'psp_MerchTxRef'         => strtoupper(uniqid($payment->getOrder()->getIncrementId().".", true)),
            'psp_TransactionId_Orig' => $payment->getCcTransId(),
            'psp_AmountToCapture'    => Mage::getModel('nps/nps')->toCents($amount),
            'psp_PosDateTime'        => date('Y-m-d H:i:s'),
            'psp_UserId'             => substr(Mage::getSingleton('admin/session')->getUser()->getUsername(),0,64),
            // 'psp_UserId'             => 'psp_test',
        );

        $response = $this->sendToNPS('Capture', $psp_parameters);

        if(is_array($response) && count($response)) {
            $parent = $payment->getCcTransId();
            $payment->setCcTransId($response['psp_TransactionId']);
            $payment->setTransactionId(date('YmdHisu'));
            $payment->setParentTransactionId( $parent );
            $payment->setIsTransactionClosed(0);
            $payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,$response);
        }else {
          Mage::throwException('Error Processing the request');
        }

        return $this;
    }


    public function refund(Varien_Object $payment, $amount)
    {
        if( $amount > $payment->getOrder()->getGrandTotal() ) {
            Mage::throwException(Mage::helper('paygate')->__('Invalid amount to refund.'));
        }

        $psp_parameters = array(
            'psp_Version'            => '1',
            'psp_MerchantId'         => Mage::getModel('nps/nps')->getConfigData('merchant_id'),
            'psp_TxSource'           => 'WEB',
            'psp_PosDateTime'        => date('Y-m-d H:i:s'),
            'psp_MerchTxRef'         => strtoupper(uniqid($payment->getOrder()->getIncrementId().".", true)),
            'psp_TransactionId_Orig' => $payment->getCcTransId(),
            'psp_AmountToRefund'     => Mage::getModel('nps/nps')->toCents($amount),
            // 'psp_CardSecurityCode'   => '666',
            // 'psp_CardExpDate'        => '1501',
            'psp_UserId'             => substr(Mage::getSingleton('admin/session')->getUser()->getUsername(),0,64),
        );

        $response = $this->sendToNPS('Refund', $psp_parameters);

        if(is_array($response) && count($response)) {
            $payment->setTransactionId(date('YmdHisu'));
            $payment->setParentTransactionId($payment->getCcTransId());
            $payment->setIsTransactionClosed(0);
            $payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,$response);
        }else {
          Mage::throwException('Error Processing the request');
        }

        return $this;
    }


    /**
     * Void the payment through gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @return Mage_Paygate_Model_Authorizenet
     */
    public function void(Varien_Object $payment)
    {
        return $this;
    }




    /**
     * Get CGI url
     *
     * @return string
     */
    public function getCgiUrl()
    {
        $uri = $this->getConfigData('cgi_url');
        return $uri;
        // return $uri ? $uri : self::CGI_URL;
    }

    /**
     *
     * @param float $amount
     * @param string $cc_type
     * @param integer $installment
     * @return integer
     */
    public function calculatePspAmount($amount=0, $cc_type=null, $installment=1) {

        if( $amount && $cc_type && $installment)
        {
            /**
             * 1ero redondear a dos decimales
             * 2do convertir a centavos
             */
            $amount = number_format(round($amount, 2),2,'','');

            /**
             * obtengo las cuotas por producto y cantidad
             */
            $table = Mage::getSingleton('core/resource')->getTableName('installment/installment');
            $table2 = Mage::getSingleton('core/resource')->getTableName('installment/installment_store');
            $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $query = 'SELECT ii.rate FROM '.$table.' ii
                INNER JOIN '.$table2.' iis ON ii.entity_id = iis.installment_id
                WHERE ii.cc_type = \''.$cc_type.'\' AND ii.qty = \''.$installment.'\'
                    AND iis.store_id = '.Mage::app()->getStore()->getId().'
                    LIMIT 1';
            $rate = $readConnection->fetchOne($query);

            /**
             * 3ero aplicarle el intereses en base al numero de cuotas
             */
            if($rate) {
              $tax = round($amount*$rate/100);
              $amount = $amount+$tax;
            }
        }

        return $amount;
    }

    public function sendToNPS($method, $fields) {
        $cli = new PSP_Client();
        $cli->setDebug(false);
        $cli->setPrintRequest(false);
        $cli->setPrintResponse(false);
        $cli->setConnectTimeout(10);
        $cli->setExecuteTimeout(60);
        $cli->setUrl(Mage::getModel('nps/nps')->getCgiUrl());
        $cli->setWsdlCache(Mage::getBaseDir('cache'), 43200);
        $cli->setSecretKey(Mage::getModel('nps/nps')->getConfigData('secretkey'));
        $cli->setMethodName($method);
        $cli->setMethodParams($fields);

        try {
          return $cli->send();
        }catch(Exception $e) {
          $order = new Mage_Sales_Model_Order();
          $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
          $this->directLinkTransact($order, null, array('Exception' => $e->getCode().' - '.$e->getMessage()), Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT, $method);
        }

        return false;
    }

    public function formatCurrencyToPspCurrency($currency) {
        switch($currency) {
            case 'ARS': return '032'; // pesos argentinos
            case 'CLP': return '152'; // pesos chilenos
            case 'COP': return '170'; //"Pesos Colombianos"
            // case '': return '350'; //??
            case 'MXN': return '484'; //"Pesos Mexicanos"
            case 'PYG': return '600'; //"Guaranies"
            case 'PEN': return '604'; //"Nuevos Soles Peruanos"
            case 'USD': return '840'; //"Dolares Estadounidenses"
            case 'UYU': return '858'; //"Pesos Uruguayos"
            case 'VEB': return '862'; //"Bolivares Venezolanos"
            case 'VEF': return '937'; //"Bolivares Fuertes Venezolanos"
            case 'BRL': return '986'; //"Reales Brasileños"
            case 'CAD': return '124'; //"Dolares Canadienses"
            case 'EUR': return '978'; //"Euro"
            case 'CRC': return '188'; //"Colon Costarricense"
            case 'DOP': return '214'; //"Pesos Dominicanos"
            // case '': return '218'; //"Sucres Ecuatorianos"
            case 'SVC': return '222'; //"Colones Salvadoreños"
            case 'GTQ': return '320'; //"Quetzales Guatemaltecos"
            case 'HNL': return '340'; //"Lempiras Hondureños"
            case 'NIC': return '558'; //"Córdobas Oro Nicaragüenses"
            case 'PAB': return '590'; //"Balboas Panameñas"
            case 'GBP': return '826'; //"Libras Esterlinas"
            default: throw new Exception('Currency ('.$currency.') need to be formated to transact with psp');
        }


/*
<option value="AFN">afgani</option>
<option value="ROL">antiguo leu rumano</option>
<option value="MGA">ariary malgache</option>
<option value="THB">baht tailandés</option>
<option value="PAB">balboa panameño</option>
<option value="ETB">birr etíope</option>
<option value="BOB">boliviano</option>
<option value="VEF">bolívar fuerte venezolano</option>
<option value="VEB">bolívar venezolano</option>
<option value="KES">chelín keniata</option>
<option value="SOS">chelín somalí</option>
<option value="TZS">chelín tanzano</option>
<option value="UGX">chelín ugandés</option>
<option value="CRC">colón costarricense</option>
<option value="SVC">colón salvadoreño</option>
<option value="CZK">corona checa</option>
<option value="DKK">corona danesa</option>
<option value="SKK">corona eslovaca</option>
<option value="EEK">corona estonia</option>
<option value="ISK">corona islandesa</option>
<option value="NOK">corona noruega</option>
<option value="SEK">corona sueca</option>
<option value="NIC">córdoba nicaragüense</option>
<option value="GMD">dalasi gambiano</option>
<option value="DZD">dinar argelino</option>
<option value="BHD">dinar bahreiní</option>
<option value="IQD">dinar iraquí</option>
<option value="JOD">dinar jordano</option>
<option value="KWD">dinar kuwaití</option>
<option value="LYD">dinar libio</option>
<option value="MKD">dinar macedonio</option>
<option value="RSD">dinar serbio</option>
<option value="TND">dinar tunecino</option>
<option value="MAD">dirham marroquí</option>
<option value="STD">dobra de Santo Tomé y Príncipe</option>
<option value="VND">dong vietnamita</option>
<option value="AMD">dram armenio</option>
<option value="AED">dírham de los Emiratos Árabes Unidos</option>
<option value="AUD">dólar australiano</option>
<option value="CAD">dólar canadiense</option>
<option value="BBD">dólar de Barbados</option>
<option value="BZD">dólar de Belice</option>
<option value="BMD">dólar de Bermudas</option>
<option value="BND">dólar de Brunéi</option>
<option value="HKD">dólar de Hong Kong</option>
<option value="JMD">dólar de Jamaica</option>
<option value="NAD">dólar de Namibia</option>
<option value="TTD">dólar de Trinidad y Tobago</option>
<option value="ZWD">dólar de Zimbabue</option>
<option value="BSD">dólar de las Bahamas</option>
<option value="KYD">dólar de las Islas Caimán</option>
<option value="FJD">dólar de las Islas Fiyi</option>
<option value="SBD">dólar de las Islas Salomón</option>
<option value="XCD">dólar del Caribe Oriental</option>
<option value="USD">dólar estadounidense</option>
<option value="GYD">dólar guyanés</option>
<option value="LRD">dólar liberiano</option>
<option value="NZD">dólar neozelandés</option>
<option value="RHD">dólar rodesiano</option>
<option value="SGD">dólar singapurense</option>
<option value="SRD">dólar surinamés</option>
<option value="GQE">ekuele de Guinea Ecuatorial</option>
<option value="CVE">escudo de Cabo Verde</option>
<option value="EUR">euro</option>
<option value="CHE">euro WIR</option>
<option value="AWG">florín de Aruba</option>
<option value="ANG">florín de las Antillas Neerlandesas</option>
<option value="HUF">florín húngaro</option>
<option value="XOF">franco CFA BCEAO</option>
<option value="XPF">franco CFP</option>
<option value="CHW">franco WIR</option>
<option value="KMF">franco comorense</option>
<option value="CDF">franco congoleño</option>
<option value="BIF">franco de Burundi</option>
<option value="DJF">franco de Yibuti</option>
<option value="GNF">franco guineano</option>
<option value="RWF">franco ruandés</option>
<option value="CHF">franco suizo</option>
<option value="HTG">gourde haitiano</option>
<option value="UAH">grivna ucraniana</option>
<option value="PYG">guaraní paraguayo</option>
<option value="PGK">kina de Papúa Nueva Guinea</option>
<option value="LAK">kip laosiano</option>
<option value="HRK">kuna croata</option>
<option value="GEK">kupon larit georgiano</option>
<option value="MWK">kwacha de Malawi</option>
<option value="ZMK">kwacha zambiano</option>
<option value="AOA">kwanza angoleño</option>
<option value="BUK">kyat birmano</option>
<option value="MMK">kyat de Myanmar</option>
<option value="GEL">lari georgiano</option>
<option value="LVL">lats letón</option>
<option value="ALL">lek albanés</option>
<option value="HNL">lempira hondureño</option>
<option value="SLL">leone de Sierra Leona</option>
<option value="MDL">leu moldavo</option>
<option value="RON">leu rumano</option>
<option value="GIP">libra de Gibraltar</option>
<option value="SHP">libra de Santa Elena</option>
<option value="FKP">libra de las Islas Malvinas</option>
<option value="EGP">libra egipcia</option>
<option value="GBP">libra esterlina británica</option>
<option value="LBP">libra libanesa</option>
<option value="SYP">libra siria</option>
<option value="SDG">libra sudanesa</option>
<option value="SZL">lilangeni suazi</option>
<option value="TRL">lira turca antigua</option>
<option value="LTL">litas lituano</option>
<option value="LSL">loti lesothense</option>
<option value="AZN">manat azerí</option>
<option value="AZM">manat azerí (1993-2006)</option>
<option value="TMM">manat turcomano</option>
<option value="BAM">marco convertible de Bosnia-Herzegovina</option>
<option value="MZN">metical mozambiqueño</option>
<option value="NGN">naira nigeriano</option>
<option value="ERN">nakfa eritreo</option>
<option value="BTN">ngultrum butanés</option>
<option value="TRY">nueva lira turca</option>
<option value="TWD">nuevo dólar taiwanés</option>
<option value="BGN">nuevo lev búlgaro</option>
<option value="ILS">nuevo sheqel israelí</option>
<option value="PEN">nuevo sol peruano</option>
<option value="MRO">ouguiya mauritano</option>
<option value="MOP">pataca de Macao</option>
<option value="TOP">paʻanga tongano</option>
<option selected="selected" value="ARS">peso argentino</option>
<option value="CLP">peso chileno</option>
<option value="COP">peso colombiano</option>
<option value="CUP">peso cubano</option>
<option value="DOP">peso dominicano</option>
<option value="PHP">peso filipino</option>
<option value="MXN">peso mexicano</option>
<option value="UYU">peso uruguayo</option>
<option value="BWP">pula botsuano</option>
<option value="GTQ">quetzal guatemalteco</option>
<option value="ZAR">rand sudafricano</option>
<option value="BRL">real brasileño</option>
<option value="IRR">rial iraní</option>
<option value="OMR">rial omaní</option>
<option value="YER">rial yemení</option>
<option value="KHR">riel camboyano</option>
<option value="MYR">ringgit malasio</option>
<option value="QAR">riyal de Qatar</option>
<option value="SAR">riyal saudí</option>
<option value="BYR">rublo bielorruso</option>
<option value="RUB">rublo ruso</option>
<option value="MVR">rufiyaa de Maldivas</option>
<option value="SCR">rupia de Seychelles</option>
<option value="LKR">rupia de Sri Lanka</option>
<option value="INR">rupia india</option>
<option value="IDR">rupia indonesia</option>
<option value="MUR">rupia mauriciana</option>
<option value="NPR">rupia nepalesa</option>
<option value="PKR">rupia pakistaní</option>
<option value="KGS">som kirguís</option>
<option value="TJS">somoni tayiko</option>
<option value="UZS">sum uzbeko</option>
<option value="BDT">taka de Bangladesh</option>
<option value="WST">tala samoano</option>
<option value="KZT">tenge kazako</option>
<option value="MNT">tugrik mongol</option>
<option value="VUV">vatu vanuatuense</option>
<option value="KPW">won norcoreano</option>
<option value="KRW">won surcoreano</option>
<option value="JPY">yen japonés</option>
<option value="CNY">yuan renminbi chino</option>
<option value="PLN">zloty polaco</option>
*/




    }

    public function formatCCTypeToPspProductId($cc_type) {
        switch($cc_type) {
            case 'AE': return '1';	//American Express
            case 'DI': return '2';	//Diners
            case 'JCB': return '4';	//JCB
            case 'MC': return '5';	//Mastercard
            case 'CAB': return '8';	//Cabal
            case 'TN': return '9';	//Naranja
            case 'KAD': return '10';	//Kadicard
            case 'VI': return '14';	//Visa
            case 'FAV': return '15';	//Favacard
            case 'LID': return '17';	//Lider
            case 'CRED': return '20';	//Credimas
            case 'NEV': return '21';	//Nevada
            case 'VIN': return '29';	//Visa Naranja
            case 'P365': return '33';	//Patagonia 365
            case 'SL': return '34';	//Sol
            case 'FAL': return '35';	//CMR Falabella
            case 'NATMC': return '38';	//Nativa MC
            case 'TSHOP': return '42';	//T Shopping
            case 'ITAL': return '43';	//Italcred
            case 'NAC': return '45';	//Club La Nacion
            case 'PER': return '46';	//Club Personal
            case 'ARN': return '47';	//Club Arnet
            case 'MAS': return '48';	//Mas (Cencosud)
            case 'NMO': return '49';	//Naranja MO
            case 'PYN': return '50';	//Pyme Nacion
            case 'CLA': return '51';	//Clarin 365
            case 'SPE': return '52';	//Club Speedy
            case 'AR': return '53';	//Argenta
            case 'VID': return '55';	//Visa Debito
            case 'MCB': return '57';	//MC Bancor
            case 'CLV': return '58';	//Club La Voz
            case 'NEX': return '61';	//Nexo
            case 'NAT': return '63';	//NATIVA
            case 'ARGE': return '65';	//Argencard
            case 'MA': return '66';	//Maestro
            case 'CE': return '69';	//Cetelem
            case 'CON': return '72';	//Consumax
            case 'MI': return '75';	//Mira
            case 'CG': return '91';	//Credi Guia
            case 'SU': return '93';	//Sucredito
            case 'COOP': return '95';	//Coopeplus
            case 'DIS': return '101';	//Discover
            case 'EL': return '102';	//Elo
            case 'MAG': return '103';	//Magna
            case 'AU': return '104';	//Aura
            case 'HIP': return '105';	//Hipercard
            // case 'COL': return '106';	//Credencial COL
            case 'RED': return '107';	//RedCompra
            case 'SC': return '108';	//SuperCard
            case 'BB': return '110';	//BBPS
            case 'RI': return '112';	//Ripley
            case 'OH': return '113';	//OH!
            case 'ME': return '114';	//Metro
            case 'UP': return '115';	//UnionPay
            case 'HI': return '116';	//Hiper
            case 'CF': return '117';	//Carrefour
            case 'GR': return '118';	//Grupar
            case 'TU': return '119';	//Tuya
            case 'CD': return '120';	//Club Dia
            case 'CTC': return '121';	//CTC Group
            case 'QI': return '122';	//Qida
            case 'COD': return '123';	//Codensa
            case 'SBB': return '124';	//Socios BBVA
            case 'UA': return '125';	//UATP
            case 'CR': return '126';	//Credz
            case 'WP': return '127';	//WebPay
            case 'COM': return '128';	//Comfama
            case 'CSU': return '129';	//Colsubsidio
            case 'CA': return '130';	//Carnet
            case 'CAD': return '131';	//Carnet Debit
            case 'ULT': return '132';	//Ultra
            case 'ELE': return '133';	//Elebar
            case 'CAA': return '134';	//Carta Automatica

          default:
              if(!empty($cc_type) && (int)$cc_type>0) {
                return (string)$cc_type;
              }else if(!empty($cc_type) && (string)$cc_type[0] == "_" && is_numeric((string)$cc_type[1])) {
                $trn_product = intval(substr($cc_type,1));
                if($trn_product>0) {
                  return (string)$trn_product;
                }
              }              
              throw new Exception('CCType ('.$cc_type.') need to be formated to transact with psp');
        }
    }

    public function formatCountryToPspCountry($country) {
        switch($country) {
            case 'AR': return 'ARG'; //argentina
            case 'MX': return 'MEX'; //mexico
            case "AT": return 'AUT'; //"Austria"
            case "BO": return 'BOL'; //"Bolivia"
            case "BR": return 'BRA'; //"Brasil"
            case "CA": return 'CAN'; //"Canadá"
            case "CL": return 'CHL'; //"Chile"
            case "CO": return 'COL'; //"Colombia"
            case "CR": return 'CRI'; //"Costa Rica"
            case "DE": return 'DEU'; //"Alemania"
            case "ES": return 'ESP'; //"España"
            case "PE": return 'PER'; //"Perú"
            case "PY": return 'PRY'; //"Paraguay"
            case "UY": return 'URY'; //"Uruguay"
            case "US": return 'USA'; //"Estados Unidos"
            case "VE": return 'VEN'; //"Venezuela"
            case "DO": return 'DOM'; //"República Dominicana"
            case "EC": return 'ECU'; //"Ecuador"
            case "SV": return 'SLV'; //"El Salvador"
            case "GT": return 'GTM'; //"Guatemala"
            case "HN": return 'HND'; //"Honduras"
            case "NI": return 'NIC'; //"Nicaragua"
            case "PA": return 'PAN'; //"Panamá"
            case "PR": return 'PRI'; //"Puerto Rico"
            case "GB": return 'GBR'; //"Reino Unido"
            case "AX": return 'ALA'; //"Islas Aland"
            case "AS": return 'ASM'; //"Samoa Americana"
            case "AD": return 'AND'; //"Andorra"
            case "BE": return 'BEL'; //"Bélgica"
            case "HR": return 'HRV'; //"Croacia"
            case "CY": return 'CYP'; //"Chipre"
            case "EE": return 'EST'; //"Estonia"
            case "ET": return 'ETH'; //"Etiopía"
            case "FI": return 'FIN'; //"Finlandia"
            case "FR": return 'FRA'; //"Francia"
            case "GF": return 'GUF'; //"Guayana Francesa"
            case "TF": return 'ATF'; //"Territorios Australes Franceses"
            case "GR": return 'GRC'; //"Grecia"
            case "GP": return 'GLP'; //"Guadalupe"
            case "IE": return 'IRL'; //"Irlanda"
            case "IT": return 'ITA'; //"Italia"
            case "LU": return 'LUX'; //"Luxemburgo"
            case "MT": return 'MLT'; //"Malta"
            case "MQ": return 'MTQ'; //"Martinica"
            case "YT": return 'MYT'; //"Mayotte"
            case "MC": return 'MCO'; //"Mónaco"
            case "ME": return 'MNE'; //"Montenegro"
            case "NL": return 'NLD'; //"Países Bajos"
            case "PT": return 'PRT'; //"Portugal"
            case "RE": return 'REU'; //"Reunión"
            case "SM": return 'SMR'; //"San Marino"
            case "SK": return 'SVK'; //"Eslovaquia"
            case "SI": return 'SVN'; //"Eslovenia"
            case "BL": return 'BLM'; //"San Bartolomé"
            case "MF": return 'MAF'; //"San Martín"
            case "PM": return 'SPM'; //"San Pedro y Miquelón"
            case "VA": return 'VAT'; //"Ciudad del Vaticano"
            default: throw new Exception('Country ('.$country.') need to be formated to transact with psp');
        }

/*
<option value="DE">Alemania</option>
<option value="AU">Australia</option>
<option value="AT">Austria</option>
<option value="BE">Bélgica</option>
<option value="BR">Brasil</option>
<option value="BG">Bulgaria</option>
<option value="CA">Canadá</option>
<option value="CL">Chile</option>
<option value="CY">Chipre</option>
<option value="KR">Corea del Sur</option>
<option value="CR">Costa Rica</option>
<option value="DK">Dinamarca</option>
<option value="EC">Ecuador</option>
<option value="AE">Emiratos Árabes Unidos</option>
<option value="SK">Eslovaquia</option>
<option value="SI">Eslovenia</option>
<option value="ES">España</option>
<option value="US">Estados Unidos</option>
<option value="EE">Estonia</option>
<option value="PH">Filipinas</option>
<option value="FI">Finlandia</option>
<option value="FR">Francia</option>
<option value="GI">Gibraltar</option>
<option value="GR">Grecia</option>
<option value="GP">Guadalupe</option>
<option value="GF">Guayana Francesa</option>
<option value="HK">Hong Kong</option>
<option value="HU">Hungría</option>
<option value="IN">India</option>
<option value="ID">Indonesia</option>
<option value="IE">Irlanda</option>
<option value="IS">Islandia</option>
<option value="IL">Israel</option>
<option value="IT">Italia</option>
<option value="JM">Jamaica</option>
<option value="JP">Japón</option>
<option value="LV">Letonia</option>
<option value="LI">Liechtenstein</option>
<option value="LT">Lituania</option>
<option value="LU">Luxemburgo</option>
<option value="MY">Malasia</option>
<option value="MT">Malta</option>
<option value="MQ">Martinica</option>
<option value="MX">México</option>
<option value="NO">Noruega</option>
<option value="NZ">Nueva Zelanda</option>
<option value="NL">Países Bajos</option>
<option value="PL">Polonia</option>
<option value="PT">Portugal</option>
<option value="GB">Reino Unido</option>
<option value="CZ">República Checa</option>
<option value="DO">República Dominicana</option>
<option value="RE">Reunión</option>
<option value="RO">Rumanía</option>
<option value="SM">San Marino</option>
<option value="SG">Singapur</option>
<option value="ZA">Sudáfrica</option>
<option value="SE">Suecia</option>
<option value="CH">Suiza</option>
<option value="TH">Tailandia</option>
<option value="TW">Taiwán</option>
<option value="TR">Turquía</option>
<option value="UY">Uruguay</option>
<option value="VE">Venezuela</option>
<option value="VN">Vietnam</option>
*/



    }

    public function directLinkTransact($order,$transactionID, $arrInformation = array(), $typename, $comment='', $closed = 0)
    {
        $payment = $order->getPayment();
        $payment->setTransactionId(date('YmdHisu'));
        $payment->setCcTransId($transactionID);
        $transaction = $payment->addTransaction($typename, null, false, $comment);
        $transaction->setParentTxnId($transactionID);
        $transaction->setIsClosed($closed);
        $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,$arrInformation);
        $transaction->save();
        $order->save();
        return $this;
    }


    public function assignData($data)
    {
        parent::assignData($data);

        if( $data->getInstallment() ) {
            Mage::getSingleton('core/session')->setInstallment($data->getInstallment());
        }

        $order = new Mage_Sales_Model_Order();
        $order->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
        if( $order->getPayment() && $data->getInstallment() ) {
            $order->getPayment()->setAdditionalInformation('installment', $data->getInstallment());
            $order->getPayment()->save();
            $order->save();
        }

        return $this;
    }

    public function toCents($float) {
        return number_format(round($float, 2),2,'','');
    }

    public function getConfigData($field, $storeId = null)
    {
      $v = parent::getConfigData($field, $storeId);
      if($field == 'payment_action') {
        $v = 'authorize';
      }
      return $v;
    }

    public function formatShippingMethod($v) {
      switch($v) {
        case 1: return "10"; //Carrier designado por el comprador
        case 1: return "20"; //Descarga de contenidos
        case 1: return "30"; //Militar
        case 1: return "40"; //Entrega - Mismo dia
        case 1: return "41"; //Entrega - Dia siguiente / Por la noche
        case 1: return "42"; //Entrega - Segundo dia
        case 1: return "43"; //Entrega - Tercer dia
        case 1: return "50"; //Retiro en comercio
        default: return "99"; //Otro
      }
    }

    public function formatShippingCarrier($v) {
      switch($v) {
        case 1: return "100"; //UPS
        case 1: return "101"; //USPS
        case 1: return "102"; //FedEx
        case 1: return "103"; //DHL
        case 1: return "104"; //Purolator
        case 1: return "105"; //Greyhound
        case 1: return "200"; //Correo Argentino
        case 1: return "201"; //OCA
        default: return "999"; //Other / Otro / Outro
      }
    }

    public function getCcAvailableTypes() {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        $availableTypes = Mage::getModel('nps/nps')->getConfigData('cctypes');
        if ($availableTypes) {
            $availableTypes = explode(',', $availableTypes);
            foreach ($types as $code=>$name) {
                if (!in_array($code, $availableTypes)) {
                    unset($types[$code]);
                }
            }
        }
        return $types;
    }

    public function getCcAvailableTypesInInstallments() {

      /**
       * country lo saco de la seccion del store
       */
      $country = Mage::getStoreConfig('general/country/default');

      /**
       * currency puede venir por parametro, si no viene uso el del store
       */
      $currency = isset($_REQUEST['currency']) ? mysql_escape_string($_REQUEST['currency']) : Mage::app()->getStore()->getBaseCurrencyCode();

      $ccAvailableTypes = $this->getCcAvailableTypes();

      $installment = Mage::getSingleton('core/resource')->getTableName('installment/installment');
      $installment_store = Mage::getSingleton('core/resource')->getTableName('installment/installment_store');
      $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
      $query = "SELECT DISTINCT(cc_type) FROM $installment ii
                INNER JOIN $installment_store iis ON ii.entity_id = iis.installment_id
                WHERE ii.country = '$country'
                AND ii.currency = '$currency'
                AND iis.store_id = " . Mage::app()->getStore()->getId();
      $result = $readConnection->fetchAll($query);
      $ccAvailableTypesInInstallments = array();
      foreach ($result as $value) {
        $ccAvailableTypesInInstallments[$value['cc_type']] = $value['cc_type'];
      }

      $cc = array_intersect_key($ccAvailableTypes, $ccAvailableTypesInInstallments);

      return $cc;
    }

}
