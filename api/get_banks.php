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
 //echo "Connected to Oracle";
 $loc = $_GET['loc'];
 $type = $_GET['type'];
 $doc = $_GET['doc'];
 $year = $_GET['year'];
 $dept = $_GET['dept'];
 $busc = $_GET['busc'];
			
				
						$query ="SELECT UW_BANKDTL.PBN_BNK_CODE, UW_BANKDTL.PBN_BNK_DESC
						FROM ailmis.UW_BANKDTL 
						Where PLC_LOC_CODE = '$loc' 
						AND PDT_DOCTYPE = '$type'
						AND GDH_DOCUMENTNO = '$doc'
						AND GDH_YEAR = '$year'
						AND PDP_DEPT_CODE = '$dept'
						AND PBC_BUSICLASS_CODE = '$busc' ";
						
						$combo = oci_parse($conn, $query);
					
												//Current
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
																'PBN_BNK_CODE' => ($res['PBN_BNK_CODE'] !== null ? $res['PBN_BNK_CODE'] : "&nbsp;"),
																'PBN_BNK_DESC' => ( $res['PBN_BNK_DESC'] !== null ? $res['PBN_BNK_DESC'] : "&nbsp;")
																
															  );	
														
														array_push($posts_arr, $post_item);
                                                }
												
												
												
											

												echo json_encode($posts_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
												
												
												
}
// Close the Oracle connection
oci_close($conn);
?>
