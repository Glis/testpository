<?php
if (session_id() == "") {session_set_cookie_params(0); session_start();} // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "af_umb_resellersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php include_once "lib/libreriaBD_portaone.php" ?>
<?php

if(!isset($_SESSION['USUARIO']))
{
    header("Location: login.php");
    exit;
}


//
// Page class
//

$af_umb_resellers_edit = NULL; // Initialize page object first

class caf_umb_resellers_edit extends caf_umb_resellers {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{6DD8CE42-32CB-41B2-9566-7C52A93FF8EA}";

	// Table name
	var $TableName = 'af_umb_resellers';

	// Page object name
	var $PageObjName = 'af_umb_resellers_edit';

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

		// Table object (af_umb_resellers)
		if (!isset($GLOBALS["af_umb_resellers"]) || get_class($GLOBALS["af_umb_resellers"]) == "caf_umb_resellers") {
			$GLOBALS["af_umb_resellers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["af_umb_resellers"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'af_umb_resellers', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $HashValue; // Hash Value

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["c_IDestino"] <> "") {
			$this->c_IDestino->setQueryStringValue($_GET["c_IDestino"]);
		}
		if (@$_GET["c_IReseller"] <> "") {
			$this->c_IReseller->setQueryStringValue($_GET["c_IReseller"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values

			// Overwrite record, reload hash value
			if ($this->CurrentAction == "overwrite") {
				$this->LoadRowHash();
				$this->CurrentAction = "U";
			}
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->c_IDestino->CurrentValue == "")
			$this->Page_Terminate("af_umb_resellerslist.php"); // Invalid key, return to list
		if ($this->c_IReseller->CurrentValue == "")
			$this->Page_Terminate("af_umb_resellerslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("af_umb_resellerslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->c_IDestino->FldIsDetailKey) {
			$this->c_IDestino->setFormValue($objForm->GetValue("x_c_IDestino"));
		}
		if (!$this->c_IReseller->FldIsDetailKey) {
			$this->c_IReseller->setFormValue($objForm->GetValue("x_c_IReseller"));
		}
		if (!$this->q_MinAl_Res->FldIsDetailKey) {
			$this->q_MinAl_Res->setFormValue($objForm->GetValue("x_q_MinAl_Res"));
		}
		if (!$this->q_MinCu_Res->FldIsDetailKey) {
			$this->q_MinCu_Res->setFormValue($objForm->GetValue("x_q_MinCu_Res"));
		}
		if (!$this->f_Ult_Mod->FldIsDetailKey) {
			$this->f_Ult_Mod->setFormValue($objForm->GetValue("x_f_Ult_Mod"));
			$this->f_Ult_Mod->CurrentValue = ew_UnFormatDateTime($this->f_Ult_Mod->CurrentValue, 7);
		}
		if (!$this->c_Usuario_Ult_Mod->FldIsDetailKey) {
			$this->c_Usuario_Ult_Mod->setFormValue($objForm->GetValue("x_c_Usuario_Ult_Mod"));
		}
		if ($this->CurrentAction <> "overwrite")
			$this->HashValue = $objForm->GetValue("k_hash");
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->c_IDestino->CurrentValue = $this->c_IDestino->FormValue;
		$this->c_IReseller->CurrentValue = $this->c_IReseller->FormValue;
		$this->q_MinAl_Res->CurrentValue = $this->q_MinAl_Res->FormValue;
		$this->q_MinCu_Res->CurrentValue = $this->q_MinCu_Res->FormValue;
		$this->f_Ult_Mod->CurrentValue = $this->f_Ult_Mod->FormValue;
		$this->f_Ult_Mod->CurrentValue = ew_UnFormatDateTime($this->f_Ult_Mod->CurrentValue, 7);
		$this->c_Usuario_Ult_Mod->CurrentValue = $this->c_Usuario_Ult_Mod->FormValue;
		if ($this->CurrentAction <> "overwrite")
			$this->HashValue = $objForm->GetValue("k_hash");
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
			if (!$this->EventCancelled)
				$this->HashValue = $this->GetRowHash($rs); // Get hash value for record
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
		$this->c_IDestino->setDbValue($rs->fields('c_IDestino'));
		$this->c_IReseller->setDbValue($rs->fields('c_IReseller'));
		$this->q_MinAl_Res->setDbValue($rs->fields('q_MinAl_Res'));
		$this->q_MinCu_Res->setDbValue($rs->fields('q_MinCu_Res'));
		$this->f_Ult_Mod->setDbValue($rs->fields('f_Ult_Mod'));
		$this->c_Usuario_Ult_Mod->setDbValue($rs->fields('c_Usuario_Ult_Mod'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->c_IDestino->DbValue = $row['c_IDestino'];
		$this->c_IReseller->DbValue = $row['c_IReseller'];
		$this->q_MinAl_Res->DbValue = $row['q_MinAl_Res'];
		$this->q_MinCu_Res->DbValue = $row['q_MinCu_Res'];
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
		// c_IDestino
		// c_IReseller
		// q_MinAl_Res
		// q_MinCu_Res
		// f_Ult_Mod
		// c_Usuario_Ult_Mod

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// c_IDestino
			if (strval($this->c_IDestino->CurrentValue) <> "") {
				$sFilterWrk = "`c_Usuario`" . ew_SearchString("=", $this->c_IDestino->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `c_Usuario`, `c_Usuario` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_usuarios`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->c_IDestino, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->c_IDestino->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
					$result = select_sql_PO("select_destino_where", array($this->c_IDestino->CurrentValue));
					$this->c_IDestino->EditValue = $result[1]['destination'];
				} else {
					$this->c_IDestino->ViewValue = $this->c_IDestino->CurrentValue;
					$result = select_sql_PO("select_destino_where", array($this->c_IDestino->CurrentValue));
					$this->c_IDestino->EditValue = $result[1]['destination'];
				}
			} else {
				$this->c_IDestino->ViewValue = NULL;
			}
			$this->c_IDestino->ViewCustomAttributes = "";

			// c_IReseller
			if (strval($this->c_IReseller->CurrentValue) <> "") {
				$sFilterWrk = "`c_Usuario`" . ew_SearchString("=", $this->c_IReseller->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `c_Usuario`, `c_Usuario` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_usuarios`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->c_IReseller, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->c_IReseller->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
					$result = select_sql_PO("select_porta_customers_where", array($this->c_IReseller->CurrentValue));
					$this->c_IReseller->EditValue = $result[1]['name'];
				} else {
					$this->c_IReseller->ViewValue = $this->c_IReseller->CurrentValue;
					$result = select_sql_PO("select_porta_customers_where", array($this->c_IReseller->CurrentValue));
					$this->c_IReseller->EditValue = $result[1]['name'];
				}
			} else {
				$this->c_IReseller->ViewValue = NULL;
			}
			$this->c_IReseller->ViewCustomAttributes = "";

			// q_MinAl_Res
			$this->q_MinAl_Res->ViewValue = $this->q_MinAl_Res->CurrentValue;
			$this->q_MinAl_Res->ViewCustomAttributes = "";

			// q_MinCu_Res
			$this->q_MinCu_Res->ViewValue = $this->q_MinCu_Res->CurrentValue;
			$this->q_MinCu_Res->ViewCustomAttributes = "";

			// f_Ult_Mod
			$this->f_Ult_Mod->ViewValue = $this->f_Ult_Mod->CurrentValue;
			$this->f_Ult_Mod->ViewValue = ew_FormatDateTime($this->f_Ult_Mod->ViewValue, 7);
			$this->f_Ult_Mod->ViewCustomAttributes = "";

			// c_Usuario_Ult_Mod
			$this->c_Usuario_Ult_Mod->ViewValue = $this->c_Usuario_Ult_Mod->CurrentValue;
			$this->c_Usuario_Ult_Mod->ViewCustomAttributes = "";

			// c_IDestino
			$this->c_IDestino->LinkCustomAttributes = "";
			$this->c_IDestino->HrefValue = "";
			$this->c_IDestino->TooltipValue = "";

			// c_IReseller
			$this->c_IReseller->LinkCustomAttributes = "";
			$this->c_IReseller->HrefValue = "";
			$this->c_IReseller->TooltipValue = "";

			// q_MinAl_Res
			$this->q_MinAl_Res->LinkCustomAttributes = "";
			$this->q_MinAl_Res->HrefValue = "";
			$this->q_MinAl_Res->TooltipValue = "";

			// q_MinCu_Res
			$this->q_MinCu_Res->LinkCustomAttributes = "";
			$this->q_MinCu_Res->HrefValue = "";
			$this->q_MinCu_Res->TooltipValue = "";

			// f_Ult_Mod
			$this->f_Ult_Mod->LinkCustomAttributes = "";
			$this->f_Ult_Mod->HrefValue = "";
			$this->f_Ult_Mod->TooltipValue = "";

			// c_Usuario_Ult_Mod
			$this->c_Usuario_Ult_Mod->LinkCustomAttributes = "";
			$this->c_Usuario_Ult_Mod->HrefValue = "";
			$this->c_Usuario_Ult_Mod->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// c_IDestino
			$this->c_IDestino->EditCustomAttributes = "";
			if (strval($this->c_IDestino->CurrentValue) <> "") {
				$sFilterWrk = "`c_Usuario`" . ew_SearchString("=", $this->c_IDestino->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `c_Usuario`, `c_Usuario` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_usuarios`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->c_IDestino, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->c_IDestino->EditValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->c_IDestino->EditValue = $this->c_IDestino->CurrentValue;
				}
			} else {
				$this->c_IDestino->EditValue = NULL;
			}
			$this->c_IDestino->ViewCustomAttributes = "";

			// c_IReseller
			$this->c_IReseller->EditCustomAttributes = "";
			if (strval($this->c_IReseller->CurrentValue) <> "") {
				$sFilterWrk = "`c_Usuario`" . ew_SearchString("=", $this->c_IReseller->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `c_Usuario`, `c_Usuario` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_usuarios`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->c_IReseller, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->c_IReseller->EditValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->c_IReseller->EditValue = $this->c_IReseller->CurrentValue;
				}
			} else {
				$this->c_IReseller->EditValue = NULL;
			}
			$this->c_IReseller->ViewCustomAttributes = "";

			// q_MinAl_Res
			$this->q_MinAl_Res->EditCustomAttributes = "";
			$this->q_MinAl_Res->EditValue = ew_HtmlEncode($this->q_MinAl_Res->CurrentValue);
			$this->q_MinAl_Res->PlaceHolder = ew_RemoveHtml($this->q_MinAl_Res->FldCaption());

			// q_MinCu_Res
			$this->q_MinCu_Res->EditCustomAttributes = "";
			$this->q_MinCu_Res->EditValue = ew_HtmlEncode($this->q_MinCu_Res->CurrentValue);
			$this->q_MinCu_Res->PlaceHolder = ew_RemoveHtml($this->q_MinCu_Res->FldCaption());

			// f_Ult_Mod
			// c_Usuario_Ult_Mod
			// Edit refer script
			// c_IDestino

			$this->c_IDestino->HrefValue = "";

			// c_IReseller
			$this->c_IReseller->HrefValue = "";

			// q_MinAl_Res
			$this->q_MinAl_Res->HrefValue = "";

			// q_MinCu_Res
			$this->q_MinCu_Res->HrefValue = "";

			// f_Ult_Mod
			$this->f_Ult_Mod->HrefValue = "";

			// c_Usuario_Ult_Mod
			$this->c_Usuario_Ult_Mod->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->c_IDestino->FldIsDetailKey && !is_null($this->c_IDestino->FormValue) && $this->c_IDestino->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->c_IDestino->FldCaption());
		}
		if (!$this->c_IReseller->FldIsDetailKey && !is_null($this->c_IReseller->FormValue) && $this->c_IReseller->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->c_IReseller->FldCaption());
		}
		if (!$this->q_MinAl_Res->FldIsDetailKey && !is_null($this->q_MinAl_Res->FormValue) && $this->q_MinAl_Res->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->q_MinAl_Res->FldCaption());
		}
		if (!ew_CheckInteger($this->q_MinAl_Res->FormValue)) {
			ew_AddMessage($gsFormError, $this->q_MinAl_Res->FldErrMsg());
		}
		if (!$this->q_MinCu_Res->FldIsDetailKey && !is_null($this->q_MinCu_Res->FormValue) && $this->q_MinCu_Res->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->q_MinCu_Res->FldCaption());
		}
		if (!ew_CheckInteger($this->q_MinCu_Res->FormValue)) {
			ew_AddMessage($gsFormError, $this->q_MinCu_Res->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// c_IDestino
			// c_IReseller
			// q_MinAl_Res

			$this->q_MinAl_Res->SetDbValueDef($rsnew, $this->q_MinAl_Res->CurrentValue, 0, $this->q_MinAl_Res->ReadOnly);

			// q_MinCu_Res
			$this->q_MinCu_Res->SetDbValueDef($rsnew, $this->q_MinCu_Res->CurrentValue, 0, $this->q_MinCu_Res->ReadOnly);

			// Check hash value
			$bRowHasConflict = ($this->GetRowHash($rs) <> $this->HashValue);

			// Call Row Update Conflict event
			if ($bRowHasConflict)
				$bRowHasConflict = $this->Row_UpdateConflict($rsold, $rsnew);
			if ($bRowHasConflict) {
				$this->setFailureMessage($Language->Phrase("RecordChangedByOtherUser"));
				$this->UpdateConflict = "U";
				$rs->Close();
				return FALSE; // Update Failed
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Load row hash
	function LoadRowHash() {
		global $conn;
		$sFilter = $this->KeyFilter();

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$RsRow = $conn->Execute($sSql);
		$this->HashValue = ($RsRow && !$RsRow->EOF) ? $this->GetRowHash($RsRow) : ""; // Get hash value for record
		$RsRow->Close();
	}

	// Get Row Hash
	function GetRowHash(&$rs) {
		if (!$rs)
			return "";
		$sHash = "";
		$sHash .= ew_GetFldHash($rs->fields('c_IDestino')); // c_IDestino
		$sHash .= ew_GetFldHash($rs->fields('c_IReseller')); // c_IReseller
		$sHash .= ew_GetFldHash($rs->fields('q_MinAl_Res')); // q_MinAl_Res
		$sHash .= ew_GetFldHash($rs->fields('q_MinCu_Res')); // q_MinCu_Res
		return md5($sHash);
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "af_umb_resellerslist.php", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, ew_CurrentUrl());
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($af_umb_resellers_edit)) $af_umb_resellers_edit = new caf_umb_resellers_edit();

// Page init
$af_umb_resellers_edit->Page_Init();

// Page main
$af_umb_resellers_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$af_umb_resellers_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var af_umb_resellers_edit = new ew_Page("af_umb_resellers_edit");
af_umb_resellers_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = af_umb_resellers_edit.PageID; // For backward compatibility

// Form object
var faf_umb_resellersedit = new ew_Form("faf_umb_resellersedit");

// Validate form
faf_umb_resellersedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_c_IDestino");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($af_umb_resellers->c_IDestino->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_c_IReseller");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($af_umb_resellers->c_IReseller->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_q_MinAl_Res");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($af_umb_resellers->q_MinAl_Res->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_q_MinAl_Res");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($af_umb_resellers->q_MinAl_Res->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_q_MinCu_Res");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($af_umb_resellers->q_MinCu_Res->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_q_MinCu_Res");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($af_umb_resellers->q_MinCu_Res->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
faf_umb_resellersedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faf_umb_resellersedit.ValidateRequired = true;
<?php } else { ?>
faf_umb_resellersedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
faf_umb_resellersedit.Lists["x_c_IDestino"] = {"LinkField":"x_c_Usuario","Ajax":null,"AutoFill":false,"DisplayFields":["x_c_Usuario","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
faf_umb_resellersedit.Lists["x_c_IReseller"] = {"LinkField":"x_c_Usuario","Ajax":null,"AutoFill":false,"DisplayFields":["x_c_Usuario","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $af_umb_resellers_edit->ShowPageHeader(); ?>
<?php
$af_umb_resellers_edit->ShowMessage();
?>
<form name="faf_umb_resellersedit" id="faf_umb_resellersedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="af_umb_resellers">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<input type="hidden" name="k_hash" id="k_hash" value="<?php echo $af_umb_resellers_edit->HashValue ?>">
<table class="ewGrid"><tr><td>
<table id="tbl_af_umb_resellersedit" class="table table-bordered table-striped">
<?php if ($af_umb_resellers->c_IDestino->Visible) { // c_IDestino ?>
	<tr id="r_c_IDestino">
		<td><span id="elh_af_umb_resellers_c_IDestino"><?php echo $af_umb_resellers->c_IDestino->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $af_umb_resellers->c_IDestino->CellAttributes() ?>>
<span id="el_af_umb_resellers_c_IDestino" class="control-group">
<span<?php echo $af_umb_resellers->c_IDestino->ViewAttributes() ?>>
<?php echo $af_umb_resellers->c_IDestino->EditValue ?></span>
</span>
<input type="hidden" data-field="x_c_IDestino" name="x_c_IDestino" id="x_c_IDestino" value="<?php echo ew_HtmlEncode($af_umb_resellers->c_IDestino->CurrentValue) ?>">
<?php echo $af_umb_resellers->c_IDestino->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($af_umb_resellers->c_IReseller->Visible) { // c_IReseller ?>
	<tr id="r_c_IReseller">
		<td><span id="elh_af_umb_resellers_c_IReseller"><?php echo $af_umb_resellers->c_IReseller->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $af_umb_resellers->c_IReseller->CellAttributes() ?>>
<span id="el_af_umb_resellers_c_IReseller" class="control-group">
<span<?php echo $af_umb_resellers->c_IReseller->ViewAttributes() ?>>
<?php echo $af_umb_resellers->c_IReseller->EditValue ?></span>
</span>
<input type="hidden" data-field="x_c_IReseller" name="x_c_IReseller" id="x_c_IReseller" value="<?php echo ew_HtmlEncode($af_umb_resellers->c_IReseller->CurrentValue) ?>">
<?php echo $af_umb_resellers->c_IReseller->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($af_umb_resellers->q_MinAl_Res->Visible) { // q_MinAl_Res ?>
	<tr id="r_q_MinAl_Res">
		<td><span id="elh_af_umb_resellers_q_MinAl_Res"><?php echo $af_umb_resellers->q_MinAl_Res->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $af_umb_resellers->q_MinAl_Res->CellAttributes() ?>>
<span id="el_af_umb_resellers_q_MinAl_Res" class="control-group">
<input class="form-control" type="number" min="0" data-field="x_q_MinAl_Res" name="x_q_MinAl_Res" id="x_q_MinAl_Res" size="30" placeholder="<?php echo ew_HtmlEncode($af_umb_resellers->q_MinAl_Res->PlaceHolder) ?>" value="<?php echo $af_umb_resellers->q_MinAl_Res->EditValue ?>"<?php echo $af_umb_resellers->q_MinAl_Res->EditAttributes() ?>>
</span>
<?php echo $af_umb_resellers->q_MinAl_Res->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($af_umb_resellers->q_MinCu_Res->Visible) { // q_MinCu_Res ?>
	<tr id="r_q_MinCu_Res">
		<td><span id="elh_af_umb_resellers_q_MinCu_Res"><?php echo $af_umb_resellers->q_MinCu_Res->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $af_umb_resellers->q_MinCu_Res->CellAttributes() ?>>
<span id="el_af_umb_resellers_q_MinCu_Res" class="control-group">
<input class="form-control" type="number" min="0" data-field="x_q_MinCu_Res" name="x_q_MinCu_Res" id="x_q_MinCu_Res" size="30" placeholder="<?php echo ew_HtmlEncode($af_umb_resellers->q_MinCu_Res->PlaceHolder) ?>" value="<?php echo $af_umb_resellers->q_MinCu_Res->EditValue ?>"<?php echo $af_umb_resellers->q_MinCu_Res->EditAttributes() ?>>
</span>
<?php echo $af_umb_resellers->q_MinCu_Res->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<?php if ($af_umb_resellers->UpdateConflict == "U") { // Record already updated by other user ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit" onclick="this.form.a_edit.value='overwrite';"><?php echo $Language->Phrase("OverwriteBtn") ?></button>
<button class="btn ewButton" name="btnReload" id="btnReload" type="submit" onclick="this.form.a_edit.value='I';"><?php echo $Language->Phrase("ReloadBtn") ?></button>
<?php } else { ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
<?php } ?>
</form>
<script type="text/javascript">
faf_umb_resellersedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$af_umb_resellers_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$af_umb_resellers_edit->Page_Terminate();
?>
