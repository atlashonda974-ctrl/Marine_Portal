<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Create connection to Oracle
require_once 'config.php';
if (!$conn) {
   $e = oci_error();
   trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
   exit;
}
else {
 //echo "Connected to Oracle"; WHERE GDH_DOC_REFERENCE_NO = '$mop'
 $mop = $_GET['mop'];

			
				
						$query ="
						SELECT
							uw_docheader.PLC_LOC_CODE, uw_docheader.PLC_LOCADESC, uw_docheader.PBC_BUSICLASS_CODE, PBC_DESC,
							uw_docheader.PDP_DEPT_CODE, uw_docheader.PDT_DOCTYPE, uw_docheader.GDH_DOC_REFERENCE_NO, uw_docheader.GDH_DOCUMENTNO,
							uw_docheader.PPS_PARTY_CODE, uw_docheader.PPS_DESC, uw_docheader.GDH_YEAR,
							uw_docheader.GDH_ISSUEDATE, uw_docheader.GDH_COMMDATE, uw_docheader.GDH_EXPIRYDATE, uw_docheader.GDH_GROSSPREMIUM,
							uw_docheader.GDH_NETPREMIUM, uw_docheader.GDH_TOTALSI, uw_docheader.PAS_ADDRESS1, uw_docheader.PIY_INSUTYPE,
							UW_GNDTL.PII_CODE, UW_GNDTL.GGD_SINGLESHIPLIMIT, UW_GNDTL.GGD_DOUBLESHIPLIMIT,
							UW_AGTDTL.PPS_PARTY_CODE
							FROM AILMIS.uw_docheader
							
							Left Join AILMIS.UW_GNDTL
							ON  uw_docheader.GDH_DOC_REFERENCE_NO = UW_GNDTL.GGD_DOC_REFERENCE_NO
							AND uw_docheader.GDH_RECORD_TYPE = UW_GNDTL.GDH_RECORD_TYPE
							
							Left JOIN AILMIS.UW_AGTDTL
							ON uw_docheader.PLC_LOC_CODE = UW_AGTDTL.PLC_LOC_CODE
							AND  uw_docheader.PDT_DOCTYPE = UW_AGTDTL.PDT_DOCTYPE 
							AND  uw_docheader.GDH_DOCUMENTNO = UW_AGTDTL.GDH_DOCUMENTNO
							AND  uw_docheader.GDH_YEAR = UW_AGTDTL.GDH_YEAR 
							AND  uw_docheader.PDP_DEPT_CODE = UW_AGTDTL.PDP_DEPT_CODE 
							AND uw_docheader.GDH_RECORD_TYPE = UW_AGTDTL.GDH_RECORD_TYPE
							
						WHERE uw_docheader.GDH_DOC_REFERENCE_NO = '$mop'
						ORDER BY GDH_ISSUEDATE
					";
					
						
						
						
					
				
					$combo = oci_parse($conn, $query);
					
												//uw_docheader
                                                if (!$combo) {
                                                                $e1 = oci_error($conn);
                                                                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                                                }
                                                $r1 = oci_execute($combo);
                                                if (!$r1) {
                                                                $e1 = oci_error($combo);
                                                                trigger_error(htmlentities($e1['message'], ENT_QUOTES), E_USER_ERROR);
                                                }
												
												
												$count = 0;
												$posts_arr = array();
                                                  while($res = oci_fetch_array($combo, OCI_ASSOC+OCI_RETURN_NULLS)) {

														
														 $post_item = array(
																'STATUS' => "Exist",	
																'PLC_LOC_CODE' => ($res['PLC_LOC_CODE'] !== null ? htmlentities($res['PLC_LOC_CODE'], ENT_QUOTES) : "&nbsp;"),
																'PLC_LOCADESC' => ($res['PLC_LOCADESC'] !== null ? htmlentities($res['PLC_LOCADESC'], ENT_QUOTES) : "&nbsp;"),
																'PBC_BUSICLASS_CODE' => ($res['PBC_BUSICLASS_CODE'] !== null ? htmlentities($res['PBC_BUSICLASS_CODE'], ENT_QUOTES) : "&nbsp;"),
																'PBC_DESC' => ($res['PBC_DESC'] !== null ? htmlentities($res['PBC_DESC'], ENT_QUOTES) : "&nbsp;"),
																
																'GDH_YEAR' => ($res['GDH_YEAR'] !== null ? htmlentities($res['GDH_YEAR'], ENT_QUOTES) : "&nbsp;"),																
																'PDP_DEPT_CODE' => ($res['PDP_DEPT_CODE'] !== null ? htmlentities($res['PDP_DEPT_CODE'], ENT_QUOTES) : "&nbsp;"),																
																'PDT_DOCTYPE' => ($res['PDT_DOCTYPE'] !== null ? htmlentities($res['PDT_DOCTYPE'], ENT_QUOTES) : "&nbsp;"),
																'GDH_DOCUMENTNO' => ($res['GDH_DOCUMENTNO'] !== null ? htmlentities($res['GDH_DOCUMENTNO'], ENT_QUOTES) : "&nbsp;"),
																'GDH_DOC_REFERENCE_NO' => ($res['GDH_DOC_REFERENCE_NO'] !== null ? htmlentities($res['GDH_DOC_REFERENCE_NO'], ENT_QUOTES) : "&nbsp;"),
																
																'PPS_PARTY_CODE' => ($res['PPS_PARTY_CODE'] !== null ? htmlentities($res['PPS_PARTY_CODE'], ENT_QUOTES) : "&nbsp;"),
																'PPS_DESC' => ($res['PPS_DESC'] !== null ? htmlentities($res['PPS_DESC'], ENT_QUOTES) : "&nbsp;"),
																'GDH_ISSUEDATE' => ($res['GDH_ISSUEDATE'] !== null ? htmlentities($res['GDH_ISSUEDATE'], ENT_QUOTES) : "&nbsp;"),
																'GDH_COMMDATE' => ($res['GDH_COMMDATE'] !== null ? htmlentities($res['GDH_COMMDATE'], ENT_QUOTES) : "&nbsp;"),
																'GDH_EXPIRYDATE' => ($res['GDH_EXPIRYDATE'] !== null ? htmlentities($res['GDH_EXPIRYDATE'], ENT_QUOTES) : "&nbsp;"),
																'GDH_GROSSPREMIUM' => ($res['GDH_GROSSPREMIUM'] !== null ? htmlentities($res['GDH_GROSSPREMIUM'], ENT_QUOTES) : "&nbsp;"),
																
																'GDH_TOTALSI' => ($res['GDH_TOTALSI'] !== null ? htmlentities($res['GDH_TOTALSI'], ENT_QUOTES) : "&nbsp;"),
																'GDH_NETPREMIUM' => ($res['GDH_NETPREMIUM'] !== null ? htmlentities($res['GDH_NETPREMIUM'], ENT_QUOTES) : "&nbsp;"),
																'PAS_ADDRESS1' => ($res['PAS_ADDRESS1'] !== null ? htmlentities($res['PAS_ADDRESS1'], ENT_QUOTES) : "&nbsp;"),
																'PIY_INSUTYPE' => ($res['PIY_INSUTYPE'] !== null ? htmlentities($res['PIY_INSUTYPE'], ENT_QUOTES) : "&nbsp;"),
																
																'PII_CODE' => ($res['PII_CODE'] !== null ? htmlentities($res['PII_CODE'], ENT_QUOTES) : "&nbsp;"),
																'GGD_SINGLESHIPLIMIT' => ($res['GGD_SINGLESHIPLIMIT'] !== null ? htmlentities($res['GGD_SINGLESHIPLIMIT'], ENT_QUOTES) : "&nbsp;"),
																'GGD_DOUBLESHIPLIMIT' => ($res['GGD_DOUBLESHIPLIMIT'] !== null ? htmlentities($res['GGD_DOUBLESHIPLIMIT'], ENT_QUOTES) : "&nbsp;").
																
																'PPS_PARTY_CODE' => ($res['PPS_PARTY_CODE'] !== null ? htmlentities($res['PPS_PARTY_CODE'], ENT_QUOTES) : "&nbsp;")																
															  );	
														
														array_push($posts_arr, $post_item);
                                                }
												
												
										

												echo json_encode($posts_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
												
												
												
}
// Close the Oracle connection
oci_close($conn);
?>
