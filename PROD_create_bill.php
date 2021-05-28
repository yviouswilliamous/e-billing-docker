<?php
session_start();
	// =============================================================
	// ===================== Setup Attributes ===========================
	// =============================================================
	// E-Billing server URL
	$SERVER_URL = "https://www.billing-easy.com/api/v1/merchant/e_bills";

	// Username
	$USER_NAME = '[USERNAME]';

	// SharedKey
	$SHARED_KEY = '[SHAREDKEY]';

	// POST URL
	$POST_URL = 'https://www.billing-easy.net';

	// Check mandatory attributes have been supplied in Http Session
	if(empty($_SESSION['eb_amount_m'])) die("Error : eb_amount_m parameter is not provided. ");
	if(empty($_SESSION['eb_shortdescription_m'])) die("Error : eb_shortdescription_m parameter is not provided. ");
	if(empty($_SESSION['eb_reference_m'])) die("Error : eb_reference parameter is not provided. ");
	if(empty($_SESSION['eb_email_m'])) die("Error : eb_email_m parameter is not provided. ");
	if(empty($_SESSION['eb_msisdn_m'])) die("Error : eb_msisdn_m parameter is not provided. ");

	// Fetch all data (including those not optional) from session
	$eb_amount = $_SESSION['eb_amount_m'];
	$eb_shortdescription = $_SESSION['eb_shortdescription_m'];
	$eb_reference = $_SESSION['eb_reference_m'];
	$eb_email = $_SESSION['eb_email_m'];
	$eb_msisdn = $_SESSION['eb_msisdn_m'];
	$eb_name = $_SESSION['eb_name_o'];
	$eb_address = $_SESSION['eb_address_o'];
	$eb_city = $_SESSION['eb_city_o'];
	$eb_detaileddescription = $_SESSION['eb_detaileddescription_o'];
	$eb_additionalinfo = $_SESSION['eb_additionalinfo_o'];
	$eb_callbackurl = $_SESSION['eb_callbackurl_o'];

	// =============================================================
	// ============== E-Billing server invocation ==================
	// =============================================================
	$global_array =
        [
        		'payer_email' => $eb_email,
		'payer_msisdn' => $eb_msisdn,
		'amount' => $eb_amount,
		'short_description' => $eb_shortdescription,
		'description' => $eb_detaileddescription,
		'due_date' => date('d/m/Y', time() + 86400),
		'external_reference' => $eb_reference,
		'payer_name' => $eb_name,
		'payer_address' => $eb_address,
		'payer_city' => $eb_city,
		'additional_info' => $eb_additionalinfo
        ];

        $content = json_encode($global_array);
        $curl = curl_init($SERVER_URL);
        curl_setopt($curl, CURLOPT_USERPWD, $USER_NAME . ":" . $SHARED_KEY);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ( $status != 201 ) {
        	die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        
	curl_close($curl);

        $response = json_decode($json_response, true);

        echo "<form action='" . $POST_URL . "' method='post' name='frm'>";
        echo "<input type='hidden' name='invoice_number' value='".$response['e_bill']['bill_id']."'>";
        echo "<input type='hidden' name='eb_callbackurl' value='".$eb_callbackurl."'>";
        echo "</form>";
        echo "<script language='JavaScript'>";
        echo "document.frm.submit();";
        echo "</script>";
?>
