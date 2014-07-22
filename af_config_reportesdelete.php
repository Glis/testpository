<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "af_config_reportesinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$af_config_reportes_delete = NULL; // Initialize page object first

class caf_config_reportes_delete extends caf_config_reportes {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{6DD8CE42-32CB-41B2-9566-7C52A93FF8EA}";

	// Table name
	var $TableName = 'af_config_reportes';

	// Page object name
	var $PageObjName = 'af_config_reportes_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (af_config_reportes)
		if (!isset($GLOBALS["af_config_reportes"]) || get_class($GLOBALS["af_config_reportes"]) == "caf_config_reportes") {
			$GLOBALS["af_config_reportes"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["af_config_reportes"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'af_config_reportes', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("af_config_reporteslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in af_config_reportes class, af_config_reportesinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->c_IConfig->setDbValue($rs->fields('c_IConfig'));
		$this->c_IReporte->setDbValue($rs->fields('c_IReporte'));
		$this->frec_Envio->setDbValue($rs->fields('frec_Envio'));
		$this->i_Dia_Envio->setDbValue($rs->fields('i_Dia_Envio'));
		$this->x_Hora_Envio->setDbValue($rs->fields('x_Hora_Envio'));
		$this->p_Destino->setDbValue($rs->fields('p_Destino'));
		$this->p_Reseller->setDbValue($rs->fields('p_Reseller'));
		$this->p_CClass->setDbValue($rs->fields('p_CClass'));
		$this->x_DirCorreo->setDbValue($rs->fields('x_DirCorreo'));
		$this->x_Titulo->setDbValue($rs->fields('x_Titulo'));
		$this->x_Mensaje->setDbValue($rs->fields('x_Mensaje'));
		$this->f_Ult_Mod->setDbValue($rs->fields('f_Ult_Mod'));
		$this->c_Usuario_Ult_Mod->setDbValue($rs->fields('c_Usuario_Ult_Mod'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->c_IConfig->DbValue = $row['c_IConfig'];
		$this->c_IReporte->DbValue = $row['c_IReporte'];
		$this->frec_Envio->DbValue = $row['frec_Envio'];
		$this->i_Dia_Envio->DbValue = $row['i_Dia_Envio'];
		$this->x_Hora_Envio->DbValue = $row['x_Hora_Envio'];
		$this->p_Destino->DbValue = $row['p_Destino'];
		$this->p_Reseller->DbValue = $row['p_Reseller'];
		$this->p_CClass->DbValue = $row['p_CClass'];
		$this->x_DirCorreo->DbValue = $row['x_DirCorreo'];
		$this->x_Titulo->DbValue = $row['x_Titulo'];
		$this->x_Mensaje->DbValue = $row['x_Mensaje'];
		$this->f_Ult_Mod->DbValue = $row['f_Ult_Mod'];
		$this->c_Usuario_Ult_Mod->DbValue = $row['c_Usuario_Ult_Mod'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// c_IConfig
		// c_IReporte
		// frec_Envio
		// i_Dia_Envio
		// x_Hora_Envio
		// p_Destino
		// p_Reseller
		// p_CClass
		// x_DirCorreo
		// x_Titulo
		// x_Mensaje
		// f_Ult_Mod
		// c_Usuario_Ult_Mod

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// c_IConfig
			$this->c_IConfig->ViewValue = $this->c_IConfig->CurrentValue;
			$this->c_IConfig->ViewCustomAttributes = "";

			// c_IReporte
			$this->c_IReporte->ViewValue = $this->c_IReporte->CurrentValue;
			$this->c_IReporte->ViewCustomAttributes = "";

			// frec_Envio
			$this->frec_Envio->ViewValue = $this->frec_Envio->CurrentValue;
			$this->frec_Envio->ViewCustomAttributes = "";

			// i_Dia_Envio
			$this->i_Dia_Envio->ViewValue = $this->i_Dia_Envio->CurrentValue;
			$this->i_Dia_Envio->ViewCustomAttributes = "";

			// x_Hora_Envio
			$this->x_Hora_Envio->ViewValue = $this->x_Hora_Envio->CurrentValue;
			$this->x_Hora_Envio->ViewCustomAttributes = "";

			// p_Destino
			$this->p_Destino->ViewValue = $this->p_Destino->CurrentValue;
			$this->p_Destino->ViewCustomAttributes = "";

			// p_Reseller
			$this->p_Reseller->ViewValue = $this->p_Reseller->CurrentValue;
			$this->p_Reseller->ViewCustomAttributes = "";

			// p_CClass
			$this->p_CClass->ViewValue = $this->p_CClass->CurrentValue;
			$this->p_CClass->ViewCustomAttributes = "";

			// x_DirCorreo
			$this->x_DirCorreo->ViewValue = $this->x_DirCorreo->CurrentValue;
			$this->x_DirCorreo->ViewCustomAttributes = "";

			// x_Titulo
			$this->x_Titulo->ViewValue = $this->x_Titulo->CurrentValue;
			$this->x_Titulo->ViewCustomAttributes = "";

			// f_Ult_Mod
			$this->f_Ult_Mod->ViewValue = $this->f_Ult_Mod->CurrentValue;
			$this->f_Ult_Mod->ViewValue = ew_FormatDateTime($this->f_Ult_Mod->ViewValue, 7);
			$this->f_Ult_Mod->ViewCustomAttributes = "";

			// c_Usuario_Ult_Mod
			$this->c_Usuario_Ult_Mod->ViewValue = $this->c_Usuario_Ult_Mod->CurrentValue;
			$this->c_Usuario_Ult_Mod->ViewCustomAttributes = "";

			// c_IConfig
			$this->c_IConfig->LinkCustomAttributes = "";
			$this->c_IConfig->HrefValue = "";
			$this->c_IConfig->TooltipValue = "";

			// c_IReporte
			$this->c_IReporte->LinkCustomAttributes = "";
			$this->c_IReporte->HrefValue = "";
			$this->c_IReporte->TooltipValue = "";

			// frec_Envio
			$this->frec_Envio->LinkCustomAttributes = "";
			$this->frec_Envio->HrefValue = "";
			$this->frec_Envio->TooltipValue = "";

			// i_Dia_Envio
			$this->i_Dia_Envio->LinkCustomAttributes = "";
			$this->i_Dia_Envio->HrefValue = "";
			$this->i_Dia_Envio->TooltipValue = "";

			// x_Hora_Envio
			$this->x_Hora_Envio->LinkCustomAttributes = "";
			$this->x_Hora_Envio->HrefValue = "";
			$this->x_Hora_Envio->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['c_IConfig'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "af_config_reporteslist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($af_config_reportes_delete)) $af_config_reportes_delete = new caf_config_reportes_delete();

// Page init
$af_config_reportes_delete->Page_Init();

// Page main
$af_config_reportes_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$af_config_reportes_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var af_config_reportes_delete = new ew_Page("af_config_reportes_delete");
af_config_reportes_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = af_config_reportes_delete.PageID; // For backward compatibility

// Form object
var faf_config_reportesdelete = new ew_Form("faf_config_reportesdelete");

// Form_CustomValidate event
faf_config_reportesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faf_config_reportesdelete.ValidateRequired = true;
<?php } else { ?>
faf_config_reportesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($af_config_reportes_delete->Recordset = $af_config_reportes_delete->LoadRecordset())
	$af_config_reportes_deleteTotalRecs = $af_config_reportes_delete->Recordset->RecordCount(); // Get record count
if ($af_config_reportes_deleteTotalRecs <= 0) { // No record found, exit
	if ($af_config_reportes_delete->Recordset)
		$af_config_reportes_delete->Recordset->Close();
	$af_config_reportes_delete->Page_Terminate("af_config_reporteslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $af_config_reportes_delete->ShowPageHeader(); ?>
<?php
$af_config_reportes_delete->ShowMessage();
?>
<form name="faf_config_reportesdelete" id="faf_config_reportesdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="af_config_reportes">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($af_config_reportes_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_af_config_reportesdelete" class="ewTable ewTableSeparate">
<?php echo $af_config_reportes->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($af_config_reportes->c_IConfig->Visible) { // c_IConfig ?>
		<td><span id="elh_af_config_reportes_c_IConfig" class="af_config_reportes_c_IConfig"><?php echo $af_config_reportes->c_IConfig->FldCaption() ?></span></td>
<?php } ?>
<?php if ($af_config_reportes->c_IReporte->Visible) { // c_IReporte ?>
		<td><span id="elh_af_config_reportes_c_IReporte" class="af_config_reportes_c_IReporte"><?php echo $af_config_reportes->c_IReporte->FldCaption() ?></span></td>
<?php } ?>
<?php if ($af_config_reportes->frec_Envio->Visible) { // frec_Envio ?>
		<td><span id="elh_af_config_reportes_frec_Envio" class="af_config_reportes_frec_Envio"><?php echo $af_config_reportes->frec_Envio->FldCaption() ?></span></td>
<?php } ?>
<?php if ($af_config_reportes->i_Dia_Envio->Visible) { // i_Dia_Envio ?>
		<td><span id="elh_af_config_reportes_i_Dia_Envio" class="af_config_reportes_i_Dia_Envio"><?php echo $af_config_reportes->i_Dia_Envio->FldCaption() ?></span></td>
<?php } ?>
<?php if ($af_config_reportes->x_Hora_Envio->Visible) { // x_Hora_Envio ?>
		<td><span id="elh_af_config_reportes_x_Hora_Envio" class="af_config_reportes_x_Hora_Envio"><?php echo $af_config_reportes->x_Hora_Envio->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$af_config_reportes_delete->RecCnt = 0;
$i = 0;
while (!$af_config_reportes_delete->Recordset->EOF) {
	$af_config_reportes_delete->RecCnt++;
	$af_config_reportes_delete->RowCnt++;

	// Set row properties
	$af_config_reportes->ResetAttrs();
	$af_config_reportes->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$af_config_reportes_delete->LoadRowValues($af_config_reportes_delete->Recordset);

	// Render row
	$af_config_reportes_delete->RenderRow();
?>
	<tr<?php echo $af_config_reportes->RowAttributes() ?>>
<?php if ($af_config_reportes->c_IConfig->Visible) { // c_IConfig ?>
		<td<?php echo $af_config_reportes->c_IConfig->CellAttributes() ?>>
<span id="el<?php echo $af_config_reportes_delete->RowCnt ?>_af_config_reportes_c_IConfig" class="control-group af_config_reportes_c_IConfig">
<span<?php echo $af_config_reportes->c_IConfig->ViewAttributes() ?>>
<?php echo $af_config_reportes->c_IConfig->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($af_config_reportes->c_IReporte->Visible) { // c_IReporte ?>
		<td<?php echo $af_config_reportes->c_IReporte->CellAttributes() ?>>
<span id="el<?php echo $af_config_reportes_delete->RowCnt ?>_af_config_reportes_c_IReporte" class="control-group af_config_reportes_c_IReporte">
<span<?php echo $af_config_reportes->c_IReporte->ViewAttributes() ?>>
<?php echo $af_config_reportes->c_IReporte->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($af_config_reportes->frec_Envio->Visible) { // frec_Envio ?>
		<td<?php echo $af_config_reportes->frec_Envio->CellAttributes() ?>>
<span id="el<?php echo $af_config_reportes_delete->RowCnt ?>_af_config_reportes_frec_Envio" class="control-group af_config_reportes_frec_Envio">
<span<?php echo $af_config_reportes->frec_Envio->ViewAttributes() ?>>
<?php echo $af_config_reportes->frec_Envio->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($af_config_reportes->i_Dia_Envio->Visible) { // i_Dia_Envio ?>
		<td<?php echo $af_config_reportes->i_Dia_Envio->CellAttributes() ?>>
<span id="el<?php echo $af_config_reportes_delete->RowCnt ?>_af_config_reportes_i_Dia_Envio" class="control-group af_config_reportes_i_Dia_Envio">
<span<?php echo $af_config_reportes->i_Dia_Envio->ViewAttributes() ?>>
<?php echo $af_config_reportes->i_Dia_Envio->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($af_config_reportes->x_Hora_Envio->Visible) { // x_Hora_Envio ?>
		<td<?php echo $af_config_reportes->x_Hora_Envio->CellAttributes() ?>>
<span id="el<?php echo $af_config_reportes_delete->RowCnt ?>_af_config_reportes_x_Hora_Envio" class="control-group af_config_reportes_x_Hora_Envio">
<span<?php echo $af_config_reportes->x_Hora_Envio->ViewAttributes() ?>>
<?php echo $af_config_reportes->x_Hora_Envio->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$af_config_reportes_delete->Recordset->MoveNext();
}
$af_config_reportes_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
faf_config_reportesdelete.Init();
</script>
<?php
$af_config_reportes_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$af_config_reportes_delete->Page_Terminate();
?>