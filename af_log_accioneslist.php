<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "af_log_accionesinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php include_once "lib/libreriaBD.php" ?>
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

$af_log_acciones_list = NULL; // Initialize page object first

class caf_log_acciones_list extends caf_log_acciones {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{6DD8CE42-32CB-41B2-9566-7C52A93FF8EA}";

	// Table name
	var $TableName = 'af_log_acciones';

	// Page object name
	var $PageObjName = 'af_log_acciones_list';

	// Grid form hidden field names
	var $FormName = 'faf_log_accioneslist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Table object (af_log_acciones)
		if (!isset($GLOBALS["af_log_acciones"]) || get_class($GLOBALS["af_log_acciones"]) == "caf_log_acciones") {
			$GLOBALS["af_log_acciones"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["af_log_acciones"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "af_log_accionesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "af_log_accionesdelete.php";
		$this->MultiUpdateUrl = "af_log_accionesupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'af_log_acciones', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;

		// Update url if printer friendly for Pdf
		if ($this->PrinterFriendlyForPdf)
			$this->ExportOptions->Items["pdf"]->Body = str_replace($this->ExportPdfUrl, $this->ExportPrintUrl . "&pdf=1", $this->ExportOptions->Items["pdf"]->Body);
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();
		if ($this->Export == "print" && @$_GET["pdf"] == "1") { // Printer friendly version and with pdf=1 in URL parameters
			$pdf = new cExportPdf($GLOBALS["Table"]);
			$pdf->Text = ob_get_contents(); // Set the content as the HTML of current page (printer friendly version)
			ob_end_clean();
			$pdf->Export();
		}

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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 15;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Set up sorting order
			$this->SetUpSortOrder();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 15; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->c_ITransaccion->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->c_ITransaccion->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->c_ITransaccion, $bCtrl); // c_ITransaccion
			$this->UpdateSort($this->f_Transaccion, $bCtrl); // f_Transaccion
			$this->UpdateSort($this->c_IDestino, $bCtrl); // c_IDestino
			$this->UpdateSort($this->cl_Accion, $bCtrl); // cl_Accion
			$this->UpdateSort($this->t_Accion, $bCtrl); // t_Accion
			$this->UpdateSort($this->nv_Accion, $bCtrl); // nv_Accion
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
				$this->f_Transaccion->setSort("DESC");
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->c_ITransaccion->setSort("");
				$this->f_Transaccion->setSort("");
				$this->c_IDestino->setSort("");
				$this->cl_Accion->setSort("");
				$this->t_Accion->setSort("");
				$this->nv_Accion->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if (TRUE)
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->c_ITransaccion->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.faf_log_accioneslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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
		$this->c_ITransaccion->setDbValue($rs->fields('c_ITransaccion'));
		$this->f_Transaccion->setDbValue($rs->fields('f_Transaccion'));
		$this->c_IDestino->setDbValue($rs->fields('c_IDestino'));
		$this->cl_Accion->setDbValue($rs->fields('cl_Accion'));
		$this->t_Accion->setDbValue($rs->fields('t_Accion'));
		$this->nv_Accion->setDbValue($rs->fields('nv_Accion'));
		$this->q_Min_Destino->setDbValue($rs->fields('q_Min_Destino'));
		$this->c_IChequeo->setDbValue($rs->fields('c_IChequeo'));
		$this->c_IReseller->setDbValue($rs->fields('c_IReseller'));
		$this->c_ICClass->setDbValue($rs->fields('c_ICClass'));
		$this->c_ICliente->setDbValue($rs->fields('c_ICliente'));
		$this->c_ICuenta->setDbValue($rs->fields('c_ICuenta'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->c_ITransaccion->DbValue = $row['c_ITransaccion'];
		$this->f_Transaccion->DbValue = $row['f_Transaccion'];
		$this->c_IDestino->DbValue = $row['c_IDestino'];
		$this->cl_Accion->DbValue = $row['cl_Accion'];
		$this->t_Accion->DbValue = $row['t_Accion'];
		$this->nv_Accion->DbValue = $row['nv_Accion'];
		$this->q_Min_Destino->DbValue = $row['q_Min_Destino'];
		$this->c_IChequeo->DbValue = $row['c_IChequeo'];
		$this->c_IReseller->DbValue = $row['c_IReseller'];
		$this->c_ICClass->DbValue = $row['c_ICClass'];
		$this->c_ICliente->DbValue = $row['c_ICliente'];
		$this->c_ICuenta->DbValue = $row['c_ICuenta'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("c_ITransaccion")) <> "")
			$this->c_ITransaccion->CurrentValue = $this->getKey("c_ITransaccion"); // c_ITransaccion
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// c_ITransaccion
		// f_Transaccion
		// c_IDestino
		// cl_Accion
		// t_Accion
		// nv_Accion
		// q_Min_Destino
		// c_IChequeo
		// c_IReseller

		$this->c_IReseller->CellCssStyle = "white-space: nowrap;";

		// c_ICClass
		$this->c_ICClass->CellCssStyle = "white-space: nowrap;";

		// c_ICliente
		$this->c_ICliente->CellCssStyle = "white-space: nowrap;";

		// c_ICuenta
		$this->c_ICuenta->CellCssStyle = "white-space: nowrap;";
		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// c_ITransaccion
			$this->c_ITransaccion->ViewValue = $this->c_ITransaccion->CurrentValue;
			$this->c_ITransaccion->ViewCustomAttributes = "";

			// f_Transaccion
			$this->f_Transaccion->ViewValue = $this->f_Transaccion->CurrentValue;
			$this->f_Transaccion->ViewValue = ew_FormatDateTime($this->f_Transaccion->ViewValue, 11);
			$this->f_Transaccion->ViewCustomAttributes = "";

			// c_IDestino
			$this->c_IDestino->ViewValue = $this->c_IDestino->CurrentValue;
			$this->c_IDestino->ViewCustomAttributes = "";
			
			$result = select_sql_PO("select_destino_where", array($this->c_IDestino->CurrentValue));
			$this->c_IDestino->ViewValue = $result[1]['description'];

			// cl_Accion
			if (strval($this->cl_Accion->CurrentValue) <> "") {
				$sFilterWrk = "`rv_Low_Value`" . ew_SearchString("=", $this->cl_Accion->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `rv_Low_Value`, `rv_Meaning` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_dominios`";
			$sWhereWrk = "";
			$lookuptblfilter = "`rv_Domain` = 'DNIO_CLASE_ACCION'";
			if (strval($lookuptblfilter) <> "") {
				ew_AddFilter($sWhereWrk, $lookuptblfilter);
			}
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cl_Accion, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->cl_Accion->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->cl_Accion->ViewValue = $this->cl_Accion->CurrentValue;
				}
			} else {
				$this->cl_Accion->ViewValue = NULL;
			}
			$this->cl_Accion->ViewCustomAttributes = "";

			// t_Accion
			if (strval($this->t_Accion->CurrentValue) <> "") {
				$sFilterWrk = "`rv_Low_Value`" . ew_SearchString("=", $this->t_Accion->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `rv_Low_Value`, `rv_Meaning` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_dominios`";
			$sWhereWrk = "";

			if($this->nv_Accion->CurrentValue == 1)$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_PLAT'";
			if($this->nv_Accion->CurrentValue == 2)$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_RES'";
			if($this->nv_Accion->CurrentValue == 3)$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_CCLASS'";
			if($this->nv_Accion->CurrentValue == 4)$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_CLI'";
			if($this->nv_Accion->CurrentValue == 5)$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_CTA'";
			
			//$lookuptblfilter = "`rv_Domain` = 'DNIO_TIPO_ACCION_PLAT'";
			if (strval($lookuptblfilter) <> "") {
				ew_AddFilter($sWhereWrk, $lookuptblfilter);
			}
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->t_Accion, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->t_Accion->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->t_Accion->ViewValue = $this->t_Accion->CurrentValue;
				}
			} else {
				$this->t_Accion->ViewValue = NULL;
			}
			$this->t_Accion->ViewCustomAttributes = "";

			// nv_Accion
			if (strval($this->nv_Accion->CurrentValue) <> "") {
				$sFilterWrk = "`rv_Low_Value`" . ew_SearchString("=", $this->nv_Accion->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `rv_Low_Value`, `rv_Meaning` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `af_dominios`";
			$sWhereWrk = "";
			$lookuptblfilter = "`rv_Domain` = 'DNIO_NIVEL_ACCION'";
			if (strval($lookuptblfilter) <> "") {
				ew_AddFilter($sWhereWrk, $lookuptblfilter);
			}
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->nv_Accion, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->nv_Accion->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->nv_Accion->ViewValue = $this->nv_Accion->CurrentValue;
				}
			} else {
				$this->nv_Accion->ViewValue = NULL;
			}
			$this->nv_Accion->ViewCustomAttributes = "";

			// q_Min_Destino
			$this->q_Min_Destino->ViewValue = $this->q_Min_Destino->CurrentValue;
			$this->q_Min_Destino->ViewCustomAttributes = "";

			// c_IChequeo
			$this->c_IChequeo->ViewValue = $this->c_IChequeo->CurrentValue;
			$this->c_IChequeo->ViewCustomAttributes = "";

			// c_IReseller
			$this->c_IReseller->ViewValue = $this->c_IReseller->CurrentValue;
			$this->c_IReseller->ViewCustomAttributes = "";

			// c_ICClass
			$this->c_ICClass->ViewValue = $this->c_ICClass->CurrentValue;
			$this->c_ICClass->ViewCustomAttributes = "";

			// c_ICliente
			$this->c_ICliente->ViewValue = $this->c_ICliente->CurrentValue;
			$this->c_ICliente->ViewCustomAttributes = "";

			// c_ICuenta
			$this->c_ICuenta->ViewValue = $this->c_ICuenta->CurrentValue;
			$this->c_ICuenta->ViewCustomAttributes = "";

			// c_ITransaccion
			$this->c_ITransaccion->LinkCustomAttributes = "";
			$this->c_ITransaccion->HrefValue = "";
			$this->c_ITransaccion->TooltipValue = "";

			// f_Transaccion
			$this->f_Transaccion->LinkCustomAttributes = "";
			$this->f_Transaccion->HrefValue = "";
			$this->f_Transaccion->TooltipValue = "";

			// c_IDestino
			$this->c_IDestino->LinkCustomAttributes = "";
			$this->c_IDestino->HrefValue = "";
			$this->c_IDestino->TooltipValue = "";

			// cl_Accion
			$this->cl_Accion->LinkCustomAttributes = "";
			$this->cl_Accion->HrefValue = "";
			$this->cl_Accion->TooltipValue = "";

			// t_Accion
			$this->t_Accion->LinkCustomAttributes = "";
			$this->t_Accion->HrefValue = "";
			$this->t_Accion->TooltipValue = "";

			// nv_Accion
			$this->nv_Accion->LinkCustomAttributes = "";
			$this->nv_Accion->HrefValue = "";
			$this->nv_Accion->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = FALSE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_af_log_acciones\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_af_log_acciones',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.faf_log_accioneslist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$ExportDoc->Export();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, $this->TableVar, TRUE);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column
		$opt = &$this->ListOptions->Add("nv_AccionDet");
		$opt->Header = "Nivel Acción Detalle";
		$opt->OnLeft = FALSE; // Link on left
		$opt->MoveTo(0); // Move to first column


	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";
		if($this->nv_Accion->CurrentValue == 1)$this->ListOptions->Items["nv_AccionDet"]->Body = "";

		if($this->nv_Accion->CurrentValue == 2){
			$res = select_sql_PO('select_porta_customers_where', array((int)$this->c_IReseller->CurrentValue));
			$this->ListOptions->Items["nv_AccionDet"]->Body = $res[1]['name'];
		}
		
		if($this->nv_Accion->CurrentValue == 3){
			$res = select_sql_PO('select_porta_customers_class_where', array((int)$this->c_ICClass->CurrentValue));
			$this->ListOptions->Items["nv_AccionDet"]->Body = $res[1]['name'];
		}

		if($this->nv_Accion->CurrentValue == 4){
			$res = select_sql_PO('select_porta_customers_where_class', array((int)$this->c_ICliente->CurrentValue));
			$this->ListOptions->Items["nv_AccionDet"]->Body = $res[1]['name'];
		}

		if($this->nv_Accion->CurrentValue == 5){
			$res = select_sql_PO('select_porta_accounts_where', array((int)$this->c_ICuenta->CurrentValue, (int)$this->c_ICliente->CurrentValue));
			$this->ListOptions->Items["nv_AccionDet"]->Body = $res[1]['id'];
		}


	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($af_log_acciones_list)) $af_log_acciones_list = new caf_log_acciones_list();

// Page init
$af_log_acciones_list->Page_Init();

// Page main
$af_log_acciones_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$af_log_acciones_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($af_log_acciones->Export == "") { ?>
<script type="text/javascript">

// Page object
var af_log_acciones_list = new ew_Page("af_log_acciones_list");
af_log_acciones_list.PageID = "list"; // Page ID
var EW_PAGE_ID = af_log_acciones_list.PageID; // For backward compatibility

// Form object
var faf_log_accioneslist = new ew_Form("faf_log_accioneslist");
faf_log_accioneslist.FormKeyCountName = '<?php echo $af_log_acciones_list->FormKeyCountName ?>';

// Form_CustomValidate event
faf_log_accioneslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faf_log_accioneslist.ValidateRequired = true;
<?php } else { ?>
faf_log_accioneslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
faf_log_accioneslist.Lists["x_cl_Accion"] = {"LinkField":"x_rv_Low_Value","Ajax":null,"AutoFill":false,"DisplayFields":["x_rv_Meaning","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
faf_log_accioneslist.Lists["x_t_Accion"] = {"LinkField":"x_rv_Low_Value","Ajax":null,"AutoFill":false,"DisplayFields":["x_rv_Meaning","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
faf_log_accioneslist.Lists["x_nv_Accion"] = {"LinkField":"x_rv_Low_Value","Ajax":null,"AutoFill":false,"DisplayFields":["x_rv_Meaning","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($af_log_acciones->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($af_log_acciones_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $af_log_acciones_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$af_log_acciones_list->TotalRecs = $af_log_acciones->SelectRecordCount();
	} else {
		if ($af_log_acciones_list->Recordset = $af_log_acciones_list->LoadRecordset())
			$af_log_acciones_list->TotalRecs = $af_log_acciones_list->Recordset->RecordCount();
	}
	$af_log_acciones_list->StartRec = 1;
	if ($af_log_acciones_list->DisplayRecs <= 0 || ($af_log_acciones->Export <> "" && $af_log_acciones->ExportAll)) // Display all records
		$af_log_acciones_list->DisplayRecs = $af_log_acciones_list->TotalRecs;
	if (!($af_log_acciones->Export <> "" && $af_log_acciones->ExportAll))
		$af_log_acciones_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$af_log_acciones_list->Recordset = $af_log_acciones_list->LoadRecordset($af_log_acciones_list->StartRec-1, $af_log_acciones_list->DisplayRecs);
$af_log_acciones_list->RenderOtherOptions();
?>
<?php $af_log_acciones_list->ShowPageHeader(); ?>
<?php
$af_log_acciones_list->ShowMessage();
?>

					<?/******************************************************
					************************FILTROS**************************
					*********************************************************/?>

<script type="text/javascript">
	$(document).on('click','#submit_filtros',function(){

		var desde = $('#initialDateFil').val();
		var hasta = $('#endDateFil').val();
		var destino = $('#dest').val();
		var clase = $('#select_clase').find("option:selected").val();
		var nivel = $('#select_nivel').find("option:selected").val();

		var dataString = "pag=log_acciones&filtro=x";
		if (desde == ""){
			dataString = dataString + "&desde=vacio";
		}else{
			dataString = dataString + "&desde=" + desde;
		}

		if (hasta == ""){
			dataString = dataString + "&hasta=vacio";
		}else{
			dataString = dataString + "&hasta=" + hasta;
		}

		if (destino == ""){
			dataString = dataString + "&destino=vacio";
		}else{
			dataString = dataString + "&destino=" + destino;
		}

		if (clase == "vacio"){
			dataString = dataString + "&clase=vacio";
		}else{
			dataString = dataString + "&clase=" + clase;
		}

		if (nivel == "vacio"){
			dataString = dataString + "&nivel=vacio";
		}else{
			dataString = dataString + "&nivel=" + nivel;
		}

		alert(dataString);
		$.ajax({  
		  type: "POST",  
		  url: "lib/functions.php",  
		  data: dataString,  
		  success: function(html) {  
			alert("html");location.reload();
		  }
		});

	});

</script>


<div class="col-sm-4">
  		<h3>Filtros</h3>
  		<div class="filtros form">
				<div class="form-group">
					<label for="initialDateFil">Desde</label>
					<input type="date" class="form-control" id="initialDateFil" placeholder="01/01/2014" value="vacio">
				</div>
				<div class="form-group">
					<label for="endDateFil">Hasta</label>
					<input type="date" class="form-control" id="endDateFil" placeholder="02/01/2014" value="vacio">
				</div>

				<div class="form-group">
					<label class= "filtro_label">Filtro Destino</label>
					<input type="text" name="dest" id="dest" class="form-control">
				</div>

				<div class="form-group">
					<label class= "filtro_label">Filtro Clase Acción</label>
					<select id= "select_clase" class= "form-control">
					<option value = 'vacio'>Todo</option>
					<? $dom_accion = select_sql('select_dominio', 'DNIO_CLASE_ACCION');
						$count = count($dom_accion);
						$k = 1;
						while ($k <= $count){
							echo "<option value= ".$dom_accion[$k]['rv_Low_Value']. ">". $dom_accion[$k]['rv_Meaning'] ."</option>";
							$k++;
						}

					?>
					</select>
				</div>

				<div class="form-group">
					<label class= "filtro_label">Filtro Nivel Acción</label>
					<select id= "select_nivel" class= "form-control">
					<option value = 'vacio'>Todo</option>
					<? $dom_accion = select_sql('select_dominio', 'DNIO_NIVEL_ACCION');
						$count = count($dom_accion);
						$k = 1;
						while ($k <= $count){
							echo "<option value= ".$dom_accion[$k]['rv_Low_Value']. ">". $dom_accion[$k]['rv_Meaning'] ."</option>";
							$k++;
						}

					?>
					</select>
				</div>


  			  <button type="submit" id ="submit_filtros" class="btn btn-primary">Filtrar</button>
  			
  		</div>
  	</div>
<?$_SESSION['filtros_log']['desde']=""; $_SESSION['filtros_log']['hasta']=""; 
  $_SESSION['filtros_log']['clase']=""; $_SESSION['filtros_log']['destino']=""; $_SESSION['filtros_log']['nivel']="";
?>


<table class="ewGrid"><tr><td class="ewGridContent">
<form name="faf_log_accioneslist" id="faf_log_accioneslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="af_log_acciones">
<div id="gmp_af_log_acciones" class="ewGridMiddlePanel">
<?php if ($af_log_acciones_list->TotalRecs > 0) { ?>
<table id="tbl_af_log_accioneslist" class="ewTable ewTableSeparate">
<?php echo $af_log_acciones->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$af_log_acciones_list->RenderListOptions();

// Render list options (header, left)
$af_log_acciones_list->ListOptions->Render("header", "left");
?>
<?php if ($af_log_acciones->c_ITransaccion->Visible) { // c_ITransaccion ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->c_ITransaccion) == "") { ?>
		<td><div id="elh_af_log_acciones_c_ITransaccion" class="af_log_acciones_c_ITransaccion"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->c_ITransaccion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->c_ITransaccion) ?>',2);"><div id="elh_af_log_acciones_c_ITransaccion" class="af_log_acciones_c_ITransaccion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->c_ITransaccion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->c_ITransaccion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->c_ITransaccion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($af_log_acciones->f_Transaccion->Visible) { // f_Transaccion ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->f_Transaccion) == "") { ?>
		<td><div id="elh_af_log_acciones_f_Transaccion" class="af_log_acciones_f_Transaccion"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->f_Transaccion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->f_Transaccion) ?>',2);"><div id="elh_af_log_acciones_f_Transaccion" class="af_log_acciones_f_Transaccion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->f_Transaccion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->f_Transaccion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->f_Transaccion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($af_log_acciones->c_IDestino->Visible) { // c_IDestino ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->c_IDestino) == "") { ?>
		<td><div id="elh_af_log_acciones_c_IDestino" class="af_log_acciones_c_IDestino"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->c_IDestino->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->c_IDestino) ?>',2);"><div id="elh_af_log_acciones_c_IDestino" class="af_log_acciones_c_IDestino">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->c_IDestino->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->c_IDestino->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->c_IDestino->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($af_log_acciones->cl_Accion->Visible) { // cl_Accion ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->cl_Accion) == "") { ?>
		<td><div id="elh_af_log_acciones_cl_Accion" class="af_log_acciones_cl_Accion"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->cl_Accion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->cl_Accion) ?>',2);"><div id="elh_af_log_acciones_cl_Accion" class="af_log_acciones_cl_Accion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->cl_Accion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->cl_Accion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->cl_Accion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($af_log_acciones->t_Accion->Visible) { // t_Accion ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->t_Accion) == "") { ?>
		<td><div id="elh_af_log_acciones_t_Accion" class="af_log_acciones_t_Accion"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->t_Accion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->t_Accion) ?>',2);"><div id="elh_af_log_acciones_t_Accion" class="af_log_acciones_t_Accion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->t_Accion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->t_Accion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->t_Accion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($af_log_acciones->nv_Accion->Visible) { // nv_Accion ?>
	<?php if ($af_log_acciones->SortUrl($af_log_acciones->nv_Accion) == "") { ?>
		<td><div id="elh_af_log_acciones_nv_Accion" class="af_log_acciones_nv_Accion"><div class="ewTableHeaderCaption"><?php echo $af_log_acciones->nv_Accion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $af_log_acciones->SortUrl($af_log_acciones->nv_Accion) ?>',2);"><div id="elh_af_log_acciones_nv_Accion" class="af_log_acciones_nv_Accion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $af_log_acciones->nv_Accion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($af_log_acciones->nv_Accion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($af_log_acciones->nv_Accion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$af_log_acciones_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($af_log_acciones->ExportAll && $af_log_acciones->Export <> "") {
	$af_log_acciones_list->StopRec = $af_log_acciones_list->TotalRecs;
} else {

	// Set the last record to display
	if ($af_log_acciones_list->TotalRecs > $af_log_acciones_list->StartRec + $af_log_acciones_list->DisplayRecs - 1)
		$af_log_acciones_list->StopRec = $af_log_acciones_list->StartRec + $af_log_acciones_list->DisplayRecs - 1;
	else
		$af_log_acciones_list->StopRec = $af_log_acciones_list->TotalRecs;
}
$af_log_acciones_list->RecCnt = $af_log_acciones_list->StartRec - 1;
if ($af_log_acciones_list->Recordset && !$af_log_acciones_list->Recordset->EOF) {
	$af_log_acciones_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $af_log_acciones_list->StartRec > 1)
		$af_log_acciones_list->Recordset->Move($af_log_acciones_list->StartRec - 1);
} elseif (!$af_log_acciones->AllowAddDeleteRow && $af_log_acciones_list->StopRec == 0) {
	$af_log_acciones_list->StopRec = $af_log_acciones->GridAddRowCount;
}

// Initialize aggregate
$af_log_acciones->RowType = EW_ROWTYPE_AGGREGATEINIT;
$af_log_acciones->ResetAttrs();
$af_log_acciones_list->RenderRow();
while ($af_log_acciones_list->RecCnt < $af_log_acciones_list->StopRec) {
	$af_log_acciones_list->RecCnt++;
	if (intval($af_log_acciones_list->RecCnt) >= intval($af_log_acciones_list->StartRec)) {
		$af_log_acciones_list->RowCnt++;

		// Set up key count
		$af_log_acciones_list->KeyCount = $af_log_acciones_list->RowIndex;

		// Init row class and style
		$af_log_acciones->ResetAttrs();
		$af_log_acciones->CssClass = "";
		if ($af_log_acciones->CurrentAction == "gridadd") {
		} else {
			$af_log_acciones_list->LoadRowValues($af_log_acciones_list->Recordset); // Load row values
		}
		$af_log_acciones->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$af_log_acciones->RowAttrs = array_merge($af_log_acciones->RowAttrs, array('data-rowindex'=>$af_log_acciones_list->RowCnt, 'id'=>'r' . $af_log_acciones_list->RowCnt . '_af_log_acciones', 'data-rowtype'=>$af_log_acciones->RowType));

		// Render row
		$af_log_acciones_list->RenderRow();

		// Render list options
		$af_log_acciones_list->RenderListOptions();
?>
	<tr<?php echo $af_log_acciones->RowAttributes() ?>>
<?php

// Render list options (body, left)
$af_log_acciones_list->ListOptions->Render("body", "left", $af_log_acciones_list->RowCnt);
?>
	<?php if ($af_log_acciones->c_ITransaccion->Visible) { // c_ITransaccion ?>
		<td<?php echo $af_log_acciones->c_ITransaccion->CellAttributes() ?>>
<span<?php echo $af_log_acciones->c_ITransaccion->ViewAttributes() ?>>
<?php echo $af_log_acciones->c_ITransaccion->ListViewValue() ?></span>
<a id="<?php echo $af_log_acciones_list->PageObjName . "_row_" . $af_log_acciones_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($af_log_acciones->f_Transaccion->Visible) { // f_Transaccion ?>
		<td<?php echo $af_log_acciones->f_Transaccion->CellAttributes() ?>>
<span<?php echo $af_log_acciones->f_Transaccion->ViewAttributes() ?>>
<?php echo $af_log_acciones->f_Transaccion->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($af_log_acciones->c_IDestino->Visible) { // c_IDestino ?>
		<td<?php echo $af_log_acciones->c_IDestino->CellAttributes() ?>>
<span<?php echo $af_log_acciones->c_IDestino->ViewAttributes() ?>>
<?php echo $af_log_acciones->c_IDestino->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($af_log_acciones->cl_Accion->Visible) { // cl_Accion ?>
		<td<?php echo $af_log_acciones->cl_Accion->CellAttributes() ?>>
<span<?php echo $af_log_acciones->cl_Accion->ViewAttributes() ?>>
<?php echo $af_log_acciones->cl_Accion->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($af_log_acciones->t_Accion->Visible) { // t_Accion ?>
		<td<?php echo $af_log_acciones->t_Accion->CellAttributes() ?>>
<span<?php echo $af_log_acciones->t_Accion->ViewAttributes() ?>>
<?php echo $af_log_acciones->t_Accion->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($af_log_acciones->nv_Accion->Visible) { // nv_Accion ?>
		<td<?php echo $af_log_acciones->nv_Accion->CellAttributes() ?>>
<span<?php echo $af_log_acciones->nv_Accion->ViewAttributes() ?>>
<?php echo $af_log_acciones->nv_Accion->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$af_log_acciones_list->ListOptions->Render("body", "right", $af_log_acciones_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($af_log_acciones->CurrentAction <> "gridadd")
		$af_log_acciones_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($af_log_acciones->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($af_log_acciones_list->Recordset)
	$af_log_acciones_list->Recordset->Close();
?>
<?php if ($af_log_acciones->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($af_log_acciones->CurrentAction <> "gridadd" && $af_log_acciones->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($af_log_acciones_list->Pager)) $af_log_acciones_list->Pager = new cNumericPager($af_log_acciones_list->StartRec, $af_log_acciones_list->DisplayRecs, $af_log_acciones_list->TotalRecs, $af_log_acciones_list->RecRange) ?>
<?php if ($af_log_acciones_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($af_log_acciones_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $af_log_acciones_list->PageUrl() ?>start=<?php echo $af_log_acciones_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($af_log_acciones_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $af_log_acciones_list->PageUrl() ?>start=<?php echo $af_log_acciones_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($af_log_acciones_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $af_log_acciones_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($af_log_acciones_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $af_log_acciones_list->PageUrl() ?>start=<?php echo $af_log_acciones_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($af_log_acciones_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $af_log_acciones_list->PageUrl() ?>start=<?php echo $af_log_acciones_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($af_log_acciones_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $af_log_acciones_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $af_log_acciones_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $af_log_acciones_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($af_log_acciones_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($af_log_acciones_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<?php if ($af_log_acciones->Export == "") { ?>
<script type="text/javascript">
faf_log_accioneslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$af_log_acciones_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($af_log_acciones->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$af_log_acciones_list->Page_Terminate();
?>
